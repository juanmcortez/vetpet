<?php

namespace App\Support;

use Illuminate\Http\Request;

class Vetpet
{
    /**
     * @var list<string>
     */
    public const SURFACES = ['owner', 'clinic', 'admin', 'api'];

    public static function baseDomain(): string
    {
        return (string) config('vetpet.domain');
    }

    public static function subdomain(string $surface): string
    {
        $subdomain = config("vetpet.subdomains.{$surface}");

        if (! is_string($subdomain) || $subdomain === '') {
            throw new \InvalidArgumentException("Unknown VETPET surface [{$surface}].");
        }

        return $subdomain;
    }

    public static function domain(string $surface): string
    {
        return self::subdomain($surface).'.'.self::baseDomain();
    }

    public static function url(string $surface, string $path = '/'): string
    {
        return self::publicUrl($surface, $path);
    }

    /**
     * Build a surface URL using the port from {@see config('app.url')} when present (local Docker).
     */
    public static function publicUrl(string $surface, string $path = '/'): string
    {
        $path = '/'.ltrim($path, '/');

        if ($path === '//') {
            $path = '/';
        }

        $reference = parse_url((string) config('app.url')) ?: [];
        $port = isset($reference['port']) ? ':'.$reference['port'] : '';

        return 'http://'.self::domain($surface).$port.$path;
    }

    public static function surfaceFromHost(string $host): ?string
    {
        $baseDomain = self::baseDomain();

        if ($host === $baseDomain) {
            return null;
        }

        if (! str_ends_with($host, '.'.$baseDomain)) {
            return null;
        }

        $subdomain = substr($host, 0, -strlen('.'.$baseDomain));

        foreach (self::SURFACES as $surface) {
            if (self::subdomain($surface) === $subdomain) {
                return $surface;
            }
        }

        return null;
    }

    public static function currentSurface(Request $request): ?string
    {
        return self::surfaceFromHost($request->getHost());
    }

    /**
     * @return array<string, string>
     */
    public static function domainMap(): array
    {
        $map = [];

        foreach (self::SURFACES as $surface) {
            $map[$surface] = self::domain($surface);
        }

        return $map;
    }
}
