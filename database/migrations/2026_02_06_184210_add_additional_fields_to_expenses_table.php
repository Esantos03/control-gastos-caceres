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
        Schema::table('expenses', function (Blueprint $table) {
            // Campos para cuotas/pagos recurrentes
            $table->integer('installments')->default(1)->after('merchant_id')->comment('Número total de cuotas');
            $table->integer('current_installment')->default(1)->after('installments')->comment('Cuota actual');
            $table->foreignId('parent_expense_id')->nullable()->after('current_installment')->constrained('expenses')->nullOnDelete()->comment('Gasto padre para cuotas');
            
            // Tipo de gasto
            $table->enum('expense_type', ['fixed', 'variable', 'occasional'])->default('variable')->after('parent_expense_id')->comment('Tipo de gasto: fijo, variable u ocasional');
            
            // Notas adicionales
            $table->text('notes')->nullable()->after('expense_type')->comment('Notas adicionales del gasto');
            
            // Estado del gasto
            $table->boolean('is_paid')->default(true)->after('notes')->comment('Si el gasto está pagado');
            
            // Índices para mejorar rendimiento
            $table->index('expense_date');
            $table->index('expense_type');
            $table->index('parent_expense_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex(['expense_date']);
            $table->dropIndex(['expense_type']);
            $table->dropIndex(['parent_expense_id']);
            
            $table->dropForeign(['parent_expense_id']);
            $table->dropColumn([
                'installments',
                'current_installment',
                'parent_expense_id',
                'expense_type',
                'notes',
                'is_paid'
            ]);
        });
    }
};
