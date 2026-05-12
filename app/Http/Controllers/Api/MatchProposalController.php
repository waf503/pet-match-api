<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\MatchProposal;
use App\Models\PetMatch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MatchProposalController extends Controller
{
    // GET /match-proposals — propuestas recibidas pendientes
    public function index(Request $request): JsonResponse
    {
        $proposals = MatchProposal::where('to_user_id', $request->user()->id)
            ->where('status', 'pending')
            ->with([
                'fromPet:id,nombre,foto',
                'fromUser:id,name,foto',
            ])
            ->latest()
            ->get();

        return response()->json($proposals);
    }

    // POST /match-proposals — enviar propuesta
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'from_pet_id' => 'required|exists:pets,id',
            'to_pet_id'   => 'required|exists:pets,id',
        ]);

        $user = $request->user();

        // from_pet debe pertenecer al usuario autenticado
        $fromPet = \App\Models\Pet::findOrFail($data['from_pet_id']);
        if ($fromPet->user_id !== $user->id) {
            return response()->json(['message' => 'No puedes proponer con una mascota ajena.'], 403);
        }

        // to_pet no puede pertenecer al mismo usuario
        $toPet = \App\Models\Pet::findOrFail($data['to_pet_id']);
        if ($toPet->user_id === $user->id) {
            return response()->json(['message' => 'No puedes hacer match con tu propia mascota.'], 400);
        }

        // Sin duplicados pendientes entre las mismas mascotas
        $exists = MatchProposal::where('from_pet_id', $data['from_pet_id'])
            ->where('to_pet_id', $data['to_pet_id'])
            ->where('status', 'pending')
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Ya enviaste una propuesta pendiente para estas mascotas.'], 400);
        }

        $proposal = MatchProposal::create([
            'from_pet_id'  => $data['from_pet_id'],
            'from_user_id' => $user->id,
            'to_pet_id'    => $data['to_pet_id'],
            'to_user_id'   => $toPet->user_id,
        ]);

        AppNotification::create([
            'user_id' => $toPet->user_id,
            'type'    => 'proposal_received',
            'data'    => [
                'proposal_id'   => $proposal->id,
                'from_pet_name' => $fromPet->nombre,
                'to_pet_name'   => $toPet->nombre,
                'from_user_name'=> $user->name,
            ],
        ]);

        return response()->json($proposal->load(['fromPet:id,nombre,foto', 'fromUser:id,name']), 201);
    }

    // PATCH /match-proposals/{proposal} — aceptar o rechazar
    public function update(Request $request, MatchProposal $proposal): JsonResponse
    {
        $user = $request->user();

        if ($proposal->to_user_id !== $user->id) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $data = $request->validate([
            'action' => 'required|in:accept,reject',
        ]);

        if ($data['action'] === 'accept') {
            $proposal->update(['status' => 'accepted']);

            $match = PetMatch::create([
                'proposal_id' => $proposal->id,
                'pet_a_id'    => $proposal->from_pet_id,
                'pet_b_id'    => $proposal->to_pet_id,
                'user_a_id'   => $proposal->from_user_id,
                'user_b_id'   => $proposal->to_user_id,
            ]);

            AppNotification::create([
                'user_id' => $proposal->from_user_id,
                'type'    => 'match_confirmed',
                'data'    => [
                    'match_id'    => $match->id,
                    'pet_a_name'  => $proposal->fromPet->nombre,
                    'pet_b_name'  => $proposal->toPet->nombre,
                ],
            ]);

            return response()->json($match->load(['petA:id,nombre,foto', 'petB:id,nombre,foto']));
        }

        // reject
        $proposal->update(['status' => 'rejected']);

        AppNotification::create([
            'user_id' => $proposal->from_user_id,
            'type'    => 'match_rejected',
            'data'    => [
                'from_pet_name' => $proposal->fromPet->nombre,
                'to_pet_name'   => $proposal->toPet->nombre,
            ],
        ]);

        return response()->json(['message' => 'Propuesta rechazada.']);
    }
}
