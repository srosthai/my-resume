<?php

namespace App\Http\Requests\Backend;

use Closure;
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
            'url' => [
                'required',
                'string',
                'url',
                'max:1000',
                function (string $attribute, mixed $value, Closure $fail): void {
                    // Same id shape as extractYouTubeId() in the music player.
                    $isYouTube = is_string($value) && preg_match(
                        '/(?:youtube(?:-nocookie)?\.com\/(?:watch\?(?:[^#]*&)?v=|embed\/|shorts\/|live\/|v\/)|youtu\.be\/)([A-Za-z0-9_-]{11})(?![A-Za-z0-9_-])/',
                        $value,
                    );

                    if (! $isYouTube) {
                        $fail('Enter a YouTube link (watch, youtu.be, embed, or shorts).');
                    }
                },
            ],
            'duration' => ['required', 'integer', 'min:1', 'max:3600'],
        ];
    }
}
