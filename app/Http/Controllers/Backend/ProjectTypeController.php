<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ProjectType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectTypeController extends Controller
{
    /**
     * Display all data of Project Types.
     * @return \Inertia\Response
     */
    public function index()
    {
        $projectTypes = ProjectType::latest()->get();
        return Inertia::render('backend/ProjectType/Index', [
            'projectTypes' => $projectTypes
        ]);
    }

    /**
     * Show the form for creating a new Project Type entry.
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('backend/ProjectType/Create');
    }

    /**
     * Store a newly created Project Type entry in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        ProjectType::create($validated);

        return redirect()->route('project-types')->with('success', 'Project Type created successfully.');
    }

    /**
     * Show the form for editing the specified Project Type entry.
     * @param \App\Models\ProjectType $projectType
     * @return \Inertia\Response
     */
    public function edit(ProjectType $projectType)
    {
        return Inertia::render('backend/ProjectType/Edit', [
            'projectType' => $projectType
        ]);
    }

    /**
     * Update the specified Project Type entry in storage.
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\ProjectType $projectType
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, ProjectType $projectType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $projectType->update($validated);

        return redirect()->route('project-types')->with('success', 'Project Type updated successfully.');
    }

    /**
     * Remove the specified Project Type entry from storage.
     * @param \App\Models\ProjectType $projectType
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ProjectType $projectType)
    {
        $projectType->delete();

        return redirect()->route('project-types')->with('success', 'Project Type deleted successfully.');
    }
}