<?php

use App\Models\User;

test('user can register', function () {
    $data = [
        'name' => 'New User',
        'email' => 'new@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'language' => 'en',
    ];

    $response = $this->postJson('/api/v1/register', $data);

    $response->assertSuccessful()
        ->assertJsonStructure([
            'user' => ['id', 'name', 'email', 'language'],
            'token',
            'token_type',
        ])
        ->assertJsonPath('user.email', 'new@example.com')
        ->assertJsonPath('token_type', 'Bearer');

    $this->assertDatabaseHas('users', ['email' => 'new@example.com']);
});

test('registration requires valid email and password', function () {
    $response = $this->postJson('/api/v1/register', [
        'name' => 'Test',
        'email' => 'invalid',
        'password' => 'short',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['email', 'password']);
});

test('user can login', function () {
    $user = User::factory()->customer()->create([
        'email' => 'login@example.com',
        'password' => 'password',
    ]);

    $response = $this->postJson('/api/v1/login', [
        'email' => 'login@example.com',
        'password' => 'password',
    ]);

    $response->assertSuccessful()
        ->assertJsonStructure(['user', 'token', 'token_type'])
        ->assertJsonPath('user.email', 'login@example.com');
});

test('login fails with wrong credentials', function () {
    User::factory()->create(['email' => 'exists@example.com']);

    $response = $this->postJson('/api/v1/login', [
        'email' => 'exists@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('authenticated user can get me', function () {
    $user = User::factory()->customer()->create();
    $token = $user->createToken('api')->plainTextToken;

    $response = $this->getJson('/api/v1/me', [
        'Authorization' => 'Bearer '.$token,
    ]);

    $response->assertSuccessful()
        ->assertJsonPath('user.id', $user->id)
        ->assertJsonPath('user.email', $user->email);
});

test('unauthenticated user cannot get me', function () {
    $response = $this->getJson('/api/v1/me');

    $response->assertUnauthorized();
});

test('user can logout', function () {
    $user = User::factory()->customer()->create();
    $token = $user->createToken('api')->plainTextToken;

    $response = $this->postJson('/api/v1/logout', [], [
        'Authorization' => 'Bearer '.$token,
    ]);

    $response->assertSuccessful();

    $user->refresh();
    expect($user->tokens()->count())->toBe(0);
});
