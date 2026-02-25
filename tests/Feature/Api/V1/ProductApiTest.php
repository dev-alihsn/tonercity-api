<?php

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductTranslation;

beforeEach(function (): void {
    $this->category = Category::factory()->withoutTranslations()->create(['parent_id' => null]);
    $this->product = Product::factory()->withoutTranslations()->create([
        'category_id' => $this->category->id,
        'is_active' => true,
    ]);
    ProductTranslation::create([
        'product_id' => $this->product->id,
        'locale' => 'en',
        'name' => 'Test Product',
        'description' => 'Description',
    ]);
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
    Product::factory()->withoutTranslations()->create(['category_id' => $otherCategory->id, 'is_active' => true]);

    $response = $this->getJson('/api/v1/products?category_id='.$this->category->id);

    $response->assertSuccessful()
        ->assertJsonCount(1, 'data');
});

test('guest can show product', function () {
    $response = $this->getJson('/api/v1/products/'.$this->product->id);

    $response->assertSuccessful()
        ->assertJsonPath('data.id', $this->product->id)
        ->assertJsonPath('data.name', 'Test Product')
        ->assertJsonPath('data.sku', $this->product->sku);
});

test('inactive product returns 404', function () {
    $this->product->update(['is_active' => false]);

    $this->getJson('/api/v1/products/'.$this->product->id)
        ->assertNotFound();
});
