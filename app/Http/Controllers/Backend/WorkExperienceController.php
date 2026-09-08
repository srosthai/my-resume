<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\WorkExperience;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class WorkExperienceController extends Controller
{
    /**
     * Display a listing of the work experiences.
     *
     * @return Response
     */
    public function index()
    {
        $workExperiences = WorkExperience::latest()->get();

        return Inertia::render('backend/WorkExperience/Index', [
            'workExperiences' => $workExperiences,
        ]);
    }

    /**
     * Show the form for creating a new work experience.
     *
     * @return Response
     */
    public function create()
    {
        return Inertia::render('backend/WorkExperience/Create');
    }

    /**
     * Store a newly created work experience in storage.
     *
     * @return RedirectResponse
     *
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'from' => 'nullable|string|max:255',
            'to' => 'nullable|string|max:255',
        ]);

        WorkExperience::create($validated);

        return redirect()->route('work-experience')->with('success', 'Work Experience created successfully.');
    }

    /**
     * Show the form for editing the specified work experience.
     *
     * @return Response
     */
    public function edit(WorkExperience $workExperience)
    {
        return Inertia::render('backend/WorkExperience/Edit', [
            'workExperience' => $workExperience,
        ]);
    }

    /**
     * Update the specified work experience in storage.
     *
     * @return RedirectResponse
     *
     * @throws ValidationException
     */
    public function update(Request $request, WorkExperience $workExperience)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'from' => 'nullable|string|max:255',
            'to' => 'nullable|string|max:255',
        ]);

        $workExperience->update($validated);

        return redirect()->route('work-experience')->with('success', 'Work Experience updated successfully.');
    }

    /**
     * Remove the specified work experience from storage.
     *
     * @return RedirectResponse
     */
    public function destroy(WorkExperience $workExperience)
    {
        $workExperience->delete();

        return redirect()->route('work-experience')->with('success', 'Work Experience deleted successfully.');
    }
}
