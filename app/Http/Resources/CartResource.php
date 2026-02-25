<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $cart = $this->resource;
        $cart->loadMissing(['items.product']);
        $itemsCount = 0;
        $subtotal = 0.0;
        foreach ($cart->items as $item) {
            $itemsCount += $item->quantity;
            $subtotal += (float) $item->product->price * $item->quantity;
        }

        return [
            'id' => $this->id,
            'items' => CartItemResource::collection($this->whenLoaded('items')),
            'items_count' => $itemsCount,
            'subtotal' => round($subtotal, 2),
        ];
    }
}
