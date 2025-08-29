<?php

namespace App\Services\FileManager\Helpers;

use App\Services\FileManager\Tools\CWebP\Contracts\ConvertImageToWebPComplex;
use App\Services\FileManager\Tools\CWebP\DTO\ConvertImageToWebPComplexDTO;
use App\Services\FileManager\Tools\CWebP\DTO\FilterOptions\CWebPResizeDTO;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class ImageResizer
{
    protected string $log_tag = '[ImageResize]';

    protected UploadedFile $new_file;

    public function handle(UploadedFile $file): static
    {
        if (! $this->verifyImage($file))
        {
            $this->setNewFile($file);

            return $this;
        }

        $this->resizeImage($file);

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

    protected function resizeImage(UploadedFile $file): void
    {
        Log::info("$this->log_tag Resizing Image");

        $output_path = $file->getPath().'/';

        $cWebPService = new ConvertImageToWebPComplex;
        $cWebPService->setOutputPath($output_path);
        $cWebPService->handle(new ConvertImageToWebPComplexDTO(
            file      : $file,
            resizeDTO : new CWebPResizeDTO(
                resize_width  : 800,
                resize_height : 600
            )
        ))->executeCommand();

        $this->new_file = new UploadedFile($cWebPService->getOutputFilePath(), $file->getFilename().'.webp', 'image/webp', null, true);

        Log::info("$this->log_tag Resizing Success");
    }
}
