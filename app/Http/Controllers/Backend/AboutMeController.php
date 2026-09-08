<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AboutMe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AboutMeController extends Controller
{
    /**
     * Display all data of About Me.
     */
    public function index()
    {
        $aboutMes = AboutMe::latest()->get();

        return Inertia::render('backend/AboutMe/Index', [
            'aboutMes' => $aboutMes,
        ]);
    }

    /**
     * Show the form for creating a new About Me entry.
     */
    public function create()
    {
        return Inertia::render('backend/AboutMe/Create');
    }

    /**
     * Srore a newly created About Me entry in storage.
     *
     * @return RedirectResponse
     *
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
            'year_experience' => 'nullable|string|max:255',
            'fucus_on' => 'nullable|string|max:255',
        ]);

        AboutMe::create($validated);

        return redirect()->route('about-me')->with('success', 'About Me created successfully.');
    }

    /**
     * Show the form for editing the specified About Me entry.
     *
     * @return Response
     */
    public function edit(AboutMe $aboutMe)
    {
        return Inertia::render('backend/AboutMe/Edit', [
            'aboutMe' => $aboutMe,
        ]);
    }

    /**
     * Update the specified About Me entry in storage.
     *
     * @return RedirectResponse
     *
     * @throws ValidationException
     */
    public function update(Request $request, AboutMe $aboutMe)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
            'year_experience' => 'nullable|string|max:255',
            'fucus_on' => 'nullable|string|max:255',
        ]);

        $aboutMe->update($validated);

        return redirect()->route('about-me')->with('success', 'About Me updated successfully.');
    }

    /**
     * Remove the specified About Me entry from storage.
     *
     * @return RedirectResponse
     */
    public function destroy(AboutMe $aboutMe)
    {
        $aboutMe->delete();

        return redirect()->route('about-me')->with('success', 'About Me deleted successfully.');
    }
}
