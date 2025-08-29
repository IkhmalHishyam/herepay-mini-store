<?php

namespace App\DTOs\Actions;

class AddToCartDTO
{
    public string $product_id;

    public int $quantity;

    public ?string $sku_matrix;

    public function __construct(array $data) 
    {
        $this->product_id = $data['product_id'];
        $this->quantity   = $data['quantity'];
        $this->sku_matrix = $data['sku_matrix'] ?? null;
    }
}