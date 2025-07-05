<?php

namespace App\Http\Controllers\Frontend;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PortfolioController extends Controller
{
    public function home()
    {
        $users = User::latest()->first();
        return Inertia::render('frontend/Home', [
            'users' => $users,
        ]);
    }

    public function about()
    {
        return Inertia::render('frontend/About', [
            'title' => 'About Me - Anton F.',
            'description' => 'Learn more about Anton F., his background, experience, and technical skills.',
        ]);
    }

    public function portfolio()
    {
        return Inertia::render('frontend/Portfolio', [
            'title' => 'Portfolio - Anton F.',
            'description' => 'Explore my collection of projects showcasing modern web development and innovative solutions.',
            'projects' => [
                [
                    'id' => 1,
                    'title' => 'Online Cinema Platform',
                    'description' => 'A comprehensive streaming platform featuring user authentication, personalized recommendations, watchlists, and responsive design for seamless viewing across all devices.',
                    'technologies' => ['React', 'Node.js', 'MongoDB', 'Express.js', 'Socket.io'],
                    'category' => 'fullstack',
                    'date' => 'September 2023',
                    'status' => 'completed'
                ],
                [
                    'id' => 2,
                    'title' => 'E-Commerce Dashboard',
                    'description' => 'Modern admin dashboard for e-commerce management with real-time analytics, inventory tracking, and order management system.',
                    'technologies' => ['Vue.js', 'TypeScript', 'PostgreSQL', 'Express.js', 'Chart.js'],
                    'category' => 'frontend',
                    'date' => 'June 2023',
                    'status' => 'completed'
                ]
            ]
        ]);
    }

    public function contact()
    {
        return Inertia::render('frontend/Contact', [
            'title' => 'Contact - Anton F.',
            'description' => 'Get in touch with Anton F. for opportunities, collaborations, or tech discussions.',
        ]);
    }

    public function more()
    {
        return Inertia::render('frontend/More', [
            'title'       => 'More - Anton F.',
            'description' => 'Additional content and features coming soon.',
        ]);
    }
}