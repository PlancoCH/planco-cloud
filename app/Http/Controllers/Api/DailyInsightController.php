<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyInsight;
use App\Models\Plant;
use App\Services\OpenAIService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DailyInsightController extends Controller
{
    /**
     * Display a listing of the daily insights.
     */
    public function index(Plant $plant): JsonResponse
    {
        $insights = $plant->insights()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $insights
        ]);
    }

    /**
     * Display the specified daily insight.
     */
    public function show(Plant $plant, DailyInsight $dailyInsight): JsonResponse
    {
        if ($dailyInsight->plant_id !== $plant->id) {
            abort(404);
        }

        return response()->json([
            'data' => $dailyInsight
        ]);
    }

    /**
     * Generate a new daily insight for the given plant using OpenAI.
     */
    public function generate(Request $request, Plant $plant, OpenAIService $openAIService): JsonResponse
    {
        $plant->load('plantType');

        // Get today's measurements
        $todayData = $plant->data()->whereDate('created_at', today())->get();
        if ($todayData->isEmpty()) {
            return response()->json([
                'message' => 'No measurements available for today to generate an insight.'
            ], 400);
        }

        // Format today's measurements
        $measurementsText = $todayData->map(function ($data) {
            return "Temp: {$data->temperature}°C, Humidity: {$data->humidity}%, "
                . "Light: {$data->light_intensity} lux, Soil Moisture: {$data->soil_moisture}%, "
                . "Score: {$data->plant_score}";
        })->implode("\n");

        // Ideal values
        $idealValues = "Ideal Temp: {$plant->plantType->ideal_temp}°C, "
            . "Ideal Moisture: {$plant->plantType->ideal_moisture}%, "
            . "Ideal Light: {$plant->plantType->ideal_light_lux} lux, "
            . "Ideal Humidity: {$plant->plantType->ideal_humidity}%";

        $plantName = $plant->nickname ?: $plant->plantType->common_name;

        // Build messages for OpenAI
        $systemMessages = [
            "You are a plant named '{$plantName}'.",
            "Here are your ideal growing conditions:\n{$idealValues}",
            "Here are your measurements for today:\n{$measurementsText}",
            "Reply in 2-3 short and concise sentences.",
            "Tell the user what you need and how you feel based on your measurements compared to your ideal conditions."
        ];

        $userMessages = [
            'How are you feeling today?'
        ];

        // Generate text via OpenAI
        $generatedText = $openAIService->request($systemMessages, $userMessages);

        if (!$generatedText) {
            return response()->json([
                'message' => 'Failed to generate insight.'
            ], 500);
        }

        // Store the daily insight
        $insight = $plant->insights()->create([
            'insight_type' => 'daily_summary',
            'message' => trim($generatedText),
            'is_read' => false,
        ]);

        return response()->json([
            'message' => 'Daily insight generated successfully.',
            'data' => $insight
        ], 201);
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
            'data' => $dailyInsight
        ]);
    }
}
