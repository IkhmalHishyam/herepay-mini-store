<?php

namespace App\DTOs\Actions\Subs;

class VariantGroupDTO
{
    public ?string $name;

    public ?string $index;

    public array $variants;

    public function __construct(array $data)
    {
        $this->name     = $data['name']  ?? null;
        $this->index    = $data['index'] ?? null;
        $this->variants = array_map(fn($variant) => new VariantDTO($variant), $data['variants'] ?? []);
    }
}
