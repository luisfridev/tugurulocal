<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->respond(function (Response $response): Response {
            if (request()->is('recomendar', '@*/reportar')) {
                if (in_array($response->getStatusCode(), [419, 429], true)) {
                    $response = response()->view(
                        $response->getStatusCode() === 419 ? 'submissions.session-expired' : 'submissions.rate-limited',
                        ['retryAfter' => $response->headers->get('Retry-After')],
                        $response->getStatusCode(),
                        $response->headers->all(),
                    );
                }
                $response->headers->set('Cache-Control', 'private, no-store');
                $response->headers->set('X-Robots-Tag', 'noindex');
                $response->headers->set('Referrer-Policy', 'same-origin');
            }

            return $response;
        });
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
