<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for both store and update of an About Me entry.
 */
class AboutMeRequest extends FormRequest
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
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string'],
            'year_experience' => ['nullable', 'string', 'max:255'],
            'fucus_on' => ['nullable', 'string', 'max:255'],
        ];
    }
}
