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
        \App\Models\WebhookLog::create([
            'event_type' => 'opty.closed_won',
            'target_url' => 'https://ordersales.farhandwih.my.id/api/webhooks/optys',
            'status_code' => 200,
            'payload' => ['opty_id' => 1, 'amount' => 15000000],
            'response' => '{"message": "Success"}'
        ]);

        \App\Models\WebhookLog::create([
            'event_type' => 'sia_contract.generated',
            'target_url' => 'https://ordersales.farhandwih.my.id/api/webhooks/sia',
            'status_code' => 500,
            'payload' => ['sia_number' => '2026123001', 'opty_id' => 2],
            'response' => '{"error": "Internal Server Error"}'
        ]);

        User::create([
            'first_name' => 'Admin',
            'last_name' => 'SalesForce',
            'email' => 'admin@example.com',
            'username' => 'admin',
            'role' => 'admin',
            'password' => Hash::make('password'),
            'menus' => ['Dashboard', 'Opty', 'Customers', 'Leads', 'Products', 'Service Instance Account', 'Contract', 'OrderSales Logs', 'User Management', 'Semua API']
        ]);

        User::create([
            'first_name' => 'Pimpinan',
            'last_name' => 'Sales',
            'email' => 'pimpinan@example.com',
            'username' => 'pimpinan_sales',
            'role' => 'pimpinan_sales',
            'password' => Hash::make('password'),
            'menus' => ['Dashboard', 'Opty', 'Customers', 'Leads', 'Products', 'Service Instance Account', 'Contract']
        ]);

        User::create([
            'first_name' => 'Sales',
            'last_name' => 'Agent',
            'email' => 'sales@example.com',
            'username' => 'sales',
            'role' => 'sales',
            'password' => Hash::make('password'),
            'menus' => ['Dashboard', 'Opty', 'Customers', 'Leads', 'Products']
        ]);
    }
}
