<?php

namespace App\Http\Resources\Customers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @see \App\Models\Customers\ShippingAddress
 */
class ShippingAddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'phone'      => $this->phone,
            'address_1'  => $this->address_1,
            'address_2'  => $this->address_2,
            'city'       => $this->city,
            'state'      => $this->state,
            'postcode'   => $this->postcode,
            'country'    => $this->country,
            'is_default' => $this->is_default,
        ];
    }
}