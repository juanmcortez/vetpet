<?php

namespace App\Providers;

use App\Support\CurrentClinic;
use App\Support\Vetpet;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Fortify;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Fortify::ignoreRoutes();

        $this->app->singleton(CurrentClinic::class, fn () => new CurrentClinic);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureVetpetDomains();
        $this->configureDefaults();
    }

    protected function configureVetpetDomains(): void
    {
        config([
            'sanctum.stateful' => array_values(array_unique(array_filter([
                ...config('sanctum.stateful', []),
                Vetpet::baseDomain(),
                Vetpet::domain('owner'),
                Vetpet::domain('clinic'),
                Vetpet::domain('admin'),
                Vetpet::domain('api'),
            ]))),
        ]);
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
