<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\WorkExperience;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WorkExperienceController extends Controller
{
    /**
     * Display a listing of the work experiences.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $workExperiences = WorkExperience::latest()->get();
        return Inertia::render('backend/WorkExperience/Index', [
            'workExperiences' => $workExperiences
        ]);
    }

    /**
     * Show the form for creating a new work experience.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('backend/WorkExperience/Create');
    }

    /**
     * Store a newly created work experience in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'nullable|string|max:255',
            'position'    => 'nullable|string|max:255',
            'company'     => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'from'        => 'nullable|string|max:255',
            'to'          => 'nullable|string|max:255',
        ]);

        WorkExperience::create($validated);

        return redirect()->route('work-experience')->with('success', 'Work Experience created successfully.');
    }

    /**
     * Show the form for editing the specified work experience.
     *
     * @param \App\Models\WorkExperience $workExperience
     * @return \Inertia\Response
     */
    public function edit(WorkExperience $workExperience)
    {
        return Inertia::render('backend/WorkExperience/Edit', [
            'workExperience' => $workExperience
        ]);
    }

    /**
     * Update the specified work experience in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\WorkExperience $workExperience
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, WorkExperience $workExperience)
    {
        $validated = $request->validate([
            'title'       => 'nullable|string|max:255',
            'position'    => 'nullable|string|max:255',
            'company'     => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'from'        => 'nullable|string|max:255',
            'to'          => 'nullable|string|max:255',
        ]);

        $workExperience->update($validated);

        return redirect()->route('work-experience')->with('success', 'Work Experience updated successfully.');
    }

    /**
     * Remove the specified work experience from storage.
     *
     * @param \App\Models\WorkExperience $workExperience
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(WorkExperience $workExperience)
    {
        $workExperience->delete();

        return redirect()->route('work-experience')->with('success', 'Work Experience deleted successfully.');
    }
}