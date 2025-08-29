<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Enums\UserRoleEnum;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'username'          => 'admin',
            'email'             => 'admin@herepay.com',
            'email_verified_at' => now(),
            'password'          => 'password',
            'role'              => UserRoleEnum::ADMIN->value
        ]);

        User::create([
            'username'          => 'customer',
            'email'             => 'customer@herepay.com',
            'email_verified_at' => now(),
            'password'          => 'password',
            'role'              => UserRoleEnum::CUSTOMER->value
        ]);
    }
}
