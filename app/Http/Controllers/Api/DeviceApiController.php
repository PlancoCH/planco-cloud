<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Device\StoreDeviceDataRequest;
use App\Http\Requests\Api\Device\UpdateWifiRssiRequest;
use App\Http\Resources\Api\DeviceConfigResource;
use Illuminate\Http\Request;

class DeviceApiController extends Controller
{
    /**
     * Store newly measured sensor data from the device to its mapped plants.
     */
    public function storeData(StoreDeviceDataRequest $request)
    {
        $device = $request->attributes->get('device');
        
        if (!$device) {
             return response()->json(['message' => 'Unauthorized. Device not authenticated.'], 401);
        }

        $validatedData = $request->validated();
        
        $recordsCreated = 0;

        // the device's data is saved to the plant_data table based on which plant it's mapped to
        foreach ($device->plants as $plant) {
            $plant->data()->create($validatedData);
            $recordsCreated++;
        }

        return response()->json([
            'message' => 'Sensor data recorded successfully.',
            'plants_updated' => $recordsCreated,
        ], 201);
    }

    /**
     * Update the device's WiFi RSSI.
     */
    public function updateWifiRssi(UpdateWifiRssiRequest $request)
    {
        $device = $request->attributes->get('device');
        
        if (!$device) {
             return response()->json(['message' => 'Unauthorized. Device not authenticated.'], 401);
        }

        $device->update([
            'wifi_rssi' => $request->validated('wifi_rssi'),
        ]);

        return response()->json([
            'message' => 'WiFi RSSI updated successfully.',
        ]);
    }

    /**
     * Get the device's configuration.
     */
    public function getConfig(Request $request)
    {
        $device = $request->attributes->get('device');
        
        if (!$device) {
             return response()->json(['message' => 'Unauthorized. Device not authenticated.'], 401);
        }

        return new DeviceConfigResource($device);
    }
}
