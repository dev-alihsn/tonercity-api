<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductTranslation;
use App\Models\User;
use App\Models\Wishlist;

beforeEach(function (): void {
    $this->user = User::factory()->customer()->create();
    $this->token = $this->user->createToken('api')->plainTextToken;
    $this->category = Category::factory()->withoutTranslations()->create(['parent_id' => null]);
    $this->product = Product::factory()->withoutTranslations()->create([
        'category_id' => $this->category->id,
        'is_active' => true,
    ]);
    ProductTranslation::create([
        'product_id' => $this->product->id,
        'locale' => 'en',
        'name' => 'Wishlist Product',
        'description' => 'Desc',
    ]);
});

test('authenticated user can list empty wishlist', function () {
    $response = $this->getJson('/api/v1/wishlist', [
        'Authorization' => 'Bearer '.$this->token,
    ]);

    $response->assertSuccessful()
        ->assertJsonPath('meta.count', 0)
        ->assertJsonCount(0, 'data');
});

test('authenticated user can add product to wishlist', function () {
    $response = $this->postJson('/api/v1/wishlist/'.$this->product->id, [], [
        'Authorization' => 'Bearer '.$this->token,
    ]);

    $response->assertCreated()
        ->assertJsonPath('product_id', $this->product->id);

    $this->assertDatabaseHas('wishlists', [
        'user_id' => $this->user->id,
        'product_id' => $this->product->id,
    ]);
});

test('adding same product again does not duplicate wishlist entry', function () {
    Wishlist::query()->create([
        'user_id' => $this->user->id,
        'product_id' => $this->product->id,
    ]);

    $response = $this->postJson('/api/v1/wishlist/'.$this->product->id, [], [
        'Authorization' => 'Bearer '.$this->token,
    ]);

    $response->assertCreated();

    expect(Wishlist::query()->where('user_id', $this->user->id)->where('product_id', $this->product->id)->count())->toBe(1);
});

test('authenticated user can remove product from wishlist', function () {
    Wishlist::query()->create([
        'user_id' => $this->user->id,
        'product_id' => $this->product->id,
    ]);

    $response = $this->deleteJson('/api/v1/wishlist/'.$this->product->id, [], [
        'Authorization' => 'Bearer '.$this->token,
    ]);

    $response->assertSuccessful();

    $this->assertDatabaseMissing('wishlists', [
        'user_id' => $this->user->id,
        'product_id' => $this->product->id,
    ]);
});

test('wishlist list returns only active products', function () {
    $inactive = Product::factory()->withoutTranslations()->create(['category_id' => $this->category->id, 'is_active' => false]);
    ProductTranslation::create(['product_id' => $inactive->id, 'locale' => 'en', 'name' => 'Inactive', 'description' => '']);
    Wishlist::query()->create(['user_id' => $this->user->id, 'product_id' => $inactive->id]);
    Wishlist::query()->create(['user_id' => $this->user->id, 'product_id' => $this->product->id]);

    $response = $this->getJson('/api/v1/wishlist', [
        'Authorization' => 'Bearer '.$this->token,
    ]);

    $response->assertSuccessful()
        ->assertJsonPath('meta.count', 1)
        ->assertJsonCount(1, 'data');
});

test('unauthenticated user cannot access wishlist', function () {
    $this->getJson('/api/v1/wishlist')->assertUnauthorized();
    $this->postJson('/api/v1/wishlist/'.$this->product->id)->assertUnauthorized();
    $this->deleteJson('/api/v1/wishlist/'.$this->product->id)->assertUnauthorized();
});
