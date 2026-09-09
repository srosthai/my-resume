<?php

namespace App\Http\Requests\Backend;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Validation for creating and updating the owner profile ("Me").
 * On update the password is optional and the email uniqueness ignores the record itself.
 */
class UserProfileRequest extends FormRequest
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
        /** @var User|null $user */
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class, 'email')->ignore($user)],
            'password' => [$user ? 'nullable' : 'required', 'confirmed', Password::defaults()],
            'dob' => ['required', 'date'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'position' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ];
    }

    /**
     * Profile attributes without the file and the (possibly empty) password.
     *
     * @return array<string, mixed>
     */
    public function profileData(): array
    {
        $data = $this->safe()->except(['image', 'password']);

        if ($this->filled('password')) {
            $data['password'] = $this->string('password')->toString();
        }

        return $data;
    }
}
