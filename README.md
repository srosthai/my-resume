# My Resume - Portfolio Application

A modern portfolio/resume application built with Laravel 12, Vue 3, Inertia.js, and TypeScript.

## Tech Stack

- Laravel 12 (PHP 8.2+)
- Vue 3 with TypeScript
- Inertia.js
- Tailwind CSS 4
- MySQL Database

## Prerequisites

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8.0+

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

Create MySQL database:

```bash
mysql -u root -p
```

```sql
CREATE DATABASE my_resume;
EXIT;
```

Update `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=my_resume
DB_USERNAME=root
DB_PASSWORD=your_password
```

Run migrations:

```bash
php artisan migrate --seed
```

### 4. Setup Storage

```bash
php artisan storage:link
sudo chmod -R 775 storage bootstrap/cache
```

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

# Format code
npm run format
./vendor/bin/pint

# Fresh database
php artisan migrate:fresh --seed
```

## Features

- Public portfolio website
- Admin dashboard (requires login)
- Manage: About, Work Experience, Education, Projects, Tech Stack, Notes
- Dark/Light theme support