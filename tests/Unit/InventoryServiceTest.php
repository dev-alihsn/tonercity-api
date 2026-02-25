<?php

use App\Models\Inventory;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\artisan;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('checks stock availability', function () {
    artisan('migrate');

    $product = Product::factory()->create();
    Inventory::factory()->create([
        'product_id' => $product->id,
        'quantity' => 10,
    ]);

    $service = app(InventoryService::class);

    expect($service->hasStock($product, 5))->toBeTrue()
        ->and($service->hasStock($product, 11))->toBeFalse();
});

it('deducts stock when sufficient', function () {
    artisan('migrate');

    $product = Product::factory()->create();
    Inventory::factory()->create([
        'product_id' => $product->id,
        'quantity' => 10,
    ]);

    $service = app(InventoryService::class);

    $updated = $service->deduct($product, 4);

    expect($updated->quantity)->toBe(6);
});

it('throws when deducting more than available', function () {
    artisan('migrate');

    $product = Product::factory()->create();
    Inventory::factory()->create([
        'product_id' => $product->id,
        'quantity' => 3,
    ]);

    $service = app(InventoryService::class);

    $this->expectException(RuntimeException::class);

    $service->deduct($product, 5);
});

it('restocks inventory', function () {
    artisan('migrate');

    $product = Product::factory()->create();
    $service = app(InventoryService::class);

    $inventory = $service->restock($product, 5);

    expect($inventory->quantity)->toBe(5);

    $inventory = $service->restock($product, 2);

    expect($inventory->quantity)->toBe(7);
});

