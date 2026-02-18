<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CardController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CurrencyController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\ExchangeRateController;
use App\Http\Controllers\Api\V1\ExpenseController;
use App\Http\Controllers\Api\V1\MerchantController;
use App\Http\Controllers\Api\V1\PaymentMethodController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\SubcategoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Control de Gastos Cáceres
|--------------------------------------------------------------------------
|
| API RESTful para el sistema de control de gastos.
| Versión: 1.0
| Autenticación: Sanctum (Token Bearer)
|
*/

// Rutas públicas
Route::prefix('v1')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});

// Rutas protegidas con autenticación
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    
    // Autenticación
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::get('dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('dashboard/recent-expenses', [DashboardController::class, 'recentExpenses']);
    
    // Gastos (Expenses)
    Route::apiResource('expenses', ExpenseController::class);
    Route::post('expenses/{expense}/generate-installments', [ExpenseController::class, 'generateInstallments']);
    Route::get('expenses/{expense}/installments', [ExpenseController::class, 'getInstallments']);
    Route::patch('expenses/{expense}/mark-paid', [ExpenseController::class, 'markAsPaid']);
    Route::patch('expenses/{expense}/mark-unpaid', [ExpenseController::class, 'markAsUnpaid']);
    
    // Categorías
    Route::apiResource('categories', CategoryController::class);
    Route::get('categories/{category}/expenses', [CategoryController::class, 'expenses']);
    Route::get('categories/{category}/budget-status', [CategoryController::class, 'budgetStatus']);
    Route::get('categories/{category}/statistics', [CategoryController::class, 'statistics']);
    
    // Subcategorías
    Route::apiResource('subcategories', SubcategoryController::class);
    Route::get('categories/{category}/subcategories', [SubcategoryController::class, 'byCategory']);
    
    // Tarjetas
    Route::apiResource('cards', CardController::class);
    Route::get('cards/{card}/expenses', [CardController::class, 'expenses']);
    Route::get('cards/{card}/usage', [CardController::class, 'usage']);
    Route::patch('cards/{card}/toggle-active', [CardController::class, 'toggleActive']);
    
    // Métodos de pago
    Route::apiResource('payment-methods', PaymentMethodController::class);
    
    // Comercios
    Route::apiResource('merchants', MerchantController::class);
    
    // Monedas
    Route::apiResource('currencies', CurrencyController::class);
    
    // Tasas de cambio
    Route::apiResource('exchange-rates', ExchangeRateController::class);
    Route::get('exchange-rates/currency/{currency}', [ExchangeRateController::class, 'byCurrency']);
    Route::get('exchange-rates/latest/{currency}', [ExchangeRateController::class, 'latest']);
    Route::post('exchange-rates/convert', [ExchangeRateController::class, 'convert']);
    
    // Reportes
    Route::prefix('reports')->group(function () {
        Route::get('monthly', [ReportController::class, 'monthly']);
        Route::get('by-category', [ReportController::class, 'byCategory']);
        Route::get('by-card', [ReportController::class, 'byCard']);
        Route::get('by-type', [ReportController::class, 'byType']);
        Route::get('budget-summary', [ReportController::class, 'budgetSummary']);
        Route::get('trends', [ReportController::class, 'trends']);
        Route::post('export', [ReportController::class, 'export']);
    });
});
