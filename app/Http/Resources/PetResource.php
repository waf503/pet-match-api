<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Obtener fotos desde la relación si está cargada
        $fotosRelation = $this->whenLoaded('photos', function () {
            return $this->photos->map(fn ($p) => [
                'id'  => $p->id,
                'url' => Storage::disk('public')->url($p->path),
            ])->values();
        });

        // foto principal: primera foto de la relación o la columna foto de respaldo
        $fotoUrl = $this->whenLoaded(
            'photos',
            function () {
                $first = $this->photos->first();
                return $first
                    ? Storage::disk('public')->url($first->path)
                    : ($this->foto ? Storage::disk('public')->url($this->foto) : null);
            },
            $this->foto ? Storage::disk('public')->url($this->foto) : null
        );

        return [
            'id'          => $this->id,
            'nombre'      => $this->nombre,
            'especie'     => $this->especie,
            'raza'        => $this->raza,
            'edad'        => $this->edad,
            'descripcion' => $this->descripcion,
            'foto'        => $fotoUrl,
            'fotos'       => $fotosRelation,
            'owner'       => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
            ],
            'distance_km' => $this->whenNotNull($this->distance_km ?? null),
            'created_at'  => $this->created_at->toDateString(),
        ];
    }
}