<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user for the queue management system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating admin user...');

        try {
            $admin = \App\Models\User::firstOrCreate(
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
                $this->info('Admin role assigned.');
            } else {
                $this->info('User already has Admin role.');
            }

            $this->info('✅ Admin user created successfully!');
            $this->table(['Field', 'Value'], [
                ['Email', 'admin@trimulyo.com'],
                ['Password', 'password123'],
                ['Role', 'Admin'],
            ]);

        } catch (\Exception $e) {
            $this->error('❌ Error: '.$e->getMessage());

            return 1;
        }

        return 0;
    }
}
