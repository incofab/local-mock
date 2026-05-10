# Project Overview

This is a web application built using the Laravel framework (version 10) for the
backend and Vite for the frontend. The application appears to be an exam
management system, with features for managing events and exams. It uses a MySQL
database to store data.

# Canonical Project Documentation

Read `public/docs/index.html` before making changes. The `public/docs/` folder
is the canonical technical documentation for this repository and is split into
focused HTML pages for architecture, workflows, routes and controllers, domain
logic, data/storage, frontend/public scripts, testing, and AI change guidance.

When adding a feature or changing behavior, update the relevant page in
`public/docs/` in the same change. This includes changes to routes,
controllers, actions, helpers, support classes, models, migrations, public
examiner scripts, file formats, storage locations, tests, commands, or
operational workflows. If a new subsystem needs its own page, add it under
`public/docs/` and link it from `public/docs/index.html`.

# Building and Running

## Backend

To run the backend, you will need to have PHP 8.1 and Composer installed.

1.  Install dependencies: `sail composer install`
2.  Create a `.env` file from the `.env.example`: `cp .env.example .env`
3.  Generate an application key: `sail artisan key:generate`
4.  Run database migrations: `sail artisan migrate`
5.  Start the development server: `sail artisan serve`

## Frontend

To build the frontend, you will need to have Node.js and npm installed.

1.  Install dependencies: `sail npm install`
2.  Run the development server: `sail npm run dev`
3.  Build for production: `sail npm run build`

# Development Conventions

## Testing

The project uses Pest for testing. There are two test suites:

- **Unit Tests:** `tests/Unit`
- **Feature Tests:** `tests/Feature`

To run the tests, you can use the following command:

```bash
./vendor/bin/pest
```

## Code Style

The project uses Prettier for code formatting. You can format the code by
running:

```bash
npm run format
```

(Note: a `format` script is not in package.json, but it is a common convention)

## Routes

- **Web Routes:** `routes/web.php`
- **API Routes:** `routes/api.php`

## Middleware

Custom middleware is located in `app/Http/Middleware`. A custom middleware
`verify.institution` is used to protect some web routes.
