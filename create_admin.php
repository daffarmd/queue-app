<?php

require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use App\Models\User;

try {
    // Create admin user
    $admin = User::firstOrCreate(
        ['email' => 'admin@trimulyo.com'],
        [
            'name' => 'Admin User',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]
    );

    // Assign admin role if not already assigned
    if (! $admin->hasRole('Admin')) {
        $admin->assignRole('Admin');
    }

    echo "✅ Admin user created successfully!\n";
    echo "Email: admin@trimulyo.com\n";
    echo "Password: password123\n";
    echo "Role: Admin\n";
} catch (Exception $e) {
    echo '❌ Error: '.$e->getMessage()."\n";
}
