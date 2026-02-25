<?php

use App\Models\Category;
use App\Models\CategoryTranslation;

test('guest can list categories', function () {
    Category::factory()->withoutTranslations()->count(2)->create(['parent_id' => null])
        ->each(function (Category $cat): void {
            CategoryTranslation::create(['category_id' => $cat->id, 'locale' => 'en', 'name' => 'Cat '.$cat->id]);
        });

    $response = $this->getJson('/api/v1/categories');

    $response->assertSuccessful()
        ->assertJsonCount(2, 'data');
});

test('guest can show category', function () {
    $category = Category::factory()->withoutTranslations()->create(['parent_id' => null]);
    CategoryTranslation::create(['category_id' => $category->id, 'locale' => 'en', 'name' => 'Toner']);

    $response = $this->getJson('/api/v1/categories/'.$category->id);

    $response->assertSuccessful()
        ->assertJsonPath('data.id', $category->id)
        ->assertJsonPath('data.name', 'Toner');
});

test('category list returns nested children', function () {
    $parent = Category::factory()->withoutTranslations()->create(['parent_id' => null]);
    CategoryTranslation::create(['category_id' => $parent->id, 'locale' => 'en', 'name' => 'Parent']);
    $child = Category::factory()->withoutTranslations()->create(['parent_id' => $parent->id]);
    CategoryTranslation::create(['category_id' => $child->id, 'locale' => 'en', 'name' => 'Child']);

    $response = $this->getJson('/api/v1/categories');

    $response->assertSuccessful()
        ->assertJsonPath('data.0.children.0.name', 'Child');
});
