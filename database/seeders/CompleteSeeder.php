<?php

namespace Database\Seeders;

use App\Models\Card;
use App\Models\Category;
use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Models\Expense;
use App\Models\Merchant;
use App\Models\PaymentMethod;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CompleteSeeder extends Seeder
{
    public function run(): void
    {
        // Usuario administrador
        $user = User::create([
            'name' => 'Administrador',
            'email' => 'admin@caceres.com.do',
            'password' => Hash::make('password'),
        ]);

        // Monedas
        $dop = Currency::create(['code' => 'DOP', 'name' => 'Peso Dominicano', 'symbol' => 'RD$']);
        $usd = Currency::create(['code' => 'USD', 'name' => 'Dólar Estadounidense', 'symbol' => '$']);
        $eur = Currency::create(['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€']);

        // Tasas de cambio (últimos 6 meses)
        $rates = [
            ['month' => 1, 'buy' => 58.50, 'sell' => 59.50],
            ['month' => 2, 'buy' => 58.75, 'sell' => 59.75],
            ['month' => 3, 'buy' => 59.00, 'sell' => 60.00],
            ['month' => 4, 'buy' => 59.25, 'sell' => 60.25],
            ['month' => 5, 'buy' => 59.50, 'sell' => 60.50],
            ['month' => 6, 'buy' => 59.75, 'sell' => 60.75],
        ];

        foreach ($rates as $rate) {
            ExchangeRate::create([
                'currency_id' => $usd->id,
                'month' => $rate['month'],
                'year' => 2026,
                'buy_rate' => $rate['buy'],
                'sell_rate' => $rate['sell'],
                'average_rate' => ($rate['buy'] + $rate['sell']) / 2,
            ]);
        }

        // Categorías con presupuestos
        $categories = [
            ['name' => 'Alimentación', 'icon' => 'cake', 'color' => '#EF4444', 'budget' => 15000],
            ['name' => 'Transporte', 'icon' => 'truck', 'color' => '#3B82F6', 'budget' => 8000],
            ['name' => 'Servicios', 'icon' => 'lightning-bolt', 'color' => '#10B981', 'budget' => 12000],
            ['name' => 'Entretenimiento', 'icon' => 'film', 'color' => '#F59E0B', 'budget' => 5000],
            ['name' => 'Salud', 'icon' => 'heart', 'color' => '#EC4899', 'budget' => 7000],
            ['name' => 'Educación', 'icon' => 'academic-cap', 'color' => '#8B5CF6', 'budget' => 6000],
            ['name' => 'Hogar', 'icon' => 'home', 'color' => '#14B8A6', 'budget' => 10000],
            ['name' => 'Ropa', 'icon' => 'sparkles', 'color' => '#F97316', 'budget' => 4000],
        ];

        $categoryModels = [];
        foreach ($categories as $cat) {
            $category = Category::create([
                'user_id' => $user->id,
                'name' => $cat['name'],
                'icon' => $cat['icon'],
                'color' => $cat['color'],
                'monthly_budget' => $cat['budget'],
                'sort_order' => 0,
                'is_active' => true,
            ]);
            $categoryModels[$cat['name']] = $category;

            // Subcategorías
            $subcategories = match($cat['name']) {
                'Alimentación' => ['Supermercado', 'Restaurantes', 'Delivery'],
                'Transporte' => ['Gasolina', 'Uber', 'Mantenimiento'],
                'Servicios' => ['Electricidad', 'Agua', 'Internet', 'Teléfono'],
                'Entretenimiento' => ['Cine', 'Streaming', 'Eventos'],
                'Salud' => ['Medicinas', 'Consultas', 'Seguro'],
                'Educación' => ['Cursos', 'Libros', 'Material'],
                'Hogar' => ['Alquiler', 'Mantenimiento', 'Decoración'],
                'Ropa' => ['Ropa', 'Calzado', 'Accesorios'],
                default => ['General'],
            };

            foreach ($subcategories as $subcat) {
                Subcategory::create([
                    'user_id' => $user->id,
                    'category_id' => $category->id,
                    'name' => $subcat,
                ]);
            }
        }

        // Métodos de pago (compartidos - sin user_id)
        $paymentMethods = [
            ['name' => 'Efectivo', 'requires_card' => false],
            ['name' => 'Tarjeta de Crédito', 'requires_card' => true],
            ['name' => 'Tarjeta de Débito', 'requires_card' => true],
            ['name' => 'Transferencia', 'requires_card' => false],
            ['name' => 'Cheque', 'requires_card' => false],
        ];
        $paymentMethodModels = [];
        foreach ($paymentMethods as $method) {
            $paymentMethodModels[$method['name']] = PaymentMethod::create($method);
        }

        // Tarjetas
        $cards = [
            ['name' => 'AMEX Gold', 'digits' => '1234', 'type' => 'credito', 'limit' => 50000, 'billing' => 15, 'payment' => 25],
            ['name' => 'Visa BHD', 'digits' => '5678', 'type' => 'credito', 'limit' => 30000, 'billing' => 10, 'payment' => 20],
            ['name' => 'Mastercard Popular', 'digits' => '9012', 'type' => 'debito', 'limit' => null, 'billing' => null, 'payment' => null],
        ];

        $cardModels = [];
        foreach ($cards as $card) {
            $cardModels[$card['name']] = Card::create([
                'user_id' => $user->id,
                'name' => $card['name'],
                'last_digits' => $card['digits'],
                'type' => $card['type'],
                'expiration_date' => now()->addYears(2),
                'credit_limit' => $card['limit'],
                'billing_day' => $card['billing'],
                'payment_day' => $card['payment'],
                'is_active' => true,
            ]);
        }

        // Comercios
        $merchants = ['Supermercado Nacional', 'Jumbo', 'Shell', 'Uber', 'Netflix', 'Amazon'];
        $merchantModels = [];
        foreach ($merchants as $merchant) {
            $merchantModels[$merchant] = Merchant::create([
                'user_id' => $user->id,
                'name' => $merchant
            ]);
        }

        // Gastos de ejemplo (mes actual)
        $expenses = [
            [
                'date' => now()->subDays(25),
                'description' => 'Compra semanal supermercado',
                'amount' => 3500,
                'currency' => $dop,
                'category' => 'Alimentación',
                'subcategory' => 'Supermercado',
                'payment' => 'Tarjeta de Crédito',
                'card' => 'AMEX Gold',
                'merchant' => 'Supermercado Nacional',
                'type' => 'variable',
            ],
            [
                'date' => now()->subDays(20),
                'description' => 'Gasolina',
                'amount' => 2000,
                'currency' => $dop,
                'category' => 'Transporte',
                'subcategory' => 'Gasolina',
                'payment' => 'Tarjeta de Débito',
                'card' => 'Mastercard Popular',
                'merchant' => 'Shell',
                'type' => 'variable',
            ],
            [
                'date' => now()->subDays(15),
                'description' => 'Factura de electricidad',
                'amount' => 4500,
                'currency' => $dop,
                'category' => 'Servicios',
                'subcategory' => 'Electricidad',
                'payment' => 'Transferencia',
                'card' => null,
                'merchant' => null,
                'type' => 'fixed',
            ],
            [
                'date' => now()->subDays(10),
                'description' => 'Suscripción Netflix',
                'amount' => 15.99,
                'currency' => $usd,
                'category' => 'Entretenimiento',
                'subcategory' => 'Streaming',
                'payment' => 'Tarjeta de Crédito',
                'card' => 'Visa BHD',
                'merchant' => 'Netflix',
                'type' => 'fixed',
            ],
            [
                'date' => now()->subDays(5),
                'description' => 'Laptop nueva (3 cuotas)',
                'amount' => 1200,
                'currency' => $usd,
                'category' => 'Educación',
                'subcategory' => 'Material',
                'payment' => 'Tarjeta de Crédito',
                'card' => 'AMEX Gold',
                'merchant' => 'Amazon',
                'type' => 'occasional',
                'installments' => 3,
            ],
        ];

        foreach ($expenses as $exp) {
            $category = $categoryModels[$exp['category']];
            $subcategory = $category->subcategories()->where('name', $exp['subcategory'])->first();
            $paymentMethod = $paymentMethodModels[$exp['payment']];
            $card = $exp['card'] ? $cardModels[$exp['card']] : null;
            $merchant = $exp['merchant'] ? $merchantModels[$exp['merchant']] : null;

            $exchangeRate = $exp['currency']->code === 'DOP' ? 1 : 59.25;
            $amountConverted = $exp['amount'] * $exchangeRate;

            Expense::create([
                'expense_date' => $exp['date'],
                'description' => $exp['description'],
                'amount' => $exp['amount'],
                'currency_id' => $exp['currency']->id,
                'exchange_rate' => $exchangeRate,
                'amount_converted' => $amountConverted,
                'category_id' => $category->id,
                'subcategory_id' => $subcategory->id,
                'payment_method_id' => $paymentMethod->id,
                'card_id' => $card?->id,
                'merchant_id' => $merchant?->id,
                'installments' => $exp['installments'] ?? 1,
                'current_installment' => 1,
                'expense_type' => $exp['type'],
                'is_paid' => true,
            ]);
        }

        $this->command->info('✅ Datos de prueba creados exitosamente');
        $this->command->info('📧 Email: admin@caceres.com.do');
        $this->command->info('🔑 Password: password');
    }
}
