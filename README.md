# VETPET Control

Multi-clinic veterinary EMR built with Laravel 13, Inertia, and Vue 3.

## Requirements

- Docker Engine with Compose v2
- GNU Make
- Git

PHP, Composer, and Node.js on the host are optional; development runs inside Docker.

## Quick start

```bash
git clone <repository-url> vetpet
cd vetpet
make up
```

`make up` will:

1. Copy `.env.example` to `.env` when missing
2. Build the PHP application image
3. Start PostgreSQL, Redis, and Mailpit
4. Run `composer install` and `npm ci`
5. Generate `APP_KEY` when needed
6. Run database migrations
7. Start Nginx, PHP-FPM, Horizon, the scheduler, and the Vite dev server

### URLs (local)

| Surface              | URL                                     |
| -------------------- | --------------------------------------- |
| Clinic (staff / EMR) | http://vet.vetpet.localhost:8480        |
| Owner portal         | http://owner.vetpet.localhost:8480      |
| Platform admin       | http://admin.vetpet.localhost:8480      |
| API (+ `/health`)    | http://api.vetpet.localhost:8480/health |
| Mailpit              | http://localhost:8025                   |
| Vite (HMR)           | http://localhost:5173                   |

Subdomains are configured in `config/vetpet.php` (`VETPET_*` env vars). Set `SESSION_DOMAIN=.vetpet.localhost` so authentication cookies work across surfaces.

Modern browsers resolve `*.vetpet.localhost` to `127.0.0.1` without editing `/etc/hosts`.

### Optional profiles

Object storage and search are behind Compose profiles:

```bash
make up-all
```

| Service       | URL                   |
| ------------- | --------------------- |
| MinIO API     | http://localhost:9000 |
| MinIO console | http://localhost:9001 |
| Meilisearch   | http://localhost:7700 |

Default MinIO credentials match `.env.example` (`vetpet` / `vetpetsecret`).

## Make commands

| Command           | Description                                                              |
| ----------------- | ------------------------------------------------------------------------ |
| `make up`         | Bootstrap and start the core stack                                       |
| `make up-all`     | Core stack plus MinIO and Meilisearch                                    |
| `make down`       | Stop all services                                                        |
| `make fresh`      | `migrate:fresh --seed`                                                   |
| `make test`       | Build frontend assets and run Pest                                       |
| `make lint`       | Pint, Larastan (level 6), Rector dry-run, ESLint/Prettier, and `vue-tsc` |
| `make shell`      | Shell in the `app` container                                             |
| `make logs s=app` | Follow logs for a service                                                |

## Environment

Copy `.env.example` to `.env` and adjust if needed. Docker-related variables:

- `UID` / `GID` — match your host user for file permissions (defaults `1000`)
- `VETPET_HTTP_PORT` — Nginx port on the host (default `8480`)
- `VITE_PORT` — Vite dev server port (default `5173`)

The testing database `vetpet_testing` is created automatically on first Postgres startup.

### Locale, timezone, and currency

Defaults target Argentina (`APP_LOCALE=es`, `APP_TIMEZONE=America/Argentina/Buenos_Aires`, `VETPET_CURRENCY=ARS`). English (`en`) is supported via cookie-backed locale switching (Appearance settings on the clinic surface). Backend strings live in `lang/{locale}/vetpet.php`; frontend copy uses `vue-i18n` under `resources/js/locales/`.

## Branches

- `production` — production releases
- `development` — integration branch for pull requests

## Continuous integration

GitHub Actions workflow [`.github/workflows/ci.yml`](.github/workflows/ci.yml) runs on pushes and pull requests targeting `production` or `development`:

| Job       | What it runs                                                             |
| --------- | ------------------------------------------------------------------------ |
| `quality` | Pint, Larastan (level 6), Rector dry-run, ESLint/Prettier, and `vue-tsc` |
| `tests`   | PostgreSQL 17 + Redis 7, migrations, `npm run build`, and Pest           |

Local parity:

- `make lint` — same checks as the `quality` job (plus Wayfinder generation)
- `make test` — build assets and run Pest (SQLite in memory by default; CI uses Postgres)
- `composer ci:check` — full pipeline when PHP/Node run on the host

### Branch protection (GitHub settings)

On the repository, require these status checks before merging into `production` and `development`:

- **CI / quality**
- **CI / tests**

Enable “Require branches to be up to date before merging” so both jobs run on the latest commit.

## License

MIT
