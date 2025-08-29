<?php

namespace App\Services\FileManager\Tools\CWebP\DTO\FilterOptions;

class CWebPCropMapDTO
{
    public int $crop_x;

    public int $crop_y;

    public int $crop_width;

    public int $crop_height;

    public function __construct(
        int $crop_x,
        int $crop_y,
        int $crop_width,
        int $crop_height
    ) {
        $this->crop_x      = $crop_x;
        $this->crop_y      = $crop_y;
        $this->crop_width  = $crop_width;
        $this->crop_height = $crop_height;
    }
}
