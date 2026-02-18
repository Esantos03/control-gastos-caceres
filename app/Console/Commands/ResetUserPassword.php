<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetUserPassword extends Command
{
    protected $signature = 'user:reset-password {email} {password=password}';
    protected $description = 'Resetear contraseña de un usuario';

    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("❌ Usuario con email '{$email}' no encontrado");
            return 1;
        }

        $user->password = Hash::make($password);
        $user->save();

        $this->info("✅ Contraseña actualizada exitosamente");
        $this->newLine();
        $this->line("Email: {$user->email}");
        $this->line("Nueva contraseña: {$password}");
        $this->newLine();
        $this->info("Ahora puedes iniciar sesión con estas credenciales");

        return 0;
    }
}
