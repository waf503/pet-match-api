<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageSent;
use App\Events\NewMessageNotification;
use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\MatchMessage;
use App\Models\PetMatch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    // GET /matches/{match}/messages
    public function index(Request $request, PetMatch $match): JsonResponse
    {
        $userId = $request->user()->id;
        if ($match->user_a_id !== $userId && $match->user_b_id !== $userId) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        // Marcar como leídos los mensajes del otro usuario
        $match->messages()
            ->where('user_id', '!=', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = $match->messages()
            ->with('user:id,name,foto')
            ->oldest()
            ->get();

        // Normalizar la foto a URL pública (consistente con el broadcast MessageSent).
        // Defensivo: si foto ya viene como URL absoluta (http://… / https://…)
        // no la envolvemos otra vez. Si es path relativo, lo resolvemos contra
        // el disco "public". Si es null o cadena vacía, lo dejamos en null.
        $messages->each(function (MatchMessage $msg) {
            if (! $msg->user) return;
            $foto = $msg->user->foto;
            if (empty($foto)) {
                $msg->user->foto = null;
                return;
            }
            if (str_starts_with($foto, 'http://') || str_starts_with($foto, 'https://')) {
                return; // ya es absoluta
            }
            $msg->user->foto = Storage::disk('public')->url($foto);
        });

        return response()->json($messages);
    }

    // POST /matches/{match}/messages
    public function store(Request $request, PetMatch $match): JsonResponse
    {
        $userId = $request->user()->id;

        if ($match->user_a_id !== $userId && $match->user_b_id !== $userId) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }
        if ($match->status !== 'active') {
            return response()->json(['message' => 'Este match ya fue cerrado.'], 422);
        }

        $data = $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $message = MatchMessage::create([
            'match_id' => $match->id,
            'user_id'  => $userId,
            'body'     => $data['body'],
        ]);

        $otherUserId = $match->user_a_id === $userId ? $match->user_b_id : $match->user_a_id;

        AppNotification::create([
            'user_id' => $otherUserId,
            'type'    => 'new_message',
            'data'    => [
                'match_id'     => $match->id,
                'sender_name'  => $request->user()->name,
                'body_preview' => mb_substr($data['body'], 0, 60),
            ],
        ]);

        // Touch match para updated_at (ordenamiento en la lista de matches)
        $match->touch();

        // Cargar relación user antes de emitir (broadcastWith la necesita)
        $message->load('user');

        // ─── Tiempo real: dos canales, dos propósitos ─────────────────────────
        //
        // 1) MessageSent → canal del match
        //    Lo consumen solo los dos participantes mientras tienen la pantalla
        //    de chat abierta y suscrita a private-match.{id}.
        broadcast(new MessageSent($message))->toOthers();

        // 2) NewMessageNotification → canal personal del destinatario
        //    Vive durante TODA la sesión autenticada (BadgeContext), así que
        //    actualiza badges/contadores estando en cualquier pantalla.
        //    Calculamos el total autoritativo aquí para que el cliente solo
        //    asigne (single source of truth).
        $unreadTotal = MatchMessage::whereHas('match', function ($q) use ($otherUserId) {
                $q->where('user_a_id', $otherUserId)
                  ->orWhere('user_b_id', $otherUserId);
            })
            ->where('user_id', '!=', $otherUserId)
            ->whereNull('read_at')
            ->count();

        $unreadInMatch = $match->messages()
            ->where('user_id', '!=', $otherUserId)
            ->whereNull('read_at')
            ->count();

        broadcast(new NewMessageNotification(
            recipientId:   $otherUserId,
            message:       $message,
            unreadTotal:   $unreadTotal,
            unreadInMatch: $unreadInMatch,
        ));

        return response()->json($message, 201);
    }
}
