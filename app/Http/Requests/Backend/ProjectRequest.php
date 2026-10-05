<?php

namespace App\Http\Requests\Backend;

use App\Enums\ProjectStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
            'gallery' => ['nullable', 'array', 'max:12'],
            'gallery.*' => ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'existing_gallery' => ['nullable', 'array', 'max:12'],
            'existing_gallery.*' => ['string', 'max:255'],
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
        return $this->safe()->except(['image', 'remove_image', 'gallery', 'existing_gallery']);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $kept = array_filter(
                (array) $this->input('existing_gallery', []),
                fn ($path) => is_string($path) && $path !== '',
            );
            $incoming = $this->file('gallery', []);
            $incomingCount = is_array($incoming) ? count($incoming) : 0;

            if (count($kept) + $incomingCount > 12) {
                $validator->errors()->add('gallery', 'You can keep up to 12 gallery images.');
            }
        });
    }
}
