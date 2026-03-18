<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConferenceDate extends Model
{
    protected $fillable = [
        'conference_id',
        'date',
        'sort_order',
    ];

    protected function casts(): array
    {
        return ['date' => 'date'];
    }

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ConferenceDateTranslation::class, 'conference_date_id');
    }

    public function translation(string $locale = 'en'): ?ConferenceDateTranslation
    {
        $loaded = $this->translations;
        return $loaded->firstWhere('locale', $locale)
            ?? $loaded->firstWhere('locale', 'en')
            ?? $loaded->first();
    }
}
