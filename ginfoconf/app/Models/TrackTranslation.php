<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['track_id', 'locale', 'name', 'description'];

    public function track(): BelongsTo
    {
        return $this->belongsTo(Track::class);
    }
}
