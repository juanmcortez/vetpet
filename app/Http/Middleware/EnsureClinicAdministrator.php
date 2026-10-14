<?php

namespace App\Http\Middleware;

use App\Support\CurrentClinic;
use App\Support\Vetpet;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureClinicAdministrator
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Vetpet::currentSurface($request) !== 'clinic') {
            return $next($request);
        }

        $user = $request->user();
        $clinic = app(CurrentClinic::class)->get();

        if ($user === null || $clinic === null || ! $user->isClinicAdministrator($clinic)) {
            abort(403);
        }

        return $next($request);
    }
}
