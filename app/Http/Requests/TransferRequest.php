<?php

namespace App\Http\Requests;

use App\Helpers\NumberHelper;
use App\Rules\CardNumberRule;
use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'source_card_number' => ['required', new CardNumberRule()],
            'destination_card_number' => ['required', new CardNumberRule()],
            'amount' => 'required|integer|min:1000|max:50000000',
        ];
    }


    protected function prepareForValidation(): void
    {

        $this->merge([
            'source_card_number' => NumberHelper::convertToEnglishNumerals($this->input('source_card_number')),
            'destination_card_number' => NumberHelper::convertToEnglishNumerals($this->input('destination_card_number')),
            'amount' => NumberHelper::convertToEnglishNumerals($this->input('amount')),
        ]);


        $this->merge([
            'amount' => (int) $this->input('amount'),
        ]);
    }

    public function messages(): array
    {
        return [
            'amount.integer' => 'The amount field must be an integer.',
            'amount.min' => 'The amount field must be at least 1000.',
            'amount.max' => 'The amount field must not exceed 50,000,000.',
        ];
    }
}
