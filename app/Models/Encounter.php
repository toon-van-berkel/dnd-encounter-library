<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Model;

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

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
    public function type(): BelongsTo {
        return $this->belongsTo(EncounterType::class, 'type_id');
    }
    public function difficulty(): BelongsTo {
        return $this->belongsTo(EncounterDifficulty::class, 'difficulty_id');
    }
}