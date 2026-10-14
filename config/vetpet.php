<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Base domain
    |--------------------------------------------------------------------------
    |
    | Hosts are shaped as {subdomain}.{domain}, for example vet.vetpet.localhost.
    |
    */

    'domain' => env('VETPET_BASE_DOMAIN', 'vetpet.localhost'),

    /*
    |--------------------------------------------------------------------------
    | Surface subdomains
    |--------------------------------------------------------------------------
    |
    | Each product surface is served from its own subdomain. Clinic staff use
    | the vet subdomain (historical APP_URL default).
    |
    */

    'subdomains' => [
        'owner' => env('VETPET_OWNER_SUBDOMAIN', 'owner'),
        'clinic' => env('VETPET_CLINIC_SUBDOMAIN', 'vet'),
        'admin' => env('VETPET_ADMIN_SUBDOMAIN', 'admin'),
        'api' => env('VETPET_API_SUBDOMAIN', 'api'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Localization & regional defaults
    |--------------------------------------------------------------------------
    |
    | Argentina-first defaults with English support for staff and owners.
    |
    */

    'supported_locales' => ['en', 'es'],

    'timezone' => env('APP_TIMEZONE', 'America/Argentina/Buenos_Aires'),

    'currency' => env('VETPET_CURRENCY', 'ARS'),

    'currency_locale' => env('VETPET_CURRENCY_LOCALE', 'es_AR'),

];
