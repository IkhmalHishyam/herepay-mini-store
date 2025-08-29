<?php

namespace App\Services\FileManager\Enums;

enum FileablePrefixes: string
{
    case AVATAR            = 'avatar';
    case PRODUCT_IMAGE     = 'product_image';
    case PRODUCT_THUMBNAIL = 'product_thumbnail';
}
