<?php

namespace Database\Seeders;

use App\Models\TechStack;
use Illuminate\Database\Seeder;

class TechStackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stacks = [
            // Backend
            ['name' => 'Laravel', 'logo' => 'https://laravel.com/img/logomark.min.svg', 'type' => 'Backend', 'description' => 'PHP framework for web artisans with elegant syntax'],
            ['name' => 'PHP', 'logo' => 'https://www.php.net/images/logos/new-php-logo.svg', 'type' => 'Backend', 'description' => 'Server-side scripting language for web development'],
            ['name' => 'Node.js', 'logo' => 'https://nodejs.org/static/logos/nodejsLight.svg', 'type' => 'Backend', 'description' => 'JavaScript runtime for server-side applications'],

            // Frontend
            ['name' => 'Vue.js', 'logo' => 'https://vuejs.org/images/logo.png', 'type' => 'Frontend', 'description' => 'Progressive JavaScript framework for building UIs'],
            ['name' => 'React', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/a/a7/React-icon.svg', 'type' => 'Frontend', 'description' => 'JavaScript library for building user interfaces'],
            ['name' => 'TypeScript', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/4/4c/Typescript_logo_2020.svg', 'type' => 'Frontend', 'description' => 'Typed superset of JavaScript for scalable apps'],
            ['name' => 'JavaScript', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/6/6a/JavaScript-logo.png', 'type' => 'Frontend', 'description' => 'Programming language of the web'],
            ['name' => 'Tailwind CSS', 'logo' => 'https://tailwindcss.com/_next/static/media/tailwindcss-mark.3c5441fc7a190f9e5f1a9eee.svg', 'type' => 'Frontend', 'description' => 'Utility-first CSS framework for rapid UI development'],
            ['name' => 'Inertia.js', 'logo' => 'https://avatars.githubusercontent.com/u/47703742', 'type' => 'Frontend', 'description' => 'Modern monolith approach for SPAs'],
            ['name' => 'Vite', 'logo' => 'https://vitejs.dev/logo.svg', 'type' => 'Frontend', 'description' => 'Next generation frontend build tool'],

            // Database
            ['name' => 'MySQL', 'logo' => 'https://www.mysql.com/common/logos/logo-mysql-170x115.png', 'type' => 'Database', 'description' => 'Popular relational database management system'],
            ['name' => 'PostgreSQL', 'logo' => 'https://www.postgresql.org/media/img/about/press/elephant.png', 'type' => 'Database', 'description' => 'Advanced open source relational database'],
            ['name' => 'Redis', 'logo' => 'https://redis.io/images/redis-white.png', 'type' => 'Database', 'description' => 'In-memory data structure store for caching'],
            ['name' => 'MongoDB', 'logo' => 'https://www.mongodb.com/assets/images/global/leaf.png', 'type' => 'Database', 'description' => 'NoSQL document database for modern apps'],

            // DevOps
            ['name' => 'Docker', 'logo' => 'https://www.docker.com/wp-content/uploads/2022/03/Moby-logo.png', 'type' => 'DevOps', 'description' => 'Container platform for consistent deployments'],
            ['name' => 'Git', 'logo' => 'https://git-scm.com/images/logos/downloads/Git-Icon-1788C.png', 'type' => 'DevOps', 'description' => 'Distributed version control system'],
            ['name' => 'Linux', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/3/35/Tux.svg', 'type' => 'DevOps', 'description' => 'Open source operating system'],
            ['name' => 'Nginx', 'logo' => 'https://nginx.org/nginx.png', 'type' => 'DevOps', 'description' => 'High-performance web server and reverse proxy'],
            ['name' => 'AWS', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/9/93/Amazon_Web_Services_Logo.svg', 'type' => 'DevOps', 'description' => 'Cloud computing platform by Amazon'],
        ];

        foreach ($stacks as $stack) {
            TechStack::create($stack);
        }
    }
}
