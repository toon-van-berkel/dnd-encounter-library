<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Encounter extends Model {
    protected $fillable = [
        'user_id',
        'type_id',
        'difficulty_id',
        'title',
        'description',
        'min_level',
        'max_level',
        'party_size',
        'image_path',
        'is_active',
    ];

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<EncounterType, $this> */
    public function type(): BelongsTo {
        return $this->belongsTo(EncounterType::class, 'type_id');
    }

    /** @return BelongsTo<EncounterDifficulty, $this> */
    public function difficulty(): BelongsTo {
        return $this->belongsTo(EncounterDifficulty::class, 'difficulty_id');
    }

    /** @return HasMany<Favorite, $this> */
    public function favorites(): HasMany {
        return $this->hasMany(Favorite::class);
    }
}
