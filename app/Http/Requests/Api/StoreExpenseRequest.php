<?php

namespace App\Http\Requests\Api;

use App\Rules\BelongsToCategory;
use App\Rules\ValidInstallmentNumber;
use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'expense_date' => ['required', 'date', 'before_or_equal:today'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency_id' => ['required', 'exists:currencies,id'],
            'exchange_rate' => ['nullable', 'numeric', 'min:0'],
            'amount_converted' => ['nullable', 'numeric', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'subcategory_id' => [
                'required',
                'exists:subcategories,id',
                new BelongsToCategory($this->category_id)
            ],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'card_id' => [
                'nullable',
                'exists:cards,id',
                new \App\Rules\RequiresCardPaymentMethod($this->payment_method_id)
            ],
            'check_number' => ['nullable', 'string', 'max:50'],
            'merchant_id' => ['nullable', 'exists:merchants,id'],
            'installments' => ['nullable', 'integer', 'min:1', 'max:60'],
            'current_installment' => [
                'nullable',
                'integer',
                'min:1',
                'lte:installments',
                new ValidInstallmentNumber($this->installments)
            ],
            'parent_expense_id' => ['nullable', 'exists:expenses,id'],
            'expense_type' => ['required', 'in:fixed,variable,occasional'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'is_paid' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'expense_date.required' => 'La fecha del gasto es requerida',
            'expense_date.before_or_equal' => 'La fecha del gasto no puede ser futura',
            'description.required' => 'La descripción es requerida',
            'amount.required' => 'El monto es requerido',
            'amount.min' => 'El monto debe ser mayor a 0',
            'currency_id.required' => 'La moneda es requerida',
            'currency_id.exists' => 'La moneda seleccionada no existe',
            'category_id.required' => 'La categoría es requerida',
            'category_id.exists' => 'La categoría seleccionada no existe',
            'subcategory_id.required' => 'La subcategoría es requerida',
            'payment_method_id.required' => 'El método de pago es requerido',
            'expense_type.required' => 'El tipo de gasto es requerido',
            'expense_type.in' => 'El tipo de gasto debe ser: fixed, variable u occasional',
            'current_installment.lte' => 'La cuota actual no puede ser mayor al número total de cuotas',
        ];
    }
}
