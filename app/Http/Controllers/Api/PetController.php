<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePetRequest;
use App\Http\Requests\UpdatePetRequest;
use App\Http\Resources\PetResource;
use App\Models\MatchProposal;
use App\Models\Pet;
use App\Models\PetMatch;
use App\Models\PetPhoto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PetController extends Controller
{
    /**
     * Lista las mascotas del usuario autenticado.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $pets = $request->user()
            ->pets()
            ->with('user', 'photos')
            ->withCount('likedByUsers as likes_count')
            ->latest()
            ->get();

        return PetResource::collection($pets);
    }

    /**
     * Registra una nueva mascota para el usuario autenticado.
     */
    public function store(StorePetRequest $request): JsonResponse
    {
        $data = $request->validated();
        unset($data['fotos'], $data['fotos_base64']);
        $data['raza'] = $data['raza'] ?? [];

        $pet = $request->user()->pets()->create($data);

        foreach ($request->input('fotos_base64', []) as $i => $b64) {
            $imageData = base64_decode($b64);
            $filename  = 'pets/' . \Illuminate\Support\Str::uuid() . '.jpg';
            \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $imageData);
            $pet->photos()->create(['path' => $filename, 'orden' => $i]);
            if ($i === 0) {
                $pet->update(['foto' => $filename]);
            }
        }

        return response()->json(
            new PetResource($pet->load('user', 'photos')),
            201
        );
    }

    /**
     * Muestra el detalle de una mascota, incluyendo el estado del match
     * desde la perspectiva del usuario autenticado.
     */
    public function show(Request $request, Pet $pet): JsonResponse
    {
        $userId = $request->user()->id;

        $pet->load('user', 'photos');
        $pet->loadCount('likedByUsers as likes_count');
        $pet->liked = $pet->likedByUsers()->where('user_id', $userId)->exists();

        // ── Estado de match desde la perspectiva del auth user ────────────────
        $matchStatus = 'none';
        $matchId     = null;
        $proposalId  = null;

        // ¿Ya hay un match activo entre alguna mascota del user y esta?
        $activeMatch = PetMatch::where(function ($q) use ($pet) {
                $q->where('pet_a_id', $pet->id)->orWhere('pet_b_id', $pet->id);
            })
            ->where(function ($q) use ($userId) {
                $q->where('user_a_id', $userId)->orWhere('user_b_id', $userId);
            })
            ->where('status', 'active')
            ->first();

        if ($activeMatch) {
            $matchStatus = 'matched';
            $matchId     = $activeMatch->id;
        } else {
            // ¿Hay una propuesta pendiente que esta mascota le envió al auth user?
            $pendingReceived = MatchProposal::where('from_pet_id', $pet->id)
                ->where('to_user_id', $userId)
                ->where('status', 'pending')
                ->first();

            if ($pendingReceived) {
                $matchStatus = 'pending_received';
                $proposalId  = $pendingReceived->id;
            } else {
                // ¿Hay una propuesta pendiente enviada por el auth user hacia esta mascota?
                $pendingSent = MatchProposal::where('to_pet_id', $pet->id)
                    ->where('from_user_id', $userId)
                    ->where('status', 'pending')
                    ->first();

                if ($pendingSent) {
                    $matchStatus = 'pending_sent';
                    $proposalId  = $pendingSent->id;
                }
            }
        }

        $pet->match_status = $matchStatus;
        $pet->match_id     = $matchId;
        $pet->proposal_id  = $proposalId;

        return response()->json(new PetResource($pet));
    }

    /**
     * Actualiza los datos de una mascota.
     */
    public function update(UpdatePetRequest $request, Pet $pet): JsonResponse
    {
        if ($pet->user_id !== $request->user()->id) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $data = $request->validated();
        unset($data['fotos'], $data['fotos_base64'], $data['delete_photo_ids']);
        $data['raza'] = $data['raza'] ?? [];

        if ($request->has('delete_photo_ids')) {
            foreach ((array) $request->input('delete_photo_ids') as $photoId) {
                $photo = $pet->photos()->find($photoId);
                if ($photo) {
                    Storage::disk('public')->delete($photo->path);
                    $photo->delete();
                }
            }
        }

        foreach ($request->input('fotos_base64', []) as $b64) {
            $currentCount = $pet->photos()->count();
            if ($currentCount >= 3) break;
            $imageData = base64_decode($b64);
            $filename  = 'pets/' . \Illuminate\Support\Str::uuid() . '.jpg';
            \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $imageData);
            $pet->photos()->create(['path' => $filename, 'orden' => $currentCount]);
        }

        $firstPhoto = $pet->photos()->orderBy('orden')->first();
        $data['foto'] = $firstPhoto ? $firstPhoto->path : null;

        $pet->update($data);

        return response()->json(new PetResource($pet->fresh()->load('user', 'photos')));
    }

    /**
     * Elimina una mascota y todas sus fotos.
     */
    public function destroy(Request $request, Pet $pet): JsonResponse
    {
        if ($pet->user_id !== $request->user()->id) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        foreach ($pet->photos as $photo) {
            Storage::disk('public')->delete($photo->path);
        }

        if ($pet->foto) {
            Storage::disk('public')->delete($pet->foto);
        }

        $pet->delete();

        return response()->json(['message' => 'Mascota eliminada correctamente.']);
    }

    /**
     * Elimina una foto individual de una mascota.
     */
    public function destroyPhoto(Request $request, Pet $pet, PetPhoto $photo): JsonResponse
    {
        if ($pet->user_id !== $request->user()->id) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        if ((int) $photo->pet_id !== $pet->id) {
            return response()->json(['message' => 'La foto no pertenece a esta mascota.'], 403);
        }

        Storage::disk('public')->delete($photo->path);
        $photo->delete();

        $firstPhoto = $pet->photos()->orderBy('orden')->first();
        $pet->update(['foto' => $firstPhoto ? $firstPhoto->path : null]);

        return response()->json(['message' => 'Foto eliminada.']);
    }

    /**
     * Devuelve las mascotas que el usuario autenticado ha marcado como favoritas.
     */
    public function likedPets(Request $request): AnonymousResourceCollection
    {
        $pets = $request->user()
            ->likedPets()
            ->with('user', 'photos')
            ->withCount('likedByUsers as likes_count')
            ->latest('pet_likes.created_at')
            ->get()
            ->each(function (Pet $pet) {
                $pet->liked = true;
            });

        return PetResource::collection($pets);
    }

    /**
     * Alterna el like del usuario autenticado en una mascota.
     */
    public function toggleLike(Request $request, Pet $pet): JsonResponse
    {
        $userId = $request->user()->id;

        $result = $pet->likedByUsers()->toggle($userId);
        $liked  = count($result['attached']) > 0;

        return response()->json([
            'liked'       => $liked,
            'likes_count' => $pet->likedByUsers()->count(),
        ]);
    }
}
