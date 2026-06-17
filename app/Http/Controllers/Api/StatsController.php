<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PlantData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $plantIds = $user->plants()->pluck('plants.id');

        $deviceCount = $user->devices()->count();
        $totalPlants = $plantIds->count();

        $ownedPlants = DB::table('plant_user')
            ->where('user_id', $user->id)
            ->where('role', 'owner')
            ->count();

        $memberPlants = DB::table('plant_user')
            ->where('user_id', $user->id)
            ->where('role', 'member')
            ->count();

        $latestScores = collect();
        $healthDistribution = ['good' => 0, 'fair' => 0, 'poor' => 0, 'unknown' => 0];

        if ($plantIds->isNotEmpty()) {
            $subQuery = DB::table('plant_data')
                ->select('plant_id', DB::raw('MAX(id) as max_id'))
                ->whereIn('plant_id', $plantIds)
                ->groupBy('plant_id');

            $latestScores = DB::table('plant_data')
                ->joinSub($subQuery, 'latest', function ($join) {
                    $join->on('plant_data.id', '=', 'latest.max_id');
                })
                ->pluck('plant_data.plant_score', 'plant_data.plant_id');

            foreach ($latestScores as $score) {
                if ($score === null) {
                    $healthDistribution['unknown']++;
                } elseif ($score >= 70) {
                    $healthDistribution['good']++;
                } elseif ($score >= 40) {
                    $healthDistribution['fair']++;
                } else {
                    $healthDistribution['poor']++;
                }
            }

            $scoredPlants = $plantIds->count() - $healthDistribution['unknown'];
            if ($scoredPlants > 0) {
                $healthDistribution['unknown'] += $plantIds->count() - $latestScores->count();
            } else {
                $healthDistribution['unknown'] = $plantIds->count();
            }
        }

        $avgScore = $latestScores->filter(fn ($s) => $s !== null)->avg();
        $avgScore = $avgScore !== null ? round($avgScore, 2) : null;

        $unreadInsights = DB::table('daily_insights')
            ->whereIn('plant_id', $plantIds)
            ->where('is_read', false)
            ->count();

        $devices = $user->devices()->get()->map(function ($device) {
            $hasRecentData = DB::table('plant_data')
                ->join('plants', 'plant_data.plant_id', '=', 'plants.id')
                ->where('plants.device_id', $device->id)
                ->where('plant_data.created_at', '>=', now()->subHours(3))
                ->exists();

            return [
                'id' => $device->id,
                'name' => $device->name,
                'wifi_rssi' => $device->wifi_rssi,
                'polling_rate' => $device->polling_rate,
                'led_enabled' => $device->led_enabled,
                'online' => $hasRecentData,
                'plant_count' => DB::table('plants')
                    ->where('device_id', $device->id)
                    ->count(),
            ];
        });

        $recentData = collect();
        if ($plantIds->isNotEmpty()) {
            $recentData = PlantData::whereIn('plant_id', $plantIds)
                ->with('plant:id,nickname')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(fn ($d) => [
                    'id' => $d->id,
                    'plant_id' => $d->plant_id,
                    'plant_nickname' => $d->plant->nickname,
                    'plant_score' => $d->plant_score,
                    'temperature' => $d->temperature,
                    'humidity' => $d->humidity,
                    'soil_moisture' => $d->soil_moisture,
                    'light_intensity' => $d->light_intensity,
                    'recorded_at' => $d->created_at->toIso8601String(),
                ]);
        }

        return response()->json([
            'devices' => [
                'total' => $deviceCount,
                'list' => $devices,
            ],
            'plants' => [
                'total' => $totalPlants,
                'owned' => $ownedPlants,
                'member_of' => $memberPlants,
            ],
            'health' => [
                'average_plant_score' => $avgScore,
                'distribution' => $healthDistribution,
            ],
            'unread_insights' => $unreadInsights,
            'recent_data' => $recentData->values(),
        ]);
    }
}
