<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StorePaymentMethodRequest;
use App\Http\Requests\Api\UpdatePaymentMethodRequest;
use App\Http\Resources\PaymentMethodResource;
use App\Models\PaymentMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PaymentMethodController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $paymentMethods = PaymentMethod::orderBy('name')->get();
        return PaymentMethodResource::collection($paymentMethods);
    }

    public function store(StorePaymentMethodRequest $request): JsonResponse
    {
        $paymentMethod = PaymentMethod::create($request->validated());

        return response()->json([
            'message' => 'Método de pago creado exitosamente',
            'data' => new PaymentMethodResource($paymentMethod),
        ], 201);
    }

    public function show(PaymentMethod $paymentMethod): JsonResponse
    {
        return response()->json([
            'data' => new PaymentMethodResource($paymentMethod),
        ]);
    }

    public function update(UpdatePaymentMethodRequest $request, PaymentMethod $paymentMethod): JsonResponse
    {
        $paymentMethod->update($request->validated());

        return response()->json([
            'message' => 'Método de pago actualizado exitosamente',
            'data' => new PaymentMethodResource($paymentMethod),
        ]);
    }

    public function destroy(PaymentMethod $paymentMethod): JsonResponse
    {
        if ($paymentMethod->expenses()->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar el método de pago porque tiene gastos asociados',
            ], 422);
        }

        $paymentMethod->delete();

        return response()->json([
            'message' => 'Método de pago eliminado exitosamente',
        ]);
    }
}
