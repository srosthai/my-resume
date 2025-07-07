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
            'title'       => 'About',
            'description' => 'Learn more about SROS THAI, our mission, and the team behind the platform.',
        ]);
    }

    public function portfolio()
    {
        return Inertia::render('frontend/Portfolio', [
            'title'       => 'Projects',
            'description' => 'Explore my collection of projects showcasing modern web development and innovative solutions.'
        ]);
    }

    public function contact()
    {
        return Inertia::render('frontend/Contact', [
            'title'       => 'Contact',
            'description' => 'Get in touch with Anton F. for opportunities, collaborations, or tech discussions.',
        ]);
    }

    public function hobby()
    {
        return Inertia::render('frontend/Hobby', [
            'title'       => 'Hobby',
            'description' => 'Explore my hobbies and interests outside of web development, including photography, travel, and more.',
        ]);
    }

    public function more()
    {
        return Inertia::render('frontend/More', [
            'title'       => 'More',
            'description' => 'Additional content and features coming soon.',
        ]);
    }
}