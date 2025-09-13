<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class NoteController extends Controller
{
    /**
     * Display all data of Notes.
     * @return \Inertia\Response
     */
    public function index()
    {
        $notes      = Note::with('user')->latest()->get();
        $categories = Note::getCategories();
        
        return Inertia::render('backend/Note/Index', [
            'notes'      => $notes,
            'categories' => $categories
        ]);
    }

    /**
     * Show the form for creating a new Note entry.
     * @return \Inertia\Response
     */
    public function create()
    {
        $categories = Note::getCategories();
        
        return Inertia::render('backend/Note/Create', [
            'categories' => $categories
        ]);
    }

    /**
     * Store a newly created Note entry in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'                        => 'required|string|max:255',
            'category'                     => 'required|string|max:100',
            'description'                  => 'required|string|max:1000',
            'tags'                         => 'nullable|array',
            'tags.*'                       => 'string|max:50',
            'content.overview'             => 'required|string',
            'content.requirements'         => 'required|array|min:1',
            'content.requirements.*'       => 'string|max:255',
            'content.steps'                => 'required|array|min:1',
            'content.steps.*.title'        => 'required|string|max:255',
            'content.steps.*.description'  => 'required|string|max:500',
            'content.steps.*.commands'     => 'required|array|min:1',
            'content.steps.*.commands.*'   => 'string|max:500',
            'status'                       => 'required|in:draft,published,archived',
            'is_featured'                  => 'boolean',
            'published_at'                 => 'nullable|date',
        ]);

        if (isset($validated['tags'])) {
            $validated['tags'] = array_filter($validated['tags'], function($tag) {
                return !empty(trim($tag));
            });
        }

        $validated['user_id'] = auth()->id();

        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        Note::create($validated);

        return redirect()->route('notes.index')->with('success', 'Note created successfully.');
    }

    /**
     * Display the specified Note.
     * @param \App\Models\Note $note
     * @return \Inertia\Response
     */
    public function show(Note $note)
    {
        $note->load('user');
        
        return Inertia::render('backend/Note/Show', [
            'note' => $note
        ]);
    }

    /**
     * Show the form for editing the specified Note.
     * @param \App\Models\Note $note
     * @return \Inertia\Response
     */
    public function edit(Note $note)
    {
        $categories = Note::getCategories();
        return Inertia::render('backend/Note/Edit', [
            'note'       => $note,
            'categories' => $categories
        ]);
    }

    /**
     * Update the specified Note in storage.
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Note $note
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Note $note)
    {
        $validated = $request->validate([
            'title'                        => 'required|string|max:255',
            'category'                     => 'required|string|max:100',
            'description'                  => 'required|string|max:1000',
            'tags'                         => 'nullable|array',
            'tags.*'                       => 'string|max:50',
            'content.overview'             => 'required|string',
            'content.requirements'         => 'required|array|min:1',
            'content.requirements.*'       => 'string|max:255',
            'content.steps'                => 'required|array|min:1',
            'content.steps.*.title'        => 'required|string|max:255',
            'content.steps.*.description'  => 'required|string|max:500',
            'content.steps.*.commands'     => 'required|array|min:1',
            'content.steps.*.commands.*'   => 'string|max:500',
            'status'                       => 'required|in:draft,published,archived',
            'is_featured'                  => 'boolean',
            'published_at'                 => 'nullable|date',
        ]);

        if (isset($validated['tags'])) {
            $validated['tags'] = array_filter($validated['tags'], function($tag) {
                return !empty(trim($tag));
            });
        }

        if ($validated['status'] === 'published' && empty($validated['published_at']) && $note->status !== 'published') {
            $validated['published_at'] = now();
        }

        $note->update($validated);

        return redirect()->route('notes.index')->with('success', 'Note updated successfully.');
    }

    /**
     * Remove the specified Note from storage.
     * @param \App\Models\Note $note
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Note $note)
    {
        $note->delete();
        return redirect()->route('notes.index')->with('success', 'Note deleted successfully.');
    }

    /**
     * Toggle featured status of the note.
     * @param \App\Models\Note $note
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleFeatured(Note $note)
    {
        $note->update(['is_featured' => !$note->is_featured]);

        $status = $note->is_featured ? 'featured' : 'unfeatured';
        
        return redirect()->back()
            ->with('success', "Note has been {$status} successfully.");
    }

    /**
     * Duplicate the specified note.
     * @param \App\Models\Note $note
     * @return \Illuminate\Http\RedirectResponse
     */
    public function duplicate(Note $note)
    {
        $duplicated                = $note->replicate();
        $duplicated->title         = $note->title . ' (Copy)';
        $duplicated->slug          = null;
        $duplicated->status        = 'draft';
        $duplicated->published_at  = null;
        $duplicated->views         = 0;
        $duplicated->is_featured   = false;
        $duplicated->user_id       = auth()->id();
        $duplicated->save();

        return redirect()->route('notes.edit', $duplicated)->with('success', 'Note duplicated successfully.');
    }
}