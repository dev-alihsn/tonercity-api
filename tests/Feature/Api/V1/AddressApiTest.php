<?php

use App\Models\Address;
use App\Models\User;

test('authenticated user can list addresses', function () {
    $user = User::factory()->customer()->create();
    Address::factory()->count(2)->create(['user_id' => $user->id]);
    $token = $user->createToken('api')->plainTextToken;

    $response = $this->getJson('/api/v1/addresses', [
        'Authorization' => 'Bearer '.$token,
    ]);

    $response->assertSuccessful()
        ->assertJsonCount(2, 'data');
});

test('unauthenticated user cannot list addresses', function () {
    $this->getJson('/api/v1/addresses')->assertUnauthorized();
});

test('authenticated user can create address', function () {
    $user = User::factory()->customer()->create();
    $token = $user->createToken('api')->plainTextToken;

    $response = $this->postJson('/api/v1/addresses', [
        'city' => 'Riyadh',
        'address_line' => '123 Main St',
        'phone' => '+966501234567',
        'is_default' => true,
    ], [
        'Authorization' => 'Bearer '.$token,
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.city', 'Riyadh')
        ->assertJsonPath('data.is_default', true);

    $this->assertDatabaseHas('addresses', [
        'user_id' => $user->id,
        'city' => 'Riyadh',
    ]);
});

test('authenticated user can show own address', function () {
    $user = User::factory()->customer()->create();
    $address = Address::factory()->create(['user_id' => $user->id]);
    $token = $user->createToken('api')->plainTextToken;

    $response = $this->getJson('/api/v1/addresses/'.$address->id, [
        'Authorization' => 'Bearer '.$token,
    ]);

    $response->assertSuccessful()
        ->assertJsonPath('data.id', $address->id);
});

test('authenticated user cannot show another user address', function () {
    $user = User::factory()->customer()->create();
    $otherAddress = Address::factory()->create(); // belongs to another user
    $token = $user->createToken('api')->plainTextToken;

    $this->getJson('/api/v1/addresses/'.$otherAddress->id, [
        'Authorization' => 'Bearer '.$token,
    ])->assertNotFound();
});

test('authenticated user can update own address', function () {
    $user = User::factory()->customer()->create();
    $address = Address::factory()->create(['user_id' => $user->id, 'city' => 'Riyadh']);
    $token = $user->createToken('api')->plainTextToken;

    $response = $this->putJson('/api/v1/addresses/'.$address->id, [
        'city' => 'Jeddah',
        'address_line' => $address->address_line,
        'phone' => $address->phone,
    ], [
        'Authorization' => 'Bearer '.$token,
    ]);

    $response->assertSuccessful()
        ->assertJsonPath('data.city', 'Jeddah');
});

test('authenticated user can delete own address', function () {
    $user = User::factory()->customer()->create();
    $address = Address::factory()->create(['user_id' => $user->id]);
    $token = $user->createToken('api')->plainTextToken;

    $response = $this->deleteJson('/api/v1/addresses/'.$address->id, [], [
        'Authorization' => 'Bearer '.$token,
    ]);

    $response->assertNoContent();
    $this->assertDatabaseMissing('addresses', ['id' => $address->id]);
});
