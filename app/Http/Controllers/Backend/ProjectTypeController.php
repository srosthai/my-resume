<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\ProjectTypeRequest;
use App\Models\ProjectType;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProjectTypeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('backend/ProjectType/Index', [
            'projectTypes' => ProjectType::latest()->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('backend/ProjectType/Create');
    }

    public function store(ProjectTypeRequest $request): RedirectResponse
    {
        ProjectType::create($request->validated());

        return redirect()->route('backend.project-types.index')->with('success', 'Project Type created successfully.');
    }

    public function edit(ProjectType $projectType): Response
    {
        return Inertia::render('backend/ProjectType/Edit', [
            'projectType' => $projectType,
        ]);
    }

    public function update(ProjectTypeRequest $request, ProjectType $projectType): RedirectResponse
    {
        $projectType->update($request->validated());

        return redirect()->route('backend.project-types.index')->with('success', 'Project Type updated successfully.');
    }

    public function destroy(ProjectType $projectType): RedirectResponse
    {
        $projectType->delete();

        return redirect()->route('backend.project-types.index')->with('success', 'Project Type deleted successfully.');
    }
}
