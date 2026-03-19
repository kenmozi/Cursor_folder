<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ConferenceMedia extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'conference_id',
        'type',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size_bytes',
    ];

    protected $appends = ['url'];

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }
}
