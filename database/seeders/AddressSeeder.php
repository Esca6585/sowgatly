<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Address;
use App\Models\Shop;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        DB::transaction(function () use ($faker) {
            $this->createAddresses($faker);
        });
    }

    /**
     * Create addresses for shops.
     *
     * @param \Faker\Generator $faker
     */
    private function createAddresses(\Faker\Generator $faker): void
    {
        $shops = Shop::doesntHave('address')->get();

        foreach ($shops as $shop) {
            Address::create([
                'shop_id' => $shop->id,
                'address_1' => $faker->streetAddress,
                'address_2' => $faker->optional(0.3)->secondaryAddress,
                'postal_code' => $faker->postcode,
            ]);
        }
    }
}