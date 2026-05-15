<?php

use App\Models\PetMatch;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Canal privado del match
|--------------------------------------------------------------------------
| Solo los dos dueños involucrados en el match pueden suscribirse.
| Retorna true = autorizado, false = denegado.
*/
Broadcast::channel('match.{matchId}', function ($user, $matchId) {
    $match = PetMatch::find($matchId);
    return $match && ($match->user_a_id === $user->id || $match->user_b_id === $user->id);
});
