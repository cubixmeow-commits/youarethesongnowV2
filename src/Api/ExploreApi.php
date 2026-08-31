<?php

declare(strict_types=1);

namespace Yatsn\Api;

use Yatsn\AI\GeminiExploreService;
use Yatsn\Auth\SessionService;
use Yatsn\Http\Request;
use Yatsn\Http\Router;
use Yatsn\Styles\StyleService;
use Yatsn\Support\BuildInfo;
use Yatsn\Support\JsonResponse;

final class ExploreApi
{
    public static function register(Router $router): void
    {
        $router->post('/api/v1/explore-directions', function (Request $request) {
            $session = SessionService::current();
            if (!$session) {
                JsonResponse::error('unauthorized', 'Sign in to continue.', 401);
            }

            $expected = (string) ($session['csrf_token'] ?? '');
            $provided = (string) ($request->header('X-CSRF-Token') ?? '');
            if ($expected === '' || $provided === '' || !hash_equals($expected, $provided)) {
                JsonResponse::error('csrf_failed', 'Refresh the page and try again.', 403);
            }

            $songDna = $request->input('songDna', []);
            if (!is_array($songDna)) {
                JsonResponse::error('song_dna_required', 'Discover a song before exploring directions.', 422);
            }

            try {
                JsonResponse::data(GeminiExploreService::directions(
                    $songDna,
                    StyleService::activeForClient()
                ));
            } catch (\InvalidArgumentException $e) {
                self::fail(
                    'song_dna_required',
                    'Discover a song before exploring directions.',
                    422,
                    GeminiExploreService::lastDiagnostic() !== ''
                        ? GeminiExploreService::lastDiagnostic()
                        : 'song-dna-required'
                );
            } catch (\RuntimeException $e) {
                $code = $e->getMessage();
                $diagnostic = GeminiExploreService::lastDiagnostic();
                if ($diagnostic === '') {
                    $diagnostic = GeminiExploreService::safeFailureStatus($code);
                }

                if ($code === 'provider_http_429') {
                    self::fail(
                        'explore_rate_limited',
                        'Gemini is busy or the free quota was reached. Try again shortly.',
                        429,
                        $diagnostic,
                        30
                    );
                }
                if (in_array($code, ['gemini_unavailable', 'provider_http_401', 'provider_http_403', 'provider_http_404'], true)) {
                    self::fail(
                        'explore_unavailable',
                        'AI directions are unavailable right now. You can still choose a direction manually.',
                        503,
                        $diagnostic
                    );
                }
                self::fail(
                    'explore_failed',
                    'We could not create visual directions for this song yet. Try again.',
                    503,
                    $diagnostic
                );
            }
        });

        // Private-build / owner readiness. Never calls the provider and never returns secrets.
        $router->get('/api/v1/explore-directions/readiness', function () {
            $session = SessionService::current();
            if (!$session) {
                JsonResponse::error('unauthorized', 'Sign in to continue.', 401);
            }
            if (!BuildInfo::allowDiagnostics()) {
                JsonResponse::error('not_found', 'Not found.', 404);
            }
            if (($session['role'] ?? '') !== 'owner') {
                JsonResponse::error('forbidden', 'Owner access required.', 403);
            }
            JsonResponse::data([
                'explore' => GeminiExploreService::readiness(),
                'build' => BuildInfo::publicSummary(),
            ]);
        });
    }

    private static function fail(
        string $code,
        string $message,
        int $status,
        string $diagnostic,
        ?int $retryAfterSeconds = null
    ): never {
        $fields = [];
        if (BuildInfo::allowDiagnostics() && $diagnostic !== '') {
            // Concise machine-readable status only. No keys, prompts, DNA, or provider bodies.
            $fields['diagnostic'] = $diagnostic;
            $build = BuildInfo::publicSummary();
            if (!empty($build['commit'])) {
                $fields['build'] = (string) $build['commit'];
            }
        }
        JsonResponse::error($code, $message, $status, $fields, null, $retryAfterSeconds);
    }
}
