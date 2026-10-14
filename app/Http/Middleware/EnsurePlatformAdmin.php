<?php

namespace App\Http\Middleware;

use App\Support\Vetpet;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class EnsurePlatformAdmin
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            abort(403);
        }

        if ($user->isPlatformAdmin()) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            abort(403);
        }

        return Inertia::render('admin/NotPlatformAdmin', [
            'clinicSettingsUrl' => Vetpet::publicUrl('clinic', '/settings/clinic'),
        ])->toResponse($request)->setStatusCode(403);
    }
}
