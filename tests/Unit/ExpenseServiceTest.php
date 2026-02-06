<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Models\PaymentMethod;
use App\Models\Subcategory;
use App\Services\BudgetService;
use App\Services\ExchangeRateService;
use App\Services\ExpenseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseServiceTest extends TestCase
{
    use RefreshDatabase;

    private ExpenseService $expenseService;
    private Currency $currency;
    private Category $category;
    private Subcategory $subcategory;
    private PaymentMethod $paymentMethod;

    protected function setUp(): void
    {
        parent::setUp();

        $this->expenseService = new ExpenseService(
            new ExchangeRateService(),
            new BudgetService()
        );

        // Crear datos de prueba
        $this->currency = Currency::create([
            'code' => 'USD',
            'name' => 'Dólar Estadounidense',
            'symbol' => '$'
        ]);

        ExchangeRate::create([
            'currency_id' => $this->currency->id,
            'month' => now()->month,
            'year' => now()->year,
            'buy_rate' => 58.50,
            'sell_rate' => 59.50,
            'average_rate' => 59.00
        ]);

        $this->category = Category::create([
            'name' => 'Alimentación',
            'color' => '#FF0000',
            'icon' => 'cake',
            'monthly_budget' => 10000,
            'is_active' => true
        ]);

        $this->subcategory = Subcategory::create([
            'category_id' => $this->category->id,
            'name' => 'Supermercado'
        ]);

        $this->paymentMethod = PaymentMethod::create([
            'name' => 'Tarjeta de Crédito'
        ]);
    }

    public function test_can_create_expense_with_automatic_conversion(): void
    {
        $data = [
            'expense_date' => now(),
            'description' => 'Compra en supermercado',
            'amount' => 100,
            'currency_id' => $this->currency->id,
            'category_id' => $this->category->id,
            'subcategory_id' => $this->subcategory->id,
            'payment_method_id' => $this->paymentMethod->id,
            'expense_type' => 'variable',
        ];

        $expense = $this->expenseService->create($data);

        $this->assertNotNull($expense->id);
        $this->assertEquals(59.00, $expense->exchange_rate);
        $this->assertEquals(5900.00, $expense->amount_converted);
    }

    public function test_can_generate_installments(): void
    {
        $parentExpense = $this->expenseService->create([
            'expense_date' => now(),
            'description' => 'Compra a plazos',
            'amount' => 300,
            'currency_id' => $this->currency->id,
            'category_id' => $this->category->id,
            'subcategory_id' => $this->subcategory->id,
            'payment_method_id' => $this->paymentMethod->id,
            'expense_type' => 'variable',
            'installments' => 3,
            'current_installment' => 1,
        ]);

        $installments = $this->expenseService->generateInstallments($parentExpense);

        $this->assertCount(2, $installments);
        $this->assertEquals(2, $installments[0]->current_installment);
        $this->assertEquals(3, $installments[1]->current_installment);
        $this->assertEquals($parentExpense->id, $installments[0]->parent_expense_id);
    }

    public function test_calculates_statistics_correctly(): void
    {
        // Crear varios gastos
        for ($i = 0; $i < 5; $i++) {
            $this->expenseService->create([
                'expense_date' => now(),
                'description' => "Gasto {$i}",
                'amount' => 100,
                'currency_id' => $this->currency->id,
                'category_id' => $this->category->id,
                'subcategory_id' => $this->subcategory->id,
                'payment_method_id' => $this->paymentMethod->id,
                'expense_type' => 'variable',
            ]);
        }

        $stats = $this->expenseService->getStatistics(now());

        $this->assertEquals(29500.00, $stats['total']); // 5 * 100 * 59
        $this->assertEquals(5, $stats['count']);
        $this->assertArrayHasKey('by_type', $stats);
        $this->assertArrayHasKey('by_category', $stats);
    }
}
