<?php

declare(strict_types=1);

namespace Yatsn\Api;

use Yatsn\AI\GeminiExploreService;
use Yatsn\Auth\SessionService;
use Yatsn\Http\Request;
use Yatsn\Http\Router;
use Yatsn\Styles\StyleService;
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
                JsonResponse::error('song_dna_required', 'Discover a song before exploring directions.', 422);
            } catch (\RuntimeException $e) {
                $code = $e->getMessage();
                if ($code === 'provider_http_429') {
                    JsonResponse::error('explore_rate_limited', 'Gemini is busy or the free quota was reached. Try again shortly.', 429, [], null, 30);
                }
                if (in_array($code, ['gemini_unavailable', 'provider_http_401', 'provider_http_403', 'provider_http_404'], true)) {
                    JsonResponse::error('explore_unavailable', 'AI directions are unavailable right now. You can still choose a direction manually.', 503);
                }
                JsonResponse::error('explore_failed', 'We could not create visual directions for this song yet. Try again.', 503);
            }
        });
    }
}
