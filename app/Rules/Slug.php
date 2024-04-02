<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Slug implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        if ($this->hasUnderscores($value)) {

            $fail(__('validation.no_underscores'));
        }

        if ($this->startWithDashes($value)) {

            $fail(__('validation.no_starting_dashes'));
        }

        if ($this->endsWithDashes($value)) {

            $fail(__('validation.no_ending_dashes'));
        }
    }

    protected function startWithDashes($value): bool
    {
        return preg_match('/^-/', $value);
    }

    protected function hasUnderscores($value): bool
    {
        return preg_match('/_/', $value);
    }

    protected function endsWithDashes($value): bool
    {
        return preg_match('/-$/', $value);
    }
}
