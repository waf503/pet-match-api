<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Breed;
use App\Models\Species;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function species(): JsonResponse
    {
        $species = Species::orderBy('orden')->get(['id', 'nombre', 'nombre_plural', 'icono']);
        return response()->json($species);
    }

    public function breeds(Request $request): JsonResponse
    {
        $speciesId = $request->query('species_id');

        $breeds = Breed::when($speciesId, fn($q) => $q->where('species_id', $speciesId))
            ->orderByDesc('popular')
            ->orderBy('nombre')
            ->get(['id', 'species_id', 'nombre', 'popular']);

        return response()->json($breeds);
    }
}
