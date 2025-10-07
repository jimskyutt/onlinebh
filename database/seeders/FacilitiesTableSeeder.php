<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FacilitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = now();
        
        $facilities = [
            ['facility_name' => 'WiFi', 'created_at' => $now, 'updated_at' => $now],
            ['facility_name' => 'Air Conditioning', 'created_at' => $now, 'updated_at' => $now],
            ['facility_name' => 'Study Area', 'created_at' => $now, 'updated_at' => $now],
            ['facility_name' => 'Laundry', 'created_at' => $now, 'updated_at' => $now],
            ['facility_name' => 'Kitchen', 'created_at' => $now, 'updated_at' => $now],
            ['facility_name' => 'Parking', 'created_at' => $now, 'updated_at' => $now],
            ['facility_name' => 'Security', 'created_at' => $now, 'updated_at' => $now],
            ['facility_name' => 'CCTV', 'created_at' => $now, 'updated_at' => $now],
            ['facility_name' => 'Bathroom per Room', 'created_at' => $now, 'updated_at' => $now],
            ['facility_name' => 'Bed', 'created_at' => $now, 'updated_at' => $now],
            ['facility_name' => 'Cabinet', 'created_at' => $now, 'updated_at' => $now],
            ['facility_name' => 'Electric Fan', 'created_at' => $now, 'updated_at' => $now],
            ['facility_name' => 'Refrigerator', 'created_at' => $now, 'updated_at' => $now],
            ['facility_name' => 'Water Dispenser', 'created_at' => $now, 'updated_at' => $now],
            ['facility_name' => 'Study Table', 'created_at' => $now, 'updated_at' => $now],
            ['facility_name' => 'Washing Area', 'created_at' => $now, 'updated_at' => $now],
            ['facility_name' => 'Common CR', 'created_at' => $now, 'updated_at' => $now],
            ['facility_name' => 'Canteen', 'created_at' => $now, 'updated_at' => $now],
            ['facility_name' => 'Lounge Area', 'created_at' => $now, 'updated_at' => $now],
            ['facility_name' => 'Generator', 'created_at' => $now, 'updated_at' => $now],
            ['facility_name' => 'Own Meter', 'created_at' => $now, 'updated_at' => $now],
        ];
    
        DB::table('facilities')->insert($facilities);
    }
}