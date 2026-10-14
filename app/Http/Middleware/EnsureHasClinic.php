<?php

namespace App\Http\Middleware;

use App\Support\CurrentClinic;
use App\Support\Vetpet;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHasClinic
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Vetpet::currentSurface($request) !== 'clinic') {
            return $next($request);
        }

        if ($request->user() === null) {
            return $next($request);
        }

        if (app(CurrentClinic::class)->get() !== null) {
            return $next($request);
        }

        if ($request->routeIs('clinic.missing', 'clinic.switch')) {
            return $next($request);
        }

        return redirect()->route('clinic.missing');
    }
}
