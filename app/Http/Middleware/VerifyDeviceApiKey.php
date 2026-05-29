<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Device;

class VerifyDeviceApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-Device-Api-Key');
        
        if (!$apiKey) {
            $apiKey = $request->bearerToken();
        }

        if (!$apiKey) {
            return response()->json(['message' => 'Unauthorized. Missing API key.'], 401);
        }

        $hashedKey = hash('sha256', $apiKey);
        $device = Device::where('api_key', $hashedKey)->first();

        if (!$device) {
            return response()->json(['message' => 'Unauthorized. Invalid API key.'], 401);
        }

        // Attach the authenticated device to the request
        $request->attributes->add(['device' => $device]);

        return $next($request);
    }
}
