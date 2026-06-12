<?php

namespace Database\Seeders;

use App\Models\Office;
use Illuminate\Database\Seeder;

class DemoOfficeSeeder extends Seeder
{
    public function run(): void
    {
        Office::firstOrCreate(
            ['name' => 'Head Office (Phnom Penh)'],
            [
                'latitude'      => 11.5564,
                'longitude'     => 104.9282,
                'radius_meters' => 100,
                'late_after'    => '09:00:00',
                'timezone'      => 'Asia/Phnom_Penh',
                'is_active'     => true,
            ]
        );
    }
}
