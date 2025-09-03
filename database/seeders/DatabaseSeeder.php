<?php

namespace Database\Seeders;
use App\Models\User;
use App\Models\Modifier;
use App\Models\ModifierValue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('12345678'),
             'role' => 1,
        ]);

    }
}
