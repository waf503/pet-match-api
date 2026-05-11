<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Devuelve los datos del usuario autenticado.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'bio'   => $user->bio,
            'foto'  => $user->foto
                          ? Storage::disk('public')->url($user->foto)
                          : null,
        ]);
    }

    /**
     * Actualiza nombre, bio y/o foto de perfil.
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name'  => ['sometimes', 'string', 'max:100'],
            'bio'   => ['nullable', 'string', 'max:300'],
            'email' => ['sometimes', 'email', Rule::unique('users')->ignore($user->id)],
            'foto'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if ($request->hasFile('foto')) {
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }
            $data['foto'] = $request->file('foto')->store('avatars', 'public');
        }

        $user->update($data);

        return response()->json([
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'bio'   => $user->bio,
            'foto'  => $user->foto
                          ? Storage::disk('public')->url($user->foto)
                          : null,
        ]);
    }
}