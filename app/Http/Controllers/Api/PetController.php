<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePetRequest;
use App\Http\Requests\UpdatePetRequest;
use App\Http\Resources\PetResource;
use App\Models\Pet;
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
            ->with('user')
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

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('pets', 'public');
        }

        $pet = $request->user()->pets()->create($data);

        return response()->json(
            new PetResource($pet->load('user')),
            201
        );
    }

    /**
     * Muestra el detalle de una mascota.
     * Cualquier usuario autenticado puede ver cualquier mascota.
     * Solo el dueño puede editarla o eliminarla.
     */
    public function show(Request $request, Pet $pet): JsonResponse
    {
        return response()->json(new PetResource($pet->load('user')));
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

        if ($request->hasFile('foto')) {
            // Elimina la foto anterior si existe
            if ($pet->foto) {
                Storage::disk('public')->delete($pet->foto);
            }
            $data['foto'] = $request->file('foto')->store('pets', 'public');
        }

        $pet->update($data);

        return response()->json(new PetResource($pet->load('user')));
    }

    /**
     * Elimina una mascota y su foto asociada.
     */
    public function destroy(Request $request, Pet $pet): JsonResponse
    {
        if ($pet->user_id !== $request->user()->id) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        if ($pet->foto) {
            Storage::disk('public')->delete($pet->foto);
        }

        $pet->delete();

        return response()->json(['message' => 'Mascota eliminada correctamente.']);
    }
}
