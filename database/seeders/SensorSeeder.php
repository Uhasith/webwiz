<?php

namespace Database\Seeders;

use App\Helpers\Utility;
use App\ModelsV2\SensorOrganization;
use App\ModelsV2\Sensors;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Webpatser\Uuid\Uuid;

class SensorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sensors = [
            [
                'id' => 1,
                'name' => 'Eco Tech',
                'status' => 'ACTIVE',
                'equipment_type_id' => 1,
                'organization_id' => 1,
                'slug' => Utility::$ecoTechSlug,
            ],
            [
                'id' => 2,
                'name' => 'Tsi Bluesky',
                'status' => 'ACTIVE',
                'equipment_type_id' => 2,
                'organization_id' => 3,
                'slug' => Utility::$tsiBlueskySlug,
            ],
            [
                'id' => 3,
                'name' => 'Nbro',
                'status' => 'ACTIVE',
                'equipment_type_id' => 2,
                'organization_id' => 2,
                'slug' => Utility::$nbroSlug,
            ],
            [
                'id' => 4,
                'name' => 'Iq Air',
                'status' => 'ACTIVE',
                'equipment_type_id' => 2,
                'organization_id' => 4,
                'slug' => Utility::$iqAirSlug,
            ],
        ];

        foreach ($sensors as $sensor) {
            Sensors::updateOrCreate(
                ['name' => $sensor['name']],
                [
                    'id' => $sensor['id'],
                    'status' => $sensor['status'],
                    'equipment_type_id' => $sensor['equipment_type_id'],
                    'slug' => $sensor['slug'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]
            );
            SensorOrganization::updateOrCreate(
                ['sensor_id' => $sensor['id'], 'organization_id' => $sensor['organization_id']],
                [
                    'id' => Uuid::generate().'',
                    'sensor_id' => $sensor['id'],
                    'organization_id' => $sensor['organization_id']
                ]
            );
        }
    }
}
