<?php

namespace App\Http\Middleware;

use App\Support\CurrentClinic;
use App\Support\Locale;
use App\Support\Vetpet;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'vetpet' => [
                'surface' => Vetpet::currentSurface($request),
                'domains' => Vetpet::domainMap(),
            ],
            'locale' => app()->getLocale(),
            'locales' => Locale::supported(),
            'regional' => [
                'timezone' => config('vetpet.timezone'),
                'currency' => config('vetpet.currency'),
                'currency_locale' => config('vetpet.currency_locale'),
            ],
            'clinic' => fn () => $this->clinicContext($request),
        ];
    }

    /**
     * @return array{current: ?array{id: int, name: string, slug: string}, available: list<array{id: int, name: string, slug: string}>}|null
     */
    protected function clinicContext(Request $request): ?array
    {
        if (Vetpet::currentSurface($request) !== 'clinic' || $request->user() === null) {
            return null;
        }

        $current = app(CurrentClinic::class)->get();

        $available = $request->user()
            ->clinics()
            ->where('clinics.is_active', true)
            ->orderBy('clinics.name')
            ->get(['clinics.id', 'clinics.name', 'clinics.slug'])
            ->map(fn ($clinic) => [
                'id' => $clinic->id,
                'name' => $clinic->name,
                'slug' => $clinic->slug,
            ])
            ->values()
            ->all();

        return [
            'current' => $current === null ? null : [
                'id' => $current->id,
                'name' => $current->name,
                'slug' => $current->slug,
            ],
            'available' => $available,
        ];
    }
}
