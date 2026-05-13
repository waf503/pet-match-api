<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $fotosRelation = $this->whenLoaded('photos', function () {
            return $this->photos->map(fn ($p) => [
                'id'  => $p->id,
                'url' => Storage::disk('public')->url($p->path),
            ])->values();
        });

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
            'raza'        => is_array($this->raza) ? $this->raza : (($this->raza && $this->raza !== '') ? [$this->raza] : []),
            'edad'        => $this->edad,
            'descripcion' => $this->descripcion,
            'foto'        => $fotoUrl,
            'fotos'       => $fotosRelation,
            'liked'       => (bool) ($this->liked ?? false),
            'likes_count' => (int) ($this->likes_count ?? 0),
            'owner'       => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
            ],
            'distance_km'  => $this->whenNotNull($this->distance_km ?? null),
            'created_at'   => $this->created_at->toDateString(),
            // Estado del match desde la perspectiva del usuario autenticado
            // Solo presente cuando se carga el detalle (show), no en listados
            'match_status' => $this->match_status ?? null,  // none|pending_sent|matched
            'match_id'     => $this->match_id     ?? null,
            'proposal_id'  => $this->proposal_id  ?? null,
        ];
    }
}