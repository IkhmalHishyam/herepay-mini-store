<?php

namespace App\Http\Requests\Carts;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddToCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', Rule::exists('products', 'id')],
            'quantity'   => ['required', 'integer', 'min:1'],
            'sku_matrix' => ['nullable', 'string']
        ];
    }
}
