<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidInstallmentNumber implements ValidationRule
{
    public function __construct(private ?int $totalInstallments)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->totalInstallments || !$value) {
            return;
        }

        if ($value > $this->totalInstallments) {
            $fail("La cuota actual no puede ser mayor al número total de cuotas ({$this->totalInstallments}).");
        }

        if ($value < 1) {
            $fail('La cuota actual debe ser al menos 1.');
        }
    }
}
