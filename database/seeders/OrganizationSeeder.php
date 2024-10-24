<?php

namespace Database\Seeders;

use App\Helpers\Utility;
use App\ModelsV2\Locations;
use App\ModelsV2\Organization;
use Illuminate\Database\Seeder;
use App\Models\Province;
use App\Models\District;

class OrganizationSeeder extends Seeder
{
    public function run()
    {
        $organizations = [
            ['name' => 'Central Environmental Authority', 'id' => 1],
            ['name' => 'Nation Building Resource Organization', 'id' => 2],
            ['name' => 'University Of Peradeniya', 'id' => 3],
            ['name' => 'DriveGreen', 'id' => 4]
        ];

        foreach ($organizations as $organization) {
            $isOrganization = Organization::firstWhere('name', $organization['name']);
            if (!$isOrganization) {
                Organization::firstOrCreate([
                    'id' => $organization['id'],
                    'name' => $organization['name'],
                    'status' => Utility::$statusActive
                ]);
            }
        }

    }
}
