<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ContactMessageRequest extends FormRequest
{
    /**
     * Words that mark a submission as spam. Kept here so the list is testable
     * and lives next to the rules it complements.
     *
     * @var list<string>
     */
    public const SPAM_WORDS = ['viagra', 'casino', 'lottery', 'winner', 'congratulations', 'click here', 'free money'];

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'subject' => ['required', 'string', 'min:5', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            // Honeypot: real browsers leave this hidden field empty.
            'website' => ['nullable', 'string', 'max:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'website.max' => 'Message appears to be spam and was blocked.',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $haystack = strtolower($this->string('message').' '.$this->string('subject'));

                foreach (self::SPAM_WORDS as $word) {
                    if (str_contains($haystack, $word)) {
                        $validator->errors()->add('message', 'Message appears to be spam and was blocked.');

                        return;
                    }
                }
            },
        ];
    }
}
