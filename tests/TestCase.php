<?php

namespace Tests;

use App\Support\Vetpet;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Uri;
use Laravel\Fortify\Features;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $clinicUrl = 'http://'.Vetpet::domain('clinic');

        config([
            'app.url' => $clinicUrl,
            'session.domain' => null,
        ]);

        URL::forceRootUrl($clinicUrl);

        $this->withServerVariables([
            'HTTP_HOST' => Vetpet::domain('clinic'),
            'SERVER_NAME' => Vetpet::domain('clinic'),
        ]);

        $this->withoutMiddleware(PreventRequestForgery::class);
    }

    /**
     * @param  Uri|string  $uri
     */
    protected function prepareUrlForRequest($uri): string
    {
        $uri = $uri instanceof Uri ? $uri->value() : $uri;

        if (is_string($uri) && (str_starts_with($uri, 'http://') || str_starts_with($uri, 'https://'))) {
            return $uri;
        }

        return parent::prepareUrlForRequest($uri);
    }

    protected function skipUnlessFortifyHas(string $feature, ?string $message = null): void
    {
        if (! Features::enabled($feature)) {
            $this->markTestSkipped($message ?? "Fortify feature [{$feature}] is not enabled.");
        }
    }
}
