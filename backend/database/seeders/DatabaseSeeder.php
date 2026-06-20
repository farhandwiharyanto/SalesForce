<?php

namespace Database\Seeders;

use App\Models\User;
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
            'name' => 'Admin User',
            'email' => 'admin@salesforce.com',
            'password' => Hash::make('password123'),
            'role' => 'admin'
        ]);

        User::create([
            'name' => 'Budi (Pimpinan Sales)',
            'email' => 'pimpinan@salesforce.com',
            'password' => Hash::make('password123'),
            'role' => 'pimpinan_sales'
        ]);

        User::create([
            'name' => 'Andi (Sales Rep)',
            'email' => 'sales@salesforce.com',
            'password' => Hash::make('password123'),
            'role' => 'sales'
        ]);
    }
}
