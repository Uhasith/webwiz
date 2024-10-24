<?php

namespace Database\Seeders;

use App\ModelsV2\Locations;
use Illuminate\Database\Seeder;
use App\Models\Province;
use App\Models\District;

class LocationSeeder extends Seeder
{
    public function run()
    {
        $locations = [
            ['name' => 'Battaramulla', 'district_id' => 23,'latitude' => 6.8980000,'longitude' => 79.9223000],
            ['name' => 'Maharagama', 'district_id' => 23,'latitude' => 6.8511000,'longitude' => 79.9212000],
            ['name' => 'Galle', 'district_id' => 18,'latitude' => 6.0329,'longitude' => 80.2168],
            ['name' => 'Kandy', 'district_id' => 1,'latitude' => 7.2906,'longitude' => 80.6337]
        ];

        foreach ($locations as $location) {
            $isLocation = Locations::firstWhere('name', $location['name']);
            if (!$isLocation) {
                Locations::firstOrCreate([
                    'name' => $location['name'],
                    'district_id' => $location['district_id'],
                    'latitude' => $location['latitude'],
                    'longitude' => $location['longitude']
                ]);
            }
        }

    }
}
