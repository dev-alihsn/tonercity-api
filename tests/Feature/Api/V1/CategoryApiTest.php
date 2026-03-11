<?php

use App\Models\Category;

test('guest can list categories', function () {
    Category::factory()->count(2)->create(['parent_id' => null]);

    $response = $this->getJson('/api/v1/categories');

    $response->assertSuccessful()
        ->assertJsonCount(2, 'data');
});

test('guest can show category', function () {
    $category = Category::factory()->create(['parent_id' => null]);

    $response = $this->getJson('/api/v1/categories/'.$category->id);

    $response->assertSuccessful()
        ->assertJsonPath('data.id', $category->id);
});

test('category list returns nested children', function () {
    $parent = Category::factory()->create(['parent_id' => null]);
    $child = Category::factory()->create(['parent_id' => $parent->id]);

    $response = $this->getJson('/api/v1/categories');

    $response->assertSuccessful()
        ->assertJsonPath('data.0.children.0.id', $child->id);
});
