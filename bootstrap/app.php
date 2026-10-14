<?php

use App\Http\Middleware\EnsureClinicAdministrator;
use App\Http\Middleware\EnsurePlatformAdmin;
use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SetLocale;
use App\Support\Vetpet;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        then: function (): void {
            Route::middleware('web')
                ->domain(Vetpet::domain('clinic'))
                ->group(base_path('routes/clinic.php'));

            Route::middleware('web')
                ->domain(Vetpet::domain('owner'))
                ->group(base_path('routes/owner.php'));

            Route::middleware('web')
                ->domain(Vetpet::domain('admin'))
                ->group(base_path('routes/admin.php'));

            Route::middleware('api')
                ->domain(Vetpet::domain('api'))
                ->group(base_path('routes/api_v1.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'clinic.admin' => EnsureClinicAdministrator::class,
            'platform.admin' => EnsurePlatformAdmin::class,
        ]);

        $middleware->encryptCookies(except: ['appearance', 'sidebar_state', 'locale']);

        $middleware->web(append: [
            HandleAppearance::class,
            SetLocale::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => Vetpet::currentSurface($request) === 'api' || $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
