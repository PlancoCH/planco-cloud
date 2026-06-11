<?php

namespace App\Jobs;

use App\Models\Plant;
use App\Services\OpenAIService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GenerateDailyInsight implements ShouldQueue
{
    use Queueable;

    protected Plant $plant;

    /**
     * Create a new job instance.
     */
    public function __construct(Plant $plant)
    {
        $this->plant = $plant;
    }

    /**
     * Execute the job.
     */
    public function handle(OpenAIService $openAIService): void
    {
        $this->plant->load('plantType');

        // Get today's measurements
        $todayData = $this->plant->data()->whereDate('created_at', today())->get();
        if ($todayData->isEmpty()) {
            return;
        }

        // Format today's measurements
        $measurementsText = $todayData->map(function ($data) {
            return "Temp: {$data->temperature}°C, Humidity: {$data->humidity}%, "
                . "Light: {$data->light_intensity} lux, Soil Moisture: {$data->soil_moisture}%, "
                . "Score: {$data->plant_score}";
        })->implode("\n");

        // Ideal values
        $idealValues = "Ideal Temp: {$this->plant->plantType->ideal_temp}°C, "
            . "Ideal Moisture: {$this->plant->plantType->ideal_moisture}%, "
            . "Ideal Light: {$this->plant->plantType->ideal_light_lux} lux, "
            . "Ideal Humidity: {$this->plant->plantType->ideal_humidity}%";

        $plantName = $this->plant->nickname ?: $this->plant->plantType->common_name;

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
            Log::error('Failed to generate daily insight for plant ' . $this->plant->id);
            return;
        }

        // Store the daily insight
        $this->plant->insights()->create([
            'insight_type' => 'daily_summary',
            'message' => trim($generatedText),
            'is_read' => false,
        ]);
    }
}
