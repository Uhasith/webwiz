<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            RolesTableSeeder::class,
            UsersTableSeeder::class,
            OrganizationSeeder::class,
            ProvinceSeeder::class,
            DistrictSeeder::class,
            EquipmentTypeSeeder::class,
            SensorSeeder::class,
            LocationSeeder::class,
            SensorLocationSeeder::class,
            // SensorDataSeeder::class // Uncomment this line if you want to seed sensor data
        ]);

    }
}
