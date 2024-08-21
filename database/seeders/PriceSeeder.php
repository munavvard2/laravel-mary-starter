<?php

namespace Database\Seeders;

use App\Models\price;
use App\Models\Pricing;
use Illuminate\Database\Seeder;

class PriceSeeder extends Seeder
{
    public function run(): void
    {
        // Avoid duplicates
        if (Pricing::count() > 0) {
            return;
        }

        Pricing::insert([
            [
                'brass_price' => 650,
                'extra_plate_price' => 300,
                'extra_aadi_price' => 250,
                'extra_teka_price' => 250,
                'majuri_price' => 100,
            ]
        ]);
    }
}
