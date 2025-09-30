<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        \Spatie\Permission\Models\Role::create(['name' => 'Admin']);
        \Spatie\Permission\Models\Role::create(['name' => 'Staff']);
        \Spatie\Permission\Models\Role::create(['name' => 'Doctor']);
        \Spatie\Permission\Models\Role::create(['name' => 'Display']);

        // Create admin user
        $admin = \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@trimulyo.com',
            'password' => bcrypt('password123'),
        ]);
        $admin->assignRole('Admin');

        // Create staff user
        $staff = \App\Models\User::create([
            'name' => 'Staff User',
            'email' => 'staff@trimulyo.com',
            'password' => bcrypt('password123'),
        ]);
        $staff->assignRole('Staff');
    }
}
