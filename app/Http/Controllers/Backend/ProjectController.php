<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\ProjectRequest;
use App\Models\Project;
use App\Models\ProjectType;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function __construct(private readonly ImageUploadService $images) {}

    public function index(): Response
    {
        return Inertia::render('backend/Project/Index', [
            'projects' => Project::with('projectType')->latest()->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('backend/Project/Create', [
            'projectTypes' => ProjectType::orderBy('name')->get(),
        ]);
    }

    public function store(ProjectRequest $request): RedirectResponse
    {
        $data = $request->projectData();

        if ($request->hasFile('image')) {
            $data['image'] = $this->images->store($request->file('image'), 'projects');
        }

        Project::create($data);

        return redirect()->route('projects')->with('success', 'Project created successfully.');
    }

    public function edit(Project $project): Response
    {
        return Inertia::render('backend/Project/Edit', [
            'project' => $project,
            'projectTypes' => ProjectType::orderBy('name')->get(),
        ]);
    }

    public function update(ProjectRequest $request, Project $project): RedirectResponse
    {
        $data = $request->projectData();

        if ($request->hasFile('image')) {
            $this->images->delete($project->image);
            $data['image'] = $this->images->store($request->file('image'), 'projects');
        } elseif ($request->boolean('remove_image')) {
            $this->images->delete($project->image);
            $data['image'] = null;
        }

        $project->update($data);

        return redirect()->route('projects')->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->images->delete($project->image);
        $project->delete();

        return redirect()->route('projects')->with('success', 'Project deleted successfully.');
    }
}
