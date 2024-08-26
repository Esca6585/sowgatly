<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition()
    {
        $turkmenCities = [
            'Ashgabat' => '744000',
            'Turkmenabat' => '746100',
            'Dashoguz' => '746000',
            'Mary' => '745400',
            'Balkanabat' => '745100',
        ];

        $city = $this->faker->randomElement(array_keys($turkmenCities));

        return [
            'shop_id' => Shop::factory(),
            'address_name' => $this->faker->streetAddress,
            'postal_code' => $turkmenCities[$city],
        ];
    }
}