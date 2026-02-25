<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OrderService
{
    public function __construct(
        private readonly InventoryService $inventoryService,
        private readonly CartService $cartService,
    ) {}

    /**
     * Create an order from the user's cart and lock inventory atomically.
     *
     * @throws RuntimeException when cart is empty or stock is insufficient
     */
    public function createFromCart(User $user, int $addressId): Order
    {
        $cart = $this->cartService->getOrCreateCart($user);

        if ($cart->items->isEmpty()) {
            throw new RuntimeException('Cart is empty.');
        }

        return DB::transaction(function () use ($cart, $user, $addressId): Order {
            $cart->load(['items.product']);

            $subtotal = 0.0;

            foreach ($cart->items as $item) {
                /** @var Product $product */
                $product = $item->product;

                if (! $product) {
                    throw new ModelNotFoundException("Product for cart item [{$item->id}] not found.");
                }

                // check and deduct stock with InventoryService which uses FOR UPDATE
                $this->inventoryService->ensureHasStock($product, $item->quantity);

                $subtotal += (float) $product->price * $item->quantity;
            }

            $shippingCost = 0.0; // placeholder - shipping calculation can be added later
            $vatAmount = round($subtotal * 0.15, 2);
            $total = round($subtotal + $shippingCost + $vatAmount, 2);

            $order = Order::query()->create([
                'user_id' => $user->id,
                'address_id' => $addressId,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'vat_amount' => $vatAmount,
                'total_amount' => $total,
                'status' => 'pending',
                'payment_status' => 'pending',
            ]);

            foreach ($cart->items as $item) {
                $product = $item->product;

                // Deduct stock (lock inside InventoryService)
                $this->inventoryService->deduct($product, $item->quantity);

                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item->quantity,
                    'unit_price' => $product->price,
                    'total_price' => round($product->price * $item->quantity, 2),
                ]);
            }

            // Clear the cart
            $this->cartService->clear($cart);

            return $order->refresh();
        });
    }
}
