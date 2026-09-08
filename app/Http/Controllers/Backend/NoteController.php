<?php

namespace App\Http\Controllers\Backend;

use App\Enums\PublishStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\NoteRequest;
use App\Models\Note;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NoteController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('backend/Note/Index', [
            'notes' => Note::with('user:id,name,image')->latest()->get(),
            'categories' => Note::getCategories(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('backend/Note/Create', [
            'categories' => Note::getCategories(),
        ]);
    }

    public function store(NoteRequest $request): RedirectResponse
    {
        $request->user()->notes()->create($request->noteData());

        return redirect()->route('notes.index')->with('success', 'Note created successfully.');
    }

    public function show(Note $note): Response
    {
        return Inertia::render('backend/Note/Show', [
            'note' => $note->load('user:id,name,image'),
        ]);
    }

    public function edit(Note $note): Response
    {
        return Inertia::render('backend/Note/Edit', [
            'note' => $note,
            'categories' => Note::getCategories(),
        ]);
    }

    public function update(NoteRequest $request, Note $note): RedirectResponse
    {
        $note->update($request->noteData());

        return redirect()->route('notes.index')->with('success', 'Note updated successfully.');
    }

    public function destroy(Note $note): RedirectResponse
    {
        $note->delete();

        return redirect()->route('notes.index')->with('success', 'Note deleted successfully.');
    }

    public function toggleFeatured(Note $note): RedirectResponse
    {
        $note->update(['is_featured' => ! $note->is_featured]);

        $status = $note->is_featured ? 'featured' : 'unfeatured';

        return redirect()->back()->with('success', "Note has been {$status} successfully.");
    }

    public function duplicate(Request $request, Note $note): RedirectResponse
    {
        $duplicate = $note->replicate(['slug', 'views']);
        $duplicate->title = $note->title.' (Copy)';
        $duplicate->status = PublishStatus::Draft;
        $duplicate->published_at = null;
        $duplicate->is_featured = false;
        $duplicate->user_id = $request->user()->id;
        $duplicate->save();

        return redirect()->route('notes.edit', $duplicate)->with('success', 'Note duplicated successfully.');
    }
}
