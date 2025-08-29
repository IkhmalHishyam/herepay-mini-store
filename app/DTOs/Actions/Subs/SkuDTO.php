<?php

namespace App\DTOs\Actions\Subs;

class SkuDTO
{
    public ?string $matrix;

    public ?float $price;

    public ?int $total_stock;

    public function __construct(array $data)
    {
        $this->matrix      = $data['matrix']      ?? null;
        $this->price       = $data['price']       ?? null;
        $this->total_stock = $data['total_stock'] ?? null;
    }
}