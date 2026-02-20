<?php

namespace App\Services;

use App\Models\ExchangeRate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class ExchangeRateService
{
    /**
     * Obtiene la tasa de cambio para una moneda y fecha específica
     */
    public function getRate(int $currencyId, Carbon $date, string $type = 'sell'): ?float
    {
        $cacheKey = "exchange_rate_{$currencyId}_{$date->year}_{$date->month}_{$type}";
        
        return Cache::remember($cacheKey, config('expenses.cache.exchange_rate_ttl'), function () use ($currencyId, $date, $type) {
            $rate = ExchangeRate::where('currency_id', $currencyId)
                ->where('year', $date->year)
                ->where('month', $date->month)
                ->first();

            if (!$rate) {
                // Si no existe tasa para ese mes, buscar la más reciente
                $rate = $this->getLatestRate($currencyId);
            }

            if (!$rate) {
                return null;
            }

            return match($type) {
                'buy' => $rate->buy_rate,
                'sell' => $rate->sell_rate,
                'average' => $rate->average_rate,
                default => $rate->sell_rate,
            };
        });
    }

    /**
     * Obtiene la tasa de cambio más reciente para una moneda
     */
    public function getLatestRate(int $currencyId): ?ExchangeRate
    {
        return ExchangeRate::where('currency_id', $currencyId)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->first();
    }

    /**
     * Convierte un monto de una moneda a otra
     */
    public function convert(float $amount, int $fromCurrencyId, Carbon $date, string $rateType = 'sell'): float
    {
        // Si es DOP (moneda base), no convertir
        if ($this->isBaseCurrency($fromCurrencyId)) {
            return $amount;
        }

        $rate = $this->getRate($fromCurrencyId, $date, $rateType);
        
        if (!$rate) {
            throw new \Exception("No se encontró tasa de cambio para la moneda ID {$fromCurrencyId}");
        }

        return round($amount * $rate, 2);
    }

    /**
     * Verifica si una moneda es la moneda base
     */
    private function isBaseCurrency(int $currencyId): bool
    {
        return Cache::remember("currency_is_base_{$currencyId}", config('expenses.cache.currency_ttl'), function () use ($currencyId) {
            return \App\Models\Currency::where('id', $currencyId)
                ->where('code', config('expenses.currency.base'))
                ->exists();
        });
    }

    /**
     * Limpia el cache de tasas de cambio
     */
    public function clearCache(): void
    {
        Cache::flush();
    }
}
