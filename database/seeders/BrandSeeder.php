<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $brands = [
            [
                'name' => 'Nike',
                'description' => 'Just Do It',
                'logo' => 'nike_logo.png',
                'status' => true,
            ],
            [
                'name' => 'Adidas',
                'description' => 'Impossible Is Nothing',
                'logo' => 'adidas_logo.png',
                'status' => true,
            ],
            [
                'name' => 'Puma',
                'description' => 'Forever Faster',
                'logo' => 'puma_logo.png',
                'status' => true,
            ],
            [
                'name' => 'Reebok',
                'description' => 'Be More Human',
                'logo' => 'reebok_logo.png',
                'status' => true,
            ],
            [
                'name' => 'Under Armour',
                'description' => 'I Will',
                'logo' => 'under_armour_logo.png',
                'status' => true,
            ],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}