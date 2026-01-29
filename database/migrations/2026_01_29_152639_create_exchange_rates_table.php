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
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('currency_id')->constrained()->cascadeOnDelete();
            $table->integer('month'); // 1-12
            $table->integer('year');
            $table->decimal('buy_rate', 10, 4);
            $table->decimal('sell_rate', 10, 4);
            $table->decimal('average_rate', 10, 4);
            $table->timestamps();
            
            // Índice único para evitar duplicados de moneda/mes/año
            $table->unique(['currency_id', 'month', 'year']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
