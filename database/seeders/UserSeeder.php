<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::firstOrCreate(
            [
                'phone_number' => 65656585,
                'email' => 'esca656585@gmail.com',
            ],
            [
                'name' => 'Rahymberdi Ahmedow',
                'image' => 'user/user-seeder/logo.png',
                'password' => Hash::make('password'),
                'status' => true,
            ]
        );

        User::firstOrCreate(
            [
                'phone_number' => 71406778,
                'email' => 'esca6585@gmail.com',
            ],
            [
                'name' => 'Rahymberdi Ahmedow',
                'image' => 'user/user-seeder/sowgatly-logo.png',
                'password' => Hash::make('password'),
                'status' => true,
            ]
        );
    }
}
