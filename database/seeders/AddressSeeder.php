<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Address;
use Faker\Factory as Faker;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < 50; $i++) {
            Address::create([
                'street' => $faker->streetAddress,
                'city' => $faker->city,
                'state' => $faker->state,
                'country' => $faker->country,
                'postal_code' => $faker->postcode,
            ]);
        }
    }
}