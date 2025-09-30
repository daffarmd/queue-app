<?php

namespace Database\Seeders;

use App\Models\Destination;
use Illuminate\Database\Seeder;

class DestinationSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $destinations = [
            [
                'name' => 'Room 101',
                'code' => 'R101',
                'description' => 'Patient room 101 - General ward',
            ],
            [
                'name' => 'Room 102',
                'code' => 'R102',
                'description' => 'Patient room 102 - General ward',
            ],
            [
                'name' => 'ICU Room 1',
                'code' => 'ICU1',
                'description' => 'Intensive Care Unit Room 1',
            ],
            [
                'name' => 'Emergency Room',
                'code' => 'ER',
                'description' => 'Emergency treatment room',
            ],
            [
                'name' => 'Operating Theater 1',
                'code' => 'OT1',
                'description' => 'Operating theater room 1',
            ],
            [
                'name' => 'X-Ray Department',
                'code' => 'XRAY',
                'description' => 'Radiology X-Ray department',
            ],
            [
                'name' => 'Laboratory',
                'code' => 'LAB',
                'description' => 'Clinical laboratory',
            ],
            [
                'name' => 'Pharmacy',
                'code' => 'PHARM',
                'description' => 'Hospital pharmacy',
            ],
        ];

        foreach ($destinations as $destination) {
            Destination::create($destination);
        }
    }
}
