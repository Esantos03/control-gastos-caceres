<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Card extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'last_digits',
        'type',
        'expiration_date',
        'credit_limit',
        'billing_day',
        'payment_day',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'expiration_date' => 'date',
        'credit_limit' => 'decimal:2',
        'billing_day' => 'integer',
        'payment_day' => 'integer',
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

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Verifica si la tarjeta está vencida
     */
    public function isExpired(): bool
    {
        if (!$this->expiration_date) {
            return false;
        }

        return $this->expiration_date->isPast();
    }

    /**
     * Obtiene el total de gastos del mes actual con esta tarjeta
     */
    public function getCurrentMonthExpensesTotal(): float
    {
        return $this->expenses()
            ->whereYear('expense_date', now()->year)
            ->whereMonth('expense_date', now()->month)
            ->sum('amount_converted');
    }

    /**
     * Obtiene el porcentaje de crédito utilizado
     */
    public function getCreditUsagePercentage(): float
    {
        if (!$this->credit_limit || $this->credit_limit == 0 || $this->type !== 'credito') {
            return 0;
        }

        $total = $this->getCurrentMonthExpensesTotal();
        return ($total / $this->credit_limit) * 100;
    }

    /**
     * Verifica si se ha excedido el límite de crédito
     */
    public function isCreditLimitExceeded(): bool
    {
        if (!$this->credit_limit || $this->type !== 'credito') {
            return false;
        }

        return $this->getCurrentMonthExpensesTotal() > $this->credit_limit;
    }
}
