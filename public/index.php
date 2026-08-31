<?php

declare(strict_types=1);

require dirname(__DIR__) . '/app/bootstrap.php';

use Yatsn\Api\ApiV1;
use Yatsn\Auth\SessionService;
use Yatsn\Http\Request;
use Yatsn\Http\Router;
use Yatsn\Sharing\GalleryService;
use Yatsn\Support\Config;
use Yatsn\Support\Database;
use Yatsn\Support\JsonResponse;
use Yatsn\Support\Migrator;
use Yatsn\Support\ShowcaseManifest;
use Yatsn\Support\View;
use Yatsn\Styles\StyleService;

Config::ensureDirectories();
\Yatsn\Support\Security::sendSecurityHeaders(is_https_request());

// Auto-migrate in development for local vertical slice convenience.
if (Config::get('app.env') === 'development') {
    try {
        Migrator::migrate();
        StyleService::seed();
    } catch (Throwable $e) {
        // Health and setup surfaces will reveal DB problems.
    }
}

$request = Request::fromGlobals();
$router = new Router();
ApiV1::register($router);

$router->get('/', function () {
    $session = SessionService::current();
    if ($session) {
        redirect('/create');
    }
    echo View::page('pages/welcome', [
        'title' => 'You Are The Song Now',
        'session' => null,
        'isHome' => true,
        'showcaseHero' => ShowcaseManifest::hero(),
        'showcaseScripts' => ['showcase'],
    ]);
    return true;
});

$router->get('/showcase', function () {
    echo View::page('pages/showcase', [
        'title' => 'V1 archive showcase',
        'session' => SessionService::current(),
        'showcaseScripts' => ['imagesloaded', 'masonry', 'showcase'],
    ]);
    return true;
});

$router->get('/sign-in', function () {
    echo View::page('pages/sign-in', [
        'title' => 'Sign in',
        'session' => SessionService::current(),
    ]);
    return true;
});

$router->get('/sign-in/complete', function (Request $request) {
    echo View::page('pages/sign-in-complete', [
        'title' => 'Completing sign-in',
        'token' => $request->query['token'] ?? '',
        'session' => null,
    ]);
    return true;
});

$router->get('/activate', function (Request $request) {
    echo View::page('pages/activate', [
        'title' => 'Activate your invitation',
        'token' => $request->query['token'] ?? '',
        'session' => null,
    ]);
    return true;
});

$router->get('/create', function () {
    $session = SessionService::current();
    if (!$session) {
        redirect('/sign-in');
    }
    echo View::page('pages/create', [
        'title' => 'Create your cinematic world',
        'session' => $session,
        'csrf' => $session['csrf_token'],
    ]);
    return true;
});

$router->get('/gallery', function () {
    $session = SessionService::current();
    if (!$session) {
        redirect('/sign-in');
    }
    echo View::page('pages/gallery', [
        'title' => 'Gallery',
        'session' => $session,
        'csrf' => $session['csrf_token'],
    ]);
    return true;
});

$router->get('/images/{imageId}', function (Request $request, array $params) {
    $session = SessionService::current();
    if (!$session) {
        redirect('/sign-in');
    }
    echo View::page('pages/image', [
        'title' => 'Your image',
        'session' => $session,
        'csrf' => $session['csrf_token'],
        'imageId' => $params['imageId'],
    ]);
    return true;
});

$router->get('/account', function () {
    $session = SessionService::current();
    if (!$session) {
        redirect('/sign-in');
    }
    echo View::page('pages/account', [
        'title' => 'Account',
        'session' => $session,
        'csrf' => $session['csrf_token'],
    ]);
    return true;
});

$router->get('/owner', function () {
    $session = SessionService::current();
    if (!$session || ($session['role'] ?? '') !== 'owner') {
        redirect('/sign-in');
    }
    echo View::page('owner/dashboard', [
        'title' => 'Owner',
        'session' => $session,
        'csrf' => $session['csrf_token'],
        'layoutClass' => 'owner-layout',
    ], 'layouts/main');
    return true;
});

$router->get('/terms', function () {
    echo View::page('pages/legal', [
        'title' => 'Terms of Service',
        'heading' => 'Terms of Service',
        'body' => 'These provisional development terms will be replaced by qualified legal language before external beta. By activating an invitation you agree that this private build is for authorized development only, that raw lyrics are not retained by the application, and that generated images remain private unless you share them.',
        'session' => SessionService::current(),
    ]);
    return true;
});

$router->get('/privacy', function () {
    echo View::page('pages/legal', [
        'title' => 'Privacy Policy',
        'heading' => 'Privacy Policy',
        'body' => 'This provisional development privacy notice will be replaced by qualified legal language before external beta. Portraits and generated images are stored privately on the application server. Raw lyrics are memory-only and are never written to the database, logs or backups. Payment records required by Stripe or law may be retained separately when live billing is enabled.',
        'session' => SessionService::current(),
    ]);
    return true;
});

$router->get('/shared/{token}', function (Request $request, array $params) {
    try {
        $data = GalleryService::sharedByToken($params['token']);
        echo View::page('pages/shared', [
            'title' => 'Shared image',
            'image' => $data['image'],
            'token' => $params['token'],
            'session' => null,
        ]);
    } catch (Throwable $e) {
        http_response_code(404);
        echo View::page('pages/legal', [
            'title' => 'Unavailable',
            'heading' => 'This shared image is unavailable',
            'body' => 'The link may have expired, been revoked, or the image may have been deleted.',
            'session' => null,
        ]);
    }
    return true;
});

$result = $router->dispatch($request);
if ($result === null) {
    if (str_starts_with($request->path, '/api/')) {
        JsonResponse::error('not_found', 'Endpoint not found.', 404);
    }
    http_response_code(404);
    echo View::page('pages/legal', [
        'title' => 'Not found',
        'heading' => 'Page not found',
        'body' => 'That page does not exist.',
        'session' => SessionService::current(),
    ]);
}
