<?php

namespace Database\Factories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition()
    {
        return [
            'street' => $this->faker->streetAddress,
            'settlement' => $this->faker->city,
            'district' => $this->faker->citySuffix,
            'province' => $this->faker->state,
            'region' => $this->faker->stateAbbr,
            'country' => $this->faker->country,
            'postal_code' => $this->faker->postcode,
        ];
    }
}