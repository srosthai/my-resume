<?php

namespace App\Http\Controllers\Backend;

use App\Enums\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectType;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function __construct(private readonly ImageUploadService $images) {}

    /**
     * Display all data of Projects.
     *
     * @return Response
     */
    public function index()
    {
        $projects = Project::with('projectType')->latest()->get();

        return Inertia::render('backend/Project/Index', [
            'projects' => $projects,
        ]);
    }

    /**
     * Show the form for creating a new Project entry.
     *
     * @return Response
     */
    public function create()
    {
        $projectTypes = ProjectType::all();

        return Inertia::render('backend/Project/Create', [
            'projectTypes' => $projectTypes,
        ]);
    }

    /**
     * Store a newly created Project entry in storage.
     *
     * @return RedirectResponse
     *
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'project_type_id' => 'nullable|exists:project_types,id',
            'technologies' => 'nullable|array',
            'created_date' => 'nullable|date',
            'status' => ['required', Rule::enum(ProjectStatus::class)],
            'links' => 'nullable|array',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $this->images->store($request->file('image'), 'projects');
        }

        Project::create($validated);

        return redirect()->route('projects')->with('success', 'Project created successfully.');
    }

    /**
     * Show the form for editing the specified Project entry.
     *
     * @return Response
     */
    public function edit(Project $project)
    {
        $projectTypes = ProjectType::all();

        return Inertia::render('backend/Project/Edit', [
            'project' => $project,
            'projectTypes' => $projectTypes,
        ]);
    }

    /**
     * Update the specified Project entry in storage.
     *
     * @return RedirectResponse
     *
     * @throws ValidationException
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'remove_image' => 'nullable|boolean',
            'project_type_id' => 'nullable|exists:project_types,id',
            'technologies' => 'nullable|array',
            'created_date' => 'nullable|date',
            'status' => ['required', Rule::enum(ProjectStatus::class)],
            'links' => 'nullable|array',
        ]);

        unset($validated['remove_image']);

        if ($request->hasFile('image')) {
            $this->images->delete($project->image);
            $validated['image'] = $this->images->store($request->file('image'), 'projects');
        } elseif ($request->boolean('remove_image')) {
            $this->images->delete($project->image);
            $validated['image'] = null;
        } else {
            $validated['image'] = $project->image;
        }

        $project->update($validated);

        return redirect()->route('projects')->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified Project entry from storage.
     *
     * @return RedirectResponse
     */
    public function destroy(Project $project)
    {
        $this->images->delete($project->image);

        $project->delete();

        return redirect()->route('projects')->with('success', 'Project deleted successfully.');
    }
}
