<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConferenceDateTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = ['conference_date_id', 'locale', 'label'];

    public function conferenceDate(): BelongsTo
    {
        return $this->belongsTo(ConferenceDate::class, 'conference_date_id');
    }
}
