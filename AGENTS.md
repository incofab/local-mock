# Repository Guidelines

## Project Structure & Module Organization

This is a Laravel 10 application. Core PHP code lives in `app/`, with domain
helpers and platform support under `app/Helpers` and `app/Support`. HTTP routes
are in `routes/`, database migrations, factories, and seeders are in
`database/`, and Blade views plus Vite assets live in `resources/views`,
`resources/css`, and `resources/js`. Publicly served assets belong in `public/`.
Tests are split between `tests/Feature` and `tests/Unit`.

## Canonical Project Documentation

Before making project changes, read `public/docs/index.html`. The `public/docs/`
folder is the canonical technical map for this application and is split into
focused HTML pages for architecture, workflows, routes and controllers, domain
logic, data/storage, frontend/public scripts, testing, and AI change guidance.

Whenever you add a feature, change behavior, modify routes/controllers/actions,
alter models or migrations, update public examiner scripts, change storage/file
formats, or adjust significant tests, update the relevant page in `public/docs/`
in the same change so future AI CLIs can understand the current system
accurately. If a new subsystem needs its own page, add it under `public/docs/`
and link it from `public/docs/index.html`.

## Build, Test, and Development Commands

- `./vendor/bin/sail composer install`: install PHP dependencies.
- `./vendor/bin/sail npm install`: install Vite, Prettier, and frontend tooling.
- `./vendor/bin/sail artisan serve`: run the Laravel app locally.
- `./vendor/bin/sail npm run dev`: start the Vite development server.
- `./vendor/bin/sail npm run build`: compile production frontend assets.
- `./vendor/bin/sail artisan test`: run the Pest/PHPUnit test suite.
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
`./vendor/bin/sail artisan test` before submitting changes; use targeted runs
like `./vendor/bin/sail artisan test tests/Feature/Helpers/ExamHandlerTest.php`
while iterating.

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

## 4. Important Note

- Before you perform any task, Study the style, theme, structure and convention
  followed in this project.
- Your implementation should follow this structure and coding style,
- Try to reuse what has already been defined, but when you need to create one,
  Make your code resuable and
- If you have any questions or need clarifications, ask the questions at once
  before you start. Responses will be provided to all your questions.
- Where possible, Make reasonable assumptions where necessary
- Add wide covering tests for every feature you implement, following the style
  and structure of the existing tests.
- When features are added or updated, the documentation should be updated to
  reflect the changes. The documentation should at all times contain the latest
  detailed information about the project.
