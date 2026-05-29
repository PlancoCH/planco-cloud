<?php

namespace App\Observers;

use App\Models\PlantData;
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
}
