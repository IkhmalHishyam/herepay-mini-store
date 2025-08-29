<?php

namespace App\DTOs\Actions\Subs;

class VariantDTO
{
    public ?string $name;

    public function __construct(array $data)
    {
        $this->name = $data['name'] ?? null;
    }
}
