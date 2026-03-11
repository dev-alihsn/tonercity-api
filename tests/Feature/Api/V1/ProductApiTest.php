<?php

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;

beforeEach(function (): void {
    $this->category = Category::factory()->withoutTranslations()->create(['parent_id' => null]);
    $this->product = Product::factory()->create([
        'is_active' => true,
    ]);
    $this->product->categories()->attach($this->category->id);
    Inventory::factory()->create(['product_id' => $this->product->id]);
});

test('guest can list products', function () {
    $response = $this->getJson('/api/v1/products');

    $response->assertSuccessful()
        ->assertJsonStructure(['data', 'links', 'meta'])
        ->assertJsonCount(1, 'data');
});

test('guest can filter products by category', function () {
    $otherCategory = Category::factory()->withoutTranslations()->create(['parent_id' => null]);
    $otherProduct = Product::factory()->create(['is_active' => true]);
    $otherProduct->categories()->attach($otherCategory->id);

    $response = $this->getJson('/api/v1/products?category_id='.$this->category->id);

    $response->assertSuccessful()
        ->assertJsonCount(1, 'data');
});

test('guest can show product', function () {
    $response = $this->getJson('/api/v1/products/'.$this->product->id);

    $response->assertSuccessful()
        ->assertJsonPath('data.id', $this->product->id)
        ->assertJsonPath('data.sku', $this->product->sku);

    $this->assertNotEmpty($response['data']['name']);
});

test('inactive product returns 404', function () {
    $this->product->update(['is_active' => false]);

    $this->getJson('/api/v1/products/'.$this->product->id)
        ->assertNotFound();
});
