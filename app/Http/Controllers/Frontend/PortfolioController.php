<?php

namespace App\Http\Controllers\Frontend;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AboutMe;
use App\Models\Education;
use App\Models\PopularSong;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\TechStack;
use App\Models\WorkExperience;
use App\Models\Note;

class PortfolioController extends Controller
{
    /**
     * Display the home page with the latest user.
     *
     * @return \Inertia\Response
     */
    public function home()
    {
        $users = User::latest()->first();
        return Inertia::render('frontend/Home', [
            'users' => $users,
        ]);
    }

    /**
     * Display the about page with user details.
     *
     * @return \Inertia\Response
     */
    public function about()
    {
        $aboutMe        = AboutMe::latest()->first() ?? [];
        $workExperience = WorkExperience::orderBy('id')->get() ?? [];
        $education      = Education::orderBy('id')->get() ?? [];
        $techStacks     = TechStack::orderBy('id')->get() ?? [];
        return Inertia::render('frontend/About', [
            'title'          => 'About',
            'description'    => 'Learn more about SROS THAI, our mission, and the team behind the platform.',
            'aboutMe'        => $aboutMe,
            'workExperience' => $workExperience,
            'education'      => $education,
            'techStacks'     => $techStacks,
        ]);
    }

    /**
     * Display the portfolio page with projects.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Inertia\Response
     */
    public function portfolio(Request $request)
    {
        $query = Project::with('projectType');
        
        if ($request->has('type') && $request->type != '') {
            $query->where('project_type_id', $request->type);
        }
        
        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        
        $projects = $query->orderBy('created_date', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($project) {
                $project->image = $project->image ? asset($project->image) : null;
                return $project;
            });
            
        $projectTypes = ProjectType::orderBy('name')->get();
        
        return Inertia::render('frontend/Portfolio', [
            'title'        => 'Projects',
            'description'  => 'Explore my projects, showcasing my skills in web development and design.',
            'projects'     => $projects,
            'projectTypes' => $projectTypes,
            'filters'      => [
                'type'     => $request->type ?? '',
                'search'   => $request->search ?? '',
            ],
        ]);
    }

    /**
     * Display the contact page.
     *
     * @return \Inertia\Response
     */
    public function contact()
    {
        return Inertia::render('frontend/Contact', [
            'title'       => 'Contact',
            'description' => 'Get in touch with Anton F. for opportunities, collaborations, or tech discussions.',
        ]);
    }

    /**
     * Display the hobby page.
     *
     * @return \Inertia\Response
     */
    public function hobby()
    {
        return Inertia::render('frontend/Hobby', [
            'title'       => 'Hobby',
            'description' => 'Explore my hobbies and interests outside of web development, including photography, travel, and more.',
        ]);
    }

    /**
     * Display the more page.
     *
     * @return \Inertia\Response
     */
    public function more()
    {
        return Inertia::render('frontend/More', [
            'title'       => 'More',
            'description' => 'Additional content and features coming soon.',
        ]);
    }

    /**
     * Display the resume page.
     *
     * @return \Inertia\Response
     */
    public function resume()
    {
        $users          = User::latest()->first();
        $aboutMe        = AboutMe::latest()->first() ?? [];
        $workExperience = WorkExperience::orderBy('id')->get() ?? [];
        $education      = Education::orderBy('id')->get() ?? [];
        $techStacks     = TechStack::orderBy('id')->get() ?? [];
        $projects       = Project::with('projectType')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get()
            ->map(function ($project) {
                $project->image = $project->image ? asset($project->image) : null;
                return $project;
            });

        return Inertia::render('frontend/Resume', [
            'title'          => 'Resume - ' . ($users->name ?? 'Professional Resume'),
            'description'    => 'Professional resume showcasing experience, skills, and achievements.',
            'users'          => $users,
            'aboutMe'        => $aboutMe,
            'workExperience' => $workExperience,
            'education'      => $education,
            'techStacks'     => $techStacks,
            'projects'       => $projects,
        ]);
    }

    /**
     * Display the note page.
     *
     * @return \Inertia\Response
     */
    public function note()
    {
        $notes = Note::published()
            ->orderBy('is_featured', 'desc')
            ->orderBy('published_at', 'desc')
            ->get();
        return Inertia::render('frontend/Note', [
            'title'          => 'My Notes',
            'description'    => 'My collection of programming notes and tutorials',
            'notes'          => $notes,
        ]); 
    }
}