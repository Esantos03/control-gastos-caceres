<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExpenseResource;
use App\Models\Card;
use App\Models\Category;
use App\Models\Expense;
use App\Services\BudgetService;
use App\Services\ExpenseService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private ExpenseService $expenseService,
        private BudgetService $budgetService
    ) {}

    /**
     * Dashboard principal
     */
    public function index(Request $request): JsonResponse
    {
        $month = $request->has('month') && $request->has('year')
            ? Carbon::create($request->year, $request->month)
            : now();

        $stats = $this->expenseService->getStatistics($month);
        $budgetSummary = $this->budgetService->getBudgetSummary($month);

        return response()->json([
            'month' => $month->format('Y-m'),
            'statistics' => $stats,
            'budget_summary' => $budgetSummary,
        ]);
    }

    /**
     * Estadísticas del dashboard
     */
    public function stats(Request $request): JsonResponse
    {
        $currentMonth = now();
        $previousMonth = now()->subMonth();

        $currentStats = $this->expenseService->getStatistics($currentMonth);
        $previousStats = $this->expenseService->getStatistics($previousMonth);

        // Calcular cambios porcentuales
        $totalChange = $previousStats['total'] > 0
            ? (($currentStats['total'] - $previousStats['total']) / $previousStats['total']) * 100
            : 0;

        // Gastos por tarjeta
        $cardExpenses = Card::with('expenses')
            ->get()
            ->map(function ($card) use ($currentMonth) {
                $total = $card->expenses()
                    ->whereYear('expense_date', $currentMonth->year)
                    ->whereMonth('expense_date', $currentMonth->month)
                    ->sum('amount_converted');

                return [
                    'card' => $card->name,
                    'total' => $total,
                    'usage_percentage' => $card->getCreditUsagePercentage(),
                    'limit' => $card->credit_limit,
                ];
            })
            ->filter(fn($item) => $item['total'] > 0)
            ->values();

        // Categorías con mayor gasto
        $topCategories = Category::with('expenses')
            ->get()
            ->map(function ($category) use ($currentMonth) {
                $total = $category->expenses()
                    ->whereYear('expense_date', $currentMonth->year)
                    ->whereMonth('expense_date', $currentMonth->month)
                    ->sum('amount_converted');

                return [
                    'category' => $category->name,
                    'total' => $total,
                    'budget' => $category->monthly_budget,
                    'percentage' => $category->getBudgetUsagePercentage(),
                ];
            })
            ->filter(fn($item) => $item['total'] > 0)
            ->sortByDesc('total')
            ->take(5)
            ->values();

        return response()->json([
            'current_month' => [
                'total' => $currentStats['total'],
                'count' => $currentStats['count'],
                'by_type' => $currentStats['by_type'],
            ],
            'previous_month' => [
                'total' => $previousStats['total'],
                'count' => $previousStats['count'],
            ],
            'comparison' => [
                'total_change' => round($totalChange, 2),
                'count_change' => $currentStats['count'] - $previousStats['count'],
            ],
            'card_expenses' => $cardExpenses,
            'top_categories' => $topCategories,
        ]);
    }

    /**
     * Gastos recientes
     */
    public function recentExpenses(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 10);

        $expenses = Expense::with(['category', 'subcategory', 'card', 'currency', 'merchant', 'paymentMethod'])
            ->orderBy('expense_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'data' => ExpenseResource::collection($expenses),
        ]);
    }
}
