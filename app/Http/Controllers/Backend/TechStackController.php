<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\TechStack;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TechStackController extends Controller
{
    /**
     * Display a listing of the tech stacks.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $techStacks = TechStack::latest()->get();
        return Inertia::render('backend/TechStack/Index', [
            'techStacks' => $techStacks
        ]);
    }

    /**
     * Show the form for creating a new tech stack.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('backend/TechStack/Create');
    }

    /**
     * Store a newly created tech stack in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'nullable|string|max:255',
            'logo'        => 'nullable|string|max:255',
            'type'        => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        TechStack::create($validated);

        return redirect()->route('tech-stacks')->with('success', 'Tech Stack created successfully.');
    }

    /**
     * Show the form for editing the specified tech stack.
     *
     * @param \App\Models\TechStack $techStack
     * @return \Inertia\Response
     */
    public function edit(TechStack $techStack)
    {
        return Inertia::render('backend/TechStack/Edit', [
            'techStack' => $techStack
        ]);
    }

    /**
     * Update the specified tech stack in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\TechStack $techStack
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, TechStack $techStack)
    {
        $validated = $request->validate([
            'name'        => 'nullable|string|max:255',
            'logo'        => 'nullable|string|max:255',
            'type'        => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $techStack->update($validated);

        return redirect()->route('tech-stacks')->with('success', 'Tech Stack updated successfully.');
    }

    /**
     * Remove the specified tech stack from storage.
     *
     * @param \App\Models\TechStack $techStack
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(TechStack $techStack)
    {
        $techStack->delete();

        return redirect()->route('tech-stacks')->with('success', 'Tech Stack deleted successfully.');
    }
}