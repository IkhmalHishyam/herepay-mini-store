<?php

namespace App\Services\FileManager\Tools\CWebP\Enums;

use App\Services\FileManager\Tools\CWebP\Exceptions\ImageFormatNotSupportedException;

enum SupportedImageFormatEnum: string
{
    case WEBP = 'webp';
    case JPEG = 'jpeg';
    case PNG  = 'png';
    case PNM  = 'pnm';
    case TIFF = 'tiff';

    public static function match(string $format): mixed
    {
        return match ($format)
        {
            'webp'  => self::WEBP,
            'jpeg'  => self::JPEG,
            'png'   => self::PNG,
            'pnm'   => self::PNM,
            'tiff'  => self::TIFF,
            default => throw new ImageFormatNotSupportedException($format),
        };
    }
}
