<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\WishlistService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WishlistController extends Controller
{
    public function __construct(
        private readonly WishlistService $wishlistService
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $items = $this->wishlistService->list(request()->user());

        $products = $items->map->product->filter->is_active;

        return ProductResource::collection($products)->additional([
            'meta' => ['count' => $products->count()],
        ]);
    }

    public function store(Product $product): JsonResponse
    {
        $this->wishlistService->add(request()->user(), $product);

        return response()->json([
            'message' => __('Product added to wishlist.'),
            'product_id' => $product->id,
        ], 201);
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->wishlistService->remove(request()->user(), $product);

        return response()->json(['message' => __('Product removed from wishlist.')], 200);
    }
}
