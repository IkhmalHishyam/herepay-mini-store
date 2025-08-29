<?php

namespace App\Services\FileManager\Enums;

enum FileModeEnum: string
{
    case PUBLIC = 'public';
    case LOCAL  = 'local';
    case S3     = 's3';
    case GCS    = 'gcs';
}
