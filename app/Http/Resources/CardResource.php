<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'last_digits' => $this->last_digits,
            'type' => $this->type,
            'expiration_date' => $this->expiration_date?->format('Y-m-d'),
            'credit_limit' => (float) $this->credit_limit,
            'billing_day' => $this->billing_day,
            'payment_day' => $this->payment_day,
            'is_active' => $this->is_active,
            'notes' => $this->notes,
            'is_expired' => $this->isExpired(),
            'current_month_total' => $this->when(
                $request->routeIs('api.v1.cards.*'),
                fn() => $this->getCurrentMonthExpensesTotal()
            ),
            'credit_usage_percentage' => $this->when(
                $request->routeIs('api.v1.cards.*'),
                fn() => round($this->getCreditUsagePercentage(), 2)
            ),
            'is_credit_limit_exceeded' => $this->when(
                $request->routeIs('api.v1.cards.*'),
                fn() => $this->isCreditLimitExceeded()
            ),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
