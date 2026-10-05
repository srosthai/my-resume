<?php

namespace App\Http\Requests\Backend;

use App\Http\Requests\Backend\Concerns\NormalizesCareerYears;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for both store and update of a Work Experience entry.
 */
class WorkExperienceRequest extends FormRequest
{
    use NormalizesCareerYears;

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
            'position' => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            ...$this->careerYearRules(),
        ];
    }
}
