<?php

namespace App\Http\Controllers;

use App\Models\AboutMe;
use App\Models\Education;
use App\Models\Feed;
use App\Models\Note;
use App\Models\PopularSong;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\TechStack;
use App\Models\WorkExperience;
use Inertia\Inertia;

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
        $recentExperience = WorkExperience::query()->inCareerOrder()
            ->limit(3)
            ->get();

        // Education
        $totalEducation = Education::count();
        $latestEducation = Education::query()
            ->orderByRaw('case when "to" is null then 0 else 1 end')
            ->orderByDesc('to')
            ->orderByDesc('from')
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
                'notes' => [
                    'total' => Note::count(),
                    'published' => Note::published()->count(),
                    'recent' => Note::query()->latest()->limit(3)->get(['id', 'title', 'status']),
                ],
                'feeds' => [
                    'total' => Feed::count(),
                    'published' => Feed::published()->visible()->count(),
                    'recent' => Feed::query()->latest()->limit(3)->get(['id', 'title', 'body', 'status']),
                ],
            ],
        ]);
    }
}
