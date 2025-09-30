<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'General Consultation',
                'code' => 'GEN',
                'description' => 'General medical consultation and examination',
            ],
            [
                'name' => 'Pharmacy',
                'code' => 'PHR',
                'description' => 'Medicine dispensing and pharmaceutical services',
            ],
            [
                'name' => 'Laboratory',
                'code' => 'LAB',
                'description' => 'Blood tests and laboratory examinations',
            ],
            [
                'name' => 'Registration',
                'code' => 'REG',
                'description' => 'Patient registration and administrative services',
            ],
        ];

        foreach ($services as $service) {
            \App\Models\Service::create($service);
        }
    }
}
