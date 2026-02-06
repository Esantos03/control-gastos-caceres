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
        Schema::table('categories', function (Blueprint $table) {
            // Color para identificación visual (formato hex)
            $table->string('color', 7)->default('#3B82F6')->after('name')->comment('Color en formato hex');
            
            // Icono (nombre del icono de Heroicons)
            $table->string('icon', 50)->default('tag')->after('color')->comment('Nombre del icono Heroicons');
            
            // Presupuesto mensual
            $table->decimal('monthly_budget', 12, 2)->nullable()->after('icon')->comment('Presupuesto mensual para esta categoría');
            
            // Orden de visualización
            $table->integer('sort_order')->default(0)->after('monthly_budget')->comment('Orden de visualización');
            
            // Si está activa
            $table->boolean('is_active')->default(true)->after('sort_order')->comment('Si la categoría está activa');
            
            // Índice
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropColumn([
                'color',
                'icon',
                'monthly_budget',
                'sort_order',
                'is_active'
            ]);
        });
    }
};
