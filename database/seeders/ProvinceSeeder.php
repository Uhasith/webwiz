<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Province;

class ProvinceSeeder extends Seeder
{
    public function run()
    {
        $provinces = [
            ['province_name' => 'Central Province', 'province_key' => 'CP'],
            ['province_name' => 'Eastern Province', 'province_key' => 'EP'],
            ['province_name' => 'North Central Province', 'province_key' => 'NCP'],
            ['province_name' => 'Northern Province', 'province_key' => 'NP'],
            ['province_name' => 'North Western Province', 'province_key' => 'NWP'],
            ['province_name' => 'Sabaragamuwa Province', 'province_key' => 'SP'],
            ['province_name' => 'Southern Province', 'province_key' => 'SoP'],
            ['province_name' => 'Uva Province', 'province_key' => 'UP'],
            ['province_name' => 'Western Province', 'province_key' => 'WP'],
        ];
        
        foreach ($provinces as $province) {
            Province::firstOrCreate(['province_key' => $province['province_key']], $province);
        }
    }
}
