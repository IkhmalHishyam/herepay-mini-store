<?php

namespace App\Models\Supports;

use App\Models\Orders\OrderProduct;
use Illuminate\Database\Eloquent\Model;
use App\Services\FileManager\FileManager;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Services\FileManager\Enums\FileablePrefixes;
use App\Services\FileManager\Enums\FileAccessLevelEnum;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class File extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = [
        'fileable',
        'file_name',
        'file_path',
        'file_size',
        'file_extension',
        'file_mime',
        'access_level',
        'driver',

        // Polymorphic
        'fileable_id',
        'fileable_type',
    ];

    protected $casts = [
        'fileable'       => FileablePrefixes::class,
        'file_name'      => 'string',
        'file_path'      => 'string',
        'file_size'      => 'float',
        'file_extension' => 'string',
        'file_mime'      => 'string',
        'access_level'   => 'string',
        'driver'         => 'string',
        'fileable_id'    => 'string',
        'fileable_type'  => 'string',
    ];

    protected $appends = [
        'file_url',
    ];

    // *****************************************************************************************************************
    // RELATIONSHIPS
    // *****************************************************************************************************************

    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }

    public function orderProducts(): MorphToMany
    {
        return $this->morphedByMany(OrderProduct::class, 'snapshotable', 'file_snapshots');
    }

    // *****************************************************************************************************************
    // ACCESSORS
    // *****************************************************************************************************************

    public function getFileUrlAttribute(): ?string
    {
        $fileManagerService = new FileManager;

        return $this->access_level === FileAccessLevelEnum::PUBLIC->value
            ? $fileManagerService->generatePublicURL($this->file_path, false)
            : $fileManagerService->generatePreSignedURL($this->file_path);
    }
}
