<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreSubcategoryRequest;
use App\Http\Requests\Api\UpdateSubcategoryRequest;
use App\Http\Resources\SubcategoryResource;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SubcategoryController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $subcategories = Subcategory::with('category')->orderBy('name')->get();
        return SubcategoryResource::collection($subcategories);
    }

    public function store(StoreSubcategoryRequest $request): JsonResponse
    {
        $subcategory = Subcategory::create($request->validated());

        return response()->json([
            'message' => 'Subcategoría creada exitosamente',
            'data' => new SubcategoryResource($subcategory->load('category')),
        ], 201);
    }

    public function show(Subcategory $subcategory): JsonResponse
    {
        return response()->json([
            'data' => new SubcategoryResource($subcategory->load('category')),
        ]);
    }

    public function update(UpdateSubcategoryRequest $request, Subcategory $subcategory): JsonResponse
    {
        $subcategory->update($request->validated());

        return response()->json([
            'message' => 'Subcategoría actualizada exitosamente',
            'data' => new SubcategoryResource($subcategory->load('category')),
        ]);
    }

    public function destroy(Subcategory $subcategory): JsonResponse
    {
        if ($subcategory->expenses()->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar la subcategoría porque tiene gastos asociados',
            ], 422);
        }

        $subcategory->delete();

        return response()->json([
            'message' => 'Subcategoría eliminada exitosamente',
        ]);
    }

    public function byCategory(Category $category): AnonymousResourceCollection
    {
        $subcategories = $category->subcategories()->orderBy('name')->get();
        return SubcategoryResource::collection($subcategories);
    }
}
