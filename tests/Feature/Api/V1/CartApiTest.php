<?php

use App\Models\Cart;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;

beforeEach(function (): void {
    $this->user = User::factory()->customer()->create();
    $this->token = $this->user->createToken('api')->plainTextToken;
    $this->category = Category::factory()->withoutTranslations()->create(['parent_id' => null]);
    $this->product = Product::factory()->create([
        'is_active' => true,
    ]);
    $this->product->categories()->attach($this->category->id);
    Inventory::factory()->create(['product_id' => $this->product->id, 'quantity' => 10]);
});

test('authenticated user can get empty cart', function () {
    $response = $this->getJson('/api/v1/cart', [
        'Authorization' => 'Bearer '.$this->token,
    ]);

    $response->assertSuccessful()
        ->assertJsonPath('data.items_count', 0)
        ->assertJsonPath('data.subtotal', 0);
});

test('authenticated user can add item to cart', function () {
    $response = $this->postJson('/api/v1/cart/items', [
        'product_id' => $this->product->id,
        'quantity' => 2,
    ], [
        'Authorization' => 'Bearer '.$this->token,
    ]);

    $response->assertSuccessful()
        ->assertJsonPath('data.items_count', 2)
        ->assertJsonCount(1, 'data.items');

    $this->assertDatabaseHas('cart_items', [
        'product_id' => $this->product->id,
        'quantity' => 2,
    ]);
});

test('add to cart validates product exists', function () {
    $response = $this->postJson('/api/v1/cart/items', [
        'product_id' => 99999,
        'quantity' => 1,
    ], [
        'Authorization' => 'Bearer '.$this->token,
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['product_id']);
});

test('add to cart fails when insufficient stock', function () {
    Inventory::query()->where('product_id', $this->product->id)->update(['quantity' => 2]);

    $response = $this->postJson('/api/v1/cart/items', [
        'product_id' => $this->product->id,
        'quantity' => 5,
    ], [
        'Authorization' => 'Bearer '.$this->token,
    ]);

    $response->assertStatus(422)
        ->assertJsonFragment(['message' => 'Insufficient stock for product ['.$this->product->id.'].']);
});

test('authenticated user can update cart item quantity', function () {
    $cart = Cart::query()->create(['user_id' => $this->user->id]);
    $cart->items()->create(['product_id' => $this->product->id, 'quantity' => 2]);

    $response = $this->putJson('/api/v1/cart/items/'.$this->product->id, [
        'quantity' => 4,
    ], [
        'Authorization' => 'Bearer '.$this->token,
    ]);

    $response->assertSuccessful()
        ->assertJsonPath('data.items_count', 4);

    $this->assertDatabaseHas('cart_items', [
        'product_id' => $this->product->id,
        'quantity' => 4,
    ]);
});

test('update cart item fails when product not in cart', function () {
    $otherProduct = Product::factory()->create(['is_active' => true]);
    $otherProduct->categories()->attach($this->category->id);
    Inventory::factory()->create(['product_id' => $otherProduct->id]);

    $response = $this->putJson('/api/v1/cart/items/'.$otherProduct->id, [
        'quantity' => 1,
    ], [
        'Authorization' => 'Bearer '.$this->token,
    ]);

    $response->assertNotFound();
});

test('authenticated user can remove item from cart', function () {
    $cart = Cart::query()->create(['user_id' => $this->user->id]);
    $cart->items()->create(['product_id' => $this->product->id, 'quantity' => 1]);

    $response = $this->deleteJson('/api/v1/cart/items/'.$this->product->id, [], [
        'Authorization' => 'Bearer '.$this->token,
    ]);

    $response->assertSuccessful()
        ->assertJsonPath('data.items_count', 0);

    $this->assertDatabaseMissing('cart_items', [
        'product_id' => $this->product->id,
    ]);
});

test('authenticated user can clear cart', function () {
    $cart = Cart::query()->create(['user_id' => $this->user->id]);
    $cart->items()->create(['product_id' => $this->product->id, 'quantity' => 1]);

    $response = $this->deleteJson('/api/v1/cart', [], [
        'Authorization' => 'Bearer '.$this->token,
    ]);

    $response->assertSuccessful()
        ->assertJsonPath('data.items_count', 0);

    expect($cart->items()->count())->toBe(0);
});

test('unauthenticated user cannot access cart', function () {
    $this->getJson('/api/v1/cart')->assertUnauthorized();
    $this->postJson('/api/v1/cart/items', ['product_id' => $this->product->id, 'quantity' => 1])->assertUnauthorized();
});
