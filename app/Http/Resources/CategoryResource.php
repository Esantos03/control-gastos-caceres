<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'color' => $this->color,
            'icon' => $this->icon,
            'monthly_budget' => (float) $this->monthly_budget,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'subcategories' => SubcategoryResource::collection($this->whenLoaded('subcategories')),
            'current_month_total' => $this->when(
                $request->routeIs('api.v1.categories.*'),
                fn() => $this->getCurrentMonthExpensesTotal()
            ),
            'budget_usage_percentage' => $this->when(
                $request->routeIs('api.v1.categories.*'),
                fn() => round($this->getBudgetUsagePercentage(), 2)
            ),
            'is_budget_exceeded' => $this->when(
                $request->routeIs('api.v1.categories.*'),
                fn() => $this->isBudgetExceeded()
            ),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
