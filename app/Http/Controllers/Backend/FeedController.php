<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Feed;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FeedController extends Controller
{
    /**
     * Display all data of Feeds.
     * @return \Inertia\Response
     */
    public function index()
    {
        $feeds         = Feed::with('user')->latest()->get();
        $activityTypes = Feed::getActivityTypes();

        return Inertia::render('backend/Feed/Index', [
            'feeds'         => $feeds,
            'activityTypes' => $activityTypes,
        ]);
    }

    /**
     * Show the form for creating a new Feed entry.
     * @return \Inertia\Response
     */
    public function create()
    {
        $activityTypes = Feed::getActivityTypes();

        return Inertia::render('backend/Feed/Create', [
            'activityTypes' => $activityTypes,
        ]);
    }

    /**
     * Store a newly created Feed entry in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'         => 'nullable|string|max:255',
            'body'          => 'required|string|max:5000',
            'images'        => 'nullable|array|max:10',
            'images.*'      => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'location'      => 'nullable|string|max:255',
            'mood'          => 'nullable|string|max:50',
            'activity_type' => 'nullable|string|max:100',
            'tags'          => 'nullable|array',
            'tags.*'        => 'string|max:50',
            'visibility'    => 'required|in:public,private',
            'status'        => 'required|in:draft,published,archived',
            'is_pinned'     => 'boolean',
            'likes_count'   => 'nullable|integer|min:0',
            'published_at'  => 'nullable|date',
        ]);

        // Handle multiple image uploads
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = uniqid() . '_' . time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/feeds'), $imageName);
                $imagePaths[] = 'uploads/feeds/' . $imageName;
            }
        }
        $validated['images'] = !empty($imagePaths) ? $imagePaths : null;

        if (isset($validated['tags'])) {
            $validated['tags'] = array_filter($validated['tags'], function ($tag) {
                return !empty(trim($tag));
            });
        }

        $validated['user_id'] = auth()->id();

        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        Feed::create($validated);

        return redirect()->route('feeds.index')->with('success', 'Feed created successfully.');
    }

    /**
     * Show the form for editing the specified Feed entry.
     * @param \App\Models\Feed $feed
     * @return \Inertia\Response
     */
    public function edit(Feed $feed)
    {
        $activityTypes = Feed::getActivityTypes();

        return Inertia::render('backend/Feed/Edit', [
            'feed'          => $feed,
            'activityTypes' => $activityTypes,
        ]);
    }

    /**
     * Update the specified Feed entry in storage.
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $feed = Feed::findOrFail($id);

        $validated = $request->validate([
            'title'            => 'nullable|string|max:255',
            'body'             => 'required|string|max:5000',
            'images'           => 'nullable|array|max:10',
            'images.*'         => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'existing_images'  => 'nullable|array',
            'existing_images.*' => 'string',
            'location'         => 'nullable|string|max:255',
            'mood'             => 'nullable|string|max:50',
            'activity_type'    => 'nullable|string|max:100',
            'tags'             => 'nullable|array',
            'tags.*'           => 'string|max:50',
            'visibility'       => 'required|in:public,private',
            'status'           => 'required|in:draft,published,archived',
            'is_pinned'        => 'boolean',
            'likes_count'      => 'nullable|integer|min:0',
            'published_at'     => 'nullable|date',
        ]);

        // Keep existing images that weren't removed
        $existingImages = $request->input('existing_images', []);
        $oldImages      = $feed->images ?? [];

        // Delete removed images from disk
        foreach ($oldImages as $oldImage) {
            if (!in_array($oldImage, $existingImages) && file_exists(public_path($oldImage))) {
                unlink(public_path($oldImage));
            }
        }

        // Handle new image uploads
        $newImagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = uniqid() . '_' . time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/feeds'), $imageName);
                $newImagePaths[] = 'uploads/feeds/' . $imageName;
            }
        }

        $allImages            = array_merge($existingImages, $newImagePaths);
        $validated['images']  = !empty($allImages) ? $allImages : null;

        unset($validated['existing_images']);

        if (isset($validated['tags'])) {
            $validated['tags'] = array_filter($validated['tags'], function ($tag) {
                return !empty(trim($tag));
            });
        }

        if ($validated['status'] === 'published' && empty($validated['published_at']) && $feed->status !== 'published') {
            $validated['published_at'] = now();
        }

        $feed->update($validated);

        return redirect()->route('feeds.index')->with('success', 'Feed updated successfully.');
    }

    /**
     * Remove the specified Feed from storage.
     * @param \App\Models\Feed $feed
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Feed $feed)
    {
        // Delete all associated images
        if ($feed->images) {
            foreach ($feed->images as $image) {
                if (file_exists(public_path($image))) {
                    unlink(public_path($image));
                }
            }
        }

        $feed->delete();

        return redirect()->route('feeds.index')->with('success', 'Feed deleted successfully.');
    }

    /**
     * Toggle pinned status of the feed.
     * @param \App\Models\Feed $feed
     * @return \Illuminate\Http\RedirectResponse
     */
    public function togglePinned(Feed $feed)
    {
        $feed->update(['is_pinned' => !$feed->is_pinned]);

        $status = $feed->is_pinned ? 'pinned' : 'unpinned';

        return redirect()->back()
            ->with('success', "Feed has been {$status} successfully.");
    }
}
