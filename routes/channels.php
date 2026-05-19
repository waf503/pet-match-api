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

/*
|--------------------------------------------------------------------------
| Canal privado personal del usuario
|--------------------------------------------------------------------------
| Cada usuario autenticado puede suscribirse SOLO a su propio canal.
| Se usa para eventos globales que deben llegar al usuario sin importar
| en qué pantalla esté (badges en tiempo real, notificaciones de mensaje
| cuando no está dentro del chat, propuestas recibidas, etc.).
*/
Broadcast::channel('user.{userId}', function ($user, int $userId) {
    return (int) $user->id === $userId;
});
