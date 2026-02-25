<?php

use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;

test('user can list their orders only', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    Order::factory()->for($user)->create();
    Order::factory()->for($other)->create();

    $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/orders');

    $response->assertStatus(200);
    $data = $response->json('data');

    expect(count($data))->toBe(1);
});

test('user can view their order with items', function () {
    $user = User::factory()->create();

    $product = Product::factory()->create(['price' => 5.00]);
    Inventory::factory()->create(['product_id' => $product->id, 'quantity' => 10]);

    $order = Order::factory()->for($user)->create();
    OrderItem::factory()->for($order)->for($product)->create(['quantity' => 2]);

    $response = $this->actingAs($user, 'sanctum')->getJson("/api/v1/orders/{$order->id}");

    $response->assertStatus(200);
    $response->assertJsonStructure(['id', 'items']);
});

test('user can cancel pending order and inventory is restocked', function () {
    $user = User::factory()->create();

    $product = Product::factory()->create(['price' => 5.00]);
    Inventory::factory()->create(['product_id' => $product->id, 'quantity' => 5]);

    $order = Order::factory()->for($user)->create(['status' => 'pending']);
    OrderItem::factory()->for($order)->for($product)->create(['quantity' => 2]);

    $response = $this->actingAs($user, 'sanctum')->postJson("/api/v1/orders/{$order->id}/cancel");

    $response->assertStatus(200);

    $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'cancelled']);
    $this->assertDatabaseHas('inventories', ['product_id' => $product->id, 'quantity' => 7]);
});

test('cannot cancel a non-pending order', function () {
    $user = User::factory()->create();

    $order = Order::factory()->for($user)->create(['status' => 'paid']);

    $response = $this->actingAs($user, 'sanctum')->postJson("/api/v1/orders/{$order->id}/cancel");

    $response->assertStatus(422);
});
