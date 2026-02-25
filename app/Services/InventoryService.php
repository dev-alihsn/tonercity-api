<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InventoryService
{
    public function hasStock(Product $product, int $quantity): bool
    {
        $inventory = $product->inventory;

        if (! $inventory) {
            return false;
        }

        return $inventory->hasStock($quantity);
    }

    public function ensureHasStock(Product $product, int $quantity): void
    {
        if (! $this->hasStock($product, $quantity)) {
            throw new RuntimeException("Insufficient stock for product [{$product->id}].");
        }
    }

    public function deduct(Product $product, int $quantity): Inventory
    {
        if ($quantity < 1) {
            throw new RuntimeException('Quantity to deduct must be at least 1.');
        }

        return DB::transaction(function () use ($product, $quantity): Inventory {
            /** @var Inventory|null $inventory */
            $inventory = Inventory::query()
                ->where('product_id', $product->id)
                ->lockForUpdate()
                ->first();

            if (! $inventory || ! $inventory->hasStock($quantity)) {
                throw new RuntimeException("Insufficient stock for product [{$product->id}].");
            }

            $inventory->decrement('quantity', $quantity);

            return $inventory->refresh();
        });
    }

    public function restock(Product $product, int $quantity): Inventory
    {
        if ($quantity < 1) {
            throw new RuntimeException('Quantity to restock must be at least 1.');
        }

        return DB::transaction(function () use ($product, $quantity): Inventory {
            /** @var Inventory $inventory */
            $inventory = Inventory::query()->firstOrCreate(
                ['product_id' => $product->id],
                ['quantity' => 0],
            );

            $inventory->increment('quantity', $quantity);

            return $inventory->refresh();
        });
    }

    public function isLowStock(Product $product): bool
    {
        $inventory = $product->inventory;

        if (! $inventory) {
            return true;
        }

        return $inventory->isLowStock();
    }
}

