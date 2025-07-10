<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectController extends Controller
{
    /**
     * Display all data of Projects.
     * @return \Inertia\Response
     */
    public function index()
    {
        $projects = Project::with('projectType')->latest()->get();
        return Inertia::render('backend/Project/Index', [
            'projects' => $projects
        ]);
    }

    /**
     * Show the form for creating a new Project entry.
     * @return \Inertia\Response
     */
    public function create()
    {
        $projectTypes = ProjectType::all();
        return Inertia::render('backend/Project/Create', [
            'projectTypes' => $projectTypes
        ]);
    }

    /**
     * Store a newly created Project entry in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'project_type_id' => 'nullable|exists:project_types,id',
            'technologies'    => 'nullable|array',
            'created_date'    => 'nullable|date',
            'status'          => 'required|in:processing,completed',
            'links'           => 'nullable|array',
        ]);

        if ($request->hasFile('image')) {
            $image      = $request->file('image');
            $imageName  = uniqid() . '_' . time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/projects'), $imageName);
            $validated['image'] = 'uploads/projects/' . $imageName;
        }

        Project::create($validated);

        return redirect()->route('projects')->with('success', 'Project created successfully.');
    }

    /**
     * Show the form for editing the specified Project entry.
     * @param \App\Models\Project $project
     * @return \Inertia\Response
     */
    public function edit(Project $project)
    {
        $projectTypes = ProjectType::all();
        return Inertia::render('backend/Project/Edit', [
            'project'      => $project,
            'projectTypes' => $projectTypes
        ]);
    }

    /**
     * Update the specified Project entry in storage.
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Project $project
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'project_type_id' => 'nullable|exists:project_types,id',
            'technologies'    => 'nullable|array',
            'created_date'    => 'nullable|date',
            'status'          => 'required|in:processing,completed',
            'links'           => 'nullable|array',
        ]);

        if ($request->hasFile('image')) {
            if ($project->image && file_exists(public_path($project->image))) {
                unlink(public_path($project->image));
            }
            $image = $request->file('image');
            $imageName = uniqid() . '_' . time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/projects'), $imageName);

            $validated['image'] = 'uploads/projects/' . $imageName;
        } elseif ($request->filled('image') && $request->input('image') === null) {
            if ($project->image && file_exists(public_path($project->image))) {
                unlink(public_path($project->image));
            }
            $validated['image'] = null;
        } else {
            $validated['image'] = $project->image;
        }


        $project->update($validated);

        return redirect()->route('projects')->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified Project entry from storage.
     * @param \App\Models\Project $project
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Project $project)
    {
        if ($project->image && file_exists(public_path($project->image))) {
            unlink(public_path($project->image));
        }

        $project->delete();

        return redirect()->route('projects')->with('success', 'Project deleted successfully.');
    }
}
