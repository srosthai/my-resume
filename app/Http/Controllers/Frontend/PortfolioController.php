<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\FeedVisibility;
use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\ContactMessageRequest;
use App\Mail\ContactMessage;
use App\Models\AboutMe;
use App\Models\Education;
use App\Models\Feed;
use App\Models\Note;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\TechStack;
use App\Models\User;
use App\Models\WorkExperience;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class PortfolioController extends Controller
{
    /**
     * Display the home page with the latest user.
     *
     * @return Response
     */
    public function home()
    {
        $users = User::owner()->first(User::publicColumns());

        $techStacks = TechStack::orderBy('id')->get(['id', 'name', 'logo', 'type']);

        $stats = [
            'projects' => Project::count(),
            'techStacks' => TechStack::count(),
            'experience' => WorkExperience::count(),
            'notes' => Note::published()->count(),
        ];

        return Inertia::render('frontend/Home', [
            'title' => 'Full Stack Developer',
            'description' => 'SROS THAI (srosthai) — Full Stack Developer from Phnom Penh, Cambodia. Building scalable, maintainable web apps with Laravel, Vue.js and modern web technologies.',
            'users' => $users,
            'techStacks' => $techStacks,
            'stats' => $stats,
        ]);
    }

    /**
     * Display the about page with user details.
     *
     * @return Response
     */
    public function about()
    {
        $user = User::owner()->first(User::publicColumns());
        $aboutMe = AboutMe::latest()->first() ?? [];
        $workExperience = WorkExperience::orderByDesc('from')->orderByDesc('id')->get() ?? [];
        $education = Education::orderByDesc('from')->orderByDesc('id')->get() ?? [];
        $techStacks = TechStack::orderBy('type')->orderBy('id')->get() ?? [];

        return Inertia::render('frontend/About', [
            'title' => 'About',
            'description' => 'Learn more about SROS THAI — a Full Stack Developer from Cambodia, his background, skills, and experience in Laravel and Vue.js development.',
            'user' => $user,
            'aboutMe' => $aboutMe,
            'workExperience' => $workExperience,
            'education' => $education,
            'techStacks' => $techStacks,
        ]);
    }

    /**
     * Display the portfolio page with projects.
     *
     * @return Response
     */
    public function portfolio(Request $request)
    {
        $query = Project::with('projectType');

        if ($request->has('type') && $request->type != '') {
            $query->where('project_type_id', $request->type);
        }

        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%')
                    ->orWhere('description', 'like', '%'.$request->search.'%');
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
            'title' => 'Projects',
            'description' => 'Explore my projects, showcasing my skills in web development and design.',
            'projects' => $projects,
            'projectTypes' => $projectTypes,
            'filters' => [
                'type' => $request->type ?? '',
                'search' => $request->search ?? '',
            ],
        ]);
    }

    /**
     * Display a single project detail page.
     */
    public function showProject(Project $project)
    {
        $project->load('projectType');
        $project->image = $project->image ? asset($project->image) : null;

        // Get previous and next project IDs for navigation
        $previousProject = Project::where('id', '<', $project->id)
            ->orderBy('id', 'desc')
            ->first(['id', 'title']);

        $nextProject = Project::where('id', '>', $project->id)
            ->orderBy('id', 'asc')
            ->first(['id', 'title']);

        return Inertia::render('frontend/ProjectDetail', [
            'title' => $project->title,
            'description' => $project->description,
            'project' => $project,
            'previousProject' => $previousProject,
            'nextProject' => $nextProject,
        ]);
    }

    /**
     * Display the contact page.
     *
     * @return Response
     */
    public function contact()
    {
        return Inertia::render('frontend/Contact', [
            'title' => 'Contact',
            'description' => 'Get in touch with SROS THAI for opportunities, collaborations, or tech discussions.',
        ]);
    }

    /**
     * Display the hobby page.
     *
     * @return Response
     */
    public function hobby()
    {
        return Inertia::render('frontend/Hobby', [
            'title' => 'Hobby',
            'description' => 'Explore my hobbies and interests outside of web development, including photography, travel, and more.',
        ]);
    }

    /**
     * Display the more page.
     *
     * @return Response
     */
    public function more()
    {
        return Inertia::render('frontend/More', [
            'title' => 'More',
            'description' => 'Additional content and features coming soon.',
        ]);
    }

    /**
     * Display the resume page.
     *
     * @return Response
     */
    public function resume()
    {
        // The resume page intentionally shows the owner's contact details.
        $users = User::owner()->first([...User::publicColumns(), 'email', 'phone', 'address']);
        $aboutMe = AboutMe::latest()->first() ?? [];
        $workExperience = WorkExperience::orderBy('id')->get() ?? [];
        $education = Education::orderBy('id')->get() ?? [];
        $techStacks = TechStack::orderBy('id')->get() ?? [];
        $projects = Project::with('projectType')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get()
            ->map(function ($project) {
                $project->image = $project->image ? asset($project->image) : null;

                return $project;
            });

        return Inertia::render('frontend/Resume', [
            'title' => 'Resume - '.($users->name ?? 'Professional Resume'),
            'description' => 'Professional resume showcasing experience, skills, and achievements.',
            'users' => $users,
            'aboutMe' => $aboutMe,
            'workExperience' => $workExperience,
            'education' => $education,
            'techStacks' => $techStacks,
            'projects' => $projects,
        ]);
    }

    /**
     * Display the feeds page.
     *
     * @return Response
     */
    public function feeds()
    {
        $feeds = Feed::published()
            ->where('visibility', FeedVisibility::Public)
            ->with('user:id,name,image')
            ->orderBy('is_pinned', 'desc')
            ->orderBy('published_at', 'desc')
            ->get()
            ->map(function ($feed) {
                if ($feed->images) {
                    $feed->images = array_map(fn ($img) => asset($img), $feed->images);
                }

                return $feed;
            });

        $activityTypes = Feed::getActivityTypes();

        return Inertia::render('frontend/Feeds', [
            'title' => 'My Feeds',
            'description' => 'Follow my lifestyle, hangouts, and adventures',
            'feeds' => $feeds,
            'activityTypes' => $activityTypes,
        ]);
    }

    /**
     * Display the note page.
     *
     * @return Response
     */
    public function note()
    {
        $notes = Note::published()
            ->orderBy('is_featured', 'desc')
            ->orderBy('published_at', 'desc')
            ->get();

        return Inertia::render('frontend/Note', [
            'title' => 'My Notes',
            'description' => 'My collection of programming notes and tutorials',
            'notes' => $notes,
        ]);
    }

    /**
     * Increment feed view count (rate-limited per IP).
     *
     * @return JsonResponse
     */
    public function incrementFeedView(Feed $feed, Request $request)
    {
        $key = 'feed_view_'.$feed->id.'_'.$request->ip();
        if (! cache()->has($key)) {
            $feed->increment('views');
            cache()->put($key, true, 300); // 5 min cooldown per IP per feed
        }

        return response()->json(['views' => $feed->fresh()->views]);
    }

    /**
     * Toggle feed like (tracked via IP, no auth needed).
     *
     * @return JsonResponse
     */
    public function toggleFeedLike(Feed $feed, Request $request)
    {
        $key = 'feed_like_'.$feed->id.'_'.$request->ip();
        $liked = cache()->has($key);

        if ($liked) {
            $feed->decrement('likes_count');
            cache()->forget($key);
        } else {
            $feed->increment('likes_count');
            cache()->put($key, true, 60 * 60 * 24 * 365); // 1 year
        }

        return response()->json([
            'likes_count' => $feed->fresh()->likes_count,
            'liked' => ! $liked,
        ]);
    }

    /**
     * Deliver a contact-form message to the site owner.
     */
    public function sendContactMessage(ContactMessageRequest $request): RedirectResponse
    {
        $recipient = config('mail.contact_to');

        if (! $recipient) {
            Log::error('Contact form: mail.contact_to (CONTACT_EMAIL) is not configured.');

            return back()->withErrors(['message' => 'The contact form is not available right now. Please email me directly.'])->withInput();
        }

        try {
            Mail::to($recipient)->send(new ContactMessage(
                $request->safe()->only(['name', 'email', 'subject', 'message']),
                $request->ip(),
                $request->userAgent(),
            ));
        } catch (\Throwable $e) {
            Log::error('Contact form: failed to send message.', ['exception' => $e]);

            return back()->withErrors(['message' => 'Failed to send your message. Please try again later or email me directly.'])->withInput();
        }

        return back()->with('success', "Message sent successfully! I'll get back to you soon.");
    }
}
