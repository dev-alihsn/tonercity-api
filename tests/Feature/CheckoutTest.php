<?php

use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;

test('checkout creates order and deducts inventory', function () {
    $user = User::factory()->create();

    $product = Product::factory()->create(['price' => 10.00]);
    Inventory::factory()->create(['product_id' => $product->id, 'quantity' => 5]);

    $address = Address::factory()->for($user)->create();

    $cart = Cart::query()->create(['user_id' => $user->id]);
    CartItem::query()->create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 2]);

    $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/checkout', [
        'address_id' => $address->id,
    ]);

    $response->assertStatus(201);

    // Inventory should be reduced
    $this->assertDatabaseHas('inventories', [
        'product_id' => $product->id,
        'quantity' => 3,
    ]);

    // Order and order_items should exist
    $this->assertDatabaseCount('orders', 1);
    $this->assertDatabaseCount('order_items', 1);
});

test('checkout fails when insufficient stock', function () {
    $user = User::factory()->create();

    $product = Product::factory()->create(['price' => 10.00]);
    Inventory::factory()->create(['product_id' => $product->id, 'quantity' => 1]);

    $address = Address::factory()->for($user)->create();

    $cart = Cart::query()->create(['user_id' => $user->id]);
    CartItem::query()->create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 2]);

    $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/checkout', [
        'address_id' => $address->id,
    ]);

    $response->assertStatus(422);

    $this->assertDatabaseCount('orders', 0);
});
