<?php

namespace App\Rules;

use App\Models\PaymentMethod;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RequiresCardPaymentMethod implements ValidationRule
{
    public function __construct(private ?int $paymentMethodId)
    {
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Si no hay card_id, no hay problema (es nullable)
        if (!$value) {
            return;
        }

        // Si hay card_id pero no hay payment_method_id
        if (!$this->paymentMethodId) {
            $fail('Debe seleccionar un método de pago cuando selecciona una tarjeta.');
            return;
        }

        // Verificar que el método de pago sea compatible con tarjetas
        $paymentMethod = PaymentMethod::find($this->paymentMethodId);
        
        if ($paymentMethod && !in_array(strtolower($paymentMethod->name), [
            'tarjeta de crédito',
            'tarjeta de débito',
            'tarjeta',
            'credito',
            'debito'
        ])) {
            $fail('Solo puede seleccionar una tarjeta si el método de pago es Tarjeta de Crédito o Débito.');
        }
    }
}
