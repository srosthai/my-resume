<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\ProjectRequest;
use App\Models\Project;
use App\Models\ProjectType;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function __construct(private readonly ImageUploadService $images) {}

    public function index(): Response
    {
        return Inertia::render('backend/Project/Index', [
            'projects' => Project::withTrashed()->with('projectType')->latest()->get(),
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

        $data['gallery'] = $this->syncGallery($request);

        Project::create($data);

        return redirect()->route('backend.projects.index')->with('success', 'Project created successfully.');
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

        $data['gallery'] = $this->syncGallery($request, $project);

        $project->update($data);

        return redirect()->route('backend.projects.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return redirect()->route('backend.projects.index')->with('success', 'Project deleted. You can restore it from the list.');
    }

    public function restore(Project $project): RedirectResponse
    {
        $project->restore();

        return redirect()->route('backend.projects.index')->with('success', 'Project restored.');
    }

    /**
     * Keep only gallery paths that already belong to this project, drop the
     * rest, and append newly uploaded files. A request that never mentions
     * the gallery leaves the current set alone.
     *
     * @return list<string>|null
     */
    private function syncGallery(ProjectRequest $request, ?Project $project = null): ?array
    {
        $uploaded = [];

        foreach ($request->file('gallery', []) as $image) {
            if ($image instanceof UploadedFile) {
                $uploaded[] = $this->images->store($image, 'projects/gallery');
            }
        }

        if ($project === null) {
            return $uploaded === [] ? null : $uploaded;
        }

        if (! $request->exists('existing_gallery') && $uploaded === []) {
            return $project->gallery;
        }

        $current = $project->gallery ?? [];
        $requested = array_values(array_filter(
            (array) $request->input('existing_gallery', []),
            fn ($path) => is_string($path) && $path !== '',
        ));
        $kept = array_values(array_intersect($current, $requested));

        foreach (array_diff($current, $kept) as $removed) {
            $this->images->delete($removed);
        }

        $paths = [...$kept, ...$uploaded];

        return $paths === [] ? null : $paths;
    }
}
