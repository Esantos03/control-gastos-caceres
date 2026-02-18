<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCurrencyRequest;
use App\Http\Requests\Api\UpdateCurrencyRequest;
use App\Http\Resources\CurrencyResource;
use App\Models\Currency;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CurrencyController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $currencies = Currency::orderBy('code')->get();
        return CurrencyResource::collection($currencies);
    }

    public function store(StoreCurrencyRequest $request): JsonResponse
    {
        $currency = Currency::create($request->validated());

        return response()->json([
            'message' => 'Moneda creada exitosamente',
            'data' => new CurrencyResource($currency),
        ], 201);
    }

    public function show(Currency $currency): JsonResponse
    {
        return response()->json([
            'data' => new CurrencyResource($currency),
        ]);
    }

    public function update(UpdateCurrencyRequest $request, Currency $currency): JsonResponse
    {
        $currency->update($request->validated());

        return response()->json([
            'message' => 'Moneda actualizada exitosamente',
            'data' => new CurrencyResource($currency),
        ]);
    }

    public function destroy(Currency $currency): JsonResponse
    {
        if ($currency->expenses()->count() > 0 || $currency->exchangeRates()->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar la moneda porque tiene registros asociados',
            ], 422);
        }

        $currency->delete();

        return response()->json([
            'message' => 'Moneda eliminada exitosamente',
        ]);
    }
}
