<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'expense_date' => $this->expense_date?->format('Y-m-d'),
            'description' => $this->description,
            'amount' => (float) $this->amount,
            'currency' => new CurrencyResource($this->whenLoaded('currency')),
            'exchange_rate' => (float) $this->exchange_rate,
            'amount_converted' => (float) $this->amount_converted,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'subcategory' => new SubcategoryResource($this->whenLoaded('subcategory')),
            'payment_method' => new PaymentMethodResource($this->whenLoaded('paymentMethod')),
            'card' => new CardResource($this->whenLoaded('card')),
            'check_number' => $this->check_number,
            'merchant' => new MerchantResource($this->whenLoaded('merchant')),
            'installments' => $this->installments,
            'current_installment' => $this->current_installment,
            'parent_expense_id' => $this->parent_expense_id,
            'has_installments' => $this->hasInstallments(),
            'is_installment' => $this->isInstallment(),
            'pending_installments' => $this->pending_installments,
            'installment_progress' => round($this->installment_progress, 2),
            'expense_type' => $this->expense_type,
            'notes' => $this->notes,
            'is_paid' => $this->is_paid,
            'child_expenses' => ExpenseResource::collection($this->whenLoaded('childExpenses')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
