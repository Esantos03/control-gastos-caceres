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
        return [
            'currency_id' => [
                'required',
                'exists:currencies,id',
                Rule::unique('exchange_rates')->where(function ($query) {
                    return $query->where('month', $this->month)
                                 ->where('year', $this->year);
                })
            ],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
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
