<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'nombre'      => $this->nombre,
            'especie'     => $this->especie,
            'raza'        => $this->raza,
            'edad'        => $this->edad,
            'descripcion' => $this->descripcion,
            'foto'        => $this->foto
                                ? Storage::disk('public')->url($this->foto)
                                : null,
            'owner'       => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
            ],
            'distance_km' => $this->whenNotNull($this->distance_km ?? null),
            'created_at'  => $this->created_at->toDateString(),
        ];
    }
}
