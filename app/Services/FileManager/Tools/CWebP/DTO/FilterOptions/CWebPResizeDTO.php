<?php

namespace App\Services\FileManager\Tools\CWebP\DTO\FilterOptions;

class CWebPResizeDTO
{
    public int $resize_width;

    public int $resize_height;

    public function __construct(
        int $resize_width,
        int $resize_height
    ) {
        $this->resize_width  = $resize_width;
        $this->resize_height = $resize_height;
    }
}
