<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\EquipmentType; // Make sure to import the model

class EquipmentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $types = [
            ['type_name' => 'Advanced devices'],
            ['type_name' => 'Low cost devices'],
        ];

        foreach ($types as $type) {
            EquipmentType::firstOrCreate(
                ['type_name' => $type['type_name']],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
