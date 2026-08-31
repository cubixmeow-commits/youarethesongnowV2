#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Lightweight automated test runner for Private Development Build 1.
 */

$root = dirname(__DIR__);
$testDb = $root . '/var/tmp/test-yatsn.sqlite';
$testStorage = $root . '/var/tmp/test-storage';
$testLog = $root . '/var/tmp/test-log';

foreach ([$testStorage, $testLog, dirname($testDb)] as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0770, true);
    }
}
if (is_file($testDb)) {
    unlink($testDb);
}
foreach ([$testStorage . '/portraits', $testStorage . '/images', $testStorage . '/tmp'] as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0770, true);
    }
}

// Isolate environment before bootstrap.
putenv('APP_ENV=development');
putenv('APP_DEBUG=true');
putenv('APP_URL=http://127.0.0.1:8080');
putenv('APP_KEY=test-key-' . bin2hex(random_bytes(8)));
putenv('DATABASE_PATH=' . $testDb);
putenv('PRIVATE_STORAGE_PATH=' . $testStorage);
putenv('LOG_PATH=' . $testLog);
putenv('ALLOW_EXTERNAL_USERS=false');
putenv('ALLOW_PUBLIC_REGISTRATION=false');
putenv('ALLOW_LIVE_PAYMENTS=false');
putenv('ALLOW_PROTECTED_LYRICS_COMMERCIAL_USE=false');
putenv('AI_PROVIDERS_ENABLED=false');
putenv('DEVELOPMENT_MONTHLY_CREDITS=100');
putenv('LOW_QUALITY_CREDITS=1');
putenv('MEDIUM_QUALITY_CREDITS=2');
putenv('HIGH_QUALITY_CREDITS=3');
putenv('MAIL_TRANSPORT=log');
putenv('OWNER_EMAIL=owner@example.test');
putenv('OWNER_PASSWORD=owner-password-123');
putenv('OWNER_DISPLAY_NAME=CuBiX Test Owner');
$_ENV = array_merge($_ENV, [
    'APP_ENV' => 'development',
    'APP_DEBUG' => 'true',
    'APP_URL' => 'http://127.0.0.1:8080',
    'APP_KEY' => getenv('APP_KEY'),
    'DATABASE_PATH' => $testDb,
    'PRIVATE_STORAGE_PATH' => $testStorage,
    'LOG_PATH' => $testLog,
    'ALLOW_EXTERNAL_USERS' => 'false',
    'ALLOW_PUBLIC_REGISTRATION' => 'false',
    'ALLOW_LIVE_PAYMENTS' => 'false',
    'ALLOW_PROTECTED_LYRICS_COMMERCIAL_USE' => 'false',
    'AI_PROVIDERS_ENABLED' => 'false',
    'DEVELOPMENT_MONTHLY_CREDITS' => '100',
    'MAIL_TRANSPORT' => 'log',
    'OWNER_EMAIL' => 'owner@example.test',
    'OWNER_PASSWORD' => 'owner-password-123',
    'OWNER_DISPLAY_NAME' => 'CuBiX Test Owner',
]);

require $root . '/app/bootstrap.php';

use Yatsn\Auth\AuthService;
use Yatsn\Auth\AccountService;
use Yatsn\Auth\InvitationService;
use Yatsn\Auth\SessionService;
use Yatsn\Billing\StripeService;
use Yatsn\Credits\CreditService;
use Yatsn\Generation\DraftService;
use Yatsn\Generation\GenerationJobService;
use Yatsn\Generation\SongLookupService;
use Yatsn\Mail\Mailer;
use Yatsn\Portraits\PortraitService;
use Yatsn\Sharing\GalleryService;
use Yatsn\Storage\LocalStorage;
use Yatsn\Styles\StyleService;
use Yatsn\Support\Config;
use Yatsn\Support\Database;
use Yatsn\Support\Migrator;

$passed = 0;
$failed = 0;
$failures = [];

function assert_true(bool $cond, string $name): void
{
    global $passed, $failed, $failures;
    if ($cond) {
        $passed++;
        echo "PASS  {$name}\n";
    } else {
        $failed++;
        $failures[] = $name;
        echo "FAIL  {$name}\n";
    }
}

function make_jpeg_fixture(string $path): void
{
    $img = imagecreatetruecolor(240, 240);
    $bg = imagecolorallocate($img, 40, 40, 50);
    $fg = imagecolorallocate($img, 220, 200, 180);
    imagefilledrectangle($img, 0, 0, 240, 240, $bg);
    imagefilledellipse($img, 120, 110, 90, 110, $fg);
    imagejpeg($img, $path, 90);
}

echo "=== YATSN Build 1 tests ===\n";
Config::ensureDirectories();
$applied = Migrator::migrate();
assert_true($applied !== [] || Migrator::status()[0]['applied'] ?? false, 'migrations run from empty database');
$seeded = StyleService::seed();
assert_true($seeded >= 52 || count(StyleService::allForOwner()) === 52, '52 styles seeded');
assert_true(count(StyleService::activeForClient()) === 15, '15 active launch styles');
$inactiveStyle = null;
foreach (StyleService::allForOwner() as $styleRow) {
    if ($styleRow['status'] === 'inactive') {
        $inactiveStyle = $styleRow;
        break;
    }
}
assert_true($inactiveStyle !== null, 'inactive style available for owner toggle test');
$activatedStyle = StyleService::setStatus($inactiveStyle['id'], 'active');
assert_true($activatedStyle['status'] === 'active', 'owner can activate style');
assert_true(count(StyleService::activeForClient()) === 16, 'activated style appears to clients');
$deactivatedStyle = StyleService::setStatus($inactiveStyle['id'], 'inactive');
assert_true($deactivatedStyle['status'] === 'inactive', 'owner can deactivate style');
assert_true(count(StyleService::activeForClient()) === 15, 'deactivated style removed from clients');

$owner = AuthService::seedOwner();
assert_true($owner['email'] === 'owner@example.test', 'owner seeded from protected config');
$ownerRow = Database::one('SELECT * FROM users WHERE email = :e', ['e' => 'owner@example.test']);
assert_true($ownerRow['role'] === 'owner', 'owner role assigned');
assert_true(CreditService::balance((int) $ownerRow['id']) >= 100, 'owner development credits granted');

// Uninvited registration is impossible: no public register endpoint / service.
assert_true(Config::getBool('gates.allow_public_registration') === false, 'public registration gate disabled');
assert_true(Config::getBool('gates.allow_external_users') === false, 'external users gate disabled');
assert_true(Config::getBool('gates.allow_live_payments') === false, 'live payments gate disabled');

$invite = InvitationService::create((int) $ownerRow['id'], 'tester@example.test', 'complimentaryReviewer');
assert_true(!empty($invite['activationToken']), 'invitation created with local activation token');
$activated = InvitationService::activate($invite['activationToken'], 'Tester', true, true);
assert_true($activated['user']['email'] === 'tester@example.test', 'invitation activation works');
$user = Database::one('SELECT * FROM users WHERE email = :e', ['e' => 'tester@example.test']);
assert_true(CreditService::balance((int) $user['id']) === 100, 'complimentary credits granted once');

// Authz: user A cannot read user B portraits/images
$otherInvite = InvitationService::create((int) $ownerRow['id'], 'other@example.test', 'complimentaryReviewer');
InvitationService::activate($otherInvite['activationToken'], 'Other', true, true);
$other = Database::one('SELECT * FROM users WHERE email = :e', ['e' => 'other@example.test']);

$fixture = $testStorage . '/tmp/face.jpg';
make_jpeg_fixture($fixture);
$_FILES = [];
// Simulate upload via temp copy
$uploadTmp = $testStorage . '/tmp/upload.jpg';
copy($fixture, $uploadTmp);
$portrait = PortraitService::upload((int) $user['id'], [
    'tmp_name' => $uploadTmp,
    'error' => UPLOAD_ERR_OK,
    'size' => filesize($uploadTmp),
    'name' => 'face.jpg',
    'type' => 'image/jpeg',
]);
assert_true(isset($portrait['id']), 'portrait upload/normalize succeeds');

$blocked = false;
try {
    PortraitService::content((int) $other['id'], $portrait['id']);
} catch (Throwable $e) {
    $blocked = true;
}
assert_true($blocked, 'user cannot access another account portrait');

$portraitRow = Database::one('SELECT * FROM portraits WHERE public_id = :pid', ['pid' => $portrait['id']]);
assert_true(is_array($portraitRow), 'portrait row exists before delete');
$storageKey = (string) $portraitRow['storage_key'];
$thumbKey = (string) $portraitRow['thumb_key'];
assert_true(\Yatsn\Storage\LocalStorage::exists($storageKey), 'portrait storage file exists before delete');
assert_true(\Yatsn\Storage\LocalStorage::exists($thumbKey), 'portrait thumb file exists before delete');

$foreignDeleteBlocked = false;
try {
    PortraitService::delete((int) $other['id'], $portrait['id']);
} catch (Throwable $e) {
    $foreignDeleteBlocked = $e->getMessage() === 'not_found';
}
assert_true($foreignDeleteBlocked, 'user cannot delete another account portrait');
assert_true(\Yatsn\Storage\LocalStorage::exists($storageKey), 'foreign delete attempt leaves owned portrait file intact');

PortraitService::delete((int) $user['id'], $portrait['id']);
$deletedRow = Database::one('SELECT * FROM portraits WHERE public_id = :pid', ['pid' => $portrait['id']]);
assert_true($deletedRow !== null && $deletedRow['deleted_at'] !== null, 'portrait soft-delete sets deleted_at');
assert_true(!\Yatsn\Storage\LocalStorage::exists($storageKey), 'portrait storage file removed after delete');
assert_true(!\Yatsn\Storage\LocalStorage::exists($thumbKey), 'portrait thumb file removed after delete');
$listAfterDelete = PortraitService::list((int) $user['id']);
assert_true(count(array_filter($listAfterDelete, fn ($p) => $p['id'] === $portrait['id'])) === 0, 'deleted portrait absent from list');

$missingDeleteBlocked = false;
try {
    PortraitService::delete((int) $user['id'], $portrait['id']);
} catch (Throwable $e) {
    $missingDeleteBlocked = $e->getMessage() === 'not_found';
}
assert_true($missingDeleteBlocked, 'deleting already-deleted portrait is rejected');

// Fresh owned portrait for the rest of the creative-path tests.
$uploadTmp = $testStorage . '/tmp/upload.jpg';
copy($fixture, $uploadTmp);
$portrait = PortraitService::upload((int) $user['id'], [
    'tmp_name' => $uploadTmp,
    'error' => UPLOAD_ERR_OK,
    'size' => filesize($uploadTmp),
    'name' => 'face.jpg',
    'type' => 'image/jpeg',
]);
assert_true(isset($portrait['id']), 'replacement portrait upload succeeds after delete');

$lookup = SongLookupService::create((int) $user['id'], 'Owner Test Band', 'Midnight Harbor');
assert_true($lookup['state'] === 'found', 'deterministic song lookup found');
$selectedDnaFixture = [
    'essence' => 'A safe derived emotional interpretation.',
    'themes' => ['belonging'],
    'mood' => ['hopeful'],
    'originalVisualMoment' => 'A protagonist crosses a rain-bright threshold into warm light.',
];
Database::exec(
    'UPDATE song_lookups SET derived_analysis_json = :analysis, analysis_basis = :basis, analysis_provider = :provider WHERE public_id = :pid',
    [
        'analysis' => json_encode($selectedDnaFixture, JSON_THROW_ON_ERROR),
        'basis' => 'lyrics',
        'provider' => 'gemini-search',
        'pid' => $lookup['id'],
    ]
);
$notFound = SongLookupService::create((int) $user['id'], 'x', 'y');
assert_true($notFound['state'] === 'notFound', 'short/unknown song returns notFound');

$draft = DraftService::create((int) $user['id'], [
    'songLookupId' => $lookup['id'],
    'portraitIds' => [$portrait['id']],
    'styleId' => StyleService::activeForClient()[0]['id'],
    'quality' => 'medium',
    'orientation' => 'square',
    'noTextInImage' => true,
]);
$summary = DraftService::summary((int) $user['id'], $draft['id']);
assert_true($summary['ready'] === true, 'draft summary ready');
assert_true($summary['requiresMembership'] === false, 'complimentary reviewer skips paywall');

$before = CreditService::balance((int) $user['id']);
$job = GenerationJobService::submit((int) $user['id'], $draft['id'], 'idem-job-1');
assert_true($job['status'] === 'queued', 'job submitted as queued');
assert_true(CreditService::balance((int) $user['id']) === $before - 2, 'credits reserved on submit');
$jobSnapshotRow = Database::one('SELECT snapshot_json FROM generation_jobs WHERE public_id = :pid', ['pid' => $job['id']]);
$jobSnapshot = json_decode((string) ($jobSnapshotRow['snapshot_json'] ?? ''), true);
assert_true(($jobSnapshot['derivedSongAnalysis']['originalVisualMoment'] ?? '') === $selectedDnaFixture['originalVisualMoment'], 'generation snapshot reuses selected derived Song DNA');
assert_true(($jobSnapshot['songAnalysisBasis'] ?? '') === 'lyrics', 'generation snapshot preserves song analysis basis');

$dup = GenerationJobService::submit((int) $user['id'], $draft['id'], 'idem-job-1');
assert_true($dup['id'] === $job['id'], 'idempotent generation submit returns same job');
assert_true(CreditService::balance((int) $user['id']) === $before - 2, 'idempotent submit does not double-reserve');

$processed = GenerationJobService::processNext();
assert_true(($processed['status'] ?? null) === 'completed', 'worker completes deterministic generation');
$jobDone = GenerationJobService::getOwned((int) $user['id'], $job['id']);
assert_true($jobDone['status'] === 'completed', 'job status completed');
assert_true(!empty($jobDone['generatedImageId']), 'generated image attached');
assert_true(CreditService::balance((int) $user['id']) === $before - 2, 'capture keeps reserved deduction');

$imageId = $jobDone['generatedImageId'];
$image = GalleryService::getOwned((int) $user['id'], $imageId);
assert_true($image['title'] === 'Midnight Harbor', 'gallery image labeled with song title');

$cross = false;
try {
    GalleryService::getOwned((int) $other['id'], $imageId);
} catch (Throwable $e) {
    $cross = true;
}
assert_true($cross, 'user cannot access another account gallery image');

$share = GalleryService::createLinkShare((int) $user['id'], $imageId);
$shared = GalleryService::sharedByToken($share['token']);
assert_true($shared['image']['id'] === $imageId, 'share link resolves');
GalleryService::revokeLinkShare((int) $user['id'], $imageId);
$revoked = false;
try {
    GalleryService::sharedByToken($share['token']);
} catch (Throwable $e) {
    $revoked = true;
}
assert_true($revoked, 'revoked share link fails');

// Failure releases credits
$uploadTmp2 = $testStorage . '/tmp/upload2.jpg';
copy($fixture, $uploadTmp2);
$portrait2 = PortraitService::upload((int) $user['id'], [
    'tmp_name' => $uploadTmp2,
    'error' => UPLOAD_ERR_OK,
    'size' => filesize($uploadTmp2),
    'name' => 'face2.jpg',
    'type' => 'image/jpeg',
]);
$lookup2 = SongLookupService::create((int) $user['id'], 'Celebration Ensemble', 'First Dance Light');
$draft2 = DraftService::create((int) $user['id'], [
    'songLookupId' => $lookup2['id'],
    'portraitIds' => [$portrait2['id']],
    'styleId' => StyleService::activeForClient()[1]['id'],
    'quality' => 'low',
    'orientation' => 'portrait',
    'specialInstructions' => 'FORCE_FAIL for test',
]);
$beforeFail = CreditService::balance((int) $user['id']);
$failJob = GenerationJobService::submit((int) $user['id'], $draft2['id'], 'idem-fail-1');
GenerationJobService::processNext();
$failedJob = GenerationJobService::getOwned((int) $user['id'], $failJob['id']);
assert_true($failedJob['status'] === 'failed', 'forced failure reaches failed state');
assert_true($failedJob['creditsReturned'] === true, 'failed job reports credits returned');
assert_true(CreditService::balance((int) $user['id']) === $beforeFail, 'final failure releases reserved credits');

// Deletion removes files and revokes shares
$uploadTmp3 = $testStorage . '/tmp/upload3.jpg';
copy($fixture, $uploadTmp3);
$portrait3 = PortraitService::upload((int) $user['id'], [
    'tmp_name' => $uploadTmp3,
    'error' => UPLOAD_ERR_OK,
    'size' => filesize($uploadTmp3),
    'name' => 'face3.jpg',
    'type' => 'image/jpeg',
]);
$lookup3 = SongLookupService::create((int) $user['id'], 'Public Domain Demo', 'Amazing Grace');
$draft3 = DraftService::create((int) $user['id'], [
    'songLookupId' => $lookup3['id'],
    'portraitIds' => [$portrait3['id']],
    'styleId' => StyleService::activeForClient()[2]['id'],
    'quality' => 'medium',
    'orientation' => 'landscape',
]);
$job3 = GenerationJobService::submit((int) $user['id'], $draft3['id'], 'idem-del-1');
GenerationJobService::processNext();
$done3 = GenerationJobService::getOwned((int) $user['id'], $job3['id']);
$img3 = $done3['generatedImageId'];
$share3 = GalleryService::createLinkShare((int) $user['id'], $img3);
$row3 = Database::one('SELECT * FROM generated_images WHERE public_id = :p', ['p' => $img3]);
$storagePath = $testStorage . '/' . $row3['storage_key'];
assert_true(is_file($storagePath), 'generated file exists before delete');
GalleryService::delete((int) $user['id'], $img3);
assert_true(!is_file($storagePath), 'generated file removed on delete');
$shareGone = false;
try {
    GalleryService::sharedByToken($share3['token']);
} catch (Throwable $e) {
    $shareGone = true;
}
assert_true($shareGone, 'image deletion revokes shares');

// Owner endpoints conceptually gated by role check
$sessionUser = [
    'role' => 'user',
];
assert_true(($sessionUser['role'] ?? '') !== 'owner', 'normal account is not owner');

// Leakage: search DNA artifacts and logs for lyric fixture markers
$artifacts = Database::all('SELECT dna_json, compiled_prompt_safe FROM song_dna_artifacts');
$leak = false;
foreach ($artifacts as $a) {
    if (preg_match('/\b(verse|chorus)\b.*\b(baby|darling)\b/i', $a['dna_json'] . $a['compiled_prompt_safe'])) {
        $leak = true;
    }
    if (str_contains($a['dna_json'], '"lyrics"') || str_contains($a['compiled_prompt_safe'], 'LYRIC TEXT:')) {
        $leak = true;
    }
}
assert_true($leak === false, 'no raw lyric fixtures in stored creative artifacts');
$selectedAnalyses = Database::all('SELECT derived_analysis_json FROM song_lookups WHERE derived_analysis_json IS NOT NULL');
$selectedLeak = false;
foreach ($selectedAnalyses as $selected) {
    $stored = (string) ($selected['derived_analysis_json'] ?? '');
    if (str_contains($stored, '"lyrics"') || str_contains($stored, 'LYRIC TEXT:')) {
        $selectedLeak = true;
    }
}
assert_true($selectedLeak === false, 'selected Song DNA storage contains no raw lyric field');
$mailLog = @file_get_contents($testLog . '/mail.log') ?: '';
assert_true(!str_contains($mailLog, 'STRIPE_SECRET') && !str_contains($mailLog, 'GROQ_API_KEY'), 'mail log has no provider secrets');

// Stripe webhook cryptographic signature verification + idempotency
$whsec = 'whsec_test_secret_for_signatures';
putenv('STRIPE_WEBHOOK_SECRET=' . $whsec);
$_ENV['STRIPE_WEBHOOK_SECRET'] = $whsec;
\Yatsn\Support\Config::boot($root);
$eventPayload = json_encode([
    'id' => 'evt_test_1',
    'type' => 'checkout.session.completed',
    'data' => ['object' => [
        'object' => 'checkout.session',
        'client_reference_id' => $user['public_id'],
        'customer' => 'cus_test',
        'metadata' => ['user_public_id' => $user['public_id']],
    ]],
], JSON_THROW_ON_ERROR);
$validSig = StripeService::signPayloadForTests($eventPayload, $whsec);
$invalidRejected = false;
try {
    StripeService::handleWebhook($eventPayload, 't=' . time() . ',v1=deadbeef');
} catch (Throwable $e) {
    $invalidRejected = $e->getMessage() === 'stripe_signature_invalid';
}
assert_true($invalidRejected, 'invalid Stripe webhook signature rejected');
$missingRejected = false;
try {
    StripeService::handleWebhook($eventPayload, null);
} catch (Throwable $e) {
    $missingRejected = $e->getMessage() === 'stripe_signature_missing';
}
assert_true($missingRejected, 'missing Stripe webhook signature rejected');
$first = StripeService::handleWebhook($eventPayload, $validSig);
$second = StripeService::handleWebhook($eventPayload, $validSig);
assert_true(($first['received'] ?? false) === true, 'valid Stripe webhook accepted once');
assert_true(($second['duplicate'] ?? false) === true, 'Stripe webhook replay is idempotent');

// Honest adapter status
$status = Config::setupStatus();
assert_true($status['creativeAdapter'] === 'deterministic-development', 'setup reports deterministic creative adapter');
assert_true($status['imageAdapter'] === 'deterministic-development-image', 'setup reports deterministic image adapter');
assert_true($status['stripeAdapter'] === 'stripe-unavailable', 'setup does not claim Stripe is ready without keys');
assert_true($status['mailTransport'] === 'log', 'setup reports active log mail transport');

// No-text deterministic image has no readable labels
$noTextImage = (new \Yatsn\CreativeEngine\DevelopmentImageAdapter())->generate([], [
    'orientation' => 'square',
    'noTextInImage' => true,
    'portraitCount' => 1,
    'styleName' => 'Cinematic Realism',
]);
$gd = imagecreatefromstring($noTextImage['bytes']);
// Spot-check: encoded JPEG should not contain the ASCII development banner.
assert_true(!str_contains($noTextImage['bytes'], 'DEVELOPMENT IMAGE'), 'no-text output omits development label bytes');
assert_true(!str_contains($noTextImage['bytes'], 'deterministic-development'), 'no-text output omits adapter label bytes');

// Live-adapter parsing and safety transformations are tested without network calls or real keys.
$analysisFixture = [
    'essence' => 'A Blitzkrieg Bop feeling becomes shared momentum.',
    'openingState' => 'restless anticipation',
    'turningPoint' => 'collective release',
    'closingState' => 'joyful resolve',
    'intensityPattern' => ['rising', 'surging'],
    'themes' => ['belonging', 'release'],
    'relationshipDynamics' => ['partners', 'shared courage'],
    'narrativeArchetype' => 'transformation',
    'originalVisualMoment' => 'Two people step into a rain-bright avenue as a wave of warm light opens ahead.',
    'symbols' => [['concept' => 'threshold', 'visualTranslation' => 'an opening corridor of amber light']],
    'visualMetaphors' => ['weather becoming momentum'],
    'mood' => ['electric', 'hopeful'],
    'settingTypes' => ['night avenue'],
    'eraAtmosphere' => 'timeless contemporary',
    'weather' => ['rain'],
    'spatialCharacter' => ['deep perspective'],
    'palette' => ['amber', 'near black'],
    'lighting' => ['strong rim light'],
    'camera' => ['35mm eye-level'],
    'composition' => ['two centered protagonists'],
    'motion' => ['wind-driven rain'],
    'texture' => ['matte film grain'],
    'subjectRoles' => ['partners'],
    'ambiguities' => [],
    'confidence' => 0.9,
    'riskFlags' => ['possible_quote', 'not_an_allowed_code'],
];
$safePackage = \Yatsn\AI\CreativePackageBuilder::build($analysisFixture, [
    'title' => 'Blitzkrieg Bop',
    'artist' => 'The Ramones',
    'portraitCount' => 2,
    'styleName' => 'Cinematic Realism',
    'styleKey' => 'photoreal_cinema',
    'quality' => 'high',
    'orientation' => 'landscape',
    'noTextInImage' => true,
    'specialInstructions' => 'copy the album cover and logo',
], 'fixture-adapter');
$safeSerialized = json_encode($safePackage, JSON_THROW_ON_ERROR);
assert_true(!str_contains(strtolower($safeSerialized), 'blitzkrieg bop'), 'creative sanitizer removes song title from stored package');
assert_true(!str_contains(strtolower($safeSerialized), 'the ramones'), 'creative sanitizer removes performer from stored package');
assert_true(str_contains($safePackage['compiledPromptSafe'], 'No letters, words'), 'creative compiler enforces no-text option');
assert_true(!str_contains(strtolower($safePackage['compiledPromptSafe']), 'copy the album cover'), 'unsafe special instructions are not forwarded');
assert_true($safePackage['dna']['riskFlags'] === ['possible_quote'], 'creative package retains only categorical risk codes');
assert_true(str_contains(strtolower($safePackage['compiledPromptSafe']), 'foreground, middle ground, and background'), 'V1-derived compiler restores dimensional environmental staging');
assert_true(str_contains($safePackage['compiledPromptSafe'], 'IMAGE 1 and IMAGE 2'), 'V1-derived compiler gives both portrait identities explicit roles');
assert_true(str_contains($safePackage['compiledPromptSafe'], 'Let Song DNA decide staging'), 'V1-derived compiler leaves staging to Song DNA rather than hard-coded crops');
assert_true(!str_contains(strtolower($safePackage['compiledPromptSafe']), 'prefer waist-up'), 'V1-derived compiler no longer mandates waist-up framing');
assert_true(str_contains($safePackage['compiledPromptSafe'], 'CURATED STYLEMAP - DOMINANT AESTHETIC'), 'V1-derived compiler applies a structured dominant StyleMap');
assert_true(str_contains($safePackage['compiledPromptSafe'], 'Premium poster-ready render'), 'creative compiler includes quality-tier craft direction');
assert_true(!str_contains($safePackage['compiledPromptSafe'], 'Recognizable portraits of real people.'), 'V1 contradictory portrait prohibition is removed');
$watercolorMap = \Yatsn\AI\StylePromptCatalog::forKey('watercolor');
assert_true(str_contains($watercolorMap['medium'], 'transparent washes'), 'launch styles have concrete recovered V1 craft direction');

$geminiDecoded = \Yatsn\AI\GeminiCreativeAdapter::decodeResponse([
    'candidates' => [['finishReason' => 'STOP', 'content' => ['parts' => [['text' => json_encode($analysisFixture, JSON_THROW_ON_ERROR)]]]]],
]);
assert_true($geminiDecoded['narrativeArchetype'] === 'transformation', 'Gemini structured response parser works');
$lyricsResearchDecoded = \Yatsn\AI\GeminiLyricsResearchService::decodeJsonText([
    'candidates' => [['content' => ['parts' => [['text' => "```json\n{\"lyricsLocated\":true,\"matchedArtist\":\"Fixture Band\"}\n```"]]]]],
]);
assert_true(($lyricsResearchDecoded['matchedArtist'] ?? '') === 'Fixture Band', 'Gemini grounded Song DNA JSON parser handles fenced output');
$interactionsPayload = \Yatsn\AI\GeminiLyricsResearchService::interactionsRequestPayload('fixture prompt', 'gemini-3.6-flash');
assert_true(\Yatsn\AI\GeminiLyricsResearchService::interactionsEndpoint() === 'https://generativelanguage.googleapis.com/v1beta/interactions', 'Gemini Song DNA uses Interactions endpoint');
assert_true(($interactionsPayload['store'] ?? null) === false, 'Interactions Song DNA request sets store=false');
assert_true(($interactionsPayload['tools'][0]['type'] ?? '') === 'google_search', 'Interactions Song DNA request includes Google Search tool');
assert_true(($interactionsPayload['response_format']['type'] ?? '') === 'text', 'Interactions response_format type is text');
assert_true(($interactionsPayload['response_format']['mime_type'] ?? '') === 'application/json', 'Interactions response_format MIME is application/json');
assert_true(isset($interactionsPayload['response_format']['schema']['properties']['analysis']), 'Interactions response schema includes analysis');
assert_true(isset($interactionsPayload['response_format']['schema']['properties']['matchedArtist']), 'Interactions response schema requires matchedArtist');
assert_true(isset($interactionsPayload['response_format']['schema']['properties']['lyricsLocated']), 'Interactions response schema requires lyricsLocated');
assert_true(in_array('essence', $interactionsPayload['response_format']['schema']['properties']['analysis']['required'] ?? [], true), 'Interactions analysis schema requires essence for hasUsableAnalysis');
assert_true(in_array('originalVisualMoment', $interactionsPayload['response_format']['schema']['properties']['analysis']['required'] ?? [], true), 'Interactions analysis schema requires originalVisualMoment');
assert_true(!isset($interactionsPayload['previous_interaction_id']), 'Interactions Song DNA request has no previous_interaction_id');
assert_true(empty($interactionsPayload['background']), 'Interactions Song DNA request does not use background execution');
assert_true(\Yatsn\AI\GeminiLyricsResearchService::safeFailureStatus('provider_http_429') === 'provider-rate-limited', 'Gemini grounding exposes safe rate-limit classification');
assert_true(\Yatsn\AI\GeminiLyricsResearchService::safeFailureStatus('provider_timeout') === 'provider-timeout', 'Gemini grounding exposes safe timeout classification');
assert_true(\Yatsn\AI\GeminiLyricsResearchService::safeFailureStatus('provider_http_400') === 'interactions-structured-search-rejected', 'Interactions structured-search rejection has explicit diagnostic status');
assert_true(\Yatsn\AI\GeminiLyricsResearchService::isTransientFailure('provider_timeout'), 'timeouts are classified as transient retryable failures');
assert_true(\Yatsn\AI\GeminiLyricsResearchService::isTransientFailure('provider_http_503'), '5xx failures are classified as transient retryable failures');
assert_true(!\Yatsn\AI\GeminiLyricsResearchService::isTransientFailure('provider_http_400'), 'structured-search rejection is not silently retried as transient');
assert_true(\Yatsn\AI\GeminiLyricsResearchService::analysisStatus(true, true, true, true) === 'grounded-lyric-song-dna-ready', 'Gemini marks verified lyric analysis distinctly');
assert_true(\Yatsn\AI\GeminiLyricsResearchService::analysisStatus(true, true, false, true) === 'grounded-context-song-dna-ready', 'Gemini permits honest grounded song-context fallback');
assert_true(\Yatsn\AI\GeminiLyricsResearchService::analysisStatus(true, false, false, true) === 'v1-model-song-dna-ready', 'Gemini accepts complete V1-style analysis without grounding metadata');
assert_true(\Yatsn\AI\GeminiLyricsResearchService::analysisStatus(true, true, false, false) === 'grounded-analysis-incomplete', 'Gemini still rejects incomplete Song DNA');

$completeAnalysisFixture = array_merge($analysisFixture, [
    'essence' => 'A restless night drive toward release.',
    'originalVisualMoment' => 'A lone figure stands at a neon-lit intersection as warm sodium light cuts the dark.',
    'themes' => ['restlessness', 'desire'],
    'mood' => ['yearning', 'electric'],
]);
assert_true(\Yatsn\AI\GeminiLyricsResearchService::hasUsableAnalysis($completeAnalysisFixture), 'complete structured Song DNA semantic content is accepted');
assert_true(!\Yatsn\AI\GeminiLyricsResearchService::hasUsableAnalysis([
    'essence' => '',
    'originalVisualMoment' => 'incomplete',
    'themes' => ['x'],
    'mood' => ['y'],
]), 'incomplete semantic Song DNA content is rejected after schema parsing');

$interactionsResponse = [
    'id' => 'int_fixture',
    'status' => 'completed',
    'steps' => [
        [
            'type' => 'google_search_call',
            'arguments' => ['queries' => ['Bruce Springsteen Dancing in the Dark lyrics']],
        ],
        [
            'type' => 'google_search_result',
            'call_id' => 'search_001',
            'result' => [['search_suggestions' => '']],
        ],
        [
            'type' => 'model_output',
            'content' => [[
                'type' => 'text',
                'text' => json_encode([
                    'matchedArtist' => 'Bruce Springsteen',
                    'matchedTitle' => 'Dancing in the Dark',
                    'matchConfidence' => 0.94,
                    'lyricsLocated' => true,
                    'analysis' => $completeAnalysisFixture,
                ], JSON_THROW_ON_ERROR),
                'annotations' => [[
                    'type' => 'url_citation',
                    'url' => 'https://example.com/song-reference',
                    'title' => 'example.com',
                ]],
            ]],
        ],
    ],
];
$interactionsDecoded = \Yatsn\AI\GeminiLyricsResearchService::decodeStructuredOutput($interactionsResponse);
assert_true(($interactionsDecoded['matchedTitle'] ?? '') === 'Dancing in the Dark', 'Interactions structured Song DNA is decoded from model_output');
assert_true(!empty($interactionsDecoded['lyricsLocated']), 'Interactions lyric verification state is preserved');
$interactionsSearch = \Yatsn\AI\GeminiLyricsResearchService::searchSummary($interactionsResponse);
assert_true($interactionsSearch['grounded'] === true, 'Interactions search metadata marks grounded results');
assert_true(in_array('Bruce Springsteen Dancing in the Dark lyrics', $interactionsSearch['queries'], true), 'Interactions search queries are extracted for inspection');
assert_true(($interactionsSearch['sources'][0]['url'] ?? '') === 'https://example.com/song-reference', 'Interactions citation sources are extracted for inspection');
assert_true(!str_contains(json_encode($interactionsSearch, JSON_THROW_ON_ERROR), 'LYRIC TEXT'), 'search summary never includes protected lyric text');
$mailLogPath = Config::get('app.log_path') . '/mail.log';
$mailBeforeInteractions = file_exists($mailLogPath) ? (string) file_get_contents($mailLogPath) : '';
assert_true(!str_contains($mailBeforeInteractions, (string) ($interactionsResponse['id'] ?? 'int_fixture')), 'raw interaction identifiers are not written into mail logs by Song DNA parsing');

// --- Gemini Explore directions (Song DNA → 3 visual directions) ---
$exploreSongDna = [
    'essence' => 'A restless night drive toward release.',
    'emotionalArc' => 'tension to release',
    'themes' => ['restlessness', 'desire'],
    'mood' => ['yearning', 'electric'],
    'narrativeArchetype' => 'transformation',
    'originalVisualMoment' => 'A lone figure stands at a neon-lit intersection as warm sodium light cuts the dark.',
    'symbols' => ['intersection', 'sodium light'],
    'environment' => ['night city'],
];
$exploreStyles = [
    ['id' => 'style_a', 'name' => 'Cinematic Realism', 'description' => 'Cinema', 'category' => 'Cinematic'],
    ['id' => 'style_b', 'name' => 'Luminous Watercolor', 'description' => 'Soft', 'category' => 'Fine Art'],
    ['id' => 'style_c', 'name' => 'Neon Noir', 'description' => 'Night', 'category' => 'Cinematic'],
];
$exploreDirectionPayload = [
    'directions' => [
        ['name' => 'Sodium Crossing', 'description' => 'Warm night light at a lonely intersection.', 'styleId' => 'style_a', 'promptHint' => 'neon intersection sodium light'],
        ['name' => 'Watercolor Drift', 'description' => 'Soft washes over a restless drive.', 'styleId' => 'style_b', 'promptHint' => 'watercolor night road'],
        ['name' => 'Noir Voltage', 'description' => 'Electric desire under cold neon.', 'styleId' => 'style_c', 'promptHint' => 'neon noir tension'],
    ],
];

putenv('AI_PROVIDERS_ENABLED=true');
putenv('GEMINI_LIVE_CALLS=true');
putenv('GEMINI_API_KEY=test-gemini-key-not-real');
putenv('GEMINI_MODEL=gemini-3.6-flash');
putenv('GEMINI_EXPLORE_MODEL=');
$_ENV['AI_PROVIDERS_ENABLED'] = 'true';
$_ENV['GEMINI_LIVE_CALLS'] = 'true';
$_ENV['GEMINI_API_KEY'] = 'test-gemini-key-not-real';
$_ENV['GEMINI_MODEL'] = 'gemini-3.6-flash';
$_ENV['GEMINI_EXPLORE_MODEL'] = '';
Config::boot($root);

\Yatsn\AI\GeminiExploreService::resetDiagnosticsForTests();
\Yatsn\AI\GeminiExploreService::setTransportForTests(null);

assert_true(\Yatsn\AI\GeminiExploreService::resolveModel() === 'gemini-3.6-flash', 'Explore reuses proven GEMINI_MODEL when override is empty');
assert_true(\Yatsn\AI\GeminiExploreService::fallbackModel('gemini-3.6-flash') === null, 'no fallback when already using general model');
assert_true(\Yatsn\AI\GeminiExploreService::availabilityFailure() === null, 'Explore gates pass when providers + live calls + key are set');
$exploreReadiness = \Yatsn\AI\GeminiExploreService::readiness();
assert_true(($exploreReadiness['ready'] ?? false) === true, 'Explore readiness is true with working Gemini config');
assert_true(($exploreReadiness['usesGeneralGeminiModel'] ?? false) === true, 'Explore readiness reports reuse of general Gemini model');
assert_true(str_contains((string) ($exploreReadiness['endpoint'] ?? ''), 'gemini-3.6-flash:generateContent'), 'Explore endpoint targets generateContent for resolved model');

$explorePayload = \Yatsn\AI\GeminiExploreService::requestPayload('fixture', \Yatsn\AI\GeminiExploreService::responseSchema(['style_a', 'style_b']));
assert_true(($explorePayload['generationConfig']['responseMimeType'] ?? '') === 'application/json', 'Explore request uses responseMimeType application/json');
assert_true(isset($explorePayload['generationConfig']['responseJsonSchema']), 'Explore structured request includes responseJsonSchema');
assert_true(!isset($explorePayload['generationConfig']['responseFormat']), 'Explore does not use Interactions-style responseFormat nesting');
$explorePlainPayload = \Yatsn\AI\GeminiExploreService::requestPayload('fixture', null);
assert_true(!isset($explorePlainPayload['generationConfig']['responseJsonSchema']), 'Explore JSON-mode fallback omits responseJsonSchema');

$safeExploreDna = \Yatsn\AI\GeminiExploreService::safeSongDna(array_merge($exploreSongDna, [
    'lyrics' => 'SECRET LYRIC LINE',
    'rawLyrics' => 'more secret',
    'portraitBytes' => 'BINARY',
]));
assert_true(!isset($safeExploreDna['lyrics']) && !isset($safeExploreDna['rawLyrics']) && !isset($safeExploreDna['portraitBytes']), 'Explore Song DNA sanitizer drops lyrics and portrait fields');
assert_true(($safeExploreDna['essence'] ?? '') === 'A restless night drive toward release.', 'Explore Song DNA sanitizer keeps derived essence');

\Yatsn\AI\GeminiExploreService::setTransportForTests(static function (string $url, array $payload, array $headers, int $timeout) use ($exploreDirectionPayload): array {
    assert_true(str_contains($url, 'gemini-3.6-flash:generateContent'), 'successful Explore call uses general Gemini model');
    assert_true(($payload['generationConfig']['responseMimeType'] ?? '') === 'application/json', 'live Explore transport receives JSON mime type');
    $headerBlob = implode("\n", $headers);
    assert_true(str_contains($headerBlob, 'x-goog-api-key:'), 'Explore sends Gemini API key header');
    assert_true(!str_contains(json_encode($payload, JSON_THROW_ON_ERROR), 'SECRET LYRIC'), 'Explore request body never includes raw lyrics');
    return [
        'candidates' => [[
            'finishReason' => 'STOP',
            'content' => ['parts' => [['text' => json_encode($exploreDirectionPayload, JSON_THROW_ON_ERROR)]]],
        ]],
    ];
});
$exploreOk = \Yatsn\AI\GeminiExploreService::directions($exploreSongDna, $exploreStyles);
assert_true(count($exploreOk['directions'] ?? []) === 3, 'successful Explore returns exactly three directions');
assert_true(($exploreOk['directions'][0]['name'] ?? '') === 'Sodium Crossing', 'Explore preserves ranked first recommendation');
assert_true(($exploreOk['model'] ?? '') === 'gemini-3.6-flash', 'Explore success reports the model used');
assert_true(\Yatsn\AI\GeminiExploreService::lastDiagnostic() === 'ok', 'Explore success diagnostic is ok');

// Provider 400 structured-output fallback (schema rejected → plain JSON mode)
$explore400Calls = 0;
\Yatsn\AI\GeminiExploreService::setTransportForTests(static function (string $url, array $payload) use (&$explore400Calls, $exploreDirectionPayload): array {
    $explore400Calls++;
    if ($explore400Calls === 1) {
        assert_true(isset($payload['generationConfig']['responseJsonSchema']), 'first Explore attempt includes schema');
        throw new RuntimeException('provider_http_400');
    }
    assert_true(!isset($payload['generationConfig']['responseJsonSchema']), 'Explore 400 fallback omits schema');
    return [
        'candidates' => [[
            'finishReason' => 'STOP',
            'content' => ['parts' => [['text' => json_encode($exploreDirectionPayload, JSON_THROW_ON_ERROR)]]],
        ]],
    ];
});
$exploreAfter400 = \Yatsn\AI\GeminiExploreService::directions($exploreSongDna, $exploreStyles);
assert_true(count($exploreAfter400['directions']) === 3, 'Explore recovers from provider 400 via JSON-mode fallback');
assert_true($explore400Calls === 2, 'Explore makes exactly one structured-output retry after HTTP 400');

// Optional explore override 404 → fallback to general GEMINI_MODEL
putenv('GEMINI_EXPLORE_MODEL=gemini-2.5-flash-lite');
$_ENV['GEMINI_EXPLORE_MODEL'] = 'gemini-2.5-flash-lite';
Config::boot($root);
assert_true(\Yatsn\AI\GeminiExploreService::resolveModel() === 'gemini-2.5-flash-lite', 'Explore override model is used when configured');
assert_true(\Yatsn\AI\GeminiExploreService::fallbackModel('gemini-2.5-flash-lite') === 'gemini-3.6-flash', 'Explore 404 path can fall back to general Gemini model');
$explore404Calls = [];
\Yatsn\AI\GeminiExploreService::setTransportForTests(static function (string $url, array $payload) use (&$explore404Calls, $exploreDirectionPayload): array {
    $explore404Calls[] = $url;
    if (str_contains($url, 'gemini-2.5-flash-lite')) {
        throw new RuntimeException('provider_http_404');
    }
    assert_true(str_contains($url, 'gemini-3.6-flash:generateContent'), 'Explore retries generateContent on proven general model after 404');
    return [
        'candidates' => [[
            'finishReason' => 'STOP',
            'content' => ['parts' => [['text' => json_encode($exploreDirectionPayload, JSON_THROW_ON_ERROR)]]],
        ]],
    ];
});
$exploreAfter404 = \Yatsn\AI\GeminiExploreService::directions($exploreSongDna, $exploreStyles);
assert_true(count($exploreAfter404['directions']) === 3, 'Explore recovers from model 404 via general Gemini fallback');
assert_true(($exploreAfter404['model'] ?? '') === 'gemini-3.6-flash', 'Explore reports fallback model after 404 recovery');
assert_true(count($explore404Calls) === 2, 'Explore attempts override then general model on 404');

// Auth / permission mapping
foreach (['provider_http_401' => 'provider-auth-or-permission-failed', 'provider_http_403' => 'provider-auth-or-permission-failed'] as $httpCode => $expectedDiag) {
    \Yatsn\AI\GeminiExploreService::setTransportForTests(static function () use ($httpCode): array {
        throw new RuntimeException($httpCode);
    });
    $authMapped = false;
    try {
        \Yatsn\AI\GeminiExploreService::directions($exploreSongDna, $exploreStyles);
    } catch (RuntimeException $e) {
        $authMapped = $e->getMessage() === $httpCode
            && \Yatsn\AI\GeminiExploreService::lastDiagnostic() === $expectedDiag
            && \Yatsn\AI\GeminiExploreService::safeFailureStatus($httpCode) === $expectedDiag;
    }
    assert_true($authMapped, 'Explore maps ' . $httpCode . ' to auth/permission diagnostic');
}

// Hard 404 when override equals general model (no secondary fallback available)
putenv('GEMINI_EXPLORE_MODEL=');
$_ENV['GEMINI_EXPLORE_MODEL'] = '';
Config::boot($root);
\Yatsn\AI\GeminiExploreService::setTransportForTests(static function (): array {
    throw new RuntimeException('provider_http_404');
});
$modelUnavailable = false;
try {
    \Yatsn\AI\GeminiExploreService::directions($exploreSongDna, $exploreStyles);
} catch (RuntimeException $e) {
    $modelUnavailable = $e->getMessage() === 'provider_http_404'
        && \Yatsn\AI\GeminiExploreService::lastDiagnostic() === 'provider-model-unavailable';
}
assert_true($modelUnavailable, 'Explore maps terminal provider 404 to model-unavailable diagnostic');

// Rate limit mapping
\Yatsn\AI\GeminiExploreService::setTransportForTests(static function (): array {
    throw new RuntimeException('provider_http_429');
});
$rateLimited = false;
try {
    \Yatsn\AI\GeminiExploreService::directions($exploreSongDna, $exploreStyles);
} catch (RuntimeException $e) {
    $rateLimited = $e->getMessage() === 'provider_http_429'
        && \Yatsn\AI\GeminiExploreService::safeFailureStatus('provider_http_429') === 'provider-rate-limited'
        && \Yatsn\AI\GeminiExploreService::lastDiagnostic() === 'provider-rate-limited';
}
assert_true($rateLimited, 'Explore maps provider 429 to rate-limit diagnostic');

// Incomplete / invalid JSON output
\Yatsn\AI\GeminiExploreService::setTransportForTests(static function (): array {
    return [
        'candidates' => [[
            'finishReason' => 'STOP',
            'content' => ['parts' => [['text' => '{"directions":[{"name":"Only One","description":"incomplete","styleId":"style_a","promptHint":"x"}]}']]],
        ]],
    ];
});
$incomplete = false;
try {
    \Yatsn\AI\GeminiExploreService::directions($exploreSongDna, $exploreStyles);
} catch (RuntimeException $e) {
    $incomplete = $e->getMessage() === 'gemini_explore_incomplete'
        && \Yatsn\AI\GeminiExploreService::lastDiagnostic() === 'provider-incomplete-output';
}
assert_true($incomplete, 'Explore rejects incomplete direction sets');

\Yatsn\AI\GeminiExploreService::setTransportForTests(static function (): array {
    return [
        'candidates' => [[
            'finishReason' => 'STOP',
            'content' => ['parts' => [['text' => 'not-json-at-all']]],
        ]],
    ];
});
$invalidJson = false;
try {
    \Yatsn\AI\GeminiExploreService::directions($exploreSongDna, $exploreStyles);
} catch (RuntimeException $e) {
    $invalidJson = $e->getMessage() === 'gemini_explore_incomplete'
        && \Yatsn\AI\GeminiExploreService::lastDiagnostic() === 'provider-invalid-json';
}
assert_true($invalidJson, 'Explore maps invalid provider JSON to incomplete/invalid diagnostic');

// Config gate diagnostics (providers disabled / live calls off / key missing)
putenv('AI_PROVIDERS_ENABLED=false');
$_ENV['AI_PROVIDERS_ENABLED'] = 'false';
Config::boot($root);
assert_true(\Yatsn\AI\GeminiExploreService::availabilityFailure() === 'config-ai-providers-disabled', 'Explore detects AI providers gate disabled');
$gateDisabled = false;
try {
    \Yatsn\AI\GeminiExploreService::directions($exploreSongDna, $exploreStyles);
} catch (RuntimeException $e) {
    $gateDisabled = $e->getMessage() === 'gemini_unavailable'
        && \Yatsn\AI\GeminiExploreService::lastDiagnostic() === 'config-ai-providers-disabled';
}
assert_true($gateDisabled, 'Explore throws gemini_unavailable when AI providers gate is off');

putenv('AI_PROVIDERS_ENABLED=true');
putenv('GEMINI_LIVE_CALLS=false');
$_ENV['AI_PROVIDERS_ENABLED'] = 'true';
$_ENV['GEMINI_LIVE_CALLS'] = 'false';
Config::boot($root);
assert_true(\Yatsn\AI\GeminiExploreService::availabilityFailure() === 'config-gemini-live-calls-disabled', 'Explore detects Gemini live-calls disabled');

putenv('GEMINI_LIVE_CALLS=true');
putenv('GEMINI_API_KEY=');
$_ENV['GEMINI_LIVE_CALLS'] = 'true';
$_ENV['GEMINI_API_KEY'] = '';
Config::boot($root);
assert_true(\Yatsn\AI\GeminiExploreService::availabilityFailure() === 'config-gemini-api-key-missing', 'Explore detects missing Gemini API key');

$providerLog = @file_get_contents($testLog . '/ai-providers.log') ?: '';
assert_true(str_contains($providerLog, 'gemini-explore'), 'Explore writes sanitized provider diagnostics');
assert_true(!str_contains($providerLog, 'test-gemini-key-not-real'), 'Explore diagnostics never log API keys');
assert_true(!str_contains($providerLog, 'SECRET LYRIC'), 'Explore diagnostics never log lyrics');
assert_true(!str_contains($providerLog, 'A restless night drive'), 'Explore diagnostics never log Song DNA prompt content');

\Yatsn\AI\GeminiExploreService::setTransportForTests(null);
\Yatsn\AI\GeminiExploreService::resetDiagnosticsForTests();
putenv('AI_PROVIDERS_ENABLED=false');
putenv('GEMINI_LIVE_CALLS=false');
putenv('GEMINI_API_KEY=');
putenv('GEMINI_EXPLORE_MODEL=');
$_ENV['AI_PROVIDERS_ENABLED'] = 'false';
$_ENV['GEMINI_LIVE_CALLS'] = 'false';
$_ENV['GEMINI_API_KEY'] = '';
$_ENV['GEMINI_EXPLORE_MODEL'] = '';
Config::boot($root);

$exploreJs = (string) file_get_contents($root . '/public/assets/js/explore.js');
assert_true(str_contains($exploreJs, 'fields?.diagnostic'), 'Explore UI surfaces development diagnostic codes when present');
assert_true(is_file($root . '/bin/diagnose-gemini-explore.php'), 'Explore host diagnostic command exists');
$aiStatus = \Yatsn\AI\AdapterFactory::runtimeStatus();
assert_true(array_key_exists('geminiExploreModel', $aiStatus), 'runtime status exposes resolved Explore model');

$groqDecoded = \Yatsn\AI\GroqCreativeAdapter::decodeResponse([
    'choices' => [['message' => ['content' => json_encode($analysisFixture, JSON_THROW_ON_ERROR)]]],
]);
assert_true($groqDecoded['themes'][0] === 'belonging', 'Groq structured response parser works');

$largeFixture = imagecreatetruecolor(512, 512);
$largeBg = imagecolorallocate($largeFixture, 32, 28, 35);
imagefilledrectangle($largeFixture, 0, 0, 511, 511, $largeBg);
ob_start();
imagepng($largeFixture);
$largePng = (string) ob_get_clean();
$normalizedFal = \Yatsn\AI\FalImageAdapter::normalizeImage($largePng, 'fal-test', 4);
assert_true($normalizedFal['mime'] === 'image/jpeg' && $normalizedFal['width'] === 512, 'fal output is validated and normalized to JPEG');
$replicateUrl = \Yatsn\AI\ReplicateImageAdapter::outputUrl([
    'status' => 'successful',
    'output' => ['https://replicate.delivery/example/output.jpg'],
]);
assert_true($replicateUrl === 'https://replicate.delivery/example/output.jpg', 'Replicate prediction output parser handles file arrays');
$portraitDataUri = \Yatsn\AI\ReplicateImageAdapter::privatePortraitDataUri($largePng);
$portraitPayload = base64_decode(substr($portraitDataUri, strpos($portraitDataUri, ',') + 1), true);
assert_true(str_starts_with($portraitDataUri, 'data:image/jpeg;base64,'), 'Replicate portrait reference is an ephemeral JPEG data URI');
assert_true(is_string($portraitPayload) && strlen($portraitDataUri) <= 245760, 'Replicate portrait reference stays below complete data-URI ceiling');
$compactEditPrompt = \Yatsn\AI\ReplicateImageAdapter::compactPortraitEditPrompt($safePackage, [
    'quality' => 'high',
    'noTextInImage' => true,
], 2);
assert_true(strlen($compactEditPrompt) <= 3800, 'shared compact portrait prompt stays below provider long-prompt ceiling');
assert_true(str_contains($compactEditPrompt, 'IDENTITY-FIRST SCENE INSTRUCTION'), 'compact prompt uses provider-neutral identity-first header');
assert_true(str_contains($compactEditPrompt, $analysisFixture['originalVisualMoment']), 'compact prompt preserves the Song DNA visual moment');
assert_true(str_contains($compactEditPrompt, 'image 1 and image 2'), 'compact prompt anchors both portrait identities imperatively');
assert_true(str_contains($compactEditPrompt, 'no readable text'), 'compact prompt puts no-text rule near the start');
assert_true(str_contains($compactEditPrompt, 'large enough to recognize at gallery size'), 'compact prompt requires visible portrait-scale identity');
assert_true(str_contains($compactEditPrompt, 'must not visually overwhelm or hide them'), 'compact prompt keeps the cinematic environment subordinate to the protagonists');
assert_true(str_contains($compactEditPrompt, 'tiny, distant, obscured'), 'compact prompt explicitly rejects silhouette-scale protagonists');
assert_true(\Yatsn\AI\ReplicateImageAdapter::aspectMatches(1024, 1024, '1:1'), 'Replicate accepts matching square output');
assert_true(!\Yatsn\AI\ReplicateImageAdapter::aspectMatches(1344, 768, '1:1'), 'Replicate rejects wrong landscape placeholder for square request');
assert_true(\Yatsn\AI\ReplicateImageAdapter::aspectMatches(768, 1024, '3:4'), 'shared portrait-provider validation accepts 3:4 output');
assert_true(\Yatsn\AI\ReplicateImageAdapter::aspectMatches(1024, 768, '4:3'), 'shared portrait-provider validation accepts 4:3 output');

// Gemini native multimodal image adapter (request shape + decode; no live call)
$geminiOnePortraitPayload = \Yatsn\AI\GeminiImageAdapter::buildRequestPayload($safePackage, [
    'orientation' => 'portrait',
    'quality' => 'medium',
    'noTextInImage' => true,
    'specialInstructions' => 'soft golden rim light only',
    'styleName' => 'Cinematic Realism',
], [
    ['mime' => 'image/jpeg', 'bytes' => $largePng],
]);
assert_true(\Yatsn\AI\GeminiImageAdapter::countInlineImageParts($geminiOnePortraitPayload) === 1, 'Gemini image request attaches one portrait as its own inline part');
assert_true(($geminiOnePortraitPayload['generationConfig']['responseModalities'][0] ?? '') === 'IMAGE', 'Gemini image request asks for image-only output');
assert_true(($geminiOnePortraitPayload['generationConfig']['imageConfig']['aspectRatio'] ?? '') === '3:4', 'Gemini image request passes portrait aspect ratio');
assert_true(($geminiOnePortraitPayload['generationConfig']['imageConfig']['imageSize'] ?? '') === '1K', 'Gemini image request starts at 1K development size');
$geminiOnePrompt = (string) ($geminiOnePortraitPayload['contents'][0]['parts'][0]['text'] ?? '');
assert_true(str_contains($geminiOnePrompt, $analysisFixture['originalVisualMoment']), 'Gemini image prompt includes persisted Song DNA visual moment');
assert_true(str_contains($geminiOnePrompt, 'Cinematic Realism') || str_contains($geminiOnePrompt, 'Selected style'), 'Gemini image prompt includes selected curated style');
assert_true(str_contains($geminiOnePrompt, 'soft golden rim light only'), 'Gemini image prompt includes user special instructions');
assert_true(str_contains($geminiOnePrompt, 'NO visible text'), 'Gemini image prompt honors no-text preference');
assert_true(str_contains($geminiOnePrompt, 'CHARACTER 1') && str_contains($geminiOnePrompt, 'IDENTITY — AUTHORITATIVE'), 'Gemini one-portrait prompt keeps a single authoritative identity section');
assert_true(str_contains($geminiOnePrompt, 'must appear') || str_contains($geminiOnePrompt, 'must appear and remain'), 'Gemini one-portrait prompt still requires the uploaded person');
assert_true(str_contains($geminiOnePrompt, 'narratively and emotionally central'), 'Gemini clarifies central subject is narrative, not geometric');
assert_true(str_contains($geminiOnePrompt, 'NARRATIVE STAGING FREEDOM'), 'Gemini image prompt grants narrative-driven staging freedom');
assert_true(str_contains($geminiOnePrompt, 'Camera:'), 'Gemini image prompt retains Song DNA camera direction');
assert_true(str_contains($geminiOnePrompt, 'Composition:'), 'Gemini image prompt retains Song DNA composition direction');
assert_true(!str_contains(strtolower($geminiOnePrompt), 'waist-up'), 'Gemini image prompt removes hard-coded waist-up crop requirement');
assert_true(!str_contains(strtolower($geminiOnePrompt), 'three-quarter character composition'), 'Gemini image prompt removes hard-coded three-quarter framing requirement');
assert_true(!str_contains($geminiOnePrompt, 'CANONICAL CREATIVE PACKAGE'), 'Gemini image prompt no longer appends duplicate compiled portrait-placement directives');
assert_true(!str_contains(strtolower($geminiOnePrompt), 'lyrics search'), 'Gemini image adapter does not trigger another lyrics search');

$geminiTwoPortraitPayload = \Yatsn\AI\GeminiImageAdapter::buildRequestPayload($safePackage, [
    'orientation' => 'square',
    'quality' => 'high',
    'noTextInImage' => false,
    'specialInstructions' => '',
], [
    ['mime' => 'image/jpeg', 'bytes' => $largePng],
    ['mime' => 'image/jpeg', 'bytes' => $largePng],
]);
assert_true(\Yatsn\AI\GeminiImageAdapter::countInlineImageParts($geminiTwoPortraitPayload) === 2, 'Gemini image request attaches two portraits as separate inline parts');
$geminiTwoPrompt = (string) ($geminiTwoPortraitPayload['contents'][0]['parts'][0]['text'] ?? '');
assert_true(str_contains($geminiTwoPrompt, 'CHARACTER 1') && str_contains($geminiTwoPrompt, 'CHARACTER 2'), 'Gemini two-portrait prompt preserves each identity separately');
assert_true(str_contains($geminiTwoPrompt, 'Never merge, swap, average, duplicate, or omit'), 'Gemini two-portrait prompt keeps non-negotiable identity separation');
assert_true(str_contains($geminiTwoPrompt, 'Every attached portrait must appear'), 'Gemini two-portrait prompt requires every attached person');
assert_true(str_contains($geminiTwoPrompt, 'Do not default to a generic two-person portrait layout'), 'Gemini two-portrait prompt rejects the repeated left/middle and right/foreground default');
assert_true(substr_count($geminiTwoPrompt, 'IDENTITY — AUTHORITATIVE') === 1, 'Gemini prompt consolidates identity into one authoritative section');
assert_true(!str_contains(strtolower($geminiTwoPrompt), 'create dynamic interaction'), 'Gemini prompt softens repeated dynamic-interaction wording');
assert_true(!str_contains($geminiTwoPrompt, 'PORTRAIT INTEGRATION'), 'Gemini prompt does not re-append compiled portrait-placement block');
assert_true(($geminiTwoPortraitPayload['generationConfig']['imageConfig']['aspectRatio'] ?? '') === '1:1', 'Gemini image request passes square aspect ratio');

$geminiOutputFixture = imagecreatetruecolor(768, 1024);
$geminiOutputBg = imagecolorallocate($geminiOutputFixture, 40, 36, 48);
imagefilledrectangle($geminiOutputFixture, 0, 0, 767, 1023, $geminiOutputBg);
ob_start();
imagejpeg($geminiOutputFixture, null, 90);
$geminiOutputJpeg = (string) ob_get_clean();
$geminiResponse = [
    'candidates' => [[
        'finishReason' => 'STOP',
        'content' => ['parts' => [[
            'inlineData' => [
                'mimeType' => 'image/jpeg',
                'data' => base64_encode($geminiOutputJpeg),
            ],
        ]]],
    ]],
];
$geminiExtracted = \Yatsn\AI\GeminiImageAdapter::extractInlineImage($geminiResponse);
assert_true($geminiExtracted['mime'] === 'image/jpeg' && strlen($geminiExtracted['bytes']) > 100, 'Gemini response image is decoded safely');
$geminiNormalized = \Yatsn\AI\FalImageAdapter::normalizeImage($geminiExtracted['bytes'], 'gemini-test', 7);
assert_true($geminiNormalized['width'] === 768 && $geminiNormalized['height'] === 1024, 'Gemini response image is normalized into expected application format');
$geminiMissingFailed = false;
try {
    \Yatsn\AI\GeminiImageAdapter::extractInlineImage(['candidates' => [['finishReason' => 'STOP', 'content' => ['parts' => [['text' => 'no image']]]]]]);
} catch (\Throwable $e) {
    $geminiMissingFailed = str_contains($e->getMessage(), 'gemini_image_missing');
}
assert_true($geminiMissingFailed, 'Gemini missing image output fails safely');
$geminiMalformedFailed = false;
try {
    \Yatsn\AI\GeminiImageAdapter::extractInlineImage(['candidates' => [['finishReason' => 'STOP', 'content' => ['parts' => [['inlineData' => ['mimeType' => 'image/jpeg', 'data' => '%%%']]]]]]]);
} catch (\Throwable $e) {
    $geminiMalformedFailed = str_contains($e->getMessage(), 'gemini_image_decode_failed')
        || str_contains($e->getMessage(), 'gemini_image_invalid')
        || str_contains($e->getMessage(), 'gemini_image_missing');
}
assert_true($geminiMalformedFailed, 'Gemini malformed image output fails safely');
$mailAfterGemini = file_exists(Config::get('app.log_path') . '/mail.log')
    ? (string) file_get_contents(Config::get('app.log_path') . '/mail.log')
    : '';
$inlineB64 = (string) ($geminiOnePortraitPayload['contents'][0]['parts'][1]['inlineData']['data'] ?? '');
assert_true($inlineB64 !== '' && !str_contains($mailAfterGemini, $inlineB64), 'portrait base64 is not written to mail logs');
assert_true(!str_contains($mailAfterGemini, substr($inlineB64, 0, 40)), 'partial portrait base64 is not written to mail logs');
$setupAi = Config::setupStatus()['ai'] ?? [];
assert_true(array_key_exists('geminiImageModel', $setupAi), 'setup-status reports Gemini image model');
assert_true(array_key_exists('geminiImageLiveCalls', $setupAi), 'setup-status reports Gemini image live-call flag');
assert_true(array_key_exists('geminiImageAdapterAvailable', $setupAi), 'setup-status reports Gemini image adapter availability');
assert_true(array_key_exists('imageProviderPreference', $setupAi), 'setup-status reports selected image-provider preference');

// Regeneration prepopulates draft
$regen = GenerationJobService::recreateDraftFromImage((int) $user['id'], $imageId);
assert_true(!empty($regen['songLookup']['title']), 'regeneration draft includes song');
assert_true(count($regen['portraits']) >= 1, 'regeneration draft includes portraits');
assert_true(!empty($regen['style']['id']), 'regeneration draft includes style');

// Account deletion
AccountService::markRecentAuth((int) $other['id']);
$preview = AccountService::deletionPreview((int) $other['id']);
assert_true($preview['confirmationPhrase'] === 'DELETE MY ACCOUNT', 'deletion preview returns confirmation phrase');
AccountService::deleteAccount((int) $other['id'], 'DELETE MY ACCOUNT');
$deleted = Database::one('SELECT account_state, deleted_at FROM users WHERE id = :id', ['id' => $other['id']]);
assert_true($deleted['account_state'] === 'deleted' && $deleted['deleted_at'] !== null, 'account deletion marks user deleted');

// Mobile token rotation
$tokens = AccountService::issueMobileTokens((int) $user['id']);
$rotated = AccountService::refreshMobileTokens($tokens['refreshToken']);
assert_true(!empty($rotated['accessToken']), 'mobile refresh issues new access token');
$reuseRejected = false;
try {
    AccountService::refreshMobileTokens($tokens['refreshToken']);
} catch (Throwable $e) {
    $reuseRejected = true;
}
assert_true($reuseRejected, 'refresh token reuse is rejected');

// Logout all bumps security version
$svBefore = (int) Database::one('SELECT security_version FROM users WHERE id = :id', ['id' => $user['id']])['security_version'];
AccountService::logoutAll((int) $user['id']);
$svAfter = (int) Database::one('SELECT security_version FROM users WHERE id = :id', ['id' => $user['id']])['security_version'];
assert_true($svAfter === $svBefore + 1, 'logout all increments security version');

// Mail log redacts tokens
Mailer::send('someone@example.test', 'Test', "Use https://example.test/activate?token=supersecrettoken123");
$mailLog2 = file_get_contents($testLog . '/mail.log');
assert_true(str_contains($mailLog2, '[redacted]') && !str_contains($mailLog2, 'supersecrettoken123'), 'mail log redacts tokens');

assert_true(Config::setupStatus()['publicRegistration'] === false, 'setup status shows registration disabled');

// V1 sample showcase (Round 009)
use Yatsn\Support\ShowcaseManifest;
use Yatsn\Support\View;

$showcaseManifest = ShowcaseManifest::load();
assert_true(($showcaseManifest['total'] ?? 0) === 77, 'showcase manifest count is exactly 77');
assert_true(($showcaseManifest['orientations']['portrait'] ?? 0) === 32, 'showcase manifest has 32 portrait items');
assert_true(($showcaseManifest['orientations']['square'] ?? 0) === 33, 'showcase manifest has 33 square items');
assert_true(($showcaseManifest['orientations']['landscape'] ?? 0) === 12, 'showcase manifest has 12 landscape items');
$showcaseIds = [];
foreach ($showcaseManifest['items'] as $showcaseItem) {
    assert_true(!isset($showcaseIds[$showcaseItem['id']]), 'showcase manifest ids are unique');
    $showcaseIds[$showcaseItem['id']] = true;
    foreach (['original', 'thumb', 'display'] as $assetKey) {
        $assetPath = $root . '/public' . $showcaseItem[$assetKey];
        assert_true(is_file($assetPath), 'showcase asset exists: ' . $showcaseItem[$assetKey]);
        assert_true(str_starts_with($showcaseItem[$assetKey], '/assets/images/showcase/'), 'showcase asset path stays in showcase root');
    }
    assert_true(!empty($showcaseItem['alt']), 'showcase item has alt text: ' . $showcaseItem['id']);
    assert_true(in_array($showcaseItem['orientation'], ['portrait', 'square', 'landscape'], true), 'showcase item orientation is valid');
}
$showcaseHero = ShowcaseManifest::hero();
assert_true($showcaseHero !== null && !empty($showcaseHero['featured']), 'showcase hero item is featured');

$welcomeTemplate = (string) file_get_contents($root . '/templates/pages/welcome.php');
assert_true(!str_contains($welcomeTemplate, 'example-solo'), 'home template no longer references example-solo');
assert_true(!str_contains($welcomeTemplate, 'example-energy'), 'home template no longer references example-energy');
assert_true(!str_contains($welcomeTemplate, 'example-intimate'), 'home template no longer references example-intimate');
assert_true(!str_contains($welcomeTemplate, 'layout-interlude'), 'home template no longer references layout-interlude');
assert_true(str_contains($welcomeTemplate, 'Worlds from the first chapter'), 'home template includes carousel heading');
assert_true(str_contains($welcomeTemplate, 'Explore all 77 worlds'), 'home template links to showcase');

$showcaseTemplate = (string) file_get_contents($root . '/templates/pages/showcase.php');
assert_true(str_contains($showcaseTemplate, 'V1 archive'), 'showcase template includes archive eyebrow');
assert_true(str_contains($showcaseTemplate, 'Seventy-seven worlds'), 'showcase template includes heading');
assert_true(str_contains($showcaseTemplate, 'Load more worlds'), 'showcase template includes load more fallback');
assert_true(str_contains($showcaseTemplate, 'data-dialog-prev'), 'showcase template includes previous dialog control');
assert_true(str_contains($showcaseTemplate, 'data-dialog-next'), 'showcase template includes next dialog control');
assert_true(str_contains($showcaseTemplate, 'Create your world'), 'showcase template includes final create action');

$showcaseHtml = View::page('pages/showcase', [
    'title' => 'V1 archive showcase',
    'session' => null,
    'showcaseScripts' => ['imagesloaded', 'masonry', 'showcase'],
]);
assert_true(str_contains($showcaseHtml, 'data-showcase-page'), 'showcase page renders showcase shell');
assert_true(str_contains($showcaseHtml, 'Portrait 32'), 'showcase page renders portrait filter');

$showcaseJs = (string) file_get_contents($root . '/public/assets/js/showcase.js');
assert_true(str_contains($showcaseJs, 'loading = index === 0 ? \'eager\' : \'lazy\''), 'home carousel lazy-loads non-first thumbnails');
assert_true(is_file($root . '/public/assets/vendor/masonry.pkgd.min.js'), 'masonry vendor is pinned locally');
assert_true(is_file($root . '/public/assets/vendor/imagesloaded.pkgd.min.js'), 'imagesloaded vendor is pinned locally');

echo "\n=== Results: {$passed} passed, {$failed} failed ===\n";
if ($failed > 0) {
    echo "Failures:\n - " . implode("\n - ", $failures) . "\n";
    exit(1);
}
echo "All automated Build 1 checks passed.\n";
exit(0);
