<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BudgetService
{
    /**
     * Verifica si una categoría ha excedido su presupuesto
     */
    public function isBudgetExceeded(int $categoryId, ?Carbon $month = null): bool
    {
        $category = Category::find($categoryId);
        
        if (!$category || !$category->monthly_budget) {
            return false;
        }

        $month = $month ?? now();
        $spent = $this->getSpentAmount($categoryId, $month);

        return $spent > $category->monthly_budget;
    }

    /**
     * Obtiene el monto gastado en una categoría para un mes
     */
    public function getSpentAmount(int $categoryId, Carbon $month): float
    {
        $cacheKey = "budget_spent_{$categoryId}_{$month->year}_{$month->month}";

        return Cache::remember($cacheKey, 300, function () use ($categoryId, $month) {
            return Expense::where('category_id', $categoryId)
                ->whereYear('expense_date', $month->year)
                ->whereMonth('expense_date', $month->month)
                ->sum('amount_converted');
        });
    }

    /**
     * Obtiene el porcentaje de presupuesto utilizado
     */
    public function getBudgetUsagePercentage(int $categoryId, ?Carbon $month = null): float
    {
        $category = Category::find($categoryId);
        
        if (!$category || !$category->monthly_budget || $category->monthly_budget == 0) {
            return 0;
        }

        $month = $month ?? now();
        $spent = $this->getSpentAmount($categoryId, $month);

        return round(($spent / $category->monthly_budget) * 100, 2);
    }

    /**
     * Obtiene el monto restante del presupuesto
     */
    public function getRemainingBudget(int $categoryId, ?Carbon $month = null): float
    {
        $category = Category::find($categoryId);
        
        if (!$category || !$category->monthly_budget) {
            return 0;
        }

        $month = $month ?? now();
        $spent = $this->getSpentAmount($categoryId, $month);

        return max(0, $category->monthly_budget - $spent);
    }

    /**
     * Verifica el presupuesto y envía alerta si es necesario
     */
    public function checkBudgetAlert(int $categoryId): void
    {
        try {
            $category = Category::find($categoryId);
            
            if (!$category || !$category->monthly_budget) {
                return;
            }

            $percentage = $this->getBudgetUsagePercentage($categoryId);

            // Alertas en 80%, 90% y 100%
            if ($percentage >= 100) {
                $this->sendBudgetAlert($category, 'exceeded', $percentage);
            } elseif ($percentage >= 90) {
                $this->sendBudgetAlert($category, 'warning', $percentage);
            } elseif ($percentage >= 80) {
                $this->sendBudgetAlert($category, 'caution', $percentage);
            }
        } catch (\Exception $e) {
            Log::error('Error checking budget alert', [
                'category_id' => $categoryId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Envía una alerta de presupuesto (placeholder para futuras notificaciones)
     */
    private function sendBudgetAlert(Category $category, string $level, float $percentage): void
    {
        Log::info('Budget alert', [
            'category' => $category->name,
            'level' => $level,
            'percentage' => $percentage,
            'budget' => $category->monthly_budget,
            'spent' => $this->getSpentAmount($category->id, now())
        ]);

        // TODO: Implementar notificaciones por email/push
        // Notification::send($user, new BudgetAlertNotification($category, $level, $percentage));
    }

    /**
     * Obtiene resumen de presupuestos para todas las categorías
     */
    public function getBudgetSummary(?Carbon $month = null): array
    {
        $month = $month ?? now();
        
        return Category::where('is_active', true)
            ->whereNotNull('monthly_budget')
            ->where('monthly_budget', '>', 0)
            ->get()
            ->map(function ($category) use ($month) {
                $spent = $this->getSpentAmount($category->id, $month);
                $percentage = $this->getBudgetUsagePercentage($category->id, $month);
                
                return [
                    'category' => $category->name,
                    'budget' => $category->monthly_budget,
                    'spent' => $spent,
                    'remaining' => max(0, $category->monthly_budget - $spent),
                    'percentage' => $percentage,
                    'status' => $this->getBudgetStatus($percentage),
                ];
            })
            ->toArray();
    }

    /**
     * Obtiene el estado del presupuesto basado en el porcentaje
     */
    private function getBudgetStatus(float $percentage): string
    {
        return match(true) {
            $percentage >= 100 => 'exceeded',
            $percentage >= 90 => 'warning',
            $percentage >= 80 => 'caution',
            default => 'ok',
        };
    }

    /**
     * Limpia el cache de presupuestos
     */
    public function clearCache(): void
    {
        Cache::flush();
    }
}
