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
        // Obtener el primer usuario o crear uno por defecto
        $defaultUserId = \App\Models\User::first()?->id ?? \App\Models\User::create([
            'name' => 'Sistema',
            'email' => 'sistema@caceres.com.do',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ])->id;

        // Agregar user_id a expenses si no existe
        if (!Schema::hasColumn('expenses', 'user_id')) {
            Schema::table('expenses', function (Blueprint $table) use ($defaultUserId) {
                $table->unsignedBigInteger('user_id')->after('id')->default($defaultUserId);
                $table->index('user_id');
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            });
        }

        // Agregar user_id a categories si no existe
        if (!Schema::hasColumn('categories', 'user_id')) {
            Schema::table('categories', function (Blueprint $table) use ($defaultUserId) {
                $table->unsignedBigInteger('user_id')->after('id')->default($defaultUserId);
                $table->index('user_id');
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            });
        }

        // Agregar user_id a subcategories si no existe
        if (!Schema::hasColumn('subcategories', 'user_id')) {
            Schema::table('subcategories', function (Blueprint $table) use ($defaultUserId) {
                $table->unsignedBigInteger('user_id')->after('id')->default($defaultUserId);
                $table->index('user_id');
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            });
        }

        // Agregar user_id a cards si no existe
        if (!Schema::hasColumn('cards', 'user_id')) {
            Schema::table('cards', function (Blueprint $table) use ($defaultUserId) {
                $table->unsignedBigInteger('user_id')->after('id')->default($defaultUserId);
                $table->index('user_id');
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            });
        }

        // Agregar user_id a merchants si no existe
        if (!Schema::hasColumn('merchants', 'user_id')) {
            Schema::table('merchants', function (Blueprint $table) use ($defaultUserId) {
                $table->unsignedBigInteger('user_id')->after('id')->default($defaultUserId);
                $table->index('user_id');
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            });
        }

        // payment_methods, currencies y exchange_rates son compartidos (no necesitan user_id)
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('subcategories', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('cards', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('merchants', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
