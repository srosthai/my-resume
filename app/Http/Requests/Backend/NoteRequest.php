<?php

namespace App\Http\Requests\Backend;

use App\Enums\PublishStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for both store and update of a Note.
 */
class NoteRequest extends FormRequest
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
            'category' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:1000'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['nullable', 'string', 'max:50'],
            'content.overview' => ['required', 'string'],
            'content.requirements' => ['required', 'array', 'min:1'],
            'content.requirements.*' => ['string', 'max:255'],
            'content.steps' => ['required', 'array', 'min:1'],
            'content.steps.*.title' => ['required', 'string', 'max:255'],
            'content.steps.*.description' => ['required', 'string', 'max:500'],
            'content.steps.*.commands' => ['required', 'array', 'min:1'],
            'content.steps.*.commands.*' => ['string', 'max:500'],
            'status' => ['required', Rule::enum(PublishStatus::class)],
            'is_featured' => ['boolean'],
            'published_at' => ['nullable', 'date'],
        ];
    }

    /**
     * Validated data with empty tags removed.
     *
     * @return array<string, mixed>
     */
    public function noteData(): array
    {
        $data = $this->validated();

        if (isset($data['tags'])) {
            $data['tags'] = array_values(array_filter($data['tags'], fn ($tag) => trim((string) $tag) !== ''));
        }

        return $data;
    }
}
