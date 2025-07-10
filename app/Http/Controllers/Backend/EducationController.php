<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Education;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EducationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $educations = Education::latest()->get();
        return Inertia::render('backend/Education/Index', [
            'educations' => $educations
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('backend/Education/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'nullable|string|max:255',
            'major'       => 'nullable|string|max:255',
            'institution' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'from'        => 'nullable|string|max:255',
            'to'          => 'nullable|string|max:255',
        ]);

        Education::create($validated);

        return redirect()->route('eductions')->with('success', 'Education created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Education $education
     * @return \Inertia\Response
     */
    public function edit(Education $education)
    {
        return Inertia::render('backend/Education/Edit', [
            'education' => $education
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Education $education
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Education $education)
    {
        $validated = $request->validate([
            'title'       => 'nullable|string|max:255',
            'major'       => 'nullable|string|max:255',
            'institution' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'from'        => 'nullable|string|max:255',
            'to'          => 'nullable|string|max:255',
        ]);

        $education->update($validated);

        return redirect()->route('eductions')->with('success', 'Education updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Education $education
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Education $education)
    {
        $education->delete();

        return redirect()->route('eductions')->with('success', 'Education deleted successfully.');
    }
}