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
            // A blank entry is how the edit form keeps this key in multipart
            // bodies. Empty strings become null before validation, so the
            // item rule has to allow null or every project with no gallery
            // fails to save.
            'existing_gallery.*' => ['nullable', 'string', 'max:255'],
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
     * Plain sentences a person can act on. The form shows each one under
     * its field and again in a toast.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Title is required.',
            'title.max' => 'Title must be 255 characters or fewer.',
            'status.required' => 'Status is required.',
            'status.enum' => 'Choose Processing or Completed.',
            'image.image' => 'The project image must be a picture.',
            'image.mimes' => 'Use a JPEG, PNG, GIF, or WebP image.',
            'image.max' => 'The project image must be 2 MB or smaller.',
            'image.uploaded' => 'The project image did not upload. Try a smaller file.',
            'gallery.max' => 'You can add up to 12 gallery images.',
            'gallery.*.image' => 'Each gallery file must be a picture.',
            'gallery.*.mimes' => 'Gallery images must be JPEG, PNG, GIF, or WebP.',
            'gallery.*.max' => 'Each gallery image must be 5 MB or smaller.',
            'gallery.*.uploaded' => 'A gallery image did not upload. Try a smaller file.',
            'existing_gallery.max' => 'You can keep up to 12 gallery images.',
            'existing_gallery.*.string' => 'One of the saved gallery images could not be kept. Remove it and try again.',
            'existing_gallery.*.max' => 'One of the saved gallery images could not be kept. Remove it and try again.',
            'project_type_id.integer' => 'Choose a project type from the list.',
            'project_type_id.exists' => 'Choose a project type from the list.',
            'technologies.*.max' => 'Each technology must be 100 characters or fewer.',
            'created_date.date' => 'Enter a real created date.',
            'links.*.array' => 'Each link needs a label and a URL.',
            'links.*.*.max' => 'Each link must be 2048 characters or fewer.',
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
