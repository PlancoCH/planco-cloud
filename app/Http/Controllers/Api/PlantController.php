<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Plant\StorePlantRequest;
use App\Http\Requests\Api\Plant\UpdatePlantRequest;
use App\Http\Requests\Api\Plant\MapDeviceToPlantRequest;
use App\Http\Resources\Api\PlantResource;
use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PlantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $plants = $request->user()->plants()->with(['plantType', 'device'])->get();
        return PlantResource::collection($plants);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePlantRequest $request)
    {
        $plant = Plant::create($request->validated());
        
        $request->user()->plants()->attach($plant->id, ['role' => 'owner']);

        $plant->load(['plantType', 'device']);
        
        return new PlantResource($plant);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Plant $plant)
    {
        if (!$request->user()->plants()->where('plants.id', $plant->id)->exists()) {
            abort(403, 'Unauthorized access to this plant.');
        }

        $plant->load(['plantType', 'device']);

        return new PlantResource($plant);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlantRequest $request, Plant $plant)
    {
        if (!$request->user()->plants()->where('plants.id', $plant->id)->exists()) {
            abort(403, 'Unauthorized access to this plant.');
        }

        $plant->update($request->validated());

        return new PlantResource($plant);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Plant $plant)
    {
        if (!$request->user()->plants()->where('plants.id', $plant->id)->exists()) {
            abort(403, 'Unauthorized access to this plant.');
        }

        $plant->delete();

        return response()->noContent();
    }

    /**
     * Map a user's device to a plant.
     */
    public function map(MapDeviceToPlantRequest $request, Plant $plant)
    {
        if (!$request->user()->plants()->where('plants.id', $plant->id)->exists()) {
            abort(403, 'Unauthorized access to this plant.');
        }

        $device = $request->user()->devices()->find($request->device_id);

        if (!$device) {
            return response()->json(['message' => 'Device not found or not owned by user.'], 404);
        }

        $plant->update(['device_id' => $device->id]);

        $plant->load(['plantType', 'device']);

        return new PlantResource($plant);
    }

    /**
     * Unmap the currently attached device from a plant.
     */
    public function unmap(Request $request, Plant $plant)
    {
        if (!$request->user()->plants()->where('plants.id', $plant->id)->exists()) {
            abort(403, 'Unauthorized access to this plant.');
        }

        $plant->update(['device_id' => null]);

        return response()->json(['message' => 'Device unmapped from plant successfully.']);
    }
}
