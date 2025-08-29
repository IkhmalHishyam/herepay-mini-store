<?php

namespace App\Services\FileManager\Tools\CWebP\Exceptions;

use Exception;

class ImageFormatNotSupportedException extends Exception
{
    public function __construct(string $format)
    {
        parent::__construct("Image format '{$format}' is not supported.");
    }
}
