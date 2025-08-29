<?php

namespace App\Services\FileManager\Tools\CWebP\Traits;

use App\Services\FileManager\Tools\CWebP\Exceptions\BadRequestException;

trait Helper
{
    public function checkQuality(int $quality): int
    {
        if ($quality < 0 || $quality > 100)
        {
            throw new BadRequestException("Quality can't be less than 0 or more than 100");
        }

        return $quality;
    }
}