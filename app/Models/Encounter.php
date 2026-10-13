<?php

namespace App\Models;

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
}
