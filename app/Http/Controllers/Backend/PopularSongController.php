<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\PopularSong;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PopularSongController extends Controller
{
    /**
     * Display a listing of popular songs.
     */
    public function index(): Response
    {
        $popularSongs = PopularSong::latest()->paginate(10);

        return Inertia::render('backend/PopularSong/Index', [
            'popularSongs' => $popularSongs,
        ]);
    }

    /**
     * Show the form for creating a new popular song.
     */
    public function create(): Response
    {
        return Inertia::render('backend/PopularSong/Create');
    }

    /**
     * Store a newly created popular song in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'    => ['required', 'string', 'max:255'],
            'artist'   => ['required', 'string', 'max:255'],
            'url'      => ['required', 'string', 'max:1000'],
            'duration' => ['required', 'integer', 'min:1', 'max:3600'], 
        ]);

        PopularSong::create($validated);

        return redirect()->route('popular-songs')
            ->with('success', 'Popular song created successfully.');
    }

    /**
     * Display the specified popular song.
     */
    public function show(PopularSong $popularSong): Response
    {
        return Inertia::render('backend/PopularSong/Show', [
            'popularSong' => $popularSong,
        ]);
    }

    /**
     * Show the form for editing the specified popular song.
     */
    public function edit(PopularSong $popularSong): Response
    {
        return Inertia::render('backend/PopularSong/Edit', [
            'popularSong' => $popularSong,
        ]);
    }

    /**
     * Update the specified popular song in storage.
     */
    public function update(Request $request, PopularSong $popularSong): RedirectResponse
    {
        $validated = $request->validate([
            'title'    => ['required', 'string', 'max:255'],
            'artist'   => ['required', 'string', 'max:255'],
            'url'      => ['required', 'string', 'max:1000'],
            'duration' => ['required', 'integer', 'min:1', 'max:3600'], // Max 1 hour
        ]);

        $popularSong->update($validated);

        return redirect()->route('popular-songs')
            ->with('success', 'Popular song updated successfully.');
    }

    /**
     * Remove the specified popular song from storage.
     */
    public function destroy(PopularSong $popularSong): RedirectResponse
    {
        $popularSong->delete();

        return redirect()->route('popular-songs')
            ->with('success', 'Popular song deleted successfully.');
    }

    /**
     * Get popular songs for frontend music player
     */
    public function getForPlayer(): \Illuminate\Http\JsonResponse
    {
        $songs = PopularSong::select('id', 'title', 'artist', 'url as src', 'duration')
            ->latest()
            ->get()
            ->map(function ($song) {
                return [
                    'id'       => $song->id,
                    'title'    => $song->title,
                    'artist'   => $song->artist,
                    'src'      => $song->src,
                    'duration' => $song->duration,
                ];
            });

        return response()->json($songs);
    }
}