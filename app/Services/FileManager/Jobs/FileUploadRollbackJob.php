<?php

namespace App\Services\FileManager\Jobs;

use App\Models\Supports\File;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FileUploadRollbackJob
{
    protected string $log_tag = '[Job|FileUploadRollbackJob]';

    use Queueable;

    protected array $uploaded_files;

    public function __construct(array $uploaded_files)
    {
        $this->uploaded_files = $uploaded_files;
    }

    public function handle(): void
    {
        Log::info("$this->log_tag Started");

        foreach ($this->uploaded_files as $uploaded_file)
        {
            $file = File::where('id', $uploaded_file['file_id'])->first();

            if (! $file)
            {
                Log::info("Rollback File Upload on File Path $uploaded_file[file_path]");

                Storage::disk(config('filesystems.default'))->delete($uploaded_file['file_path']);
            } else
            {
                Log::info("Waive Rollback File Upload on File Path {$uploaded_file['file_path']}");
            }
        }

        Log::info("$this->log_tag Finished");
    }

    public function fail($exception = null): void
    {
        Log::error("$this->log_tag[fail] $exception");
    }
}
