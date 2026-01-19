<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectType;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $webAppType = ProjectType::where('name', 'Web Application')->first();
        $ecommerceType = ProjectType::where('name', 'E-Commerce')->first();
        $apiType = ProjectType::where('name', 'API Development')->first();
        $dashboardType = ProjectType::where('name', 'Dashboard')->first();
        $cmsType = ProjectType::where('name', 'CMS')->first();

        $projects = [
            [
                'title' => 'Portfolio Website',
                'description' => 'A modern portfolio website built with Laravel 12, Vue.js 3, and Inertia.js. Features include dark mode, responsive design, blog system, and contact form with email integration.',
                'image' => null,
                'project_type_id' => $webAppType?->id,
                'technologies' => ['Laravel', 'Vue.js', 'Inertia.js', 'Tailwind CSS', 'TypeScript'],
                'created_date' => '2024-01-15',
                'status' => 'completed',
                'links' => [['github' => 'https://github.com/srosthai/portfolio'], ['live' => 'https://srosthai.com']],
            ],
            [
                'title' => 'E-Commerce Platform',
                'description' => 'Full-featured e-commerce platform with product management, shopping cart, Stripe payment integration, order tracking, and admin dashboard. Supports multiple vendors and inventory management.',
                'image' => null,
                'project_type_id' => $ecommerceType?->id,
                'technologies' => ['Laravel', 'Vue.js', 'Stripe', 'MySQL', 'Redis'],
                'created_date' => '2023-08-20',
                'status' => 'completed',
                'links' => [['github' => 'https://github.com/srosthai/ecommerce']],
            ],
            [
                'title' => 'RESTful API Service',
                'description' => 'Scalable REST API built with Laravel Sanctum authentication, rate limiting, comprehensive documentation with Swagger/OpenAPI, and webhook support for third-party integrations.',
                'image' => null,
                'project_type_id' => $apiType?->id,
                'technologies' => ['Laravel', 'PHP', 'MySQL', 'Redis', 'Docker'],
                'created_date' => '2023-05-10',
                'status' => 'completed',
                'links' => [['github' => 'https://github.com/srosthai/api-service']],
            ],
            [
                'title' => 'Task Management System',
                'description' => 'Collaborative task management application with real-time updates using WebSockets, team workspaces, Kanban boards, progress tracking, and notification system.',
                'image' => null,
                'project_type_id' => $webAppType?->id,
                'technologies' => ['Laravel', 'Vue.js', 'WebSockets', 'PostgreSQL', 'Pusher'],
                'created_date' => '2024-03-01',
                'status' => 'processing',
                'links' => [['github' => 'https://github.com/srosthai/task-manager']],
            ],
            [
                'title' => 'Analytics Dashboard',
                'description' => 'Real-time analytics dashboard for monitoring business metrics, user behavior, and sales performance. Features interactive charts, data export, and scheduled reports.',
                'image' => null,
                'project_type_id' => $dashboardType?->id,
                'technologies' => ['Vue.js', 'Chart.js', 'Laravel', 'MySQL', 'Tailwind CSS'],
                'created_date' => '2023-11-15',
                'status' => 'completed',
                'links' => [['github' => 'https://github.com/srosthai/analytics-dashboard']],
            ],
            [
                'title' => 'Content Management System',
                'description' => 'Headless CMS with a powerful admin panel, media library, SEO tools, and multi-language support. RESTful API for frontend integration with any framework.',
                'image' => null,
                'project_type_id' => $cmsType?->id,
                'technologies' => ['Laravel', 'Vue.js', 'Inertia.js', 'MySQL', 'AWS S3'],
                'created_date' => '2023-02-28',
                'status' => 'completed',
                'links' => [['github' => 'https://github.com/srosthai/headless-cms']],
            ],
        ];

        foreach ($projects as $project) {
            Project::create($project);
        }
    }
}
