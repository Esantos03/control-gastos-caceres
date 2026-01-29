<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExchangeRate extends Model
{
    protected $fillable = ['currency_id', 'month', 'year', 'buy_rate', 'sell_rate', 'average_rate'];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'buy_rate' => 'decimal:4',
        'sell_rate' => 'decimal:4',
        'average_rate' => 'decimal:4',
    ];

    // Accessor para obtener el nombre del mes
    public function getMonthNameAttribute(): string
    {
        $months = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        return $months[$this->month] ?? '';
    }

    // Accessor para obtener el periodo completo
    public function getPeriodAttribute(): string
    {
        return $this->month_name . ' ' . $this->year;
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
