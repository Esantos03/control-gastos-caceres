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
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->boolean('requires_card')->default(false)->after('name');
        });

        // Actualizar los métodos de pago existentes que requieren tarjeta
        DB::table('payment_methods')
            ->whereIn('name', ['Tarjeta de Crédito', 'Tarjeta de Débito'])
            ->update(['requires_card' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->dropColumn('requires_card');
        });
    }
};
