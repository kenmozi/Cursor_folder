<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConferenceTranslation extends Model
{
    protected $fillable = [
        'conference_id',
        'locale',
        'title',
        'subtitle',
        'description',
        'cfp_text',
        'venue_text',
        'contact_text',
    ];

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class);
    }
}
