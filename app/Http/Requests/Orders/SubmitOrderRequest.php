<?php

namespace App\Http\Requests\Orders;

use Illuminate\Foundation\Http\FormRequest;

class SubmitOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_success'     => ['required', 'boolean'],
            'customer_name'  => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'address_1'      => ['required', 'string', 'max:255'],
            'address_2'      => ['nullable', 'string', 'max:255'],
            'city'           => ['required', 'string', 'max:100'],
            'state'          => ['required', 'string', 'max:100'],
            'postcode'       => ['required', 'string', 'max:20'],
            'country'        => ['required', 'string', 'max:100'],
            'order_notes'    => ['nullable', 'string', 'max:1000'],
        ];
    }
}
