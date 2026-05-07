<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PetResource;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FeedController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $authUser = $request->user();
        $especie  = $request->query('especie');

        $pets = Pet::with('user')
            ->where('user_id', '!=', $authUser->id)
            ->whereNotNull('foto')
            ->when($especie, fn ($q) => $q->where('especie', $especie))
            ->get();

        // Ordenar por distancia si el usuario tiene ubicación guardada
        $authLat = (float) $authUser->latitude;
        $authLng = (float) $authUser->longitude;

        if ($authLat && $authLng) {
            $pets = $pets
                ->map(function (Pet $pet) use ($authLat, $authLng) {
                    $ownerLat = (float) $pet->user->latitude;
                    $ownerLng = (float) $pet->user->longitude;

                    $pet->distance_km = ($ownerLat && $ownerLng)
                        ? round($this->haversine($authLat, $authLng, $ownerLat, $ownerLng), 1)
                        : null;

                    return $pet;
                })
                ->sortBy(fn ($pet) => $pet->distance_km ?? PHP_INT_MAX)
                ->values();
        }

        return PetResource::collection($pets);
    }

    private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $R    = 6371; // Radio de la Tierra en km
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return $R * 2 * asin(sqrt($a));
    }
}
