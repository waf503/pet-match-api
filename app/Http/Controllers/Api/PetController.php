<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePetRequest;
use App\Http\Requests\UpdatePetRequest;
use App\Http\Resources\PetResource;
use App\Models\Pet;
use App\Models\PetPhoto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
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
        unset($data['fotos']);

        $pet = $request->user()->pets()->create($data);

        // Guardar fotos en pet_photos y sincronizar pets.foto con la primera
        foreach ($request->file('fotos') as $i => $file) {
            $path = $file->store('pets', 'public');
            $pet->photos()->create(['path' => $path, 'orden' => $i]);
            if ($i === 0) {
                $pet->update(['foto' => $path]);
            }
        }

        return response()->json(
            new PetResource($pet->load('user', 'photos')),
            201
        );
    }

    /**
     * Muestra el detalle de una mascota.
     */
    public function show(Request $request, Pet $pet): JsonResponse
    {
        return response()->json(new PetResource($pet->load('user', 'photos')));
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
        unset($data['fotos'], $data['delete_photo_ids']);

        // Eliminar fotos solicitadas
        if ($request->has('delete_photo_ids')) {
            foreach ((array) $request->input('delete_photo_ids') as $photoId) {
                $photo = $pet->photos()->find($photoId);
                if ($photo) {
                    Storage::disk('public')->delete($photo->path);
                    $photo->delete();
                }
            }
        }

        // Agregar nuevas fotos (respetando el límite de 3 en total)
        if ($request->hasFile('fotos')) {
            $currentCount = $pet->photos()->count();
            foreach ($request->file('fotos') as $file) {
                if ($currentCount >= 3) break;
                $path = $file->store('pets', 'public');
                $pet->photos()->create(['path' => $path, 'orden' => $currentCount]);
                $currentCount++;
            }
        }

        // Sincronizar pets.foto con la primera foto disponible
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

        // Sincronizar pets.foto
        $firstPhoto = $pet->photos()->orderBy('orden')->first();
        $pet->update(['foto' => $firstPhoto ? $firstPhoto->path : null]);

        return response()->json(['message' => 'Foto eliminada.']);
    }
}