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
use App\Models\PopularSong;
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
use Illuminate\Support\Str;
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
        $workExperience = WorkExperience::query()->inCareerOrder()->get();
        $education = Education::query()->inCareerOrder()->get();
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
        $filters = $request->validate([
            'type' => ['nullable', 'integer', 'exists:project_types,id'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $type = $filters['type'] ?? null;
        $search = trim((string) ($filters['search'] ?? ''));

        $query = Project::with('projectType');

        if ($type !== null) {
            $query->where('project_type_id', $type);
        }

        if ($search !== '') {
            // Escape LIKE wildcards so a visitor cannot turn the search into a pattern scan.
            $term = '%'.addcslashes($search, '%_\\').'%';
            // ESCAPE is explicit because SQLite has no default escape character.
            $query->where(function ($q) use ($term) {
                $q->whereRaw('title LIKE ? ESCAPE ?', [$term, '\\'])
                    ->orWhereRaw('description LIKE ? ESCAPE ?', [$term, '\\']);
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
                'type' => $type !== null ? (string) $type : '',
                'search' => $search,
            ],
        ]);
    }

    /**
     * Display a single project detail page.
     */
    public function showProject(string $project)
    {
        // Slugs are canonical; old numeric URLs still resolve and redirect permanently.
        $model = Project::where('slug', $project)->first();

        if ($model === null && ctype_digit($project)) {
            $model = Project::findOrFail((int) $project);

            if ($model->slug) {
                return redirect()->route('portfolio.show', $model->slug, 301);
            }
        }

        abort_if($model === null, 404);
        $project = $model;

        $project->load('projectType');
        $project->image = $project->image ? asset($project->image) : null;

        // Same order as the portfolio list: newest created_date first, then highest id.
        $ordered = Project::query()
            ->orderByDesc('created_date')
            ->orderByDesc('id')
            ->get(['id', 'title', 'slug']);
        $index = $ordered->search(fn (Project $item) => $item->id === $project->id);
        $previousProject = is_int($index) && $index > 0 ? $ordered[$index - 1] : null;
        $nextProject = is_int($index) ? $ordered->get($index + 1) : null;

        return Inertia::render('frontend/ProjectDetail', [
            'title' => $project->title,
            'description' => $project->description,
            'project' => $project,
            'previousProject' => $previousProject,
            'nextProject' => $nextProject,
            'jsonLd' => [
                '@context' => 'https://schema.org',
                '@type' => 'CreativeWork',
                'name' => $project->title,
                'description' => Str::limit(strip_tags((string) $project->description), 200),
                'image' => $project->image,
                'url' => route('portfolio.show', $project->slug),
                'dateCreated' => optional($project->created_date)->toDateString(),
                'author' => ['@id' => config('seo.url').'/#person'],
            ],
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
        $songs = PopularSong::query()
            ->latest()
            ->get(['id', 'title', 'artist', 'url', 'duration'])
            ->map(fn (PopularSong $song) => [
                'id' => $song->id,
                'title' => $song->title,
                'artist' => $song->artist,
                'src' => $song->url,
                'duration' => $song->duration,
            ])
            ->values();

        return Inertia::render('frontend/More', [
            'title' => 'Music',
            'description' => 'Songs from the library, the same list as the music player.',
            'songs' => $songs,
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
    public function feeds(Request $request)
    {
        $feeds = Feed::published()
            ->visible()
            ->with('user:id,name,image')
            ->orderBy('is_pinned', 'desc')
            ->orderBy('published_at', 'desc')
            ->get()
            ->map(function ($feed) use ($request) {
                if ($feed->images) {
                    $feed->images = array_map(fn ($img) => asset($img), $feed->images);
                }

                $feed->setAttribute('liked', $this->visitorLikes($feed, $request));

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
     * A single published note at its permanent URL.
     */
    public function showNote(Note $note, Request $request)
    {
        abort_unless($note->isPublished(), 404);

        $this->countView($note, 'note', $request);

        $related = Note::published()
            ->where('category', $note->category)
            ->whereKeyNot($note->id)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get(['id', 'title', 'slug', 'category', 'description', 'published_at']);

        $summary = Str::limit(strip_tags($note->description), 160);

        return Inertia::render('frontend/NoteShow', [
            'title' => $note->title,
            'description' => $summary,
            'note' => $note,
            'related' => $related,
            'jsonLd' => [
                '@context' => 'https://schema.org',
                '@type' => 'TechArticle',
                'headline' => $note->title,
                'description' => $summary,
                'articleSection' => $note->category,
                'keywords' => implode(', ', $note->tags ?? []),
                'url' => route('note.show', $note->slug),
                'datePublished' => $note->published_at?->toAtomString(),
                'dateModified' => $note->updated_at?->toAtomString(),
                'author' => ['@id' => config('seo.url').'/#person'],
            ],
        ]);
    }

    /**
     * A single public feed entry at its permanent URL.
     */
    public function showFeed(Feed $feed, Request $request)
    {
        abort_unless($feed->isPublished() && $feed->visibility === FeedVisibility::Public, 404);

        $this->countView($feed, 'feed', $request);

        $feed->load('user:id,name,image');
        $feed->images = $feed->images ? array_map(fn ($img) => asset($img), $feed->images) : null;
        $feed->setAttribute('liked', $this->visitorLikes($feed, $request));

        $summary = Str::limit(strip_tags($feed->body), 160);

        return Inertia::render('frontend/FeedShow', [
            'title' => $feed->title ?: $summary,
            'description' => $summary,
            'feed' => $feed,
            'jsonLd' => [
                '@context' => 'https://schema.org',
                '@type' => 'SocialMediaPosting',
                'headline' => $feed->title ?: $summary,
                'articleBody' => $feed->body,
                'image' => $feed->images,
                'url' => route('feeds.show', $feed->slug),
                'datePublished' => $feed->published_at?->toAtomString(),
                'author' => ['@id' => config('seo.url').'/#person'],
            ],
        ]);
    }

    /**
     * Count one view per visitor per five minutes.
     */
    private function countView(Note|Feed $model, string $kind, Request $request): void
    {
        $key = "{$kind}_view_{$model->id}_{$request->ip()}";

        if (! cache()->has($key)) {
            $model->increment('views');
            cache()->put($key, true, 300);
        }
    }

    /**
     * Increment feed view count (rate-limited per IP).
     *
     * @return JsonResponse
     */
    public function incrementFeedView(Feed $feed, Request $request)
    {
        $this->abortUnlessPublic($feed);

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
        $this->abortUnlessPublic($feed);

        $key = $this->likeCacheKey($feed, $request);
        $liked = cache()->has($key);

        if ($liked) {
            Feed::query()->whereKey($feed->id)->where('likes_count', '>', 0)->decrement('likes_count');
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

    private function abortUnlessPublic(Feed $feed): void
    {
        abort_unless($feed->isPublished() && $feed->visibility === FeedVisibility::Public, 404);
    }

    private function visitorLikes(Feed $feed, Request $request): bool
    {
        return cache()->has($this->likeCacheKey($feed, $request));
    }

    private function likeCacheKey(Feed $feed, Request $request): string
    {
        return 'feed_like_'.$feed->id.'_'.$request->ip();
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
