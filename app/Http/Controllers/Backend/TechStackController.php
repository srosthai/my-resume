<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\TechStackRequest;
use App\Models\TechStack;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TechStackController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('backend/TechStack/Index', [
            'techStacks' => TechStack::latest()->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('backend/TechStack/Create');
    }

    public function store(TechStackRequest $request): RedirectResponse
    {
        TechStack::create($request->validated());

        return redirect()->route('tech-stacks')->with('success', 'Tech Stack created successfully.');
    }

    public function edit(TechStack $techStack): Response
    {
        return Inertia::render('backend/TechStack/Edit', [
            'techStack' => $techStack,
        ]);
    }

    public function update(TechStackRequest $request, TechStack $techStack): RedirectResponse
    {
        $techStack->update($request->validated());

        return redirect()->route('tech-stacks')->with('success', 'Tech Stack updated successfully.');
    }

    public function destroy(TechStack $techStack): RedirectResponse
    {
        $techStack->delete();

        return redirect()->route('tech-stacks')->with('success', 'Tech Stack deleted successfully.');
    }
}
