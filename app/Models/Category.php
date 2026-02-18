<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'color',
        'icon',
        'monthly_budget',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'monthly_budget' => 'decimal:2',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Boot del modelo - Agregar Global Scope para multi-tenancy
     */
    protected static function booted(): void
    {
        static::addGlobalScope('user', function ($query) {
            if (auth()->check()) {
                $query->where('user_id', auth()->id());
            }
        });

        static::creating(function ($model) {
            if (auth()->check() && !$model->user_id) {
                $model->user_id = auth()->id();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subcategories(): HasMany
    {
        return $this->hasMany(Subcategory::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Obtiene el total de gastos del mes actual para esta categoría
     */
    public function getCurrentMonthExpensesTotal(): float
    {
        return $this->expenses()
            ->whereYear('expense_date', now()->year)
            ->whereMonth('expense_date', now()->month)
            ->sum('amount_converted');
    }

    /**
     * Obtiene el porcentaje de presupuesto utilizado
     */
    public function getBudgetUsagePercentage(): float
    {
        if (!$this->monthly_budget || $this->monthly_budget == 0) {
            return 0;
        }

        $total = $this->getCurrentMonthExpensesTotal();
        return ($total / $this->monthly_budget) * 100;
    }

    /**
     * Verifica si se ha excedido el presupuesto
     */
    public function isBudgetExceeded(): bool
    {
        if (!$this->monthly_budget) {
            return false;
        }

        return $this->getCurrentMonthExpensesTotal() > $this->monthly_budget;
    }
}
