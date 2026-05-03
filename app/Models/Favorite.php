<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{

    protected $casts = [
        'verse_ids' => 'array', // или 'json' или 'object'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'verse_ids',
        'created_at',
        'updated_at',
    ];
}
