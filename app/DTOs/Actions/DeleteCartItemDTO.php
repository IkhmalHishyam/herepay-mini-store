<?php

namespace App\DTOs\Actions;

class DeleteCartItemDTO
{
    public string $product_id;

    public string $sku_matrix;

    public function __construct(array $data) 
    {
        $this->product_id = $data['product_id'];
        $this->sku_matrix = $data['sku_matrix'];
    }
}
