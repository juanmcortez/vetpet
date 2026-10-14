# AGENTS.md

Guidance for humans and coding agents working on **VETPET Control** (multi-clinic veterinary EMR).

## Product context

- **Stack:** Laravel 13, Inertia v2, Vue 3, TypeScript, Tailwind CSS 4, PostgreSQL, Redis.
- **Surfaces:** Subdomains for clinic (`vet`), owner, admin, and API (`config/vetpet.php`).
- **Locale:** Spanish default (`es`), English (`en`), Argentina timezone and ARS (`APP_TIMEZONE`, `VETPET_CURRENCY`).

## Branches and delivery

| Branch        | Use                                                 |
| ------------- | --------------------------------------------------- |
| `development` | Integration; open PRs here                          |
| `production`  | Release branch; merge from `development` when ready |

CI (`.github/workflows/ci.yml`) must pass **CI / quality** and **CI / tests** before merge.

## Local development (Docker-first)

Host PHP/Node are optional. Prefer:

```bash
make up      # bootstrap stack
make lint    # Pint, PHPStan L6, Rector dry-run, ESLint/Prettier, vue-tsc
make test    # build assets + Pest
```

Inside containers: `make shell`, `make logs s=app`.

Wayfinder generates `resources/js/actions`, `routes`, and `wayfinder` — **gitignored**; `make install` / `make lint` / CI regenerate with `php artisan wayfinder:generate --with-form`.

## Code standards

- **PHP:** PSR-12 via Laravel Pint (`pint.json`). Larastan **level 6** (`phpstan.neon`). Rector with `rector.php` (Laravel sets; do not enable arrow-function rector that fights Pint).
- **Frontend:** `npm run lint` (`vp check` + `vue-tsc`). Match existing Vue/TS patterns; use `vue-i18n` for user-facing copy (`resources/js/locales/`).
- **Scope:** Smallest correct diff; match surrounding naming and structure; no unrelated refactors.
- **Secrets:** Never commit `.env`, keys, or credentials.

## Architecture notes

- Routes live in `routes/clinic.php`, `owner.php`, `admin.php`, `api_v1.php` (not a single `web.php`).
- Fortify auth is bound to the **clinic** subdomain.
- API health: `GET /health` on the API host; Sanctum for `v1` token routes.
- Shared Inertia props: `HandleInertiaRequests` (auth, `vetpet`, locale, regional).

## Tests

- Pest in `tests/`; feature tests use `vetpetUrl()` for subdomain hosts (`tests/Pest.php`).
- Prefer meaningful feature tests over trivial unit assertions.

## Commits and PRs

- Write clear commit messages (why, not only what).
- PRs should state intent, link issues when applicable, and note how you ran `make lint` / `make test` (or CI).
- Use the GitHub PR template checklist.

## When unsure

- Read `README.md` for URLs and env vars.
- Follow existing conventions in the nearest module before introducing new abstractions.
