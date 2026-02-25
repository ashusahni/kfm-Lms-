# Rocket LMS – Backend (Laravel)

Laravel API and web backend. All PHP/Laravel code lives here.

## Structure

```
backend/
├── app/           # Application code (Controllers, Models, Middleware, …)
├── bootstrap/     # Laravel bootstrap
├── config/        # Config files
├── database/      # Migrations, seeders
├── lang/          # Translations
├── public/        # Document root (index.php, assets, spa/)
├── resources/     # Views (Blade), lang, raw assets
├── routes/        # web.php, api.php, panel.php, admin.php
├── storage/       # Logs, cache, sessions, uploads
├── tests/         # PHPUnit tests
├── vendor/        # Composer dependencies
├── .env           # Environment config (create from .env.example)
├── artisan        # CLI
├── composer.json
└── ...
```

The React frontend builds into **public/spa/** (`frontend` → `npm run build`). Laravel serves that for the SPA routes.

## Setup

```bash
cd backend
composer install
cp .env.example .env   # if .env doesn't exist
php artisan key:generate
php artisan migrate
php artisan serve
```

Then open **http://localhost:8000**. The SPA is at `/` (and other routes defined in `routes/web.php`).

## API

- **Base:** `http://localhost:8000/api`
- **Home (SPA):** `GET /api/home` – JSON for the React home page
- **Guest:** `/api/development/` – courses, bundles, categories, search, blogs, etc.
- **Auth:** `/api/development/login`, `/api/development/logout` (JWT)
- **Panel (authenticated):** `/api/development/panel/` – cart, profile, etc.

## Document root

In production, point the web server document root to **backend/public** (this directory’s `public/` folder).

## SPA routes

Laravel serves the React app (from `public/spa/`) for: `/`, `/classes`, `/course/:slug`, `/bundles`, `/blog`, `/login`, `/register`, `/cart`, `/profile`, `/instructors`, `/upcoming_courses`, `/search`, `/contact`. See `routes/web.php`.
