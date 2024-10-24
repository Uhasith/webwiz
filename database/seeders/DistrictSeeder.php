<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Province;
use App\Models\District;

class DistrictSeeder extends Seeder
{
    public function run()
    {
        $districts = [
            // Central Province districts
            ['district_name' => 'Kandy', 'province_key' => 'CP'],
            ['district_name' => 'Matale', 'province_key' => 'CP'],
            ['district_name' => 'Nuwara Eliya', 'province_key' => 'CP'],
            // Eastern Province districts
            ['district_name' => 'Ampara', 'province_key' => 'EP'],
            ['district_name' => 'Batticaloa', 'province_key' => 'EP'],
            ['district_name' => 'Trincomalee', 'province_key' => 'EP'],
            // North Central Province districts
            ['district_name' => 'Anuradhapura', 'province_key' => 'NCP'],
            ['district_name' => 'Polonnaruwa', 'province_key' => 'NCP'],
            // Northern Province districts
            ['district_name' => 'Jaffna', 'province_key' => 'NP'],
            ['district_name' => 'Kilinochchi', 'province_key' => 'NP'],
            ['district_name' => 'Mannar', 'province_key' => 'NP'],
            ['district_name' => 'Mullaitivu', 'province_key' => 'NP'],
            ['district_name' => 'Vavuniya', 'province_key' => 'NP'],
            // North Western Province districts
            ['district_name' => 'Kurunegala', 'province_key' => 'NWP'],
            ['district_name' => 'Puttalam', 'province_key' => 'NWP'],
            // Sabaragamuwa Province districts
            ['district_name' => 'Kegalle', 'province_key' => 'SP'],
            ['district_name' => 'Ratnapura', 'province_key' => 'SP'],
            // Southern Province districts
            ['district_name' => 'Galle', 'province_key' => 'SoP'],
            ['district_name' => 'Matara', 'province_key' => 'SoP'],
            ['district_name' => 'Hambantota', 'province_key' => 'SoP'],
            // Uva Province districts
            ['district_name' => 'Badulla', 'province_key' => 'UP'],
            ['district_name' => 'Moneragala', 'province_key' => 'UP'],
            // Western Province districts
            ['district_name' => 'Colombo', 'province_key' => 'WP'],
            ['district_name' => 'Gampaha', 'province_key' => 'WP'],
            ['district_name' => 'Kalutara', 'province_key' => 'WP'],
        ];
        
        foreach ($districts as $district) {
            $province = Province::firstWhere('province_key', $district['province_key']);
            if ($province) {
                District::firstOrCreate([
                    'district_name' => $district['district_name'],
                    'province_id' => $province->id,
                ]);
            }
        }
        
    }
}