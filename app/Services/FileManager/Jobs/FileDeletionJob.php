<?php

namespace App\Services\FileManager\Jobs;

use App\Models\Supports\File;
use App\Services\FileManager\Contracts\FMDeleteService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class FileDeletionJob
{
    protected string $log_tag = '[Job|FileDeletionJob]';

    use Queueable;

    protected File $file;

    public function __construct(File $file)
    {
        $this->file = $file;
    }

    public function handle(): void
    {
        Log::info("$this->log_tag Started");

        $fileManager = new FMDeleteService;
        $fileManager->handle($this->file);

        Log::info("$this->log_tag Finished");
    }

    public function fail($exception = null): void
    {
        Log::error("$this->log_tag[fail] $exception");
    }
}
