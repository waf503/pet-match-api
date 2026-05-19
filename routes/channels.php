<?php

use App\Models\PetMatch;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Canal de presencia del match
|--------------------------------------------------------------------------
| Solo los dos dueños involucrados pueden suscribirse.
|
| Devolvemos un ARRAY con info del usuario (en lugar de bool) → Laravel/Reverb
| trata este canal como "presence channel", lo que permite:
|   - Saber en tiempo real quién está suscrito (presencia "En línea")
|   - Recibir eventos `pusher_internal:member_added` / `member_removed`
|   - Mantener los mismos broadcasts que un canal privado normal
|
| El cliente se suscribe como `presence-match.{matchId}`.
*/
Broadcast::channel('match.{matchId}', function ($user, $matchId) {
    $match = PetMatch::find($matchId);
    if (! $match || ($match->user_a_id !== $user->id && $match->user_b_id !== $user->id)) {
        return null; // no autorizado
    }
    return ['id' => $user->id, 'name' => $user->name];
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
