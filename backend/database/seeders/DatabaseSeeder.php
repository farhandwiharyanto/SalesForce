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
            'event_type' => 'deal.closed_won',
            'target_url' => 'https://ordersales.farhandwih.my.id/api/webhooks/deals',
            'status_code' => 200,
            'payload' => ['deal_id' => 1, 'amount' => 15000000],
            'response' => '{"message": "Success"}'
        ]);

        \App\Models\WebhookLog::create([
            'event_type' => 'sia_contract.generated',
            'target_url' => 'https://ordersales.farhandwih.my.id/api/webhooks/sia',
            'status_code' => 500,
            'payload' => ['sia_number' => '2026123001', 'deal_id' => 2],
            'response' => '{"error": "Internal Server Error"}'
        ]);

        User::create([
            'name' => 'Admin SalesForce',
            'username' => 'admin',
            'role' => 'admin',
            'password' => Hash::make('password'),
            'menus' => ['Dashboard', 'Deals', 'Customers', 'Leads', 'Products', 'SIA Contracts', 'OrderSales Logs', 'User Management']
        ]);

        User::create([
            'name' => 'Pimpinan Sales',
            'username' => 'pimpinan_sales',
            'role' => 'pimpinan_sales',
            'password' => Hash::make('password'),
            'menus' => ['Dashboard', 'Deals', 'Customers', 'Leads', 'Products', 'SIA Contracts']
        ]);

        User::create([
            'name' => 'Sales',
            'username' => 'sales',
            'role' => 'sales',
            'password' => Hash::make('password'),
            'menus' => ['Dashboard', 'Deals', 'Customers', 'Leads', 'Products']
        ]);
    }
}
