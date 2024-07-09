<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = [
            // id=1
            [
                'name_tm' => 'Batonçik Nestle "Good Mix" arahis 33 gr',
                'name_en' => 'Bar Nestle "Good Mix" peanuts 33 gr',
                'name_ru' => 'Батончик Nestle "Good Mix" арахис 33 гр',
                'description' => 'Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts and visual mockups.',
                'price' => 20.40,
                'sale_price' => null,
                'discount' => 0,
                'sale_type' => 'new',
                'stock' => 100,
                'code' => base_convert(mt_rand(122, 782) . substr(time(), 2), 10, 36),
                'images' => 'assets/product/product-seeder/nestle_good_mix_33_gr.jpg',
            ],
            // id=2
            [
                'name_tm' => 'Batonçik Nestle "Good Mix" arahis 33 gr',
                'name_en' => 'Bar Nestle "Good Mix" peanuts 33 gr',
                'name_ru' => 'Батончик Nestle "Good Mix" арахис 33 гр',
                'description' => 'Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts and visual mockups.',
                'price' => 20.40,
                'sale_price' => null,
                'discount' => 0,
                'sale_type' => 'new',
                'stock' => 100,
                'code' => base_convert(mt_rand(122, 782) . substr(time(), 2), 10, 36),
                'images' => 'assets/product/product-seeder/nestle_good_mix_33_gr.jpg',
            ],
            // id=3
            [
                'name_tm' => 'Batonçik Nestle "Good Mix" arahis 33 gr',
                'name_en' => 'Bar Nestle "Good Mix" peanuts 33 gr',
                'name_ru' => 'Батончик Nestle "Good Mix" арахис 33 гр',
                'description' => 'Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts and visual mockups.',
                'price' => 20.40,
                'sale_price' => null,
                'discount' => 0,
                'sale_type' => 'new',
                'stock' => 100,
                'code' => base_convert(mt_rand(122, 782) . substr(time(), 2), 10, 36),
                'images' => 'assets/product/product-seeder/nestle_good_mix_33_gr.jpg',
            ],
            // id=4
            [
                'name_tm' => 'Batonçik Nestle "Good Mix" arahis 33 gr',
                'name_en' => 'Bar Nestle "Good Mix" peanuts 33 gr',
                'name_ru' => 'Батончик Nestle "Good Mix" арахис 33 гр',
                'description' => 'Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts and visual mockups.',
                'price' => 20.40,
                'sale_price' => null,
                'discount' => 0,
                'sale_type' => 'new',
                'stock' => 100,
                'code' => base_convert(mt_rand(122, 782) . substr(time(), 2), 10, 36),
                'images' => 'assets/product/product-seeder/nestle_good_mix_33_gr.jpg',
            ],
            // id=5
            [
                'name_tm' => 'Batonçik Nestle "Good Mix" arahis 33 gr',
                'name_en' => 'Bar Nestle "Good Mix" peanuts 33 gr',
                'name_ru' => 'Батончик Nestle "Good Mix" арахис 33 гр',
                'description' => 'Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts and visual mockups.',
                'price' => 20.40,
                'sale_price' => null,
                'discount' => 0,
                'sale_type' => 'new',
                'stock' => 100,
                'code' => base_convert(mt_rand(122, 782) . substr(time(), 2), 10, 36),
                'images' => 'assets/product/product-seeder/nestle_good_mix_33_gr.jpg',
            ],
            // id=6
            [
                'name_tm' => 'Batonçik Nestle "Good Mix" arahis 33 gr',
                'name_en' => 'Bar Nestle "Good Mix" peanuts 33 gr',
                'name_ru' => 'Батончик Nestle "Good Mix" арахис 33 гр',
                'description' => 'Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts and visual mockups.',
                'price' => 20.40,
                'sale_price' => null,
                'discount' => 0,
                'sale_type' => 'new',
                'stock' => 100,
                'code' => base_convert(mt_rand(122, 782) . substr(time(), 2), 10, 36),
                'images' => 'assets/product/product-seeder/nestle_good_mix_33_gr.jpg',
            ],
            // id=7
            [
                'name_tm' => 'Batonçik Nestle "Good Mix" arahis 33 gr',
                'name_en' => 'Bar Nestle "Good Mix" peanuts 33 gr',
                'name_ru' => 'Батончик Nestle "Good Mix" арахис 33 гр',
                'description' => 'Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts and visual mockups.',
                'price' => 20.40,
                'sale_price' => null,
                'discount' => 0,
                'sale_type' => 'new',
                'stock' => 100,
                'code' => base_convert(mt_rand(122, 782) . substr(time(), 2), 10, 36),
                'images' => 'assets/product/product-seeder/nestle_good_mix_33_gr.jpg',
            ],
        ];

        // <-- begin:: Products -->
        foreach ($products as $product) 
        {
            Product::create([
                'name_tm' => $product['name_tm'],
                'name_en' => $product['name_en'],
                'name_ru' => $product['name_ru'],
                'description' => $product['description'],
                'price' => $product['price'],
                'sale_price' => $product['sale_price'],
                'discount' => $product['discount'],
                'sale_type' => $product['sale_type'],
                'stock' => $product['stock'],
                'code' => $product['code'],
                'images' => $product['images'],
            ]); 
        }
        // <-- end:: Products -->

    }
}
