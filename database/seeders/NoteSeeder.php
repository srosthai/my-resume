<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Note;
use App\Models\User;

class NoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        
        if (!$user) {
            $user = User::create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        $notes = [
            [
                'title' => 'How to Install Laravel',
                'category' => 'Laravel',
                'description' => 'Complete guide to installing Laravel framework with all necessary steps and requirements.',
                'tags' => ['PHP', 'Laravel', 'Installation', 'Composer'],
                'content' => [
                    'overview' => 'Laravel is a powerful PHP framework that makes web development elegant and simple. This guide will walk you through the installation process step by step.',
                    'requirements' => [
                        'PHP >= 8.1',
                        'Composer (PHP dependency manager)',
                        'OpenSSL PHP Extension',
                        'PDO PHP Extension',
                        'Mbstring PHP Extension',
                        'Tokenizer PHP Extension',
                        'XML PHP Extension',
                        'Ctype PHP Extension',
                        'JSON PHP Extension',
                        'BCMath PHP Extension'
                    ],
                    'steps' => [
                        [
                            'title' => 'Install Composer',
                            'description' => 'Download and install Composer from getcomposer.org',
                            'commands' => [
                                'curl -sS https://getcomposer.org/installer | php',
                                'mv composer.phar /usr/local/bin/composer'
                            ]
                        ],
                        [
                            'title' => 'Create Laravel Project',
                            'description' => 'Use Composer to create a new Laravel project',
                            'commands' => [
                                'composer create-project laravel/laravel your-project-name',
                                'cd your-project-name'
                            ]
                        ],
                        [
                            'title' => 'Set Permissions',
                            'description' => 'Set proper permissions for storage and bootstrap/cache directories',
                            'commands' => [
                                'chmod -R 755 storage',
                                'chmod -R 755 bootstrap/cache'
                            ]
                        ],
                        [
                            'title' => 'Configure Environment',
                            'description' => 'Copy .env.example to .env and generate application key',
                            'commands' => [
                                'cp .env.example .env',
                                'php artisan key:generate'
                            ]
                        ],
                        [
                            'title' => 'Start Development Server',
                            'description' => 'Run the Laravel development server',
                            'commands' => ['php artisan serve']
                        ]
                    ]
                ],
                'status' => 'published',
                'is_featured' => true,
                'published_at' => now(),
                'user_id' => $user->id
            ],
            [
                'title' => 'Vue.js 3 Setup with Composition API',
                'category' => 'Vue.js',
                'description' => 'Learn how to set up Vue.js 3 with Composition API and modern development tools.',
                'tags' => ['Vue.js', 'JavaScript', 'Composition API', 'Vite'],
                'content' => [
                    'overview' => 'Vue.js 3 brings many improvements including the Composition API, better TypeScript support, and improved performance. This guide covers the setup process.',
                    'requirements' => [
                        'Node.js >= 16.0',
                        'npm or yarn package manager',
                        'Basic knowledge of JavaScript',
                        'Text editor (VS Code recommended)'
                    ],
                    'steps' => [
                        [
                            'title' => 'Create Vue Project',
                            'description' => 'Create a new Vue.js project using create-vue',
                            'commands' => [
                                'npm create vue@latest my-vue-app',
                                'cd my-vue-app'
                            ]
                        ],
                        [
                            'title' => 'Install Dependencies',
                            'description' => 'Install project dependencies',
                            'commands' => ['npm install']
                        ],
                        [
                            'title' => 'Start Development Server',
                            'description' => 'Run the development server',
                            'commands' => ['npm run dev']
                        ],
                        [
                            'title' => 'Build for Production',
                            'description' => 'Build the project for production',
                            'commands' => ['npm run build']
                        ]
                    ]
                ],
                'status' => 'published',
                'is_featured' => false,
                'published_at' => now(),
                'user_id' => $user->id
            ],
            [
                'title' => 'Next.js 14 App Router Setup',
                'category' => 'Next.js',
                'description' => 'Complete guide to setting up Next.js 14 with the new App Router and modern features.',
                'tags' => ['Next.js', 'React', 'TypeScript', 'App Router'],
                'content' => [
                    'overview' => 'Next.js 14 introduces the stable App Router, improved performance, and better developer experience. This guide covers the setup and basic configuration.',
                    'requirements' => [
                        'Node.js >= 18.0',
                        'npm, yarn, or pnpm package manager',
                        'Basic knowledge of React',
                        'Git (recommended)'
                    ],
                    'steps' => [
                        [
                            'title' => 'Create Next.js Project',
                            'description' => 'Create a new Next.js project with TypeScript',
                            'commands' => [
                                'npx create-next-app@latest my-next-app --typescript --tailwind --eslint --app',
                                'cd my-next-app'
                            ]
                        ],
                        [
                            'title' => 'Install Additional Dependencies',
                            'description' => 'Install commonly used packages',
                            'commands' => ['npm install @types/node @types/react @types/react-dom']
                        ],
                        [
                            'title' => 'Start Development Server',
                            'description' => 'Run the development server',
                            'commands' => ['npm run dev']
                        ],
                        [
                            'title' => 'Build and Start Production',
                            'description' => 'Build and start the production server',
                            'commands' => [
                                'npm run build',
                                'npm start'
                            ]
                        ]
                    ]
                ],
                'status' => 'published',
                'is_featured' => true,
                'published_at' => now(),
                'user_id' => $user->id
            ],
            [
                'title' => 'Bootstrap 5 Integration',
                'category' => 'Bootstrap',
                'description' => 'How to integrate Bootstrap 5 into your web projects with modern build tools.',
                'tags' => ['Bootstrap', 'CSS', 'Sass', 'JavaScript'],
                'content' => [
                    'overview' => 'Bootstrap 5 removes jQuery dependency and introduces new components and utilities. This guide shows how to integrate it into your projects.',
                    'requirements' => [
                        'Node.js (for npm packages)',
                        'Basic knowledge of HTML/CSS',
                        'Build tool (Webpack, Vite, etc.)'
                    ],
                    'steps' => [
                        [
                            'title' => 'Install Bootstrap via npm',
                            'description' => 'Install Bootstrap and its dependencies',
                            'commands' => ['npm install bootstrap @popperjs/core']
                        ],
                        [
                            'title' => 'Import Bootstrap CSS',
                            'description' => 'Import Bootstrap CSS in your main CSS file',
                            'commands' => ['@import "~bootstrap/scss/bootstrap";']
                        ],
                        [
                            'title' => 'Import Bootstrap JS',
                            'description' => 'Import Bootstrap JavaScript components',
                            'commands' => ['import "bootstrap/dist/js/bootstrap.bundle.min.js";']
                        ],
                        [
                            'title' => 'CDN Alternative',
                            'description' => 'Use CDN for quick prototyping',
                            'commands' => [
                                '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">',
                                '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>'
                            ]
                        ]
                    ]
                ],
                'status' => 'published',
                'is_featured' => false,
                'published_at' => now(),
                'user_id' => $user->id
            ]
        ];

        foreach ($notes as $noteData) {
            Note::create($noteData);
        }
    }
}