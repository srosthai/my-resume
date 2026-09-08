<?php

namespace App\Http\Requests\Backend;

use App\Enums\ProjectStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for both store and update of a Project.
 */
class ProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Route group is already restricted to the owner.
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
            'project_type_id' => ['nullable', 'integer', 'exists:project_types,id'],
            'technologies' => ['nullable', 'array'],
            'technologies.*' => ['string', 'max:100'],
            'created_date' => ['nullable', 'date'],
            'status' => ['required', Rule::enum(ProjectStatus::class)],
            'links' => ['nullable', 'array'],
            'links.*' => ['array'],
            'links.*.*' => ['string', 'max:2048'],
        ];
    }

    /**
     * Attributes that map straight onto the model.
     *
     * @return array<string, mixed>
     */
    public function projectData(): array
    {
        return $this->safe()->except(['image', 'remove_image']);
    }
}
