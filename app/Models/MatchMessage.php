<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchMessage extends Model
{
    protected $table = 'match_messages';

    protected $fillable = ['match_id', 'user_id', 'body', 'read_at'];

    protected $casts = ['read_at' => 'datetime'];

    public function match(): BelongsTo { return $this->belongsTo(PetMatch::class, 'match_id'); }
    public function user(): BelongsTo  { return $this->belongsTo(User::class); }
}
