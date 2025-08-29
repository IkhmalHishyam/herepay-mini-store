<?php

namespace App\Services\FileManager\Enums;

enum FileAccessLevelEnum: string
{
    case PUBLIC  = 'public';
    case PRIVATE = 'private';
}
