<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // Create Admin User
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'age' => 30,
            'email_verified_at' => now(),
        ]);

        // Create Emergency Company User
        $emergencyUser = User::create([
            'name' => 'Emergency Services',
            'email' => 'emergency@example.com',
            'password' => Hash::make('emergency123'),
            'role' => 'company',
            'age' => 25,
            'email_verified_at' => now(),
        ]);

        Company::create([
            'user_id' => $emergencyUser->id,
            'name' => 'Emergency Response Team',
            'location' => 'City Center',
            'type' => 'emergency',
            'is_active' => true,
        ]);

        // Create Municipality Company User
        $municipalityUser = User::create([
            'name' => 'Municipality',
            'email' => 'municipality@example.com',
            'password' => Hash::make('municipality123'),
            'role' => 'company',
            'age' => 25,
            'email_verified_at' => now(),
        ]);

        Company::create([
            'user_id' => $municipalityUser->id,
            'name' => 'City Municipality',
            'location' => 'Government Building',
            'type' => 'municipality',
            'is_active' => true,
        ]);
    }
}