<?php

namespace App\Observers;

use App\Models\PlantData;
use App\Models\DailyInsight;
use App\Jobs\GenerateDailyInsight;
use App\Services\PlantScoringService;

class PlantDataObserver
{
    protected PlantScoringService $scoringService;

    public function __construct(PlantScoringService $scoringService)
    {
        $this->scoringService = $scoringService;
    }

    /**
     * Handle the PlantData "creating" event.
     */
    public function creating(PlantData $plantData): void
    {
        // Only calculate if the score wasn't explicitly set during creation
        if (is_null($plantData->plant_score)) {
            $plantData->plant_score = $this->scoringService->calculateScore($plantData);
        }
    }

    /**
     * Handle the PlantData "created" event.
     */
    public function created(PlantData $plantData): void
    {
        // If it's past 6 PM, check if a daily insight was already generated today
        if (now()->hour >= 18) {
            $hasInsight = DailyInsight::where('plant_id', $plantData->plant_id)
                ->where('insight_type', 'daily_summary')
                ->whereDate('created_at', today())
                ->exists();

            if (!$hasInsight) {
                // Dispatch job to generate insight using OpenAI (avoid blocking the device request)
                GenerateDailyInsight::dispatch($plantData->plant);
            }
        }
    }
}

