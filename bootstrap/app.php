<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function () {
            // Web routes with controller namespace
            Route::middleware('web')
                 ->namespace('App\Application\Controllers')
                 ->group(base_path('app/Application/routes/web.php'));

            // API routes with API controller namespace
            Route::prefix('api')
                 ->middleware('api')
                 ->namespace('App\Application\Controllers\Api')
                 ->group(base_path('app/Application/routes/api.php'));
        },
        commands: base_path('app/Application/routes/console.php'),
        channels: base_path('app/Application/routes/channels.php'),
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Global middleware (previously in Http/Kernel $middleware)
        $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);

        // Custom route middleware aliases (previously $routeMiddleware in Http/Kernel)
        $middleware->alias([
            'admin'                  => \App\Http\Middleware\AdminMiddleware::class,
            'admin-permissions'      => \App\Http\Middleware\AdminPermissionMiddleware::class,
            'website-permissions'    => \App\Http\Middleware\WebsitePermissionMiddleware::class,
            'authApi'                => \App\Http\Middleware\ApiMiddleware::class,
            'localize'               => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes::class,
            'localizationRedirect'   => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
            'localeSessionRedirect'  => \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Exceptions that should not be reported
        $exceptions->dontReport([
            \Illuminate\Auth\AuthenticationException::class,
            \Illuminate\Auth\Access\AuthorizationException::class,
            \Symfony\Component\HttpKernel\Exception\HttpException::class,
            \Illuminate\Database\Eloquent\ModelNotFoundException::class,
            \Illuminate\Session\TokenMismatchException::class,
            \Illuminate\Validation\ValidationException::class,
        ]);

        // Return JSON for unauthenticated API requests
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }
            return redirect()->guest(route('login'));
        });
    })
    ->create();
