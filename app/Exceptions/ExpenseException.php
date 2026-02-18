<?php

namespace App\Exceptions;

use Exception;

class ExpenseException extends Exception
{
    public static function creationFailed(string $message = ''): self
    {
        return new self("No se pudo crear el gasto. {$message}");
    }

    public static function updateFailed(string $message = ''): self
    {
        return new self("No se pudo actualizar el gasto. {$message}");
    }

    public static function deleteFailed(string $message = ''): self
    {
        return new self("No se pudo eliminar el gasto. {$message}");
    }

    public static function installmentGenerationFailed(string $message = ''): self
    {
        return new self("No se pudieron generar las cuotas. {$message}");
    }

    public static function invalidInstallmentConfiguration(): self
    {
        return new self("El gasto no tiene cuotas configuradas correctamente.");
    }
}
