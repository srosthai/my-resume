# My Resume - Portfolio Application

A modern portfolio/resume application built with Laravel 12, Vue 3, Inertia.js, and TypeScript.

## Tech Stack

- Laravel 12 (PHP 8.2+)
- Vue 3 with TypeScript
- Inertia.js
- Tailwind CSS 4
- SQLite by default (MySQL or PostgreSQL work too)

## Prerequisites

- PHP 8.2+
- Composer
- Node.js 22+
- SQLite (bundled with PHP) or MySQL 8 / PostgreSQL 15

## Installation

### 1. Clone and Install Dependencies

```bash
git clone <repository-url>
cd my-resume
composer install
npm install
```

### 2. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Database Setup

The default `.env.example` uses SQLite, which needs no server:

```bash
touch database/database.sqlite
php artisan migrate --seed
```

To use MySQL or PostgreSQL instead, set `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` and `DB_PASSWORD` in `.env` before migrating.

### 4. Create the owner account

Registration is disabled by default (`AUTH_REGISTRATION_ENABLED=false`). The seeder creates the owner; to create one by hand:

```bash
php artisan tinker --execute="App\Models\User::factory()->owner()->create(['email' => 'you@example.com', 'password' => 'a-strong-password-123']);"
```

### 5. Uploads and permissions

Uploaded images live in `public/uploads` (the `uploads` disk); the directory is git-ignored.

```bash
mkdir -p public/uploads
sudo chmod -R 775 storage bootstrap/cache public/uploads
```

### 6. Contact form and production settings

- `CONTACT_EMAIL` receives contact-form messages. Use `MAIL_MAILER=resend` with `RESEND_KEY` and a verified `MAIL_FROM_ADDRESS` in production.
- `TRUSTED_PROXIES` defaults to Cloudflare's ranges; set `*` only if the origin is reachable exclusively through the proxy.
- `CSP_ENABLED=true` sends a nonce-based Content Security Policy.

## Running the Application

### Development Mode

```bash
composer dev
```

This starts Laravel server, Vite, and queue worker.

Access: http://localhost:8000

### Or Run Separately

```bash
# Terminal 1
php artisan serve

# Terminal 2
npm run dev
```

## Build for Production

```bash
npm run build
```

## Common Commands

```bash
# Clear cache
php artisan config:clear && php artisan cache:clear

# Run tests
composer test

# Format, lint and type-check (what CI runs)
./vendor/bin/pint --test
npm run format:check
npm run lint
npm run typecheck

# Fresh database
php artisan migrate:fresh --seed
```

## Features

- Public portfolio website
- Admin dashboard (requires login)
- Manage: About, Work Experience, Education, Projects, Tech Stack, Notes
- Dark/Light theme support
- Single-owner admin: registration is off and every backend route requires the owner account