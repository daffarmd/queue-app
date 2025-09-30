<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Queue>
 */
class QueueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $service = Service::factory()->create();
        $number = $this->faker->numberBetween(1, 100);

        return [
            'service_id' => $service->id,
            'patient_name' => $this->faker->name(),
            'number' => $number,
            'code' => $service->code.'-'.str_pad($number, 3, '0', STR_PAD_LEFT),
            'counter' => $this->faker->optional()->numerify('#'),
            'status' => $this->faker->randomElement(['waiting', 'called', 'done', 'skipped', 'recalled']),
            'called_at' => $this->faker->optional()->dateTimeThisMonth(),
            'finished_at' => $this->faker->optional()->dateTimeThisMonth(),
        ];
    }
}
