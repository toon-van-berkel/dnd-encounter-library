<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorite extends Model {
    protected $fillable = [
        'encounter_id',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function encounter(): BelongsTo {
        return $this->belongsTo(Encounter::class);
    }
}
