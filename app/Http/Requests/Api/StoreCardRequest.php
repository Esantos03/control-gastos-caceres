<?php

namespace App\Http\Requests\Api;

use App\Rules\ValidBillingCycle;
use Illuminate\Foundation\Http\FormRequest;

class StoreCardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $config = config('expenses.validation');
        
        return [
            'name' => ['required', 'string', 'max:' . $config['description_length']],
            'last_digits' => ['nullable', 'string', 'max:' . $config['card_last_digits']],
            'type' => ['required', 'in:credito,debito'],
            'expiration_date' => ['nullable', 'date', 'after:today'],
            'credit_limit' => ['nullable', 'numeric', 'min:0'],
            'billing_day' => ['nullable', 'integer', 'min:' . $config['billing_day']['min'], 'max:' . $config['billing_day']['max']],
            'payment_day' => [
                'nullable',
                'integer',
                'min:' . $config['payment_day']['min'],
                'max:' . $config['payment_day']['max'],
                new ValidBillingCycle($this->billing_day)
            ],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
