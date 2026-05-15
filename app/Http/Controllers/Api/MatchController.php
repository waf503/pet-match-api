<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\PetMatch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MatchController extends Controller
{
    // GET /matches — mis matches activos
    public function index(Request $request): JsonResponse
    {
        $userId  = $request->user()->id;

        $matches = PetMatch::where(function ($q) use ($userId) {
                $q->where('user_a_id', $userId)->orWhere('user_b_id', $userId);
            })
            ->where('status', 'active')
            ->with([
                'petA:id,nombre,foto',
                'petB:id,nombre,foto',
                'userA:id,name,foto',
                'userB:id,name,foto',
            ])
            ->withCount(['messages as unread_count' => function ($q) use ($userId) {
                $q->where('user_id', '!=', $userId)->whereNull('read_at');
            }])
            ->latest('updated_at')
            ->get()
            ->map(function (PetMatch $m) use ($userId) {
                $lastMsg   = $m->messages()->latest()->first();
                $otherPet  = $m->user_a_id === $userId ? $m->petB  : $m->petA;
                $otherUser = $m->user_a_id === $userId ? $m->userB : $m->userA;
                return [
                    'id'         => $m->id,
                    'status'     => $m->status,
                    'created_at' => $m->created_at,
                    'other_pet'  => [
                        'id'     => $otherPet->id,
                        'nombre' => $otherPet->nombre,
                        'foto'   => $otherPet->foto
                            ? Storage::disk('public')->url($otherPet->foto)
                            : null,
                    ],
                    'other_user'   => ['id' => $otherUser->id, 'name' => $otherUser->name],
                    'unread_count' => $m->unread_count,
                    'last_message' => $lastMsg ? [
                        'body'       => $lastMsg->body,
                        'created_at' => $lastMsg->created_at,
                        'is_mine'    => $lastMsg->user_id === $userId,
                    ] : null,
                ];
            });

        return response()->json($matches);
    }

    // GET /matches/{match}
    public function show(Request $request, PetMatch $match): JsonResponse
    {
        $userId = $request->user()->id;
        if ($match->user_a_id !== $userId && $match->user_b_id !== $userId) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $match->load(['petA:id,nombre,foto', 'petB:id,nombre,foto', 'userA:id,name', 'userB:id,name']);

        $fotoUrl = fn ($pet) => $pet && $pet->foto
            ? Storage::disk('public')->url($pet->foto)
            : null;

        return response()->json([
            'id'       => $match->id,
            'status'   => $match->status,
            'pet_a'    => ['id' => $match->petA->id, 'nombre' => $match->petA->nombre, 'foto' => $fotoUrl($match->petA)],
            'pet_b'    => ['id' => $match->petB->id, 'nombre' => $match->petB->nombre, 'foto' => $fotoUrl($match->petB)],
            'user_a'   => ['id' => $match->userA->id, 'name' => $match->userA->name],
            'user_b'   => ['id' => $match->userB->id, 'name' => $match->userB->name],
        ]);
    }

    // PATCH /matches/{match} — cerrar match
    public function update(Request $request, PetMatch $match): JsonResponse
    {
        $userId = $request->user()->id;
        if ($match->user_a_id !== $userId && $match->user_b_id !== $userId) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $data = $request->validate([
            'status'       => 'required|in:completed,cancelled',
            'close_reason' => 'nullable|string|max:255',
        ]);

        $match->update([
            'status'       => $data['status'],
            'close_reason' => $data['close_reason'] ?? null,
            'closed_by'    => $userId,
        ]);

        $otherUserId = $match->user_a_id === $userId ? $match->user_b_id : $match->user_a_id;

        AppNotification::create([
            'user_id' => $otherUserId,
            'type'    => 'match_closed',
            'data'    => [
                'match_id'     => $match->id,
                'status'       => $data['status'],
                'close_reason' => $data['close_reason'] ?? null,
            ],
        ]);

        return response()->json($match);
    }
}
