<?php

use App\Models\Queue;
use App\Models\Service;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Create roles
    Role::create(['name' => 'Admin']);
    Role::create(['name' => 'Staff']);
    Role::create(['name' => 'Doctor']);
});

test('staff user can access staff dashboard', function () {
    $user = User::factory()->create();
    $user->assignRole('Staff');

    $response = $this->actingAs($user)->get('/staff');

    $response->assertStatus(200);
    $response->assertSeeLivewire('staff-dashboard');
});

test('admin user can access staff dashboard', function () {
    $user = User::factory()->create();
    $user->assignRole('Admin');

    $response = $this->actingAs($user)->get('/staff');

    $response->assertStatus(200);
});

test('unauthorized user cannot access staff dashboard', function () {
    $user = User::factory()->create();
    // No role assigned

    $response = $this->actingAs($user)->get('/staff');

    $response->assertStatus(403);
});

test('staff can create queue', function () {
    $user = User::factory()->create();
    $user->assignRole('Staff');
    $service = Service::factory()->create();
    $destination = \App\Models\Destination::factory()->create();

    $response = $this->actingAs($user)->post('/queues', [
        'service_id' => $service->id,
        'destination_id' => $destination->id,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('queues', [
        'service_id' => $service->id,
        'destination_id' => $destination->id,
        'status' => 'waiting',
    ]);
});

test('staff can call queue', function () {
    $user = User::factory()->create();
    $user->assignRole('Staff');
    $service = Service::factory()->create();
    $queue = Queue::factory()->create([
        'service_id' => $service->id,
        'status' => 'waiting',
    ]);

    $response = $this->actingAs($user)->patch("/queues/{$queue->id}/call");

    $response->assertStatus(200);
    $this->assertDatabaseHas('queues', [
        'id' => $queue->id,
        'status' => 'called',
    ]);
});

test('dashboard redirects based on user role', function () {
    // Test Admin redirect
    $admin = User::factory()->create();
    $admin->assignRole('Admin');

    $response = $this->actingAs($admin)->get('/dashboard');
    $response->assertRedirect('/admin');

    // Test Staff redirect
    $staff = User::factory()->create();
    $staff->assignRole('Staff');

    $response = $this->actingAs($staff)->get('/dashboard');
    $response->assertRedirect('/staff');
});
