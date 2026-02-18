<?php

namespace App\Http\Requests\Api;

use App\Rules\ValidBillingCycle;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'last_digits' => ['nullable', 'string', 'max:4'],
            'type' => ['sometimes', 'in:credito,debito'],
            'expiration_date' => ['nullable', 'date', 'after:today'],
            'credit_limit' => ['nullable', 'numeric', 'min:0'],
            'billing_day' => ['nullable', 'integer', 'min:1', 'max:31'],
            'payment_day' => [
                'nullable',
                'integer',
                'min:1',
                'max:31',
                new ValidBillingCycle($this->billing_day ?? $this->route('card')->billing_day)
            ],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
