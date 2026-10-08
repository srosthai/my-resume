<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\EducationRequest;
use App\Models\Education;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class EducationController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('backend/Education/Index', [
            'educations' => Education::query()->inCareerOrder()->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('backend/Education/Create');
    }

    public function store(EducationRequest $request): RedirectResponse
    {
        Education::create($request->validated());

        return redirect()->route('backend.education.index')->with('success', 'Education created successfully.');
    }

    public function edit(Education $education): Response
    {
        return Inertia::render('backend/Education/Edit', [
            'education' => $education,
        ]);
    }

    public function update(EducationRequest $request, Education $education): RedirectResponse
    {
        $education->update($request->validated());

        return redirect()->route('backend.education.index')->with('success', 'Education updated successfully.');
    }

    public function destroy(Education $education): RedirectResponse
    {
        $education->delete();

        return redirect()->route('backend.education.index')->with('success', 'Education deleted successfully.');
    }
}
