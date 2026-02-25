<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Eloquent\Collection;

class WishlistService
{
    /**
     * @return Collection<int, Wishlist>
     */
    public function list(User $user): Collection
    {
        return Wishlist::query()
            ->where('user_id', $user->id)
            ->with(['product.translations', 'product.thumbnail', 'product.inventory'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function add(User $user, Product $product): Wishlist
    {
        return Wishlist::query()->firstOrCreate(
            [
                'user_id' => $user->id,
                'product_id' => $product->id,
            ]
        );
    }

    public function remove(User $user, Product $product): void
    {
        Wishlist::query()
            ->where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->delete();
    }

    public function has(User $user, Product $product): bool
    {
        return Wishlist::query()
            ->where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->exists();
    }
}
