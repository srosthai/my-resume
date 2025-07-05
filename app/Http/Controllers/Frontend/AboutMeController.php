<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
