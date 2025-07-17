<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Note;
use App\Models\User;
use Inertia\Inertia;
use App\Models\AboutMe;
use App\Models\Project;
use App\Models\Education;
use App\Models\TechStack;
use App\Models\PopularSong;
use App\Models\ProjectType;
use Illuminate\Http\Request;
use App\Models\WorkExperience;
use Resend\Laravel\Facades\Resend;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

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

    /**
     * Send contact message via Resend API
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendContactMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'    => 'required|string|max:255|min:2',
            'email'   => 'required|email|max:255',
            'subject' => 'required|string|max:255|min:5',
            'message' => 'required|string|max:5000|min:10',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $spamWords      = ['viagra', 'casino', 'lottery', 'winner', 'congratulations', 'click here', 'free money'];
        $messageContent = strtolower($request->message . ' ' . $request->subject);
        
        foreach ($spamWords as $word) {
            if (strpos($messageContent, $word) !== false) {
                return redirect()->back()->withErrors(['message' => 'Message appears to be spam and was blocked.'])->withInput();
            }
        }

        $key = 'contact_form_' . $request->ip();
        if (cache()->has($key)) {
            return redirect()->back()->withErrors(['message' => 'Please wait before sending another message.'])->withInput();
        }
        cache()->put($key, true, 300);

        try {
            if (!env('RESEND_KEY')) {
                throw new \Exception('RESEND_KEY is not configured in .env file');
            }

            $result = Resend::emails()->send([
                'from'     => 'Portfolio Contact <noreply@resend.dev>',
                'reply_to' => $request->email,
                'to'       => [env('CONTACT_EMAIL', 'srosthai00@gmail.com')],
                'subject'  => '[Portfolio Contact] ' . $request->subject,
                'html'     => view('emails.contact', [
                    'name'           => $request->name,
                    'email'          => $request->email,
                    'subject'        => $request->subject,
                    'messageContent' => $request->message,
                    'userAgent'      => $request->userAgent(),
                    'ipAddress'      => $request->ip(),
                    'timestamp'      => now()->format('Y-m-d H:i:s'),
                ])->render(),
                'headers'  => [
                    'X-Entity-Ref-ID' => uniqid(),
                ],
            ]);

            return redirect()->back()->with('success', 'Message sent successfully! I\'ll get back to you soon.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['message' => 'Failed to send message: ' . $e->getMessage()])->withInput();
        }
    }
}