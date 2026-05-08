# Repository Guidelines

## Project Structure & Module Organization

This is a Laravel 10 application. Core PHP code lives in `app/`, with domain
helpers and platform support under `app/Helpers` and `app/Support`. HTTP routes
are in `routes/`, database migrations, factories, and seeders are in
`database/`, and Blade views plus Vite assets live in `resources/views`,
`resources/css`, and `resources/js`. Publicly served assets belong in `public/`.
Tests are split between `tests/Feature` and `tests/Unit`.

## Build, Test, and Development Commands

- `sail composer install`: install PHP dependencies.
- `sail npm install`: install Vite, Prettier, and frontend tooling.
- `sail artisan serve`: run the Laravel app locally.
- `sail npm run dev`: start the Vite development server.
- `sail npm run build`: compile production frontend assets.
- `sail artisan test`: run the Pest/PHPUnit test suite.
- `./vendor/bin/pint`: format PHP code with Laravel Pint.
- `npx prettier --write resources/**/*.blade.php resources/**/*.{js,css}`:
  format Blade, JavaScript, and CSS when needed.

## Coding Style & Naming Conventions

Follow `.editorconfig`: UTF-8, LF endings, final newline, trimmed trailing
whitespace, and 4-space indentation by default. YAML files use 2 spaces;
`docker-compose.yml` uses 4. PHP should follow Laravel conventions: PSR-4
classes under the `App\` namespace, StudlyCase class names, camelCase methods,
and snake_case database columns. Prettier is configured with single quotes,
semicolons, 80-column wrapping, 2-space formatting for supported frontend files,
and `@prettier/plugin-php`.

## Testing Guidelines

Tests use Pest with PHPUnit configuration in `phpunit.xml`. Put integration and
HTTP behavior in `tests/Feature`, focused unit coverage in `tests/Unit`, and
name files `*Test.php` such as `ExamHandlerTest.php`. The test environment uses
array cache/mail drivers, sync queues, and testing-specific env values. Run
`sail artisan test` before submitting changes; use targeted runs like
`sail artisan test tests/Feature/Helpers/ExamHandlerTest.php` while iterating.

## Commit & Pull Request Guidelines

Recent commits use short, imperative summaries such as `fix`, `minor fix`, and
`delete exam`. Keep commits concise but prefer a specific subject, for example
`fix exam course migration`. Pull requests should include a brief description,
test results, linked issue or task when applicable, and screenshots for UI
changes. Call out migrations, env changes, or deployment-sensitive behavior.

## Security & Configuration Tips

Do not commit secrets from `.env`; use `.env.example` for shared defaults and
`.env.testing` for test-specific settings. Keep generated dependencies in
`vendor/` and `node_modules/` out of source control.
