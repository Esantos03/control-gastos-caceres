<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreExchangeRateRequest;
use App\Http\Requests\Api\UpdateExchangeRateRequest;
use App\Http\Resources\ExchangeRateResource;
use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Services\ExchangeRateService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ExchangeRateController extends Controller
{
    public function __construct(
        private ExchangeRateService $exchangeRateService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = ExchangeRate::with('currency');

        if ($request->has('currency_id')) {
            $query->where('currency_id', $request->currency_id);
        }

        if ($request->has('year')) {
            $query->where('year', $request->year);
        }

        if ($request->has('month')) {
            $query->where('month', $request->month);
        }

        $query->orderBy('year', 'desc')->orderBy('month', 'desc');

        $perPage = $request->get('per_page', config('expenses.pagination.default_per_page'));
        $exchangeRates = $query->paginate($perPage);

        return ExchangeRateResource::collection($exchangeRates);
    }

    public function store(StoreExchangeRateRequest $request): JsonResponse
    {
        $exchangeRate = ExchangeRate::create($request->validated());

        // Limpiar cache
        $this->exchangeRateService->clearCache();

        return response()->json([
            'message' => 'Tasa de cambio creada exitosamente',
            'data' => new ExchangeRateResource($exchangeRate->load('currency')),
        ], 201);
    }

    public function show(ExchangeRate $exchangeRate): JsonResponse
    {
        return response()->json([
            'data' => new ExchangeRateResource($exchangeRate->load('currency')),
        ]);
    }

    public function update(UpdateExchangeRateRequest $request, ExchangeRate $exchangeRate): JsonResponse
    {
        $exchangeRate->update($request->validated());

        // Limpiar cache
        $this->exchangeRateService->clearCache();

        return response()->json([
            'message' => 'Tasa de cambio actualizada exitosamente',
            'data' => new ExchangeRateResource($exchangeRate->load('currency')),
        ]);
    }

    public function destroy(ExchangeRate $exchangeRate): JsonResponse
    {
        $exchangeRate->delete();

        // Limpiar cache
        $this->exchangeRateService->clearCache();

        return response()->json([
            'message' => 'Tasa de cambio eliminada exitosamente',
        ]);
    }

    public function byCurrency(Currency $currency): AnonymousResourceCollection
    {
        $exchangeRates = $currency->exchangeRates()
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return ExchangeRateResource::collection($exchangeRates);
    }

    public function latest(Currency $currency): JsonResponse
    {
        $latestRate = $this->exchangeRateService->getLatestRate($currency->id);

        if (!$latestRate) {
            return response()->json([
                'message' => 'No se encontró tasa de cambio para esta moneda',
            ], 404);
        }

        return response()->json([
            'data' => new ExchangeRateResource($latestRate->load('currency')),
        ]);
    }

    public function convert(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'date' => 'nullable|date',
            'rate_type' => 'nullable|in:buy,sell,average',
        ]);

        try {
            $date = $request->date ? Carbon::parse($request->date) : now();
            $rateType = $request->rate_type ?? 'average';

            $convertedAmount = $this->exchangeRateService->convert(
                $request->amount,
                $request->currency_id,
                $date,
                $rateType
            );

            $rate = $this->exchangeRateService->getRate(
                $request->currency_id,
                $date,
                $rateType
            );

            return response()->json([
                'original_amount' => $request->amount,
                'converted_amount' => $convertedAmount,
                'exchange_rate' => $rate,
                'rate_type' => $rateType,
                'date' => $date->format('Y-m-d'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al convertir moneda',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
