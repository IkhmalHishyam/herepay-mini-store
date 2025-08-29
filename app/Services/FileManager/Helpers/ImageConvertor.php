<?php

namespace App\Services\FileManager\Helpers;

use App\Services\FileManager\Tools\CWebP\Contracts\ConvertImageToWebP;
use App\Services\FileManager\Tools\CWebP\DTO\ConvertImageToWebPDTO;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class ImageConvertor
{
    protected string $log_tag = '[ImageConvertor]';

    protected UploadedFile $new_file;

    public function handle(UploadedFile $file): static
    {
        if (! $this->verifyImage($file))
        {
            $this->setNewFile($file);

            return $this;
        }

        $this->convertImageToWebPUsingCWebP($file);
        $this->setNewFile($file);

        return $this;
    }

    // *****************************************************************************************************************
    // GETTERS
    // *****************************************************************************************************************

    public function getNewFile(): UploadedFile
    {
        return $this->new_file;
    }

    // *****************************************************************************************************************
    // HELPERS
    // *****************************************************************************************************************

    protected function verifyImage(UploadedFile $file): bool
    {
        $mime_type = explode('/', $file->getMimeType())[0];

        if ($mime_type === 'image')
        {
            return true;
        }

        return false;
    }

    protected function setNewFile(UploadedFile $file): void
    {
        $this->new_file = $file;
    }

    protected function convertImageToWebPUsingCWebP(UploadedFile $file): bool
    {
        try
        {
            Log::info("$this->log_tag Converting image to webp using CWebP");

            $output_path = $file->getPath().'/';

            $cWebPService = new ConvertImageToWebP;
            $cWebPService->setOutputPath($output_path);
            $cWebPService->handle(new ConvertImageToWebPDTO(
                file : $file,
            ))->executeCommand();

            $this->new_file = new UploadedFile($cWebPService->getOutputFilePath(), $file->getFilename().'.webp', 'image/webp', null, true);

            Log::info("$this->log_tag Conversion Successful");

            return true;
        } catch (\Exception $e)
        {
            Log::error("$this->log_tag Conversion Failed: ".$e->getMessage());

            return false;
        }
    }
}
