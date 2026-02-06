<?php

namespace App\Rules;

use App\Models\Subcategory;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BelongsToCategory implements ValidationRule
{
    public function __construct(private ?int $categoryId)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->categoryId || !$value) {
            return;
        }

        $subcategory = Subcategory::find($value);

        if (!$subcategory || $subcategory->category_id !== $this->categoryId) {
            $fail('La subcategoría seleccionada no pertenece a la categoría elegida.');
        }
    }
}
