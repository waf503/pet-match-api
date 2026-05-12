<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'nombre', 'especie', 'raza', 'edad', 'descripcion', 'foto'])]
class Pet extends Model
{
    protected $casts = [
        'raza' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(PetPhoto::class)->orderBy('orden');
    }

    public function likedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'pet_likes')->withTimestamps();
    }
}