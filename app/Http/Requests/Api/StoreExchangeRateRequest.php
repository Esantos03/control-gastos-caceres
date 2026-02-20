<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreExchangeRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $config = config('expenses.validation');
        
        return [
            'currency_id' => [
                'required',
                'exists:currencies,id',
                Rule::unique('exchange_rates')->where(function ($query) {
                    return $query->where('month', $this->month)
                                 ->where('year', $this->year);
                })
            ],
            'month' => ['required', 'integer', 'min:' . $config['exchange_rate']['month']['min'], 'max:' . $config['exchange_rate']['month']['max']],
            'year' => ['required', 'integer', 'min:' . $config['exchange_rate']['year']['min'], 'max:' . $config['exchange_rate']['year']['max']],
            'buy_rate' => ['required', 'numeric', 'min:0'],
            'sell_rate' => ['required', 'numeric', 'min:0'],
            'average_rate' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'currency_id.unique' => 'Ya existe una tasa de cambio para esta moneda en el mes y año especificados',
        ];
    }
}
