<?php

namespace App\Providers;

use App\Models\Card;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Merchant;
use App\Policies\CardPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\ExpensePolicy;
use App\Policies\MerchantPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar policies
        Gate::policy(Expense::class, ExpensePolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Card::class, CardPolicy::class);
        Gate::policy(Merchant::class, MerchantPolicy::class);
    }
}
