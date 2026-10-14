<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EncounterType extends Model {
    protected $fillable = [
        'name',
    ];

    public function encounters(): HasMany {
        return $this->hasMany(Encounter::class, 'type_id');
    }
}