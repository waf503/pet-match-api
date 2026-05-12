<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PetMatch extends Model
{
    protected $table = 'pet_matches';

    protected $fillable = [
        'proposal_id',
        'pet_a_id', 'pet_b_id',
        'user_a_id', 'user_b_id',
        'status', 'close_reason', 'closed_by',
    ];

    public function proposal(): BelongsTo { return $this->belongsTo(MatchProposal::class); }
    public function petA(): BelongsTo     { return $this->belongsTo(Pet::class, 'pet_a_id'); }
    public function petB(): BelongsTo     { return $this->belongsTo(Pet::class, 'pet_b_id'); }
    public function userA(): BelongsTo    { return $this->belongsTo(User::class, 'user_a_id'); }
    public function userB(): BelongsTo    { return $this->belongsTo(User::class, 'user_b_id'); }
    public function messages(): HasMany   { return $this->hasMany(MatchMessage::class, 'match_id'); }

    public function otherPet(int $userId): Pet
    {
        return $this->user_a_id === $userId ? $this->petB : $this->petA;
    }

    public function otherUser(int $userId): User
    {
        return $this->user_a_id === $userId ? $this->userB : $this->userA;
    }

    public function unreadCount(int $userId): int
    {
        return $this->messages()
            ->where('user_id', '!=', $userId)
            ->whereNull('read_at')
            ->count();
    }
}
