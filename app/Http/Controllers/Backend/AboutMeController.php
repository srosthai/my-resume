<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class AboutMeController extends Controller
{
    public function index()
    {
        return Inertia::render('frontend/AboutMe', [
            'title'       => 'About Me',
            'description' => 'Learn more about me, my background, and my skills.',
        ]);
    }
}
