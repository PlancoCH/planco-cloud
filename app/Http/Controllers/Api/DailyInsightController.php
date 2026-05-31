<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyInsight;
use App\Models\Plant;
use App\Http\Resources\DailyInsightResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DailyInsightController extends Controller
{
    /**
     * Display a listing of the daily insights.
     */
    public function index(Plant $plant): AnonymousResourceCollection
    {
        $insights = $plant->insights()->orderBy('created_at', 'desc')->get();

        return DailyInsightResource::collection($insights);
    }

    /**
     * Display the specified daily insight.
     */
    public function show(Plant $plant, DailyInsight $dailyInsight): DailyInsightResource
    {
        if ($dailyInsight->plant_id !== $plant->id) {
            abort(404);
        }

        return new DailyInsightResource($dailyInsight);
    }

    /**
     * Mark the specified daily insight as read.
     */
    public function markAsRead(Plant $plant, DailyInsight $dailyInsight): JsonResponse
    {
        if ($dailyInsight->plant_id !== $plant->id) {
            abort(404);
        }

        $dailyInsight->update([
            'is_read' => true
        ]);

        return response()->json([
            'message' => 'Insight marked as read.',
            'data' => new DailyInsightResource($dailyInsight)
        ]);
    }
}
