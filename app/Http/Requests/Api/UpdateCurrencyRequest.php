<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCurrencyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['sometimes', 'string', 'max:3', Rule::unique('currencies')->ignore($this->route('currency'))],
            'name' => ['sometimes', 'string', 'max:255'],
            'symbol' => ['sometimes', 'string', 'max:5'],
        ];
    }
}
