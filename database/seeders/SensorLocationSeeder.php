<?php

namespace Database\Seeders;

use App\Helpers\Utility;
use App\ModelsV2\SensorLocations;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Webpatser\Uuid\Uuid;

class SensorLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $sensorLocations = [
             [
                 'sensor_id' => 1, // Don't change
                 'location_id' => 1, // Don't change
                 'locationindex' => '',
                 'organization_id' => 1,
                 'latitude' => 6.8980000,
                 'longitude' => 79.9223000,
                 'status' => Utility::$statusActive
             ],
             [
                 'sensor_id' => 2, // Don't change
                 'location_id' => 2, // Don't change
                 'locationindex' => '',
                 'organization_id' => 2,
                 'latitude' => 6.8511000,
                 'longitude' => 79.9212000,
                 'status' => Utility::$statusActive
             ],
             [
                 'sensor_id' => 3, // Don't change
                 'location_id' => 3, // Don't change
                 'locationindex' => '',
                 'organization_id' => 3,
                 'latitude' => 6.0329000,
                 'longitude' => 80.2168000,
                 'status' => Utility::$statusActive
             ],
             [
                 'sensor_id' => 4, // Don't change
                 'location_id' => 1, // Don't change
                 'locationindex' => '666c390ffcfe4213b71d4193',
                 'organization_id' => 1,
                 'latitude' => 6.901035,
                 'longitude' => 79.926513,
                 'status' => Utility::$statusActive
             ]
         ];

         foreach ($sensorLocations as $sensorLocation) {
             SensorLocations::updateOrCreate(
                 ['sensor_id' => $sensorLocation['sensor_id'], 'location_id' => $sensorLocation['location_id']],
                 [
                     'sensor_id' => $sensorLocation['sensor_id'],
                     'location_id' => $sensorLocation['location_id'],
                     'locationindex' => $sensorLocation['locationindex'],
                     'organization_id' => $sensorLocation['organization_id'],
                     'latitude' => $sensorLocation['latitude'],
                     'longitude' => $sensorLocation['longitude'],
                     'status' => $sensorLocation['status']
                 ]
             );
         }
    }
}
