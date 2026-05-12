<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MatchProposal extends Model
{
    protected $fillable = [
        'from_pet_id', 'from_user_id',
        'to_pet_id',   'to_user_id',
        'status',
    ];

    public function fromPet(): BelongsTo  { return $this->belongsTo(Pet::class, 'from_pet_id'); }
    public function toPet(): BelongsTo    { return $this->belongsTo(Pet::class, 'to_pet_id'); }
    public function fromUser(): BelongsTo { return $this->belongsTo(User::class, 'from_user_id'); }
    public function toUser(): BelongsTo   { return $this->belongsTo(User::class, 'to_user_id'); }
    public function match(): HasOne       { return $this->hasOne(PetMatch::class, 'proposal_id'); }
}
