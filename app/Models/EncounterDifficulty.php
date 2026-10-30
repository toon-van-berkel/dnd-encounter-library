<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EncounterDifficulty extends Model {
    protected $fillable = [
        'name',
    ];

    /** @return HasMany<Encounter, $this> */
    public function encounters(): HasMany {
        return $this->hasMany(Encounter::class, 'difficulty_id');
    }
}
