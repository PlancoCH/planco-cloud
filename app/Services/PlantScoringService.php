<?php

namespace App\Services;

use App\Models\PlantData;
use App\Models\PlantType;

class PlantScoringService
{
    /**
     * Calculate the health score of a plant based on current data and ideal ranges.
     *
     * @param PlantData $plantData
     * @return int|null Score from 0 to 100, or null if plant type is missing.
     */
    public function calculateScore(PlantData $plantData): ?int
    {
        $plantType = $plantData->plant?->plantType;

        if (!$plantType) {
            return null;
        }

        $sensitivity = [
            'temp'     => 0.1,    // -10 points diff per ~1°C
            'moisture' => 0.05,   // more tolerant, % fluctuates more
            'light'    => 0.0001, // lux values are large
            'humidity' => 0.05,
        ];

        $values = [
            'temp'     => (float) $plantData->temperature,
            'moisture' => (float) $plantData->soil_moisture,
            'light'    => (float) $plantData->light_intensity,
            'humidity' => (float) $plantData->humidity,
        ];

        $ideal = [
            'temp'     => (float) $plantType->ideal_temp,
            'moisture' => (float) $plantType->ideal_moisture,
            'light'    => (float) $plantType->ideal_light_lux,
            'humidity' => (float) $plantType->ideal_humidity,
        ];

        $subScores = array_map(
            fn(string $key) => exp(-$sensitivity[$key] * abs($values[$key] - $ideal[$key])),
            array_keys($values)
        );

        return (int) round(array_sum($subScores) / count($subScores) * 100);
    }
}
