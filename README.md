# My Resume - Portfolio Application

A modern portfolio/resume application built with Laravel 12, Vue 3, Inertia.js, and TypeScript. Features both a public-facing frontend and an authenticated backend admin panel.

## Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Vue 3 with TypeScript
- **Routing**: Inertia.js
- **Styling**: Tailwind CSS 4
- **Build Tool**: Vite
- **Database**: MySQL
- **UI Components**: Reka UI + Custom Components
- **Icons**: Lucide Vue Next

## Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- Git
- MySQL 8.0 or higher

## Installation Steps

### 1. Clone the Repository

```bash
git clone <repository-url>
cd my-resume
```

### 2. Install PHP Dependencies

```bash
composer install
```

If you encounter permission issues, use:
```bash
sudo -S composer install
```

### 3. Install Node Dependencies

```bash
npm install
```

### 4. Environment Configuration

Copy the example environment file and configure it:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

### 5. Database Setup

#### Create MySQL Database

First, create a MySQL database for the application:

```bash
mysql -u root -p
```

Then in the MySQL prompt:

```sql
CREATE DATABASE my_resume;
CREATE USER 'resume_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON my_resume.* TO 'resume_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### Configure Database Connection

Update your `.env` file with the MySQL database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=my_resume
DB_USERNAME=resume_user
DB_PASSWORD=your_password
```

#### Run Migrations and Seeders

```bash
php artisan migrate --seed
```

If you encounter permission issues:
```bash
sudo php artisan migrate --seed
```

### 6. Storage and Cache Setup

Set up storage symlink and clear caches:

```bash
php artisan storage:link
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

If permission issues occur:
```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R $USER:$USER storage bootstrap/cache
```

### 7. Build Frontend Assets

For development:
```bash
npm run dev
```

For production:
```bash
npm run build
```

## Running the Application

### Development Mode

Run all services (Laravel server, queue worker, and Vite) in parallel:

```bash
composer dev
```

This will start:
- Laravel development server on `http://localhost:8000`
- Vite dev server for hot module replacement
- Queue worker for background jobs

### Alternative: Run Services Separately

**Terminal 1 - Laravel Server:**
```bash
php artisan serve
```

**Terminal 2 - Vite Dev Server:**
```bash
npm run dev
```

**Terminal 3 - Queue Worker (optional):**
```bash
php artisan queue:listen --tries=1
```

### SSR Mode (Optional)

For server-side rendering:

```bash
composer dev:ssr
```

Or manually:
```bash
npm run build:ssr
php artisan inertia:start-ssr
php artisan serve
```

## Accessing the Application

- **Public Frontend**: http://localhost:8000
- **Admin Panel**: http://localhost:8000/dashboard (requires authentication)

### Default Admin Credentials

Check your database seeder or create an admin user:

```bash
php artisan tinker
```

```php
\App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
    'email_verified_at' => now(),
]);
```

## Development Commands

### Code Quality

```bash
# Lint JavaScript/TypeScript/Vue
npm run lint

# Format code with Prettier
npm run format

# Check formatting
npm run format:check

# Format PHP code with Laravel Pint
./vendor/bin/pint
```

### Testing

```bash
# Run all tests
composer test

# Run Pest tests
./vendor/bin/pest

# Run specific test file
./vendor/bin/pest tests/Feature/ExampleTest.php
```

### Database

```bash
# Fresh migration with seeding
php artisan migrate:fresh --seed

# Rollback last migration
php artisan migrate:rollback

# View real-time logs
php artisan pail
```

## Project Structure

```
my-resume/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Backend/       # Admin CRUD controllers
│   │   │   ├── Frontend/      # Public-facing controllers
│   │   │   └── Auth/          # Authentication
│   │   └── Requests/          # Form validation
│   └── Models/                # Eloquent models
├── resources/
│   ├── js/
│   │   ├── pages/             # Inertia.js pages
│   │   │   ├── frontend/      # Public pages
│   │   │   ├── backend/       # Admin pages
│   │   │   └── auth/          # Auth pages
│   │   ├── components/        # Vue components
│   │   ├── layouts/           # Layout components
│   │   ├── composables/       # Vue composables
│   │   └── types/             # TypeScript types
│   ├── css/
│   │   └── app.css           # Global styles
│   └── views/                # Blade templates
├── routes/
│   ├── web.php               # Main routes
│   ├── auth.php              # Auth routes
│   └── settings.php          # Settings routes
├── database/
│   └── migrations/           # Database migrations
└── tests/                    # Pest PHP tests
```

## Common Issues and Solutions

### Permission Denied Errors

If you encounter permission errors, use the password `thai@123` with sudo:

```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R $USER:$USER storage bootstrap/cache
```

### Port Already in Use

If port 8000 is already in use:

```bash
php artisan serve --port=8080
```

### Node Modules Issues

If you experience npm issues, try clearing cache:

```bash
rm -rf node_modules package-lock.json
npm cache clean --force
npm install
```

### Database Connection Issues

Ensure your `.env` file has correct MySQL database configuration:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=my_resume
DB_USERNAME=resume_user
DB_PASSWORD=your_password
```

**Common MySQL errors:**

- **Access denied**: Check username and password in `.env`
- **Database doesn't exist**: Run `CREATE DATABASE my_resume;` in MySQL
- **Connection refused**: Ensure MySQL service is running: `sudo service mysql start`

## Features

### Public Frontend
- Home page with introduction
- About page with bio and skills
- Portfolio showcase
- Work experience timeline
- Education history
- Tech stack display
- Contact information
- Hobby/interests page
- Notes section
- Resume download

### Admin Backend
- Dashboard overview
- CRUD operations for:
  - About Me information
  - Work Experience entries
  - Education records
  - Tech Stack items
  - Project Types
  - Projects with portfolio
  - Popular Songs playlist
  - Notes with featured toggle
  - User management

### Theme Support
- Light mode
- Dark mode
- System preference detection
- Persistent theme selection

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run tests and linters
5. Submit a pull request

## License

[Your License Here]

## Support

For issues and questions, please create an issue in the repository.
