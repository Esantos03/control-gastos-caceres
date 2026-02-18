<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExchangeRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'currency_id' => ['sometimes', 'exists:currencies,id'],
            'month' => ['sometimes', 'integer', 'min:1', 'max:12'],
            'year' => ['sometimes', 'integer', 'min:2000', 'max:2100'],
            'buy_rate' => ['sometimes', 'numeric', 'min:0'],
            'sell_rate' => ['sometimes', 'numeric', 'min:0'],
            'average_rate' => ['sometimes', 'numeric', 'min:0'],
        ];
    }
}
