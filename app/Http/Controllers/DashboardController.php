<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\User;
use App\Models\AboutMe;
use App\Models\Project;
use App\Models\Education;
use App\Models\TechStack;
use App\Models\PopularSong;
use App\Models\ProjectType;
use App\Models\WorkExperience;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $aboutMe = AboutMe::first();
        $user = auth()->user();
        
        // Projects summary
        $totalProjects = Project::count();
        $projectsByType = ProjectType::withCount('projects')->get();
        $recentProjects = Project::with('projectType')
            ->orderBy('created_date', 'desc')
            ->limit(5)
            ->get();
        
        // Tech stack categories
        $techStacksByType = TechStack::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->get()
            ->pluck('count', 'type');
        
        // Work experience
        $totalExperience = WorkExperience::count();
        $recentExperience = WorkExperience::orderBy('from', 'desc')
            ->limit(3)
            ->get();
        
        // Education
        $totalEducation = Education::count();
        $latestEducation = Education::orderBy('to', 'desc')
            ->first();
        
        // Popular songs
        $totalSongs = PopularSong::count();
        $recentSongs = PopularSong::orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
        
        return Inertia::render('Dashboard', [
            'summary' => [
                'aboutMe' => $aboutMe,
                'user' => $user,
                'projects' => [
                    'total' => $totalProjects,
                    'byType' => $projectsByType,
                    'recent' => $recentProjects,
                ],
                'techStacks' => [
                    'byType' => $techStacksByType,
                    'total' => TechStack::count(),
                ],
                'workExperience' => [
                    'total' => $totalExperience,
                    'recent' => $recentExperience,
                ],
                'education' => [
                    'total' => $totalEducation,
                    'latest' => $latestEducation,
                ],
                'songs' => [
                    'total' => $totalSongs,
                    'recent' => $recentSongs,
                ],
            ]
        ]);
    }
}