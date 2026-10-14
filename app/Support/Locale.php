<?php

namespace App\Support;

use Illuminate\Support\Facades\App;

class Locale
{
    /**
     * @return list<string>
     */
    public static function supported(): array
    {
        /** @var list<string> $locales */
        $locales = config('vetpet.supported_locales', ['en', 'es']);

        return $locales;
    }

    public static function isSupported(string $locale): bool
    {
        return in_array($locale, self::supported(), true);
    }

    public static function resolveFromAcceptLanguage(?string $header): ?string
    {
        if ($header === null || $header === '') {
            return null;
        }

        foreach (explode(',', $header) as $part) {
            $tag = strtolower(trim(explode(';', $part)[0]));
            $primary = explode('-', $tag)[0];

            if (self::isSupported($primary)) {
                return $primary;
            }
        }

        return null;
    }

    public static function apply(?string $locale): string
    {
        $locale = $locale && self::isSupported($locale)
            ? $locale
            : (string) config('app.locale');

        App::setLocale($locale);

        return $locale;
    }
}
