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
        Schema::table('cards', function (Blueprint $table) {
            // Fecha de vencimiento
            $table->date('expiration_date')->nullable()->after('type')->comment('Fecha de vencimiento de la tarjeta');
            
            // Límite de crédito (solo para tarjetas de crédito)
            $table->decimal('credit_limit', 12, 2)->nullable()->after('expiration_date')->comment('Límite de crédito');
            
            // Día de corte (1-31)
            $table->integer('billing_day')->nullable()->after('credit_limit')->comment('Día de corte del mes (1-31)');
            
            // Día de pago (1-31)
            $table->integer('payment_day')->nullable()->after('billing_day')->comment('Día de pago del mes (1-31)');
            
            // Si está activa
            $table->boolean('is_active')->default(true)->after('payment_day')->comment('Si la tarjeta está activa');
            
            // Notas
            $table->text('notes')->nullable()->after('is_active')->comment('Notas adicionales');
            
            // Índice
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropColumn([
                'expiration_date',
                'credit_limit',
                'billing_day',
                'payment_day',
                'is_active',
                'notes'
            ]);
        });
    }
};
