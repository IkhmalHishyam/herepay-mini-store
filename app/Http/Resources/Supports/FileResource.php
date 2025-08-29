<?php

namespace App\Http\Resources\Supports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @see \App\Models\Supports\File
 */
class FileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'fileable'  => $this->fileable,
            'file_name' => $this->file_name,
            'file_url'  => $this->file_url,
        ];
    }
}
