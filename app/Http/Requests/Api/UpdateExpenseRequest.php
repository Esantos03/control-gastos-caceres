<?php

namespace App\Http\Requests\Api;

use App\Rules\BelongsToCategory;
use App\Rules\ValidInstallmentNumber;
use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'expense_date' => ['sometimes', 'date'],
            'description' => ['sometimes', 'string', 'max:255'],
            'amount' => ['sometimes', 'numeric', 'min:0'],
            'currency_id' => ['sometimes', 'exists:currencies,id'],
            'exchange_rate' => ['nullable', 'numeric', 'min:0'],
            'amount_converted' => ['nullable', 'numeric', 'min:0'],
            'category_id' => ['sometimes', 'exists:categories,id'],
            'subcategory_id' => [
                'nullable',
                'exists:subcategories,id',
                new BelongsToCategory($this->category_id ?? $this->route('expense')->category_id)
            ],
            'payment_method_id' => ['sometimes', 'exists:payment_methods,id'],
            'card_id' => ['nullable', 'exists:cards,id'],
            'check_number' => ['nullable', 'string', 'max:50'],
            'merchant_id' => ['nullable', 'exists:merchants,id'],
            'installments' => ['sometimes', 'integer', 'min:1', 'max:60'],
            'current_installment' => [
                'nullable',
                'integer',
                'min:1',
                new ValidInstallmentNumber($this->installments ?? $this->route('expense')->installments)
            ],
            'expense_type' => ['sometimes', 'in:fijo,variable,ocasional'],
            'notes' => ['nullable', 'string'],
            'is_paid' => ['boolean'],
        ];
    }
}
