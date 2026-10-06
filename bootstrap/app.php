<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Guard admin unauthenticated → về trang login admin
        $middleware->redirectGuestsTo(fn (Request $request) => route('admin.login'));
        // Guard admin authenticated → về trang Dashboard
        $middleware->redirectUsersTo(fn (Request $request) => route('admin.dashboard'));

        $middleware->web(append: [
            \App\Http\Middleware\TrackPageVisitMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
