<?php

namespace App\Repository\Admin;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\ModelsV2\SensorDatas;
use App\Models\ExternalAccess;
use App\ModelsV2\DailySensorData;
use App\ModelsV2\SensorLocations;
use App\ModelsV2\HourlySensorData;
use App\ModelsV2\AnuallySensorData;
use App\ModelsV2\MonthlySensorData;
use Illuminate\Support\Facades\Log;

class ExternalAccessRepo
{
    public function getData(array $data): array
    {
        $externalAccess = $data['externalAccess'];
        $startDate = Carbon::parse($data['start_date'])->startOfDay();
        $endDate = Carbon::parse($data['end_date'])->endOfDay();

        // Initialize data collection
        $data = [];
        $matchingSensorLocations = [];

        if (is_array($externalAccess->sensors) && !count($externalAccess->sensors) > 0) {
            throw new Exception('No sensors found');
        }

        if (is_array($externalAccess->sensors) && count($externalAccess->sensors) > 0 && is_array($externalAccess->locations)) {

            // Base query
            $query = SensorLocations::whereIn('sensor_id', $externalAccess->sensors);

            // Apply location filter only if locations array is not empty
            if (count($externalAccess->locations) > 0) {
                $query->whereIn('location_id', $externalAccess->locations);
            }

            // Get matching sensor location IDs
            $matchingSensorLocations = $query->pluck('id');

            Log::info($matchingSensorLocations->toArray());
        }

        // Load data based on boolean fields
        if ($externalAccess->recent_data) {
            $data['recent_data'] = [
                SensorDatas::with('location', 'sensor', 'weatherRecords')
                    ->whereIn('sensor_location_id', $matchingSensorLocations)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get()
                    ->groupBy([
                        fn($data) => $data->location->name,
                        fn($data) => $data->sensor->slug
                    ]),
            ];
        }

        if ($externalAccess->hourly_data) {
            $data['hourly'] = [
                HourlySensorData::with('location', 'sensor', 'weatherRecords')
                    ->whereIn('sensor_location_id', $matchingSensorLocations)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get()
                    ->groupBy([
                        fn($data) => $data->location->name,
                        fn($data) => $data->sensor->slug
                    ]),
            ];
        }

        if ($externalAccess->daily_data) {
            $data['daily'] = [
                DailySensorData::with('location', 'sensor', 'weatherRecords')
                    ->whereIn('sensor_location_id', $matchingSensorLocations)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get()
                    ->groupBy([
                        fn($data) => $data->location->name,
                        fn($data) => $data->sensor->slug
                    ]),
            ];
        }

        if ($externalAccess->monthly_data) {
            $data['monthly'] = [
                MonthlySensorData::with('location', 'sensor', 'weatherRecords')
                    ->whereIn('sensor_location_id', $matchingSensorLocations)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get()
                    ->groupBy([
                        fn($data) => $data->location->name,
                        fn($data) => $data->sensor->slug
                    ]),

            ];
        }

        if ($externalAccess->annually_data) {
            $data['annually'] = [
                AnuallySensorData::with('location', 'sensor', 'weatherRecords')
                    ->whereIn('sensor_location_id', $matchingSensorLocations)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get()
                    ->groupBy([
                        fn($data) => $data->location->name,
                        fn($data) => $data->sensor->slug
                    ]),
            ];
        }

        return $data;
    }

    public function create(array $data): ExternalAccess
    {
        $data['api_key'] = Str::random(32);

        // Create and return the ExternalAccess record
        return ExternalAccess::create($data);
    }

    public function update(string $id, array $data): ExternalAccess
    {
        $record = ExternalAccess::findOrFail($id);

        // Update and return the ExternalAccess record
        $record->update($data);

        return $record;
    }
}
