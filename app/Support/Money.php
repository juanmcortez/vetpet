<?php

namespace App\Support;

use NumberFormatter;

class Money
{
    public static function format(float|int $amount, ?string $currency = null): string
    {
        $formatter = new NumberFormatter(
            (string) config('vetpet.currency_locale'),
            NumberFormatter::CURRENCY,
        );

        $formatted = $formatter->formatCurrency(
            (float) $amount,
            $currency ?? (string) config('vetpet.currency'),
        );

        return $formatted !== false ? $formatted : (string) $amount;
    }
}
