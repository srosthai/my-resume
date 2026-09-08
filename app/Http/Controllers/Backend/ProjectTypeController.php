<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ProjectType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ProjectTypeController extends Controller
{
    /**
     * Display all data of Project Types.
     *
     * @return Response
     */
    public function index()
    {
        $projectTypes = ProjectType::latest()->get();

        return Inertia::render('backend/ProjectType/Index', [
            'projectTypes' => $projectTypes,
        ]);
    }

    /**
     * Show the form for creating a new Project Type entry.
     *
     * @return Response
     */
    public function create()
    {
        return Inertia::render('backend/ProjectType/Create');
    }

    /**
     * Store a newly created Project Type entry in storage.
     *
     * @return RedirectResponse
     *
     * @throws ValidationException
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
     *
     * @return Response
     */
    public function edit(ProjectType $projectType)
    {
        return Inertia::render('backend/ProjectType/Edit', [
            'projectType' => $projectType,
        ]);
    }

    /**
     * Update the specified Project Type entry in storage.
     *
     * @return RedirectResponse
     *
     * @throws ValidationException
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
     *
     * @return RedirectResponse
     */
    public function destroy(ProjectType $projectType)
    {
        $projectType->delete();

        return redirect()->route('project-types')->with('success', 'Project Type deleted successfully.');
    }
}
