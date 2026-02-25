<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CartService
{
    public function __construct(
        private readonly InventoryService $inventoryService
    ) {}

    public function getOrCreateCart(User $user): Cart
    {
        $cart = Cart::query()
            ->where('user_id', $user->id)
            ->with(['items.product'])
            ->first();

        if ($cart) {
            return $cart;
        }

        return Cart::query()->create([
            'user_id' => $user->id,
        ]);
    }

    public function addItem(Cart $cart, Product $product, int $quantity = 1): CartItem
    {
        if ($quantity < 1) {
            throw new RuntimeException('Quantity must be at least 1.');
        }

        $this->inventoryService->ensureHasStock($product, $quantity);

        return DB::transaction(function () use ($cart, $product, $quantity): CartItem {
            $item = $cart->items()->where('product_id', $product->id)->first();

            if ($item) {
                $newQuantity = $item->quantity + $quantity;
                $this->inventoryService->ensureHasStock($product, $newQuantity);
                $item->update(['quantity' => $newQuantity]);

                return $item->refresh();
            }

            return $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);
        });
    }

    public function updateQuantity(Cart $cart, Product $product, int $quantity): CartItem
    {
        $item = $cart->items()->where('product_id', $product->id)->firstOrFail();

        if ($quantity < 1) {
            $item->delete();

            throw new RuntimeException('Quantity must be at least 1; use remove to delete item.');
        }

        $this->inventoryService->ensureHasStock($product, $quantity);

        $item->update(['quantity' => $quantity]);

        return $item->refresh();
    }

    public function removeItem(Cart $cart, Product $product): void
    {
        $cart->items()->where('product_id', $product->id)->delete();
    }

    public function clear(Cart $cart): void
    {
        $cart->items()->delete();
    }

    /**
     * @return array{items_count: int, subtotal: float}
     */
    public function calculateTotals(Cart $cart): array
    {
        $cart->load(['items.product']);

        $itemsCount = 0;
        $subtotal = 0.0;

        foreach ($cart->items as $item) {
            $itemsCount += $item->quantity;
            $subtotal += (float) $item->product->price * $item->quantity;
        }

        return [
            'items_count' => $itemsCount,
            'subtotal' => round($subtotal, 2),
        ];
    }
}
