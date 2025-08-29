<?php

namespace App\Services\FileManager\DTOs;

class FileFilterOptionDTO
{
    public bool $convert_to_webp;

    public bool $resize;

    public function __construct(
        bool $convert_to_webp = true,
        bool $resize = false,
    ) {
        $this->convert_to_webp = $convert_to_webp;
        $this->resize          = $resize;
    }
}
