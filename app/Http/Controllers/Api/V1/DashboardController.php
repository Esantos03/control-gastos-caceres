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

        // Obtener la tasa de cambio más reciente del dólar
        $dollarRate = \App\Models\ExchangeRate::whereHas('currency', function ($query) {
            $query->where('code', 'USD');
        })
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();

        // Gastos por tarjeta (solo USD convertidos con tasa actual)
        $cardExpenses = Card::with(['expenses' => function ($query) use ($currentMonth) {
            $query->whereYear('expense_date', $currentMonth->year)
                  ->whereMonth('expense_date', $currentMonth->month)
                  ->with('currency');
        }])
            ->get()
            ->map(function ($card) use ($dollarRate) {
                $total = $card->expenses->sum(function ($expense) use ($dollarRate) {
                    // Solo convertir gastos en USD
                    if ($expense->currency->code === 'USD' && $dollarRate) {
                        return round($expense->amount * $dollarRate->sell_rate, 2);
                    }
                    return 0;
                });

                return [
                    'card' => $card->name,
                    'card_display' => $card->display_name,
                    'last_digits' => $card->last_digits,
                    'total' => $total,
                    'usage_percentage' => $card->getCreditUsagePercentage(),
                    'limit' => $card->credit_limit,
                ];
            })
            ->filter(fn($item) => $item['total'] > 0)
            ->values();

        // Categorías con mayor gasto (solo USD convertidos con tasa actual)
        $topCategories = Category::with(['expenses' => function ($query) use ($currentMonth) {
            $query->whereYear('expense_date', $currentMonth->year)
                  ->whereMonth('expense_date', $currentMonth->month)
                  ->with('currency');
        }])
            ->get()
            ->map(function ($category) use ($currentMonth, $dollarRate) {
                $total = $category->expenses->sum(function ($expense) use ($dollarRate) {
                    // Solo convertir gastos en USD
                    if ($expense->currency->code === 'USD' && $dollarRate) {
                        return round($expense->amount * $dollarRate->sell_rate, 2);
                    }
                    return 0;
                });

                return [
                    'category' => $category->name,
                    'total' => $total,
                    'budget' => $category->monthly_budget,
                    'percentage' => $category->getBudgetUsagePercentage(),
                ];
            })
            ->filter(fn($item) => $item['total'] > 0)
            ->sortByDesc('total')
            ->take(config('expenses.pagination.dashboard_top_cards'))
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
        $limit = $request->get('limit', config('expenses.pagination.dashboard_recent_expenses'));

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
