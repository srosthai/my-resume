<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\AboutMeRequest;
use App\Models\AboutMe;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AboutMeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('backend/AboutMe/Index', [
            'aboutMes' => AboutMe::latest()->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('backend/AboutMe/Create');
    }

    public function store(AboutMeRequest $request): RedirectResponse
    {
        AboutMe::create($request->validated());

        return redirect()->route('backend.about-me.index')->with('success', 'About Me created successfully.');
    }

    public function edit(AboutMe $aboutMe): Response
    {
        return Inertia::render('backend/AboutMe/Edit', [
            'aboutMe' => $aboutMe,
        ]);
    }

    public function update(AboutMeRequest $request, AboutMe $aboutMe): RedirectResponse
    {
        $aboutMe->update($request->validated());

        return redirect()->route('backend.about-me.index')->with('success', 'About Me updated successfully.');
    }

    public function destroy(AboutMe $aboutMe): RedirectResponse
    {
        $aboutMe->delete();

        return redirect()->route('backend.about-me.index')->with('success', 'About Me deleted successfully.');
    }
}
