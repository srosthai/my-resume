<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for both store and update of an Education entry.
 */
class EducationRequest extends FormRequest
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
            'major' => ['nullable', 'string', 'max:255'],
            'institution' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'from' => ['nullable', 'string', 'max:255'],
            'to' => ['nullable', 'string', 'max:255'],
        ];
    }
}
