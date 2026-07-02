<?php

namespace Database\Seeders;

use App\Models\PatternProduct;
use Illuminate\Database\Seeder;

class PatternCheckoutSeeder extends Seeder
{
    public function run(): void
    {
        PatternProduct::query()->updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Interview Flash Sale Product',
                'stock' => 10,
                'price_in_paise' => 99900,
            ]
        );
    }
}
