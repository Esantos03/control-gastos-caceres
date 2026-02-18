<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreMerchantRequest;
use App\Http\Requests\Api\UpdateMerchantRequest;
use App\Http\Resources\MerchantResource;
use App\Models\Merchant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MerchantController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $merchants = Merchant::orderBy('name')->get();
        return MerchantResource::collection($merchants);
    }

    public function store(StoreMerchantRequest $request): JsonResponse
    {
        $merchant = Merchant::create($request->validated());

        return response()->json([
            'message' => 'Comercio creado exitosamente',
            'data' => new MerchantResource($merchant),
        ], 201);
    }

    public function show(Merchant $merchant): JsonResponse
    {
        return response()->json([
            'data' => new MerchantResource($merchant),
        ]);
    }

    public function update(UpdateMerchantRequest $request, Merchant $merchant): JsonResponse
    {
        $merchant->update($request->validated());

        return response()->json([
            'message' => 'Comercio actualizado exitosamente',
            'data' => new MerchantResource($merchant),
        ]);
    }

    public function destroy(Merchant $merchant): JsonResponse
    {
        if ($merchant->expenses()->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar el comercio porque tiene gastos asociados',
            ], 422);
        }

        $merchant->delete();

        return response()->json([
            'message' => 'Comercio eliminado exitosamente',
        ]);
    }
}
