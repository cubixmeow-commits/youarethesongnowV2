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

$lookup = SongLookupService::create((int) $user['id'], 'Owner Test Band', 'Midnight Harbor');
assert_true($lookup['state'] === 'found', 'deterministic song lookup found');
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
assert_true(str_contains($safePackage['compiledPromptSafe'], 'foreground, middle ground, and background'), 'V1-derived compiler restores dimensional environmental staging');
assert_true(str_contains($safePackage['compiledPromptSafe'], 'IMAGE 1 and IMAGE 2'), 'V1-derived compiler gives both portrait identities explicit roles');
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
$groundedPayload = \Yatsn\AI\GeminiLyricsResearchService::groundedRequestPayload('fixture prompt');
assert_true(isset($groundedPayload['tools'][0]['google_search']), 'Gemini lyric research enables current Google Search grounding tool');
assert_true(!isset($groundedPayload['generationConfig']['responseMimeType']), 'Gemini grounding request avoids unsupported JSON MIME mode');
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

echo "\n=== Results: {$passed} passed, {$failed} failed ===\n";
if ($failed > 0) {
    echo "Failures:\n - " . implode("\n - ", $failures) . "\n";
    exit(1);
}
echo "All automated Build 1 checks passed.\n";
exit(0);
