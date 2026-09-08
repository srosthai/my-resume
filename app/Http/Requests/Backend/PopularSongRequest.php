<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for both store and update of a Popular Song.
 */
class PopularSongRequest extends FormRequest
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
            'artist' => ['required', 'string', 'max:255'],
            'url' => ['required', 'string', 'url', 'max:1000'],
            'duration' => ['required', 'integer', 'min:1', 'max:3600'],
        ];
    }
}
