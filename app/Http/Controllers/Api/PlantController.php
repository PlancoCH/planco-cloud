<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Plant\StorePlantRequest;
use App\Http\Requests\Api\Plant\UpdatePlantRequest;
use App\Http\Requests\Api\Plant\MapDeviceToPlantRequest;
use App\Http\Requests\Api\Plant\JoinPlantRequest;
use App\Http\Resources\Api\PlantResource;
use App\Models\Plant;
use Illuminate\Support\Str;
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
        $userPlant = $request->user()->plants()->where('plants.id', $plant->id)->first();

        if (!$userPlant) {
            abort(403, 'Unauthorized access to this plant.');
        }

        $userPlant->load(['plantType', 'device']);

        return new PlantResource($userPlant);
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

    /**
     * Generate or retrieve a sharing token for the plant.
     */
    public function share(Request $request, Plant $plant)
    {
        if (!$request->user()->plants()->where('plants.id', $plant->id)->exists()) {
            abort(403, 'Unauthorized access to this plant.');
        }

        if (!$plant->sharing_token) {
            $plant->update(['sharing_token' => Str::random(32)]);
        }

        return response()->json(['sharing_token' => $plant->sharing_token]);
    }

    /**
     * Revoke the sharing token for the plant.
     */
    public function revokeShare(Request $request, Plant $plant)
    {
        if (!$request->user()->plants()->where('plants.id', $plant->id)->exists()) {
            abort(403, 'Unauthorized access to this plant.');
        }

        $plant->update(['sharing_token' => null]);

        return response()->json(['message' => 'Sharing token revoked successfully.']);
    }

    /**
     * Join a plant using a sharing token.
     */
    public function join(JoinPlantRequest $request)
    {
        $plant = Plant::where('sharing_token', $request->sharing_token)->firstOrFail();

        $user = $request->user();

        if ($user->plants()->where('plants.id', $plant->id)->exists()) {
            return response()->json(['message' => 'You are already a member of this plant.'], 409);
        }

        $user->plants()->attach($plant->id, ['role' => 'member']);

        return new PlantResource($plant);
    }

    public function image(Plant $plant)
    {
        if ($plant->custom_image) {

            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime_type = $finfo->buffer($plant->custom_image);

            if (!$mime_type || $mime_type === 'application/x-empty') {
                $mime_type = 'image/jpeg';
            }

            return response($plant->custom_image)->header('Content-Type', $mime_type);
        }

        if (!$plant->plantType->standard_image) {
            abort(404, 'No image found for this plant.');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo->buffer($plant->plantType->standard_image);

        if (!$mime_type || $mime_type === 'application/x-empty') {
            $mime_type = 'image/jpeg';
        }

        return response($plant->plantType->standard_image)->header('Content-Type', $mime_type);
    }

    public function updateImage(Request $request, Plant $plant)
    {
        if (!$request->user()->plants()->where('plants.id', $plant->id)->exists()) {
            abort(403, 'Unauthorized access to this plant.');
        }

        $imageData = $request->getContent();

        if (empty($imageData)) {
            return response()->json(['message' => 'No image data provided in request body.'], 400);
        }

        if (strlen($imageData) > 1_073_741_824) {
            return response()->json(['message' => 'Image exceeds maximum size of 1 GB.'], 400);
        }

        $image = @imagecreatefromstring($imageData);
        if (!$image) {
            return response()->json(['message' => 'Invalid or unsupported image format.'], 400);
        }

        ob_start();
        imagejpeg($image, null, 85);
        $jpeg = ob_get_clean();
        imagedestroy($image);

        $plant->update(['custom_image' => $jpeg]);

        return response()->json(['message' => 'Plant image updated successfully.']);
    }
}
