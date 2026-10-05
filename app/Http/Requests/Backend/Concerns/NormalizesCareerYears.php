<?php

namespace App\Http\Requests\Backend\Concerns;

use Closure;

/**
 * Career periods are years. An empty end, or the word Present, means current.
 */
trait NormalizesCareerYears
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'from' => $this->careerYear($this->input('from')),
            'to' => $this->careerYear($this->input('to')),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function careerYearRules(): array
    {
        return [
            'from' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'to' => [
                'nullable',
                'integer',
                'min:1900',
                'max:2100',
                function (string $attribute, mixed $value, Closure $fail): void {
                    $from = $this->input('from');

                    if ($value !== null && $from !== null && (int) $value < (int) $from) {
                        $fail('The end year must be the same as or after the start year.');
                    }
                },
            ],
        ];
    }

    private function careerYear(mixed $value): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }

        $text = trim((string) $value);

        if ($text === '' || strcasecmp($text, 'Present') === 0) {
            return null;
        }

        if (preg_match('/^(19|20)\d{2}$/', $text) === 1) {
            return (int) $text;
        }

        return $value;
    }
}
