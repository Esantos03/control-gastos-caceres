<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidBillingCycle implements ValidationRule
{
    public function __construct(private ?int $billingDay)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->billingDay || !$value) {
            return;
        }

        // El día de pago debe ser después del día de corte
        if ($value <= $this->billingDay) {
            $fail('El día de pago debe ser posterior al día de corte.');
        }

        // Validar que la diferencia sea razonable (al menos 5 días)
        if (($value - $this->billingDay) < 5) {
            $fail('Debe haber al menos 5 días entre el día de corte y el día de pago.');
        }
    }
}
