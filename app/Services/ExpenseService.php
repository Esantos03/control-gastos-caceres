<?php

namespace App\Services;

use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpenseService
{
    public function __construct(
        private ExchangeRateService $exchangeRateService,
        private BudgetService $budgetService
    ) {}

    /**
     * Crea un nuevo gasto con conversión automática de moneda
     */
    public function create(array $data): Expense
    {
        try {
            return DB::transaction(function () use ($data) {
                // Auto-calcular tasa de cambio si no se proporciona
                if (empty($data['exchange_rate'])) {
                    $date = Carbon::parse($data['expense_date']);
                    $exchangeRate = $this->exchangeRateService->getRate(
                        $data['currency_id'],
                        $date,
                        'average'
                    );
                    // Si no se encuentra tasa de cambio, usar 1 como default
                    $data['exchange_rate'] = $exchangeRate ?? 1;
                }

                // Auto-calcular monto convertido (siempre calcular si no se proporciona)
                if (empty($data['amount_converted'])) {
                    $exchangeRate = $data['exchange_rate'] ?? 1;
                    $data['amount_converted'] = round($data['amount'] * $exchangeRate, 2);
                }

                $expense = Expense::create($data);

                // Verificar presupuesto y enviar alerta si es necesario
                $this->budgetService->checkBudgetAlert($expense->category_id);

                Log::info('Expense created successfully', [
                    'user_id' => auth()->id(),
                    'expense_id' => $expense->id,
                    'amount' => $expense->amount_converted
                ]);

                return $expense;
            });
        } catch (\Exception $e) {
            Log::error('Error creating expense', [
                'user_id' => auth()->id(),
                'data' => $data,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw \App\Exceptions\ExpenseException::creationFailed($e->getMessage());
        }
    }

    /**
     * Actualiza un gasto existente
     */
    public function update(Expense $expense, array $data): Expense
    {
        try {
            return DB::transaction(function () use ($expense, $data) {
                // Recalcular si cambió la moneda o el monto
                if (isset($data['currency_id']) || isset($data['amount'])) {
                    $date = Carbon::parse($data['expense_date'] ?? $expense->expense_date);
                    $currencyId = $data['currency_id'] ?? $expense->currency_id;
                    $amount = $data['amount'] ?? $expense->amount;

                    if (empty($data['exchange_rate'])) {
                        $data['exchange_rate'] = $this->exchangeRateService->getRate(
                            $currencyId,
                            $date,
                            'average'
                        ) ?? 1;
                    }

                    $data['amount_converted'] = round($amount * $data['exchange_rate'], 2);
                }

                $expense->update($data);

                // Verificar presupuesto
                $this->budgetService->checkBudgetAlert($expense->category_id);

                Log::info('Expense updated successfully', [
                    'user_id' => auth()->id(),
                    'expense_id' => $expense->id
                ]);

                return $expense->fresh();
            });
        } catch (\Exception $e) {
            Log::error('Error updating expense', [
                'user_id' => auth()->id(),
                'expense_id' => $expense->id,
                'error' => $e->getMessage(),
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            throw \App\Exceptions\ExpenseException::updateFailed($e->getMessage());
        }
    }

    /**
     * Genera cuotas automáticamente para un gasto
     */
    public function generateInstallments(Expense $parentExpense): array
    {
        if (!$parentExpense->hasInstallments()) {
            throw \App\Exceptions\ExpenseException::invalidInstallmentConfiguration();
        }

        $installments = [];
        $monthlyAmount = round($parentExpense->amount / $parentExpense->installments, 2);
        $monthlyAmountConverted = round($parentExpense->amount_converted / $parentExpense->installments, 2);

        try {
            DB::transaction(function () use ($parentExpense, $monthlyAmount, $monthlyAmountConverted, &$installments) {
                for ($i = 2; $i <= $parentExpense->installments; $i++) {
                    $installmentDate = Carbon::parse($parentExpense->expense_date)->addMonths($i - 1);

                    $installment = Expense::create([
                        'expense_date' => $installmentDate,
                        'description' => $parentExpense->description . " (Cuota {$i}/{$parentExpense->installments})",
                        'amount' => $monthlyAmount,
                        'currency_id' => $parentExpense->currency_id,
                        'exchange_rate' => $parentExpense->exchange_rate,
                        'amount_converted' => $monthlyAmountConverted,
                        'category_id' => $parentExpense->category_id,
                        'subcategory_id' => $parentExpense->subcategory_id,
                        'payment_method_id' => $parentExpense->payment_method_id,
                        'card_id' => $parentExpense->card_id,
                        'merchant_id' => $parentExpense->merchant_id,
                        'installments' => $parentExpense->installments,
                        'current_installment' => $i,
                        'parent_expense_id' => $parentExpense->id,
                        'expense_type' => $parentExpense->expense_type,
                        'is_paid' => false,
                    ]);

                    $installments[] = $installment;
                }

                Log::info('Installments generated successfully', [
                    'user_id' => auth()->id(),
                    'parent_expense_id' => $parentExpense->id,
                    'installments_count' => count($installments)
                ]);
            });

            return $installments;
        } catch (\Exception $e) {
            Log::error('Error generating installments', [
                'user_id' => auth()->id(),
                'parent_expense_id' => $parentExpense->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw \App\Exceptions\ExpenseException::installmentGenerationFailed($e->getMessage());
        }
    }

    /**
     * Obtiene el total de gastos para un período
     */
    public function getTotalForPeriod(Carbon $startDate, Carbon $endDate, ?int $categoryId = null): float
    {
        $query = Expense::whereBetween('expense_date', [$startDate, $endDate]);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        return $query->sum('amount_converted');
    }

    /**
     * Obtiene estadísticas de gastos
     */
    public function getStatistics(Carbon $month): array
    {
        $startDate = $month->copy()->startOfMonth();
        $endDate = $month->copy()->endOfMonth();

        return [
            'total' => $this->getTotalForPeriod($startDate, $endDate),
            'by_type' => Expense::whereBetween('expense_date', [$startDate, $endDate])
                ->selectRaw('expense_type, SUM(amount_converted) as total')
                ->groupBy('expense_type')
                ->pluck('total', 'expense_type')
                ->toArray(),
            'by_category' => Expense::whereBetween('expense_date', [$startDate, $endDate])
                ->with('category:id,name')
                ->selectRaw('category_id, SUM(amount_converted) as total')
                ->groupBy('category_id')
                ->get()
                ->mapWithKeys(fn($item) => [$item->category->name => $item->total])
                ->toArray(),
            'count' => Expense::whereBetween('expense_date', [$startDate, $endDate])->count(),
        ];
    }
}
