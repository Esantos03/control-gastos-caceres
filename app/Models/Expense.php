<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Expense extends Model
{
    protected $fillable = [
        'expense_date',
        'description',
        'amount',
        'currency_id',
        'exchange_rate',
        'amount_converted',
        'category_id',
        'subcategory_id',
        'payment_method_id',
        'card_id',
        'merchant_id',
        'installments',
        'current_installment',
        'parent_expense_id',
        'expense_type',
        'notes',
        'is_paid',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'amount_converted' => 'decimal:2',
        'installments' => 'integer',
        'current_installment' => 'integer',
        'is_paid' => 'boolean',
    ];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function parentExpense(): BelongsTo
    {
        return $this->belongsTo(Expense::class, 'parent_expense_id');
    }

    public function childExpenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'parent_expense_id');
    }

    /**
     * Verifica si este gasto tiene cuotas
     */
    public function hasInstallments(): bool
    {
        return $this->installments > 1;
    }

    /**
     * Verifica si este gasto es una cuota de otro gasto
     */
    public function isInstallment(): bool
    {
        return $this->parent_expense_id !== null;
    }

    /**
     * Obtiene el número de cuotas pendientes
     */
    public function getPendingInstallmentsAttribute(): int
    {
        if (!$this->hasInstallments()) {
            return 0;
        }

        return $this->installments - $this->current_installment;
    }

    /**
     * Obtiene el porcentaje de cuotas pagadas
     */
    public function getInstallmentProgressAttribute(): float
    {
        if (!$this->hasInstallments()) {
            return 100;
        }

        return ($this->current_installment / $this->installments) * 100;
    }
}
