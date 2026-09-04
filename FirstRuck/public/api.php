<?php

declare(strict_types=1);

use FirstRuck\RecommendationEngine;

require_once dirname(__DIR__) . '/src/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

try {
    $action = (string) ($_GET['action'] ?? 'bootstrap');
    $database = first_ruck_database();
    $engine = new RecommendationEngine();

    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
    }

    if ($action === 'bootstrap' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $profileStatement = $database->prepare('SELECT answers_json, generated_profile_json FROM profiles WHERE session_key = :session_key LIMIT 1');
        $profileStatement->execute(['session_key' => session_id()]);
        $stored = $profileStatement->fetch();

        respond([
            'ok' => true,
            'csrf_token' => $_SESSION['csrf_token'],
            'profile' => $stored ? json_decode($stored['generated_profile_json'], true, 512, JSON_THROW_ON_ERROR) : null,
            'answers' => $stored ? json_decode($stored['answers_json'], true, 512, JSON_THROW_ON_ERROR) : null,
        ]);
    }

    if ($action === 'recommend' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        assert_csrf_token();
        $payload = request_json();
        $answers = validate_answers($payload['answers'] ?? null);
        $profile = $engine->buildProfile($answers);

        $statement = $database->prepare(
            'INSERT INTO profiles (session_key, answers_json, generated_profile_json)
             VALUES (:session_key, :answers_json, :profile_json)
             ON CONFLICT(session_key) DO UPDATE SET
                answers_json = excluded.answers_json,
                generated_profile_json = excluded.generated_profile_json,
                updated_at = CURRENT_TIMESTAMP'
        );
        $statement->execute([
            'session_key' => session_id(),
            'answers_json' => json_encode($answers, JSON_THROW_ON_ERROR),
            'profile_json' => json_encode($profile, JSON_THROW_ON_ERROR),
        ]);

        $trailStatement = $database->query('SELECT * FROM trails ORDER BY name');
        $recommendations = $engine->rank($answers, $trailStatement->fetchAll());
        $profileIdStatement = $database->prepare('SELECT id FROM profiles WHERE session_key = :session_key');
        $profileIdStatement->execute(['session_key' => session_id()]);
        $profileId = (int) $profileIdStatement->fetchColumn();

        $eventStatement = $database->prepare('INSERT INTO recommendation_events (profile_id, recommendation_json) VALUES (:profile_id, :recommendation_json)');
        $eventStatement->execute([
            'profile_id' => $profileId,
            'recommendation_json' => json_encode($recommendations, JSON_THROW_ON_ERROR),
        ]);

        respond([
            'ok' => true,
            'profile' => $profile,
            'recommendations' => $recommendations,
            'data_mode' => 'demonstration',
        ]);
    }

    respond(['ok' => false, 'error' => 'That First Ruck action is unavailable.'], 404);
} catch (InvalidArgumentException $exception) {
    respond(['ok' => false, 'error' => $exception->getMessage()], 422);
} catch (Throwable $exception) {
    error_log(sprintf('First Ruck API error: %s', $exception->getMessage()));
    respond(['ok' => false, 'error' => 'Unable to save your profile. Check your connection and try again.'], 500);
}

function request_json(): array
{
    $raw = file_get_contents('php://input');
    $decoded = json_decode($raw ?: '{}', true);
    if (!is_array($decoded)) {
        throw new InvalidArgumentException('Send a valid profile before continuing.');
    }
    return $decoded;
}

function assert_csrf_token(): void
{
    $provided = (string) ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (!hash_equals((string) ($_SESSION['csrf_token'] ?? ''), $provided)) {
        respond(['ok' => false, 'error' => 'Your session expired. Refresh the page and try again.'], 419);
    }
}

function validate_answers(mixed $answers): array
{
    if (!is_array($answers)) {
        throw new InvalidArgumentException('Complete your profile before continuing.');
    }

    $required = [
        'goal', 'weekly_movement', 'comfortable_minutes', 'equipment',
        'available_load', 'surface', 'hill_comfort', 'sessions_per_week',
        'route_type', 'setting', 'body_consideration', 'location_label',
    ];
    foreach ($required as $key) {
        if (!isset($answers[$key]) || $answers[$key] === '') {
            throw new InvalidArgumentException('Choose an answer for each step before continuing.');
        }
    }

    $clean = [];
    foreach ($answers as $key => $value) {
        if (!is_string($key) || (!is_string($value) && !is_numeric($value))) {
            continue;
        }
        $clean[$key] = mb_substr(trim((string) $value), 0, 120);
    }
    return $clean;
}

function respond(array $payload, int $status = 200): never
{
    http_response_code($status);
    echo json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
    exit;
}

