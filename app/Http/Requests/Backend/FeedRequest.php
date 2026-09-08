<?php

namespace App\Http\Requests\Backend;

use App\Enums\FeedVisibility;
use App\Enums\PublishStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for both store and update of a Feed.
 */
class FeedRequest extends FormRequest
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
            'body' => ['required', 'string', 'max:5000'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'existing_images' => ['nullable', 'array'],
            'existing_images.*' => ['string'],
            'location' => ['nullable', 'string', 'max:255'],
            'mood' => ['nullable', 'string', 'max:50'],
            'activity_type' => ['nullable', 'string', 'max:100'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['nullable', 'string', 'max:50'],
            'visibility' => ['required', Rule::enum(FeedVisibility::class)],
            'status' => ['required', Rule::enum(PublishStatus::class)],
            'is_pinned' => ['boolean'],
            'likes_count' => ['nullable', 'integer', 'min:0'],
            'published_at' => ['nullable', 'date'],
        ];
    }

    /**
     * Validated attributes that map onto the model (files handled separately).
     *
     * @return array<string, mixed>
     */
    public function feedData(): array
    {
        $data = $this->safe()->except(['images', 'existing_images']);

        if (isset($data['tags'])) {
            $data['tags'] = array_values(array_filter($data['tags'], fn ($tag) => trim((string) $tag) !== ''));
        }

        return $data;
    }
}
