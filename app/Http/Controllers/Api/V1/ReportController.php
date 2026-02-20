<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Category;
use App\Models\Expense;
use App\Services\BudgetService;
use App\Services\ExpenseService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        private ExpenseService $expenseService,
        private BudgetService $budgetService
    ) {}

    /**
     * Reporte mensual
     */
    public function monthly(Request $request): JsonResponse
    {
        $month = $request->has('month') && $request->has('year')
            ? Carbon::create($request->year, $request->month)
            : now();

        $stats = $this->expenseService->getStatistics($month);
        $budgetSummary = $this->budgetService->getBudgetSummary($month);

        return response()->json([
            'period' => $month->format('Y-m'),
            'month_name' => $month->locale('es')->monthName,
            'year' => $month->year,
            'statistics' => $stats,
            'budget_summary' => $budgetSummary,
        ]);
    }

    /**
     * Reporte por categoría
     */
    public function byCategory(Request $request): JsonResponse
    {
        $month = $request->has('month') && $request->has('year')
            ? Carbon::create($request->year, $request->month)
            : now();

        $startDate = $month->copy()->startOfMonth();
        $endDate = $month->copy()->endOfMonth();

        $categories = Category::with(['expenses' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('expense_date', [$startDate, $endDate]);
        }])->get()->map(function ($category) {
            $expenses = $category->expenses;
            
            return [
                'category' => $category->name,
                'color' => $category->color,
                'budget' => $category->monthly_budget,
                'total' => $expenses->sum('amount_converted'),
                'count' => $expenses->count(),
                'average' => $expenses->avg('amount_converted'),
                'percentage_of_budget' => $category->getBudgetUsagePercentage(),
                'by_type' => $expenses->groupBy('expense_type')->map(fn($items) => [
                    'count' => $items->count(),
                    'total' => $items->sum('amount_converted'),
                ]),
            ];
        })->filter(fn($item) => $item['count'] > 0)->values();

        return response()->json([
            'period' => $month->format('Y-m'),
            'categories' => $categories,
            'total' => $categories->sum('total'),
        ]);
    }

    /**
     * Reporte por tarjeta
     */
    public function byCard(Request $request): JsonResponse
    {
        $month = $request->has('month') && $request->has('year')
            ? Carbon::create($request->year, $request->month)
            : now();

        $startDate = $month->copy()->startOfMonth();
        $endDate = $month->copy()->endOfMonth();

        $cards = Card::with(['expenses' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('expense_date', [$startDate, $endDate]);
        }])->get()->map(function ($card) {
            $expenses = $card->expenses;
            
            return [
                'card' => $card->name,
                'card_display' => $card->display_name,
                'last_digits' => $card->last_digits,
                'type' => $card->type,
                'credit_limit' => $card->credit_limit,
                'total' => $expenses->sum('amount_converted'),
                'count' => $expenses->count(),
                'average' => $expenses->avg('amount_converted'),
                'usage_percentage' => $card->getCreditUsagePercentage(),
                'by_category' => $expenses->groupBy('category.name')->map(fn($items) => [
                    'count' => $items->count(),
                    'total' => $items->sum('amount_converted'),
                ]),
            ];
        })->filter(fn($item) => $item['count'] > 0)->values();

        return response()->json([
            'period' => $month->format('Y-m'),
            'cards' => $cards,
            'total' => $cards->sum('total'),
        ]);
    }

    /**
     * Reporte por tipo de gasto
     */
    public function byType(Request $request): JsonResponse
    {
        $month = $request->has('month') && $request->has('year')
            ? Carbon::create($request->year, $request->month)
            : now();

        $startDate = $month->copy()->startOfMonth();
        $endDate = $month->copy()->endOfMonth();

        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])->get();

        $byType = $expenses->groupBy('expense_type')->map(function ($items, $type) {
            return [
                'type' => $type,
                'count' => $items->count(),
                'total' => $items->sum('amount_converted'),
                'average' => $items->avg('amount_converted'),
                'percentage' => 0, // Se calculará después
            ];
        });

        $total = $byType->sum('total');
        
        $byType = $byType->map(function ($item) use ($total) {
            $item['percentage'] = $total > 0 ? round(($item['total'] / $total) * 100, 2) : 0;
            return $item;
        });

        return response()->json([
            'period' => $month->format('Y-m'),
            'by_type' => $byType->values(),
            'total' => $total,
        ]);
    }

    /**
     * Resumen de presupuestos
     */
    public function budgetSummary(Request $request): JsonResponse
    {
        $month = $request->has('month') && $request->has('year')
            ? Carbon::create($request->year, $request->month)
            : now();

        $summary = $this->budgetService->getBudgetSummary($month);

        $totalBudget = collect($summary)->sum('budget');
        $totalSpent = collect($summary)->sum('spent');
        $totalRemaining = collect($summary)->sum('remaining');

        return response()->json([
            'period' => $month->format('Y-m'),
            'summary' => [
                'total_budget' => $totalBudget,
                'total_spent' => $totalSpent,
                'total_remaining' => $totalRemaining,
                'usage_percentage' => $totalBudget > 0 ? round(($totalSpent / $totalBudget) * 100, 2) : 0,
            ],
            'categories' => $summary,
            'alerts' => collect($summary)->filter(fn($item) => $item['status'] !== 'ok')->values(),
        ]);
    }

    /**
     * Tendencias (últimos meses configurables)
     */
    public function trends(Request $request): JsonResponse
    {
        $months = $request->get('months', config('expenses.reports.default_trend_months'));
        $trends = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $stats = $this->expenseService->getStatistics($month);

            $trends[] = [
                'period' => $month->format('Y-m'),
                'month_name' => $month->locale('es')->monthName,
                'year' => $month->year,
                'total' => $stats['total'],
                'count' => $stats['count'],
                'by_type' => $stats['by_type'],
            ];
        }

        return response()->json([
            'trends' => $trends,
        ]);
    }

    /**
     * Exportar reporte (placeholder para futura implementación)
     */
    public function export(Request $request): JsonResponse
    {
        // TODO: Implementar exportación a Excel/PDF
        return response()->json([
            'message' => 'Funcionalidad de exportación en desarrollo',
        ], 501);
    }
}
