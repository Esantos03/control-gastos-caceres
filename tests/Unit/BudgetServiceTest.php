<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Currency;
use App\Models\Expense;
use App\Models\PaymentMethod;
use App\Models\Subcategory;
use App\Services\BudgetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetServiceTest extends TestCase
{
    use RefreshDatabase;

    private BudgetService $budgetService;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->budgetService = new BudgetService();

        $this->category = Category::create([
            'name' => 'Transporte',
            'color' => '#00FF00',
            'icon' => 'truck',
            'monthly_budget' => 5000,
            'is_active' => true
        ]);
    }

    public function test_detects_budget_exceeded(): void
    {
        $this->createExpense(6000);

        $this->assertTrue($this->budgetService->isBudgetExceeded($this->category->id));
    }

    public function test_calculates_budget_usage_percentage(): void
    {
        $this->createExpense(2500);

        $percentage = $this->budgetService->getBudgetUsagePercentage($this->category->id);

        $this->assertEquals(50.0, $percentage);
    }

    public function test_calculates_remaining_budget(): void
    {
        $this->createExpense(3000);

        $remaining = $this->budgetService->getRemainingBudget($this->category->id);

        $this->assertEquals(2000.0, $remaining);
    }

    public function test_returns_zero_remaining_when_exceeded(): void
    {
        $this->createExpense(6000);

        $remaining = $this->budgetService->getRemainingBudget($this->category->id);

        $this->assertEquals(0.0, $remaining);
    }

    private function createExpense(float $amount): void
    {
        $currency = Currency::create([
            'code' => 'DOP',
            'name' => 'Peso Dominicano',
            'symbol' => 'RD$'
        ]);

        $subcategory = Subcategory::create([
            'category_id' => $this->category->id,
            'name' => 'Gasolina'
        ]);

        $paymentMethod = PaymentMethod::create([
            'name' => 'Efectivo'
        ]);

        Expense::create([
            'expense_date' => now(),
            'description' => 'Test expense',
            'amount' => $amount,
            'currency_id' => $currency->id,
            'exchange_rate' => 1,
            'amount_converted' => $amount,
            'category_id' => $this->category->id,
            'subcategory_id' => $subcategory->id,
            'payment_method_id' => $paymentMethod->id,
            'expense_type' => 'variable',
        ]);
    }
}
