<?php

namespace App\Rules;

use App\Helpers\NumberHelper;
use App\Models\Card;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CardNumberRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */

    private $prefixes = [
        '603799', '589210', '627353', '627760', '502229',
        '627488', '621986', '639346', '639607', '636214',
        '502806', '504706', '505416', '603769', '628023',
        '627961',
    ];
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = NumberHelper::convertToEnglishNumerals($value);

        if (!preg_match('/^\d{16}$/', $value)) {
            $fail('The :attribute must be a 16-digit number.');
            return;
        }

        if (!$this->isValidIranianCardNumber($value)) {
            $fail('The :attribute is not a valid Iranian card number.');
            return;
        }

    }

    private function isValidIranianCardNumber(string $cardNumber): bool
    {
        foreach ($this->prefixes as $prefix) {
            if (preg_match('/^' . $prefix . '\d{10}$/', $cardNumber)) {
                return true;
            }
        }
        return false;
    }
}
