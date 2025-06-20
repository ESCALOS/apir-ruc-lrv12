<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'name' => 'Carlos Escate',
            'email' => 'stornblood6969@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        // Company::factory(20)->create();
    }
}
