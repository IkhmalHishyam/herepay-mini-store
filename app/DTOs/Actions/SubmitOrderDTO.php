<?php

namespace App\DTOs\Actions;

class SubmitOrderDTO
{
    public bool $is_success;

    public string $customer_name;

    public string $customer_email;

    public string $customer_phone;

    public string $address_1;

    public ?string $address_2;

    public string $city;

    public string $state;

    public string $postcode;

    public string $country;

    public ?string $order_notes;

    public function __construct(array $data)
    {
        $this->is_success     = $data['is_success'];
        $this->customer_name  = $data['customer_name'];
        $this->customer_email = $data['customer_email'];
        $this->customer_phone = $data['customer_phone'];
        $this->address_1      = $data['address_1'];
        $this->address_2      = $data['address_2'] ?? null;
        $this->city           = $data['city'];
        $this->state          = $data['state'];
        $this->postcode       = $data['postcode'];
        $this->country        = $data['country'];
        $this->order_notes    = $data['order_notes'] ?? null;
    }
}
