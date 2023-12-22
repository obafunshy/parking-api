<?php

namespace Database\Seeders;

use App\Models\PriceType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PriceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PriceType::create([
            'weekday_price' => 2,
            'weekend_price' => 3,
            'summer_price' => 5,
            'winter_price' => 6,
        ]);
    }
}
