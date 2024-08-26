<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shop;
use App\Models\Address;
use Illuminate\Support\Facades\DB;

class AddressSeeder extends Seeder
{
    protected $turkmenCities = [
        'Ashgabat' => '744000',
        'Turkmenabat' => '746100',
        'Dashoguz' => '746000',
        'Mary' => '745400',
        'Balkanabat' => '745100',
    ];

    protected $streetNames = [
        'Magtymguly', 'Bitarap', 'Garassyzlyk', 'Oguzhan', 'Andalyp',
        'Gorogly', 'Atatürk', 'Berkararlyk', 'Galkynysh', 'Parahat',
    ];

    public function run()
    {
        ini_set('memory_limit', '1G');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Address::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->seedAddresses();
        $this->ensureMajorCitiesRepresented();
    }

    protected function seedAddresses()
    {
        $batchSize = 10;
        Shop::doesntHave('address')->chunkById($batchSize, function ($shops) {
            foreach ($shops as $shop) {
                $this->createAddress($shop->id);
            }
            $this->command->info("Processed " . $shops->count() . " shops");
        });
    }

    protected function createAddress($shopId)
    {
        $city = array_rand($this->turkmenCities);
        Address::create([
            'shop_id' => $shopId,
            'address_name' => $this->generateAddressName($city),
            'postal_code' => $this->turkmenCities[$city],
        ]);
    }

    protected function generateAddressName($city)
    {
        $street = $this->streetNames[array_rand($this->streetNames)];
        $number = mt_rand(1, 100);
        return "$street Street, $number, $city";
    }

    protected function ensureMajorCitiesRepresented()
    {
        foreach ($this->turkmenCities as $city => $postalCode) {
            if (!Address::where('address_name', 'like', "%$city%")->exists()) {
                $shop = Shop::factory()->create();
                Address::create([
                    'shop_id' => $shop->id,
                    'address_name' => $this->generateAddressName($city),
                    'postal_code' => $postalCode,
                ]);
            }
        }
    }
}