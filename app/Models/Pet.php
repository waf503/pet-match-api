<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'nombre', 'especie', 'raza', 'edad', 'descripcion', 'foto'])]
class Pet extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
