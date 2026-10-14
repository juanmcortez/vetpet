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

| Service | URL |
| --- | --- |
| Application (starter kit) | http://vet.vetpet.localhost:8480 |
| Mailpit | http://localhost:8025 |
| Vite (HMR) | http://localhost:5173 |

Modern browsers resolve `*.vetpet.localhost` to `127.0.0.1` without editing `/etc/hosts`.

### Optional profiles

Object storage and search are behind Compose profiles:

```bash
make up-all
```

| Service | URL |
| --- | --- |
| MinIO API | http://localhost:9000 |
| MinIO console | http://localhost:9001 |
| Meilisearch | http://localhost:7700 |

Default MinIO credentials match `.env.example` (`vetpet` / `vetpetsecret`).

## Make commands

| Command | Description |
| --- | --- |
| `make up` | Bootstrap and start the core stack |
| `make up-all` | Core stack plus MinIO and Meilisearch |
| `make down` | Stop all services |
| `make fresh` | `migrate:fresh --seed` |
| `make test` | Build frontend assets and run Pest |
| `make lint` | PHP Pint (check) and frontend `npm run check` |
| `make shell` | Shell in the `app` container |
| `make logs s=app` | Follow logs for a service |

## Environment

Copy `.env.example` to `.env` and adjust if needed. Docker-related variables:

- `UID` / `GID` — match your host user for file permissions (defaults `1000`)
- `VETPET_HTTP_PORT` — Nginx port on the host (default `8480`)
- `VITE_PORT` — Vite dev server port (default `5173`)

The testing database `vetpet_testing` is created automatically on first Postgres startup.

## Branches

- `production` — production releases
- `development` — integration branch for pull requests

## License

MIT
