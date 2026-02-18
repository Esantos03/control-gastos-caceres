<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CheckUsers extends Command
{
    protected $signature = 'users:check';
    protected $description = 'Verificar usuarios en la base de datos';

    public function handle()
    {
        $this->info('=== Usuarios en la Base de Datos ===');
        $this->newLine();

        $users = User::all();

        if ($users->isEmpty()) {
            $this->error('❌ No hay usuarios en la base de datos');
            $this->newLine();
            $this->info('Ejecuta: php artisan db:seed --class=DatabaseSeeder');
            return;
        }

        foreach ($users as $user) {
            $this->line("ID: {$user->id}");
            $this->line("Nombre: {$user->name}");
            $this->line("Email: {$user->email}");
            $this->line("Creado: {$user->created_at}");
            $this->line(str_repeat('-', 50));
        }

        $this->info("Total de usuarios: {$users->count()}");
    }
}
