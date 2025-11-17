# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 12 + Vue 3 + Inertia.js portfolio/resume application with both a public-facing frontend and an authenticated backend admin panel. The application uses Vite for asset bundling, TypeScript for type safety, and Tailwind CSS 4 for styling.

## Development Commands

### Running the Application

```bash
# Development mode (starts Laravel server, queue worker, and Vite in parallel)
composer dev

# SSR development mode (with server-side rendering)
composer dev:ssr

# Frontend only (Vite dev server)
npm run dev

# Build for production
npm run build

# Build with SSR support
npm run build:ssr
```

### Code Quality

```bash
# Lint and auto-fix JavaScript/TypeScript/Vue files
npm run lint

# Format code with Prettier
npm run format

# Check formatting without modifying files
npm run format:check

# Laravel PHP formatting (uses Pint)
./vendor/bin/pint
```

### Testing

```bash
# Run all tests
composer test

# Run Pest PHP tests directly
./vendor/bin/pest

# Run specific test file
./vendor/bin/pest tests/Feature/ExampleTest.php
```

### Laravel Artisan Commands

```bash
# Database migrations
php artisan migrate
php artisan migrate:rollback
php artisan migrate:fresh --seed

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Queue management
php artisan queue:listen --tries=1
php artisan queue:work

# View logs in real-time
php artisan pail

# Inertia SSR server
php artisan inertia:start-ssr
```

## Architecture Overview

### Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Vue 3 with TypeScript
- **Routing**: Inertia.js for SPA-like experience
- **Styling**: Tailwind CSS 4 with custom components
- **Build Tool**: Vite
- **UI Components**: Reka UI (headless components) with custom styling
- **Icons**: Lucide Vue Next
- **Testing**: Pest PHP

### Directory Structure

#### Backend (Laravel)

- `app/Http/Controllers/` - Controllers organized into:
  - `Backend/` - Admin panel controllers (CRUD operations)
  - `Frontend/` - Public-facing controllers (PortfolioController)
  - `Auth/` - Authentication controllers
  - `Settings/` - Settings management
  - `DashboardController.php` - Main dashboard
- `app/Models/` - Eloquent models:
  - User, AboutMe, Education, WorkExperience, TechStack
  - Project, ProjectType, PopularSong, Note
- `app/Http/Requests/` - Form request validation classes
- `app/Http/Middleware/` - Custom middleware
- `routes/` - Route definitions:
  - `web.php` - Main application routes
  - `auth.php` - Authentication routes
  - `settings.php` - Settings routes

#### Frontend (Vue 3 + TypeScript)

- `resources/js/`
  - `app.ts` - Main application entry point
  - `ssr.ts` - Server-side rendering entry point
  - `pages/` - Inertia.js page components organized by area:
    - `frontend/` - Public pages (home, about, portfolio, contact, etc.)
    - `backend/` - Admin CRUD pages (organized by resource)
    - `auth/` - Authentication pages
    - `settings/` - Settings pages
    - `Dashboard.vue` - Admin dashboard
  - `layouts/` - Layout components:
    - `AppLayout.vue` - Main application layout
    - `AuthLayout.vue` - Authentication pages layout
    - `FrontendLayout.vue` - Public-facing layout
  - `components/` - Reusable components:
    - `ui/` - ~120 UI components (ignored by ESLint, likely from shadcn-vue or similar)
    - `magicui/` - Special effect/animation components
  - `composables/` - Vue composables:
    - `useAppearance.ts` - Theme management (light/dark/system)
    - `useInitials.ts` - User initials helper
  - `lib/` - Utility functions:
    - `utils.ts` - Contains `cn()` helper for className merging (clsx + tailwind-merge)
  - `types/` - TypeScript type definitions

- `resources/css/app.css` - Global styles and Tailwind imports
- `resources/views/` - Blade templates (minimal, used for Inertia root)

### Route Organization

Routes follow a consistent pattern:

**Public Frontend Routes** (no auth required):
- `/` - Home
- `/about`, `/portfolio`, `/contact`, `/hobby`, `/more`, `/resume`, `/note`
- `/api/popular-songs` - Public API endpoint

**Authenticated Backend Routes** (require auth + verified middleware):
- URL pattern: `/backend/{resource}/{action}` or shorthand route names
- Index pages use short URLs: `/about-me`, `/work-experience`, `/eductions`, `/tech-stacks`, etc.
- CRUD actions: `/backend/{resource}/create`, `/backend/{resource}/{id}/edit`, etc.
- Example: About Me
  - Index: `GET /about-me` ’ `backend.about-me.index`
  - Create: `GET /backend/about-me/create` ’ `backend.about-me.create`
  - Store: `POST /backend/about-me` ’ `backend.about-me.store`
  - Edit: `GET /backend/about-me/{id}/edit` ’ `backend.about-me.edit`
  - Update: `PUT /backend/about-me/{id}` ’ `backend.about-me.update`
  - Delete: `DELETE /backend/about-me/{id}` ’ `backend.about-me.destroy`

### Backend CRUD Resources

The application manages these resources through admin panel:
- Users (Me)
- About Me
- Work Experience
- Education
- Tech Stacks
- Project Types
- Projects
- Popular Songs
- Notes (with featured toggle and duplicate functionality)

Each resource follows Laravel Resource Controller conventions with corresponding:
- Controller in `app/Http/Controllers/Backend/`
- Model in `app/Models/`
- Vue pages in `resources/js/pages/backend/{Resource}/` with Index.vue, Create.vue, Edit.vue

### Inertia.js Integration

- Pages are resolved automatically from `resources/js/pages/` directory
- Page components receive props from controllers via `Inertia::render()`
- Use `@inertiajs/vue3` router functions: `router.get()`, `router.post()`, `router.put()`, `router.delete()`
- Forms use Inertia form helpers for automatic error handling and loading states
- Ziggy provides Laravel route helpers in JavaScript: `route('route.name', params)`

### Styling Conventions

- Uses Tailwind CSS 4 with Vite plugin (`@tailwindcss/vite`)
- Tailwind classes are merged using `cn()` utility from `resources/js/lib/utils.ts`
- Component variants managed by `class-variance-authority`
- Custom animations from `tw-animate-css`
- UI components follow a consistent pattern with shadcn-vue style architecture

### Theme System

- Supports light, dark, and system themes via `useAppearance()` composable
- Theme preference stored in localStorage and cookie (for SSR)
- Theme initialized on app mount in `app.ts` via `initializeTheme()`
- Dark mode toggled via `dark` class on `<html>` element

### TypeScript Configuration

- Path alias: `@/` maps to `resources/js/`
- Strict TypeScript enabled with some relaxed rules:
  - `vue/multi-word-component-names: off`
  - `@typescript-eslint/no-explicit-any: off`

### Code Quality Tools

- **ESLint**: Configured with Vue 3 + TypeScript rules
  - Ignores: `vendor/`, `node_modules/`, `public/`, `bootstrap/ssr/`, `resources/js/components/ui/*`
- **Prettier**: Auto-formats with plugins for:
  - Import organization (`prettier-plugin-organize-imports`)
  - Tailwind class sorting (`prettier-plugin-tailwindcss`)
- **Pint**: Laravel's PHP code formatter (not explicitly configured in package.json, use via `./vendor/bin/pint`)

### Database

- Default: SQLite (configured in `.env`)
- Migrations in `database/migrations/`
- Uses Eloquent ORM for all database operations

### Email Integration

- Uses Resend for email delivery (`resend/resend-laravel`)
- Email templates in `resources/views/emails/`

### SSR Support

- Optional server-side rendering via Inertia.js SSR
- SSR entry point: `resources/js/ssr.ts`
- SSR build output: `bootstrap/ssr/`
- Run SSR server: `php artisan inertia:start-ssr`

## Development Workflow

1. **Adding a new backend resource**:
   - Create model in `app/Models/`
   - Create migration in `database/migrations/`
   - Create controller in `app/Http/Controllers/Backend/`
   - Add routes in `routes/web.php` following existing pattern
   - Create Vue pages in `resources/js/pages/backend/{Resource}/`:
     - `Index.vue` (list view)
     - `Create.vue` (create form)
     - `Edit.vue` (edit form)
   - Create form request validation classes in `app/Http/Requests/` if needed

2. **Adding new UI components**:
   - Place in `resources/js/components/` (or `components/ui/` if following shadcn pattern)
   - Use TypeScript for props definition
   - Use `cn()` utility for className merging
   - Follow existing component patterns for consistency

3. **Working with forms**:
   - Use Inertia form helpers: `useForm()` from `@inertiajs/vue3`
   - Leverage automatic error handling and loading states
   - Use Ziggy's `route()` helper for form action URLs

4. **Making API changes**:
   - Update controller methods
   - If changing routes, regenerate Ziggy routes: `php artisan ziggy:generate`
   - Type definitions in `resources/js/types/` for shared data structures

## Key Dependencies

- **@inertiajs/vue3**: SPA experience with Laravel routing
- **ziggy-js**: Use Laravel routes in JavaScript
- **reka-ui**: Headless UI components
- **lucide-vue-next**: Icon library
- **@vueuse/core**: Vue composition utilities
- **class-variance-authority**: Component variant management
- **tailwind-merge**: Tailwind class merging
