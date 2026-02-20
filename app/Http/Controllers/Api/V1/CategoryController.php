<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCategoryRequest;
use App\Http\Requests\Api\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ExpenseResource;
use App\Models\Category;
use App\Services\BudgetService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    public function __construct(
        private BudgetService $budgetService
    ) {}

    /**
     * Listar categorías
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Category::with('subcategories');

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $query->orderBy('sort_order')->orderBy('name');

        return CategoryResource::collection($query->get());
    }

    /**
     * Crear nueva categoría
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = Category::create($request->validated());

        return response()->json([
            'message' => 'Categoría creada exitosamente',
            'data' => new CategoryResource($category),
        ], 201);
    }

    /**
     * Mostrar una categoría específica
     */
    public function show(Category $category): JsonResponse
    {
        return response()->json([
            'data' => new CategoryResource($category->load('subcategories')),
        ]);
    }

    /**
     * Actualizar una categoría
     */
    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $category->update($request->validated());

        return response()->json([
            'message' => 'Categoría actualizada exitosamente',
            'data' => new CategoryResource($category),
        ]);
    }

    /**
     * Eliminar una categoría
     */
    public function destroy(Category $category): JsonResponse
    {
        // Verificar si tiene gastos asociados
        if ($category->expenses()->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar la categoría porque tiene gastos asociados',
            ], 422);
        }

        $category->delete();

        return response()->json([
            'message' => 'Categoría eliminada exitosamente',
        ]);
    }

    /**
     * Obtener gastos de una categoría
     */
    public function expenses(Request $request, Category $category): AnonymousResourceCollection
    {
        $query = $category->expenses()->with(['subcategory', 'card', 'currency', 'merchant', 'paymentMethod']);

        if ($request->has('month') && $request->has('year')) {
            $query->whereMonth('expense_date', $request->month)
                  ->whereYear('expense_date', $request->year);
        }

        $query->orderBy('expense_date', 'desc');

        $perPage = $request->get('per_page', config('expenses.pagination.default_per_page'));
        $expenses = $query->paginate($perPage);

        return ExpenseResource::collection($expenses);
    }

    /**
     * Obtener estado del presupuesto de una categoría
     */
    public function budgetStatus(Request $request, Category $category): JsonResponse
    {
        $month = $request->has('month') && $request->has('year')
            ? Carbon::create($request->year, $request->month)
            : now();

        $spent = $this->budgetService->getSpentAmount($category->id, $month);
        $percentage = $this->budgetService->getBudgetUsagePercentage($category->id, $month);
        $remaining = $this->budgetService->getRemainingBudget($category->id, $month);
        $exceeded = $this->budgetService->isBudgetExceeded($category->id, $month);

        $thresholds = config('expenses.budget_alerts');
        $status = $exceeded ? 'exceeded' : 
                  ($percentage >= $thresholds['warning'] ? 'warning' : 
                  ($percentage >= $thresholds['caution'] ? 'caution' : 'ok'));

        return response()->json([
            'category' => $category->name,
            'budget' => $category->monthly_budget,
            'spent' => $spent,
            'remaining' => $remaining,
            'percentage' => $percentage,
            'exceeded' => $exceeded,
            'status' => $status,
            'month' => $month->format('Y-m'),
        ]);
    }

    /**
     * Obtener estadísticas de una categoría
     */
    public function statistics(Request $request, Category $category): JsonResponse
    {
        $month = $request->has('month') && $request->has('year')
            ? Carbon::create($request->year, $request->month)
            : now();

        $startDate = $month->copy()->startOfMonth();
        $endDate = $month->copy()->endOfMonth();

        $expenses = $category->expenses()
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->get();

        return response()->json([
            'category' => $category->name,
            'month' => $month->format('Y-m'),
            'total_expenses' => $expenses->count(),
            'total_amount' => $expenses->sum('amount_converted'),
            'average_amount' => $expenses->avg('amount_converted'),
            'by_type' => $expenses->groupBy('expense_type')->map(fn($items) => [
                'count' => $items->count(),
                'total' => $items->sum('amount_converted'),
            ]),
            'by_subcategory' => $expenses->groupBy('subcategory.name')->map(fn($items) => [
                'count' => $items->count(),
                'total' => $items->sum('amount_converted'),
            ]),
        ]);
    }
}
