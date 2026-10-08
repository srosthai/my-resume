<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\FeedRequest;
use App\Models\Feed;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Inertia\Inertia;
use Inertia\Response;

class FeedController extends Controller
{
    public function __construct(private readonly ImageUploadService $images) {}

    public function index(): Response
    {
        return Inertia::render('backend/Feed/Index', [
            'feeds' => Feed::withTrashed()->with('user:id,name,image')->latest()->get(),
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

        return redirect()->route('backend.feeds.index')->with('success', 'Feed created successfully.');
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
        $data['images'] = $this->syncImages($request, $feed);

        $feed->update($data);

        return redirect()->route('backend.feeds.index')->with('success', 'Feed updated successfully.');
    }

    public function destroy(Feed $feed): RedirectResponse
    {
        $feed->delete();

        return redirect()->route('backend.feeds.index')->with('success', 'Feed deleted. You can restore it from the list.');
    }

    /**
     * Keep only image paths that already belong to this feed, drop the rest,
     * and append newly uploaded files. A request that never mentions the
     * photos, and uploads nothing, leaves the current set alone. A blank
     * entry is how the edit form keeps this key in a multipart body when the
     * owner has cleared every photo.
     *
     * @return list<string>|null
     */
    private function syncImages(FeedRequest $request, Feed $feed): ?array
    {
        $uploaded = [];

        foreach ($request->file('images', []) as $image) {
            if ($image instanceof UploadedFile) {
                $uploaded[] = $this->images->store($image, 'feeds');
            }
        }

        if (! $request->exists('existing_images') && $uploaded === []) {
            return $feed->images;
        }

        $current = $feed->images ?? [];
        $requested = array_values(array_filter(
            (array) $request->input('existing_images', []),
            fn ($path) => is_string($path) && $path !== '',
        ));
        $kept = array_values(array_intersect($current, $requested));

        foreach (array_diff($current, $kept) as $removed) {
            $this->images->delete($removed);
        }

        $paths = [...$kept, ...$uploaded];

        return $paths === [] ? null : $paths;
    }

    public function restore(Feed $feed): RedirectResponse
    {
        $feed->restore();

        return redirect()->route('backend.feeds.index')->with('success', 'Feed restored.');
    }

    public function togglePinned(Feed $feed): RedirectResponse
    {
        $feed->update(['is_pinned' => ! $feed->is_pinned]);

        $status = $feed->is_pinned ? 'pinned' : 'unpinned';

        return redirect()->back()->with('success', "Feed has been {$status} successfully.");
    }
}
