<?php

namespace App\Http\Middleware;

use App\Models\Clinic;
use App\Models\User;
use App\Support\CurrentClinic;
use App\Support\Vetpet;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentClinic
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

        if ($user === null) {
            return $next($request);
        }

        $currentClinic = app(CurrentClinic::class);
        $sessionId = $request->session()->get(CurrentClinic::SESSION_KEY);

        if (is_int($sessionId) || (is_string($sessionId) && ctype_digit($sessionId))) {
            $clinic = $this->resolveClinicForUser($user, (int) $sessionId);

            if ($clinic !== null) {
                $currentClinic->set($clinic);

                return $next($request);
            }
        }

        $default = $user->clinics()
            ->where('clinics.is_active', true)
            ->orderBy('clinics.name')
            ->first();

        if ($default !== null) {
            $request->session()->put(CurrentClinic::SESSION_KEY, $default->id);
            $currentClinic->set($default);
        }

        return $next($request);
    }

    private function resolveClinicForUser(User $user, int $clinicId): ?Clinic
    {
        /** @var Clinic|null $clinic */
        $clinic = $user->clinics()
            ->where('clinics.id', $clinicId)
            ->where('clinics.is_active', true)
            ->first();

        return $clinic;
    }
}
