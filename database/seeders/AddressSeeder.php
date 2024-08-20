<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Address;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class AddressSeeder extends Seeder
{
    /**
     * The number of addresses to create per settlement.
     */
    private const ADDRESSES_PER_SETTLEMENT = 3;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        DB::transaction(function () use ($faker) {
            $this->createTurkmenistanAddresses($faker);
        });
    }

    /**
     * Create addresses for Turkmenistan regions.
     *
     * @param \Faker\Generator $faker
     */
    private function createTurkmenistanAddresses(\Faker\Generator $faker): void
    {
        $turkmenistanData = $this->getTurkmenistanData();

        $addresses = [];

        foreach ($turkmenistanData as $region => $provinces) {
            foreach ($provinces as $province => $districts) {
                foreach ($districts as $district => $settlements) {
                    foreach ($settlements as $settlement) {
                        $addresses = array_merge($addresses, $this->generateAddresses($faker, $region, $province, $district, $settlement));
                    }
                }
            }
        }

        // Use chunk insert for better performance
        foreach (array_chunk($addresses, 100) as $chunk) {
            Address::insert($chunk);
        }
    }

    /**
     * Generate addresses for a specific settlement.
     *
     * @param \Faker\Generator $faker
     * @param string $region
     * @param string $province
     * @param string $district
     * @param string $settlement
     * @return array
     */
    private function generateAddresses(\Faker\Generator $faker, string $region, string $province, string $district, string $settlement): array
    {
        $addresses = [];

        for ($i = 0; $i < self::ADDRESSES_PER_SETTLEMENT; $i++) {
            $addresses[] = [
                'street' => $faker->streetName,
                'settlement' => $settlement,
                'district' => $district,
                'province' => $province,
                'region' => $region,
                'country' => 'Turkmenistan',
                'postal_code' => $this->generateTurkmenPostalCode($region),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        return $addresses;
    }

    /**
     * Generate a Turkmenistan postal code.
     *
     * @param string $region
     * @return string
     */
    private function generateTurkmenPostalCode(string $region): string
    {
        $regionCodes = [
            'Ahal' => '74',
            'Balkan' => '75',
            'Daşoguz' => '76',
            'Lebap' => '77',
            'Mary' => '78',
            'Aşgabat' => '74'
        ];

        $regionCode = $regionCodes[$region] ?? '74';
        return $regionCode . rand(1000, 9999);
    }

    /**
     * Get Turkmenistan administrative divisions data.
     *
     * @return array
     */
    private function getTurkmenistanData(): array
    {
        return [
            'Ahal' => [
                'Akbugday' => [
                    'Anew' => ['Anew', 'Ýaşlyk', 'Gökje'],
                    'Bäherden' => ['Bäherden', 'Duşak', 'Ýaşlyk'],
                ],
                'Babadayhan' => [
                    'Babadayhan' => ['Babadayhan', 'Garlyk', 'Dänew'],
                ],
                // Add more provinces and districts as needed
            ],
            'Balkan' => [
                'Balkanabat' => [
                    'Balkanabat' => ['Balkanabat', 'Jebel', 'Gumdag'],
                ],
                'Türkmenbaşy' => [
                    'Türkmenbaşy' => ['Türkmenbaşy', 'Awaza', 'Kenar'],
                ],
                // Add more provinces and districts as needed
            ],
            'Daşoguz' => [
                'Daşoguz' => [
                    'Daşoguz' => ['Daşoguz', 'Görogly', 'Gubadag'],
                ],
                'Gurbansoltan Eje' => [
                    'Ýylanly' => ['Ýylanly', 'Akdepe', 'Gökçäge'],
                ],
                // Add more provinces and districts as needed
            ],
            'Lebap' => [
                'Türkmenabat' => [
                    'Türkmenabat' => ['Türkmenabat', 'Magdanly', 'Gowurdak'],
                ],
                'Kerki' => [
                    'Kerki' => ['Kerki', 'Halaç', 'Seýdi'],
                ],
                // Add more provinces and districts as needed
            ],
            'Mary' => [
                'Mary' => [
                    'Mary' => ['Mary', 'Baýramaly', 'Ýolöten'],
                ],
                'Sakarçäge' => [
                    'Sakarçäge' => ['Sakarçäge', 'Garagum', 'Tagtabazar'],
                ],
                // Add more provinces and districts as needed
            ],
            'Aşgabat' => [
                'Aşgabat' => [
                    'Aşgabat' => ['Aşgabat', 'Büzmeýin', 'Ruhabat'],
                ],
            ],
        ];
    }
}