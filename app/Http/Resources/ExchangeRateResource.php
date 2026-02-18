<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExchangeRateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'currency' => new CurrencyResource($this->whenLoaded('currency')),
            'month' => $this->month,
            'year' => $this->year,
            'month_name' => $this->month_name,
            'period' => $this->period,
            'buy_rate' => (float) $this->buy_rate,
            'sell_rate' => (float) $this->sell_rate,
            'average_rate' => (float) $this->average_rate,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
