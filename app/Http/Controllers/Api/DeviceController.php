<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Device\MapDeviceRequest;
use App\Http\Requests\Api\Device\UpdateDeviceRequest;
use App\Http\Resources\Api\DeviceResource;
use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /**
     * Display a listing of the user's mapped devices.
     */
    public function index(Request $request)
    {
        return DeviceResource::collection(
            $request->user()->devices()->get()
        );
    }

    /**
     * Display the specified device.
     */
    public function show(Request $request, Device $device)
    {
        if ($device->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized.');
        }

        return new DeviceResource($device);
    }

    /**
     * Update the user's device settings.
     */
    public function update(UpdateDeviceRequest $request, Device $device)
    {
        $device->update($request->validated());

        return new DeviceResource($device);
    }

    /**
     * Map a device to the authenticated user using the mapping key.
     */
    public function map(MapDeviceRequest $request)
    {
        $mappingKeyHash = hash('sha256', $request->mapping_key);

        $device = Device::where('mapping_key', $mappingKeyHash)->first();

        if (! $device) {
            return response()->json(['message' => 'Invalid mapping key.'], 404);
        }

        if ($device->user_id !== null) {
            return response()->json(['message' => 'Device is already mapped.'], 403);
        }

        $device->update(['user_id' => $request->user()->id]);

        return new DeviceResource($device);
    }

    /**
     * Unmap a device from the authenticated user.
     */
    public function unmap(Request $request, Device $device)
    {
        if ($device->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized.');
        }

        $device->update(['user_id' => null]);

        return response()->json(['message' => 'Device unmapped successfully.']);
    }
}
