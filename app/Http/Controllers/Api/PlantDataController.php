<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plant;
use App\Models\PlantData;
use App\Http\Resources\PlantDataResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PlantDataController extends Controller
{
    /**
     * Display a time-filterable listing of the plant data.
     */
    public function index(Request $request, Plant $plant): AnonymousResourceCollection
    {
        // Verify user has access to this plant
        if (!$request->user()->plants()->where('plants.id', $plant->id)->exists()) {
            abort(403, 'Unauthorized access to this plant.');
        }

        $query = $plant->data()->orderBy('created_at', 'desc');

        // Optional time filters in ISO-8601 or standard SQL format
        if ($request->has('start_date')) {
            $query->where('created_at', '>=', $request->input('start_date'));
        }

        if ($request->has('end_date')) {
            $query->where('created_at', '<=', $request->input('end_date'));
        }

        return PlantDataResource::collection($query->get());
    }

    /**
     * Display the specified plant data entry.
     */
    public function show(Request $request, Plant $plant, PlantData $plantData): PlantDataResource
    {
        // Verify user has access to this plant
        if (!$request->user()->plants()->where('plants.id', $plant->id)->exists()) {
            abort(403, 'Unauthorized access to this plant.');
        }

        // Verify the data entry belongs to this plant
        if ($plantData->plant_id !== $plant->id) {
            abort(404, 'Data not found for this plant.');
        }

        return new PlantDataResource($plantData);
    }
}
