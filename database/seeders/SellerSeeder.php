<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Seller;

class SellerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Seller::firstOrCreate(
            [
                'phone_number' => 65656585,
            ],
            [
                'name' => 'Rahmanberdi Ahmedow',
                'image' => 'seller/seller-seeder/logo.png',
                'password' => Hash::make('password'),
                'status' => true,
            ]
        );

        Seller::firstOrCreate(
            [
                'phone_number' => 71406778,
            ],
            [
                'name' => 'Rahymberdi Ahmedow',
                'image' => 'seller/seller-seeder/logo.png',
                'password' => Hash::make('password'),
                'status' => true,
            ]
        );
    }
}
