<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;
use App\Models\Address;

class RegionSeeder extends Seeder
{
    private $postalCode = 744000;

    public function run()
    {
        // Create Turkmenistan as the root
        $turkmenistan = Region::create([
            'name' => 'Turkmenistan', 
            'type' => 'country',
            'address_id' => $this->createAddress('Turkmenistan')
        ]);

        // Create provinces (velayats)
        $provinces = [
            'Ahal',
            'Balkan',
            'Dashoguz',
            'Lebap',
            'Mary'
        ];

        foreach ($provinces as $province) {
            $provinceModel = Region::create([
                'name' => $province,
                'parent_id' => $turkmenistan->id,
                'type' => 'province',
                'address_id' => $this->createAddress($province)
            ]);

            // Add some example cities and villages for each province
            $this->addCitiesAndVillages($provinceModel);
        }

        // Add Ashgabat as a special city-state
        Region::create([
            'name' => 'Ashgabat',
            'parent_id' => $turkmenistan->id,
            'type' => 'city',
            'address_id' => $this->createAddress('Ashgabat')
        ]);
    }

    private function addCitiesAndVillages($province)
    {
        // Add some example cities (you should replace these with actual cities)
        for ($i = 1; $i <= 3; $i++) {
            $city = Region::create([
                'name' => $province->name . " City {$i}",
                'parent_id' => $province->id,
                'type' => 'city',
                'address_id' => $this->createAddress($province->name . " City {$i}")
            ]);

            // Add some example villages for each city
            for ($j = 1; $j <= 2; $j++) {
                Region::create([
                    'name' => $city->name . " Village {$j}",
                    'parent_id' => $city->id,
                    'type' => 'village',
                    'address_id' => $this->createAddress($city->name . " Village {$j}")
                ]);
            }
        }
    }

    private function createAddress($regionName)
    {
        return Address::create([
            'street' => 'Main Street',
            'city' => $regionName,
            'state' => $regionName,
            'country' => 'Turkmenistan',
            'postal_code' => $this->getNextPostalCode()
        ])->id;
    }

    private function getNextPostalCode()
    {
        return strval($this->postalCode++);
    }
}