<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\FeedRequest;
use App\Models\Feed;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class FeedController extends Controller
{
    public function __construct(private readonly ImageUploadService $images) {}

    public function index(): Response
    {
        return Inertia::render('backend/Feed/Index', [
            'feeds' => Feed::with('user:id,name,image')->latest()->get(),
            'activityTypes' => Feed::getActivityTypes(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('backend/Feed/Create', [
            'activityTypes' => Feed::getActivityTypes(),
        ]);
    }

    public function store(FeedRequest $request): RedirectResponse
    {
        $data = $request->feedData();

        $paths = [];
        foreach ($request->file('images', []) as $image) {
            $paths[] = $this->images->store($image, 'feeds');
        }
        $data['images'] = $paths !== [] ? $paths : null;

        $request->user()->feeds()->create($data);

        return redirect()->route('feeds.index')->with('success', 'Feed created successfully.');
    }

    public function edit(Feed $feed): Response
    {
        return Inertia::render('backend/Feed/Edit', [
            'feed' => $feed,
            'activityTypes' => Feed::getActivityTypes(),
        ]);
    }

    public function update(FeedRequest $request, Feed $feed): RedirectResponse
    {
        $data = $request->feedData();

        // Only paths that already belong to this feed may be kept; anything
        // else in existing_images (foreign paths, URLs, traversal) is dropped.
        $current = $feed->images ?? [];
        $kept = array_values(array_intersect($current, (array) $request->input('existing_images', [])));

        foreach (array_diff($current, $kept) as $removed) {
            $this->images->delete($removed);
        }

        foreach ($request->file('images', []) as $image) {
            $kept[] = $this->images->store($image, 'feeds');
        }

        $data['images'] = $kept !== [] ? $kept : null;

        $feed->update($data);

        return redirect()->route('feeds.index')->with('success', 'Feed updated successfully.');
    }

    public function destroy(Feed $feed): RedirectResponse
    {
        foreach ($feed->images ?? [] as $image) {
            $this->images->delete($image);
        }

        $feed->delete();

        return redirect()->route('feeds.index')->with('success', 'Feed deleted successfully.');
    }

    public function togglePinned(Feed $feed): RedirectResponse
    {
        $feed->update(['is_pinned' => ! $feed->is_pinned]);

        $status = $feed->is_pinned ? 'pinned' : 'unpinned';

        return redirect()->back()->with('success', "Feed has been {$status} successfully.");
    }
}
