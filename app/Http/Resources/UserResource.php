<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\Supports\FileResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Customers\CustomerStatResource;
use App\Http\Resources\Customers\ShippingAddressResource;

/**
 * @see \App\Models\User
 */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'username'         => $this->username,
            'email'            => $this->email,
            'role'             => $this->role,
            'avatar'           => $this->whenLoaded((new FileResource($this->avatar))->toArray($request)),
            'customer_stat'    => $this->whenLoaded((new CustomerStatResource($this->customer_stat))->toArray($request)),
            'shipping_address' => $this->whenLoaded((new ShippingAddressResource($this->defaultShippingAddress))->toArray($request)),
        ];
    }
}
