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
        $config = config('expenses.validation');
        
        return [
            'currency_id' => ['sometimes', 'exists:currencies,id'],
            'month' => ['sometimes', 'integer', 'min:' . $config['exchange_rate']['month']['min'], 'max:' . $config['exchange_rate']['month']['max']],
            'year' => ['sometimes', 'integer', 'min:' . $config['exchange_rate']['year']['min'], 'max:' . $config['exchange_rate']['year']['max']],
            'buy_rate' => ['sometimes', 'numeric', 'min:0'],
            'sell_rate' => ['sometimes', 'numeric', 'min:0'],
            'average_rate' => ['sometimes', 'numeric', 'min:0'],
        ];
    }
}
