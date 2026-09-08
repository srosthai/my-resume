<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\WorkExperienceRequest;
use App\Models\WorkExperience;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class WorkExperienceController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('backend/WorkExperience/Index', [
            'workExperiences' => WorkExperience::latest()->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('backend/WorkExperience/Create');
    }

    public function store(WorkExperienceRequest $request): RedirectResponse
    {
        WorkExperience::create($request->validated());

        return redirect()->route('work-experience')->with('success', 'Work Experience created successfully.');
    }

    public function edit(WorkExperience $workExperience): Response
    {
        return Inertia::render('backend/WorkExperience/Edit', [
            'workExperience' => $workExperience,
        ]);
    }

    public function update(WorkExperienceRequest $request, WorkExperience $workExperience): RedirectResponse
    {
        $workExperience->update($request->validated());

        return redirect()->route('work-experience')->with('success', 'Work Experience updated successfully.');
    }

    public function destroy(WorkExperience $workExperience): RedirectResponse
    {
        $workExperience->delete();

        return redirect()->route('work-experience')->with('success', 'Work Experience deleted successfully.');
    }
}
