<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Shop;
use App\Models\Region; // Add this line to import the Region model

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Assuming you have regions already seeded, get the first region as an example
        $region = Region::first();

        if (!$region) {
            throw new \Exception('No regions found. Please seed regions first.');
        }

        Shop::firstOrCreate(
            [
                'name' => 'Moda House',
                'email' => 'esca656585@gmail.com',
            ],
            [
                'address' => 'G.Kulyýew Köçe, Begler ýoly',
                'mon_fri_open' => '09:00',
                'mon_fri_close' => '18:00',
                'sat_sun_open' => '09:00',
                'sat_sun_close' => '13:00',
                'image' => 'shop/shop-seeder/modahouse-logo.jpg',
                'user_id' => 1,
                'region_id' => $region->id, // Add this line
            ]
        );

        Shop::firstOrCreate(
            [
                'name' => 'Sowgatly',
                'email' => 'esca6585@gmail.com',
            ],
            [
                'address' => 'G.Kulyýew Köçe, Begler ýoly',
                'mon_fri_open' => '10:00',
                'mon_fri_close' => '19:00',
                'sat_sun_open' => 'işlänok',
                'sat_sun_close' => 'işlänok',
                'image' => 'shop/shop-seeder/sowgatly-logo.png',
                'user_id' => 2,
                'region_id' => $region->id, // Add this line
            ]
        );
    }
}