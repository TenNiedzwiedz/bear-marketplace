# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project state

This is an early-stage **Laravel 13 / PHP 8.3** application named `bear-marketplace`. It is currently a near-vanilla Laravel skeleton: the only routes are `/` (welcome view) and the `/up` health check, and the only domain model is `User`. There is no marketplace domain (products, listings, orders, etc.) implemented yet — expect to be building it out. `tests/` still contains only the framework's example tests.

## Commands

```bash
composer setup                 # First-time bootstrap: install deps, copy .env, key:generate, migrate, npm install + build
composer dev                   # Run everything at once (server + queue worker + `pail` log tailing + Vite) via concurrently
composer test                  # Clears config cache, then runs the full PHPUnit suite
php artisan test                       # Run tests directly
php artisan test --filter=SomeTest     # Run a single test class or method
php artisan test tests/Feature/Foo.php # Run one test file
./vendor/bin/pint              # Format code (Laravel Pint) — run before finishing changes
./vendor/bin/pint --dirty      # Format only uncommitted changes
php artisan tinker             # REPL for poking at models / the container
npm run dev                    # Vite dev server only (if not using `composer dev`)
```

## Architecture notes

- **Laravel 13 slim structure.** There is no `app/Http/Kernel.php` or `app/Console/Kernel.php`. Middleware, exception handling, and routing are configured in `bootstrap/app.php`; service providers are registered in `bootstrap/providers.php`. Register new middleware/exception behavior there, not in a Kernel.
- **API requests already render JSON errors.** `bootstrap/app.php` sets exceptions to render as JSON for `api/*` paths, but there is no `routes/api.php` yet — add one via `->withRouting(api: ...)` (or `php artisan install:api`) when building API endpoints.
- **Database is MariaDB, not SQLite.** `DB_CONNECTION=mariadb` (see `.env.example`). You need a running MariaDB before migrating/testing locally against a real DB. The test suite (`phpunit.xml`) overrides to `DB_DATABASE=testing`.
- **Database-backed infrastructure.** Sessions, cache, and the queue all default to the `database` driver. Running the queue matters — `composer dev` starts a `queue:listen` worker for this reason. Cache/jobs/sessions tables come from the migrations in `database/migrations/`.
- **Local services via Sail.** `compose.yaml` defines the dev stack: the app (`laravel.test`), **MariaDB 11**, and **Mailpit** (SMTP on 1025, dashboard on 8025). Run with `./vendor/bin/sail up`.

## Conventions

- **Tests use PHPUnit 12, not Pest** — write `extends TestCase` class-based tests under `tests/Unit` or `tests/Feature` (the two configured suites). Note `laravel/pail` and the Pest plugin are present, but the suite is PHPUnit.
- **Frontend is Tailwind CSS v4 + Vite 8.** Tailwind is configured through the `@tailwindcss/vite` plugin (no `tailwind.config.js`); entry points are `resources/css/app.css` and `resources/js/app.js`, bundled by `vite.config.js`.
- Code style is enforced by **Pint** (Laravel preset). Match the existing formatting; run Pint rather than hand-formatting.
