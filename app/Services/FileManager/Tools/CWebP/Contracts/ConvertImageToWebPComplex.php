<?php

namespace App\Services\FileManager\Tools\CWebP\Contracts;

use App\Services\FileManager\Tools\CWebP\CWebPFoundation;
use App\Services\FileManager\Tools\CWebP\DTO\ConvertImageToWebPComplexDTO;
use App\Services\FileManager\Tools\CWebP\DTO\FilterOptions\CWebPCropMapDTO;
use App\Services\FileManager\Tools\CWebP\DTO\FilterOptions\CWebPResizeDTO;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class ConvertImageToWebPComplex extends CWebPFoundation
{
    public function handle(ConvertImageToWebPComplexDTO $serviceDTO): static
    {
        $this->setResize($serviceDTO->resizeDTO);
        $this->setCropMap($serviceDTO->cropMapDTO);
        $this->setQuality($serviceDTO->quality);
        $this->setInput($serviceDTO->file);
        $this->setOutput($serviceDTO->file_name, $serviceDTO->extension);

        Log::info("Command : $this->command");

        return $this;
    }

    // *****************************************************************************************************************
    // GETTERS
    // *****************************************************************************************************************

    public function getOutputFilePath(): string
    {
        return $this->output_file_path;
    }

    // *****************************************************************************************************************
    // HELPERS
    // *****************************************************************************************************************

    protected function setResize(?CWebPResizeDTO $resizeDTO): void
    {
        if ($resizeDTO)
        {
            $this->command .= " -resize $resizeDTO->resize_width $resizeDTO->resize_height";
        }
    }

    protected function setCropMap(?CWebPCropMapDTO $cropMapDTO): void
    {
        if ($cropMapDTO)
        {
            $this->command .= " -crop $cropMapDTO->crop_width $cropMapDTO->crop_height $cropMapDTO->crop_x $cropMapDTO->crop_y";
        }
    }

    protected function setQuality(int $quality): void
    {
        $this->command .= " -q $quality";
    }

    protected function setInput(UploadedFile|string $file): void
    {
        $file instanceof UploadedFile
            ? $this->command .= " {$file->getRealPath()}"
            : $this->command .= " $file";
    }

    protected function setOutput(string $file_name, string $extension): void
    {
        $this->output_file_path = "{$this->output_path}/{$file_name}.{$extension}";

        $this->command .= " -o $this->output_file_path";
    }
}
