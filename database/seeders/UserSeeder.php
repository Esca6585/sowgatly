<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create specific users
        $this->createSpecificUsers();

        // Create additional random users
        User::factory()->count(10)->create();
    }

    private function createSpecificUsers()
    {
        $users = [
            [
                'phone_number' => '65656585',
                'email' => 'esca656585@gmail.com',
                'name' => 'Rahymberdi Ahmedow',
                'image' => 'user/user-seeder/logo.png',
            ],
            [
                'phone_number' => '71406778',
                'email' => 'esca6585@gmail.com',
                'name' => 'Rahymberdi Ahmedow',
                'image' => 'user/user-seeder/sowgatly-logo.png',
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                [
                    'phone_number' => $userData['phone_number'],
                    'email' => $userData['email'],
                ],
                [
                    'name' => $userData['name'],
                    'image' => $userData['image'],
                    'password' => Hash::make('password'),
                    'status' => true,
                ]
            );
        }
    }
}