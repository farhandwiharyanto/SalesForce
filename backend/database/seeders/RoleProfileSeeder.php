<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleProfileSeeder extends Seeder
{
    public function run(): void
    {
        $menus = ['Dashboard', 'Inbox', 'Opty', 'Customers', 'Leads', 'Products', 'Service Instance Account', 'Contract', 'OrderSales Logs', 'User Management', 'Semua API'];
        $legacyMenus = function($allowed) use ($menus) {
            $parsed = [];
            foreach ($menus as $m) {
                if (in_array($m, $allowed)) {
                    $parsed[] = ['name' => $m, 'view' => true, 'create' => true, 'edit' => true, 'delete' => true];
                }
            }
            return $parsed;
        };

        $roles = [
            'Admin' => $legacyMenus($menus),
            'Pimpinan Sales' => $legacyMenus(['Dashboard', 'Opty', 'Customers', 'Leads', 'Products', 'Service Instance Account', 'Contract', 'Inbox']),
            'Director Sales' => $legacyMenus(['Dashboard', 'Opty', 'Customers', 'Contract', 'Inbox']),
            'Verificator' => $legacyMenus(['Dashboard', 'Opty', 'Contract', 'Inbox']),
            'Sales' => $legacyMenus(['Dashboard', 'Opty', 'Customers', 'Leads', 'Products', 'Service Instance Account', 'Contract'])
        ];

        foreach ($roles as $name => $roleMenus) {
            \App\Models\RoleProfile::firstOrCreate(['name' => $name], ['menus' => $roleMenus]);
        }
    }
}
