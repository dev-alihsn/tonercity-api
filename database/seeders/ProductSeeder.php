<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::query()->whereNull('parent_id')->get();

        Product::factory()
            ->count(fake()->numberBetween(3, 8))
            ->create()
            ->each(function (Product $product) use ($categories): void {
                // Translations are now created automatically by the factory's afterCreating hook
                Inventory::factory()->create([
                    'product_id' => $product->id,
                    'quantity' => fake()->numberBetween(5, 100),
                ]);

                foreach ($categories as $category) {
                    $product->categories()->attach($category->id);
                }
            });
    }
}
