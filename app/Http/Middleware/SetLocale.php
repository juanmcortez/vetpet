<?php

namespace App\Http\Middleware;

use App\Support\Locale;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->cookie('locale')
            ?? Locale::resolveFromAcceptLanguage($request->header('Accept-Language'))
            ?? config('app.locale');

        Locale::apply(is_string($locale) ? $locale : null);

        return $next($request);
    }
}
