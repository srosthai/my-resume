<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\PopularSongRequest;
use App\Models\PopularSong;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PopularSongController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('backend/PopularSong/Index', [
            'popularSongs' => PopularSong::latest()->paginate(10),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('backend/PopularSong/Create');
    }

    public function store(PopularSongRequest $request): RedirectResponse
    {
        PopularSong::create($request->validated());

        return redirect()->route('backend.popular-songs.index')->with('success', 'Popular song created successfully.');
    }

    public function show(PopularSong $popularSong): Response
    {
        return Inertia::render('backend/PopularSong/Show', [
            'popularSong' => $popularSong,
        ]);
    }

    public function edit(PopularSong $popularSong): Response
    {
        return Inertia::render('backend/PopularSong/Edit', [
            'popularSong' => $popularSong,
        ]);
    }

    public function update(PopularSongRequest $request, PopularSong $popularSong): RedirectResponse
    {
        $popularSong->update($request->validated());

        return redirect()->route('backend.popular-songs.index')->with('success', 'Popular song updated successfully.');
    }

    public function destroy(PopularSong $popularSong): RedirectResponse
    {
        $popularSong->delete();

        return redirect()->route('backend.popular-songs.index')->with('success', 'Popular song deleted successfully.');
    }

    /**
     * Playlist for the public music player.
     */
    public function getForPlayer(): JsonResponse
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
            ]);

        return response()->json($songs);
    }
}
