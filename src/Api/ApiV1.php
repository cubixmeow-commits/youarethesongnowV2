<?php

declare(strict_types=1);

namespace Yatsn\Api;

use Yatsn\Auth\AccountService;
use Yatsn\Auth\AuthService;
use Yatsn\Auth\InvitationService;
use Yatsn\Auth\SessionService;
use Yatsn\Billing\StripeService;
use Yatsn\Credits\CreditService;
use Yatsn\Generation\DraftService;
use Yatsn\Generation\GenerationJobService;
use Yatsn\Generation\SongLookupService;
use Yatsn\Http\Request;
use Yatsn\Http\Router;
use Yatsn\Portraits\PortraitService;
use Yatsn\Sharing\GalleryService;
use Yatsn\Styles\StyleService;
use Yatsn\Support\Config;
use Yatsn\Support\Database;
use Yatsn\Support\GateService;
use Yatsn\Support\JsonResponse;

final class ApiV1
{
    public static function register(Router $router): void
    {
        $router->get('/api/v1/health', function () {
            JsonResponse::data([
                'status' => 'ok',
                'service' => 'yatsn-v2',
                'time' => now_utc(),
            ]);
        });

        $router->post('/api/v1/auth/activations/complete', function (Request $request) {
            try {
                $result = InvitationService::activate(
                    (string) $request->input('token', ''),
                    (string) $request->input('displayName', ''),
                    (bool) $request->input('acceptTerms', false),
                    (bool) $request->input('acceptPrivacy', false)
                );
                JsonResponse::data($result);
            } catch (\Throwable $e) {
                self::mapAuthError($e);
            }
        });

        $router->post('/api/v1/auth/magic-links', function (Request $request) {
            AuthService::requestMagicLink((string) $request->input('email', ''));
            JsonResponse::data(['sent' => true]);
        });

        $router->post('/api/v1/auth/magic-links/complete', function (Request $request) {
            try {
                JsonResponse::data(AuthService::completeMagicLink((string) $request->input('token', '')));
            } catch (\Throwable $e) {
                self::mapAuthError($e);
            }
        });

        $router->post('/api/v1/auth/password-sessions', function (Request $request) {
            try {
                JsonResponse::data(AuthService::passwordSignIn(
                    (string) $request->input('email', ''),
                    (string) $request->input('password', '')
                ));
            } catch (\Throwable $e) {
                JsonResponse::error('invalid_credentials', 'Those sign-in details did not work.', 401);
            }
        });

        $router->post('/api/v1/auth/logout', function (Request $request) {
            self::requireCsrf($request);
            SessionService::revokeCurrent();
            JsonResponse::data(['signedOut' => true]);
        });

        $router->get('/api/v1/me', function () {
            $session = self::requireSession();
            $user = Database::one('SELECT * FROM users WHERE id = :id', ['id' => $session['user_id']]);
            JsonResponse::data([
                'user' => InvitationService::publicUser($user),
                'csrfToken' => $session['csrf_token'],
                'credits' => CreditService::summary((int) $session['user_id']),
                'membership' => StripeService::membership((int) $session['user_id']),
            ]);
        });

        $router->put('/api/v1/me/password', function (Request $request) {
            $session = self::requireSession();
            self::requireCsrf($request);
            try {
                AuthService::setPassword((int) $session['user_id'], (string) $request->input('password', ''));
                JsonResponse::data(['updated' => true]);
            } catch (\InvalidArgumentException $e) {
                JsonResponse::error('password_too_short', 'Use at least 10 characters.', 422);
            }
        });

        $router->get('/api/v1/styles', function () {
            self::requireSession();
            JsonResponse::data(StyleService::activeForClient());
        });

        $router->get('/api/v1/product-options', function () {
            self::requireSession();
            JsonResponse::data(StyleService::productOptions());
        });

        $router->get('/api/v1/portraits', function () {
            $session = self::requireSession();
            JsonResponse::data(PortraitService::list((int) $session['user_id']));
        });

        $router->post('/api/v1/portraits', function (Request $request) {
            $session = self::requireSession();
            self::requireCsrf($request);
            try {
                if (!isset($request->files['file'])) {
                    JsonResponse::error('upload_missing', 'We could not upload this photo. Choose another photo or try again.', 422);
                }
                JsonResponse::data(PortraitService::upload((int) $session['user_id'], $request->files['file']), 201);
            } catch (\Throwable $e) {
                JsonResponse::error('upload_failed', 'We could not upload this photo. Choose another photo or try again.', 422);
            }
        });

        $router->get('/api/v1/portraits/{portraitId}/content', function (Request $request, array $params) {
            $session = self::requireSession();
            try {
                $content = PortraitService::content((int) $session['user_id'], $params['portraitId'], isset($request->query['thumb']));
                self::sendBinary($content['bytes'], $content['mime']);
            } catch (\Throwable $e) {
                JsonResponse::error('not_found', 'Portrait not found.', 404);
            }
        });

        $router->delete('/api/v1/portraits/{portraitId}', function (Request $request, array $params) {
            $session = self::requireSession();
            self::requireCsrf($request);
            try {
                PortraitService::delete((int) $session['user_id'], $params['portraitId']);
                JsonResponse::data(['deleted' => true]);
            } catch (\RuntimeException $e) {
                if ($e->getMessage() === 'not_found') {
                    JsonResponse::error('not_found', 'Portrait not found.', 404);
                }
                throw $e;
            }
        });

        $router->post('/api/v1/song-lookups', function (Request $request) {
            $session = self::requireSession();
            self::requireCsrf($request);
            try {
                JsonResponse::data(SongLookupService::create(
                    (int) $session['user_id'],
                    (string) $request->input('artist', ''),
                    (string) $request->input('title', '')
                ), 201);
            } catch (\RuntimeException $e) {
                if ($e->getMessage() === 'rate_limited') {
                    JsonResponse::error('rate_limited', 'Please wait before searching again.', 429, [], null, 60);
                }
                throw $e;
            } catch (\InvalidArgumentException $e) {
                JsonResponse::error('song_required', 'Enter the artist and song title.', 422);
            }
        });

        $router->get('/api/v1/song-lookups/{lookupId}', function (Request $request, array $params) {
            $session = self::requireSession();
            $row = SongLookupService::findOwned((int) $session['user_id'], $params['lookupId']);
            if ($row === null) {
                JsonResponse::error('not_found', 'Lookup not found.', 404);
            }
            JsonResponse::data(SongLookupService::public($row));
        });

        $router->post('/api/v1/creation-drafts', function (Request $request) {
            $session = self::requireSession();
            self::requireCsrf($request);
            JsonResponse::data(DraftService::create((int) $session['user_id'], $request->body), 201);
        });

        $router->get('/api/v1/creation-drafts/{draftId}', function (Request $request, array $params) {
            $session = self::requireSession();
            $draft = DraftService::findOwned((int) $session['user_id'], $params['draftId']);
            if ($draft === null) {
                JsonResponse::error('not_found', 'Draft not found.', 404);
            }
            JsonResponse::data(DraftService::public($draft, (int) $session['user_id']));
        });

        $router->patch('/api/v1/creation-drafts/{draftId}', function (Request $request, array $params) {
            $session = self::requireSession();
            self::requireCsrf($request);
            try {
                JsonResponse::data(DraftService::update((int) $session['user_id'], $params['draftId'], $request->body));
            } catch (\Throwable $e) {
                JsonResponse::error('draft_update_failed', $e->getMessage(), 422);
            }
        });

        $router->post('/api/v1/creation-drafts/{draftId}/summary', function (Request $request, array $params) {
            $session = self::requireSession();
            self::requireCsrf($request);
            JsonResponse::data(DraftService::summary((int) $session['user_id'], $params['draftId']));
        });

        $router->get('/api/v1/credits', function () {
            $session = self::requireSession();
            JsonResponse::data(CreditService::summary((int) $session['user_id']));
        });

        $router->get('/api/v1/credit-transactions', function () {
            $session = self::requireSession();
            JsonResponse::data(CreditService::transactions((int) $session['user_id']), 200, ['nextCursor' => null]);
        });

        $router->get('/api/v1/membership', function () {
            $session = self::requireSession();
            JsonResponse::data(StripeService::membership((int) $session['user_id']));
        });

        $router->post('/api/v1/billing/checkout-sessions', function (Request $request) {
            $session = self::requireSession();
            self::requireCsrf($request);
            $key = $request->header('Idempotency-Key') ?? (string) $request->input('idempotencyKey', '');
            try {
                JsonResponse::data(StripeService::createCheckoutSession(
                    (int) $session['user_id'],
                    (string) $request->input('draftId', ''),
                    $key
                ));
            } catch (\RuntimeException $e) {
                if ($e->getMessage() === 'stripe_not_configured') {
                    JsonResponse::error(
                        'stripe_not_configured',
                        'Stripe test credentials are not installed yet. Your creation is saved.',
                        503
                    );
                }
                JsonResponse::error($e->getMessage(), 'Checkout is unavailable right now. Your creation is saved.', 503);
            }
        });

        $router->post('/api/v1/billing/dev-activate', function (Request $request) {
            // Development-only membership grant for local vertical-slice testing. Not a payment.
            $session = self::requireSession();
            self::requireCsrf($request);
            if (Config::get('app.env') !== 'development') {
                JsonResponse::error('forbidden', 'Not available.', 403);
            }
            $key = $request->header('Idempotency-Key') ?? opaque_id();
            JsonResponse::data(StripeService::grantDevelopmentMembership(
                (int) $session['user_id'],
                'Local development membership substitute',
                $key
            ));
        });

        $stripeWebhook = function (Request $request): void {
            try {
                JsonResponse::data(StripeService::handleWebhook($request->rawBody, $request->header('Stripe-Signature')));
            } catch (\Throwable $e) {
                JsonResponse::error('webhook_rejected', 'Webhook rejected.', 400);
            }
        };
        $router->post('/api/v1/webhooks/stripe', $stripeWebhook);
        $router->post('/api/v1/billing/stripe-webhook', $stripeWebhook);

        $router->post('/api/v1/generation-jobs', function (Request $request) {
            $session = self::requireSession();
            self::requireCsrf($request);
            $key = $request->header('Idempotency-Key') ?? '';
            try {
                JsonResponse::data(GenerationJobService::submit(
                    (int) $session['user_id'],
                    (string) $request->input('draftId', ''),
                    $key
                ), 201);
            } catch (\RuntimeException $e) {
                match ($e->getMessage()) {
                    'membership_required' => JsonResponse::error('membership_required', 'Membership is required before generation.', 402),
                    'insufficient_credits' => JsonResponse::error('insufficient_credits', 'Not enough credits for this image.', 402),
                    'active_job_exists' => JsonResponse::error('active_job_exists', 'You already have an image being created.', 409),
                    'draft_not_ready' => JsonResponse::error('draft_not_ready', 'Finish your creation before submitting.', 422),
                    default => JsonResponse::error('generation_failed', 'Unable to start generation.', 400),
                };
            } catch (\InvalidArgumentException $e) {
                JsonResponse::error('idempotency_required', 'Idempotency-Key is required.', 400);
            }
        });

        $router->get('/api/v1/generation-jobs/{jobId}', function (Request $request, array $params) {
            $session = self::requireSession();
            try {
                // Local development convenience: advance one queued job while the client polls.
                if (Config::get('app.env') === 'development') {
                    try {
                        GenerationJobService::processNext();
                    } catch (\Throwable $e) {
                        // Ignore worker errors here; job status remains authoritative.
                    }
                }
                JsonResponse::data(GenerationJobService::getOwned((int) $session['user_id'], $params['jobId']));
            } catch (\Throwable $e) {
                JsonResponse::error('not_found', 'Job not found.', 404);
            }
        });

        $router->get('/api/v1/generation-jobs', function () {
            $session = self::requireSession();
            JsonResponse::data(GenerationJobService::listOwned((int) $session['user_id']), 200, ['nextCursor' => null]);
        });

        $router->get('/api/v1/images', function () {
            $session = self::requireSession();
            JsonResponse::data(GalleryService::list((int) $session['user_id']), 200, ['nextCursor' => null]);
        });

        $router->get('/api/v1/images/{imageId}', function (Request $request, array $params) {
            $session = self::requireSession();
            try {
                JsonResponse::data(GalleryService::getOwned((int) $session['user_id'], $params['imageId']));
            } catch (\Throwable $e) {
                JsonResponse::error('not_found', 'Image not found.', 404);
            }
        });

        $router->get('/api/v1/images/{imageId}/content', function (Request $request, array $params) {
            $session = self::requireSession();
            try {
                $content = GalleryService::content((int) $session['user_id'], $params['imageId'], $request->query['variant'] ?? 'display');
                self::sendBinary($content['bytes'], $content['mime']);
            } catch (\Throwable $e) {
                JsonResponse::error('not_found', 'Image not found.', 404);
            }
        });

        $router->get('/api/v1/images/{imageId}/download', function (Request $request, array $params) {
            $session = self::requireSession();
            try {
                $content = GalleryService::content((int) $session['user_id'], $params['imageId'], 'download');
                self::sendBinary($content['bytes'], $content['mime'], $content['filename'], true);
            } catch (\Throwable $e) {
                JsonResponse::error('not_found', 'Image not found.', 404);
            }
        });

        $router->delete('/api/v1/images/{imageId}', function (Request $request, array $params) {
            $session = self::requireSession();
            self::requireCsrf($request);
            GalleryService::delete((int) $session['user_id'], $params['imageId']);
            JsonResponse::data(['deleted' => true]);
        });

        $router->post('/api/v1/images/{imageId}/regenerations', function (Request $request, array $params) {
            $session = self::requireSession();
            self::requireCsrf($request);
            try {
                $draft = GenerationJobService::recreateDraftFromImage((int) $session['user_id'], $params['imageId']);
                JsonResponse::data([
                    'draftId' => $draft['id'],
                    'draft' => $draft,
                    'sourceImageId' => $params['imageId'],
                    'message' => 'Adjust your choices and create another version.',
                ]);
            } catch (\Throwable $e) {
                JsonResponse::error('not_found', 'Image not found.', 404);
            }
        });

        $router->patch('/api/v1/me/profile', function (Request $request) {
            $session = self::requireSession();
            self::requireCsrf($request);
            try {
                JsonResponse::data(AccountService::updateProfile((int) $session['user_id'], (string) $request->input('displayName', '')));
            } catch (\Throwable $e) {
                JsonResponse::error('profile_update_failed', 'Unable to update profile.', 422);
            }
        });

        $router->post('/api/v1/me/email-changes', function (Request $request) {
            $session = self::requireSession();
            self::requireCsrf($request);
            try {
                AccountService::requestEmailChange((int) $session['user_id'], (string) $request->input('email', ''));
                JsonResponse::data(['sent' => true]);
            } catch (\RuntimeException $e) {
                if ($e->getMessage() === 'recent_auth_required') {
                    JsonResponse::error('recent_auth_required', 'Confirm your identity again to continue.', 401);
                }
                JsonResponse::error($e->getMessage(), 'Unable to start email change.', 422);
            }
        });

        $router->post('/api/v1/me/email-changes/complete', function (Request $request) {
            try {
                JsonResponse::data(AccountService::completeEmailChange((string) $request->input('token', '')));
            } catch (\Throwable $e) {
                JsonResponse::error('token_invalid', 'That link is invalid.', 400);
            }
        });

        $router->post('/api/v1/auth/password-resets', function (Request $request) {
            AccountService::requestPasswordReset((string) $request->input('email', ''));
            JsonResponse::data(['sent' => true]);
        });

        $router->post('/api/v1/auth/password-resets/complete', function (Request $request) {
            try {
                AccountService::completePasswordReset((string) $request->input('token', ''), (string) $request->input('password', ''));
                JsonResponse::data(['updated' => true]);
            } catch (\Throwable $e) {
                JsonResponse::error('password_reset_failed', 'Unable to reset password.', 400);
            }
        });

        $router->delete('/api/v1/me/password', function (Request $request) {
            $session = self::requireSession();
            self::requireCsrf($request);
            try {
                AccountService::removePassword((int) $session['user_id']);
                JsonResponse::data(['removed' => true]);
            } catch (\RuntimeException $e) {
                JsonResponse::error($e->getMessage(), 'Unable to remove password.', 401);
            }
        });

        $router->get('/api/v1/me/sessions', function () {
            $session = self::requireSession();
            JsonResponse::data(AccountService::listSessions((int) $session['user_id']));
        });

        $router->post('/api/v1/auth/logout-all', function (Request $request) {
            $session = self::requireSession();
            self::requireCsrf($request);
            AccountService::logoutAll((int) $session['user_id']);
            SessionService::revokeCurrent();
            JsonResponse::data(['signedOut' => true]);
        });

        $router->post('/api/v1/me/deletion-preview', function (Request $request) {
            $session = self::requireSession();
            self::requireCsrf($request);
            JsonResponse::data(AccountService::deletionPreview((int) $session['user_id']));
        });

        $router->post('/api/v1/me/deletion-confirmation', function (Request $request) {
            $session = self::requireSession();
            self::requireCsrf($request);
            try {
                AccountService::deleteAccount((int) $session['user_id'], (string) $request->input('confirmation', ''));
                SessionService::revokeCurrent();
                JsonResponse::data(['deleted' => true]);
            } catch (\Throwable $e) {
                JsonResponse::error($e->getMessage(), 'Unable to delete account.', 422);
            }
        });

        $router->post('/api/v1/auth/refresh', function (Request $request) {
            try {
                JsonResponse::data(AccountService::refreshMobileTokens((string) $request->input('refreshToken', '')));
            } catch (\Throwable $e) {
                JsonResponse::error('token_invalid', 'Refresh failed.', 401);
            }
        });

        $router->post('/api/v1/auth/mobile-sessions', function (Request $request) {
            // Issue mobile tokens after password or magic-link web auth for Flutter clients.
            $session = self::requireSession();
            JsonResponse::data(AccountService::issueMobileTokens((int) $session['user_id']));
        });

        $router->post('/api/v1/billing/portal-sessions', function (Request $request) {
            $session = self::requireSession();
            self::requireCsrf($request);
            try {
                JsonResponse::data(StripeService::createPortalSession((int) $session['user_id']));
            } catch (\RuntimeException $e) {
                JsonResponse::error($e->getMessage(), 'Billing portal is unavailable.', 503);
            }
        });

        $router->post('/api/v1/owner/totp/setup', function (Request $request) {
            $session = self::requireOwner();
            self::requireCsrf($request);
            try {
                JsonResponse::data(AccountService::enableTotp((int) $session['user_id']));
            } catch (\Throwable $e) {
                JsonResponse::error($e->getMessage(), 'Unable to start two-factor setup.', 422);
            }
        });

        $router->post('/api/v1/owner/totp/confirm', function (Request $request) {
            $session = self::requireOwner();
            self::requireCsrf($request);
            try {
                AccountService::confirmTotp((int) $session['user_id'], (string) $request->input('code', ''));
                JsonResponse::data(['enabled' => true]);
            } catch (\Throwable $e) {
                JsonResponse::error('totp_invalid', 'That code was not accepted.', 422);
            }
        });

        $router->get('/api/v1/owner/gates', function () {
            self::requireOwner();
            JsonResponse::data(GateService::status());
        });

        $router->post('/api/v1/images/{imageId}/link-share', function (Request $request, array $params) {
            $session = self::requireSession();
            self::requireCsrf($request);
            JsonResponse::data(GalleryService::createLinkShare((int) $session['user_id'], $params['imageId']));
        });

        $router->delete('/api/v1/images/{imageId}/link-share', function (Request $request, array $params) {
            $session = self::requireSession();
            self::requireCsrf($request);
            GalleryService::revokeLinkShare((int) $session['user_id'], $params['imageId']);
            JsonResponse::data(['revoked' => true]);
        });

        $router->post('/api/v1/images/{imageId}/email-shares', function (Request $request, array $params) {
            $session = self::requireSession();
            self::requireCsrf($request);
            try {
                JsonResponse::data(GalleryService::emailShare(
                    (int) $session['user_id'],
                    $params['imageId'],
                    (string) $request->input('email', '')
                ));
            } catch (\Throwable $e) {
                JsonResponse::error('share_failed', $e->getMessage(), 422);
            }
        });

        $router->get('/api/v1/shared/images/{shareToken}', function (Request $request, array $params) {
            try {
                JsonResponse::data(GalleryService::sharedByToken($params['shareToken']));
            } catch (\Throwable $e) {
                JsonResponse::error('share_invalid', 'This shared image is unavailable.', 404);
            }
        });

        $router->get('/api/v1/shared/images/{shareToken}/content', function (Request $request, array $params) {
            try {
                $content = GalleryService::sharedContent($params['shareToken']);
                self::sendBinary($content['bytes'], $content['mime']);
            } catch (\Throwable $e) {
                JsonResponse::error('share_invalid', 'This shared image is unavailable.', 404);
            }
        });

        $router->get('/api/v1/shared/images/{shareToken}/download', function (Request $request, array $params) {
            try {
                $content = GalleryService::sharedContent($params['shareToken'], true);
                self::sendBinary($content['bytes'], $content['mime'], $content['filename'], true);
            } catch (\Throwable $e) {
                JsonResponse::error('share_invalid', 'This shared image is unavailable.', 404);
            }
        });

        // Owner endpoints
        $router->get('/api/v1/owner/setup-status', function () {
            self::requireOwner();
            JsonResponse::data(Config::setupStatus());
        });

        $router->get('/api/v1/owner/invitations', function () {
            self::requireOwner();
            JsonResponse::data(InvitationService::listForOwner(), 200, ['nextCursor' => null]);
        });

        $router->post('/api/v1/owner/invitations', function (Request $request) {
            $session = self::requireOwner();
            self::requireCsrf($request);
            try {
                JsonResponse::data(InvitationService::create(
                    (int) $session['user_id'],
                    (string) $request->input('email', ''),
                    (string) $request->input('commercialAccess', 'paidBeta')
                ), 201);
            } catch (\Throwable $e) {
                JsonResponse::error('invitation_failed', $e->getMessage(), 422);
            }
        });

        $router->post('/api/v1/owner/invitations/{invitationId}/revoke', function (Request $request, array $params) {
            $session = self::requireOwner();
            self::requireCsrf($request);
            InvitationService::revoke($params['invitationId'], (int) $session['user_id']);
            JsonResponse::data(['revoked' => true]);
        });

        $router->get('/api/v1/owner/users', function () {
            self::requireOwner();
            $rows = Database::all(
                'SELECT public_id, email, display_name, role, commercial_access, account_state, membership_status, created_at
                 FROM users WHERE deleted_at IS NULL ORDER BY id DESC LIMIT 100'
            );
            JsonResponse::data(array_map(static fn(array $r): array => [
                'id' => $r['public_id'],
                'email' => $r['email'],
                'displayName' => $r['display_name'],
                'role' => $r['role'],
                'commercialAccess' => $r['commercial_access'],
                'accountState' => $r['account_state'],
                'membershipStatus' => $r['membership_status'],
                'createdAt' => $r['created_at'],
            ], $rows), 200, ['nextCursor' => null]);
        });

        $router->get('/api/v1/owner/styles', function () {
            self::requireOwner();
            JsonResponse::data(StyleService::allForOwner(), 200, ['nextCursor' => null]);
        });

        $router->patch('/api/v1/owner/styles/{styleId}', function (Request $request, array $params) {
            self::requireOwner();
            self::requireCsrf($request);
            $status = (string) $request->input('status', '');
            if (!in_array($status, ['active', 'inactive'], true)) {
                JsonResponse::error('invalid_status', 'Status must be active or inactive.', 422);
            }
            try {
                JsonResponse::data(StyleService::setStatus((string) $params['styleId'], $status));
            } catch (\RuntimeException $e) {
                if ($e->getMessage() === 'not_found') {
                    JsonResponse::error('not_found', 'Style not found.', 404);
                }
                throw $e;
            }
        });

        $router->get('/api/v1/owner/jobs', function () {
            self::requireOwner();
            $rows = Database::all(
                'SELECT j.public_id, j.status, j.quality, j.orientation, j.credit_cost, j.failure_code, j.created_at, j.completed_at,
                        u.email AS user_email
                 FROM generation_jobs j
                 INNER JOIN users u ON u.id = j.user_id
                 ORDER BY j.id DESC LIMIT 100'
            );
            JsonResponse::data(array_map(static fn(array $r): array => [
                'id' => $r['public_id'],
                'status' => $r['status'],
                'quality' => $r['quality'],
                'orientation' => $r['orientation'],
                'creditCost' => (int) $r['credit_cost'],
                'failureCode' => $r['failure_code'],
                'userEmail' => $r['user_email'],
                'createdAt' => $r['created_at'],
                'completedAt' => $r['completed_at'],
            ], $rows), 200, ['nextCursor' => null]);
        });

        $router->get('/api/v1/owner/totals', function () {
            self::requireOwner();
            $users = Database::one('SELECT COUNT(*) AS c FROM users WHERE deleted_at IS NULL');
            $images = Database::one('SELECT COUNT(*) AS c FROM generated_images WHERE deleted_at IS NULL');
            $jobs = Database::one('SELECT COUNT(*) AS c FROM generation_jobs');
            $spend = Database::one('SELECT COALESCE(SUM(cost_cents), 0) AS c FROM provider_costs');
            JsonResponse::data([
                'users' => (int) ($users['c'] ?? 0),
                'images' => (int) ($images['c'] ?? 0),
                'jobs' => (int) ($jobs['c'] ?? 0),
                'providerSpendCents' => (int) ($spend['c'] ?? 0),
                'setup' => Config::setupStatus(),
            ]);
        });

        $router->post('/api/v1/owner/credits/adjust', function (Request $request) {
            $session = self::requireOwner();
            self::requireCsrf($request);
            $email = strtolower(trim((string) $request->input('email', '')));
            $amount = (int) $request->input('amount', 0);
            $reason = trim((string) $request->input('reason', ''));
            $key = $request->header('Idempotency-Key') ?? opaque_id();
            if ($reason === '') {
                JsonResponse::error('reason_required', 'A reason is required.', 422);
            }
            $user = Database::one('SELECT * FROM users WHERE email = :e AND deleted_at IS NULL', ['e' => $email]);
            if ($user === null) {
                JsonResponse::error('not_found', 'User not found.', 404);
            }
            $row = CreditService::adjust((int) $user['id'], $amount, $reason, $key);
            JsonResponse::data($row);
        });
    }

    private static function requireSession(): array
    {
        $session = SessionService::current();
        if ($session === null) {
            JsonResponse::error('unauthenticated', 'Sign in required.', 401);
        }
        return $session;
    }

    private static function requireOwner(): array
    {
        $session = self::requireSession();
        if (($session['role'] ?? '') !== 'owner') {
            JsonResponse::error('forbidden', 'Owner access required.', 403);
        }
        return $session;
    }

    private static function requireCsrf(Request $request): void
    {
        $token = $request->header('X-CSRF-Token') ?? (string) $request->input('_csrf', '');
        if (!SessionService::verifyCsrf($token)) {
            JsonResponse::error('csrf_invalid', 'Security token missing or invalid.', 419);
        }
    }

    private static function mapAuthError(\Throwable $e): never
    {
        $code = $e->getMessage();
        match ($code) {
            'invitation_invalid', 'token_invalid', 'invalid_credentials' => JsonResponse::error($code, 'That link is invalid.', 400),
            'invitation_expired', 'token_expired' => JsonResponse::error($code, 'That link has expired.', 400),
            'terms_required' => JsonResponse::error($code, 'Accept the Terms and Privacy Policy to continue.', 422),
            default => JsonResponse::error('auth_failed', 'Unable to complete authentication.', 400),
        };
    }

    private static function sendBinary(string $bytes, string $mime, ?string $filename = null, bool $download = false): never
    {
        header('Content-Type: ' . $mime);
        header('Cache-Control: no-store');
        if ($filename !== null) {
            $disp = $download ? 'attachment' : 'inline';
            header('Content-Disposition: ' . $disp . '; filename="' . str_replace('"', '', $filename) . '"');
        }
        header('Content-Length: ' . strlen($bytes));
        echo $bytes;
        exit;
    }
}
