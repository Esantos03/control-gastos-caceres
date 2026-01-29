<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->date('expense_date');
            $table->string('description');
            $table->decimal('amount', 12, 2);

            $table->foreignId('currency_id')->constrained()->cascadeOnDelete();
            $table->decimal('exchange_rate', 10, 2)->nullable();
            $table->decimal('amount_converted', 12, 2);

            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subcategory_id')->constrained()->cascadeOnDelete();

            $table->foreignId('payment_method_id')->constrained()->cascadeOnDelete();
            $table->foreignId('card_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('merchant_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
