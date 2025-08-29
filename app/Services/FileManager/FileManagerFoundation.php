<?php

namespace App\Services\FileManager;

use App\Traits\Responsables\ExceptionHandlerTrait;

class FileManagerFoundation
{
    protected \DateTimeInterface $cache_public_default_ttl;

    protected \DateTimeInterface $cache_private_default_ttl;

    public function __construct()
    {
        $this->cache_public_default_ttl  = now()->addDays(1);
        $this->cache_private_default_ttl = now()->addMinutes(5);
    }
}
