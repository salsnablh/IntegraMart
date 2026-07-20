<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ObjectFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'disk',
        'bucket',
        'path',
        'filename',
        'mime_type',
        'size',
        'related_type',
        'related_id',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
        ];
    }

    public function related(): MorphTo
    {
        return $this->morphTo();
    }
}
