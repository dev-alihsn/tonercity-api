<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Http\Resources\CartResource;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cartService
    ) {}

    public function show(): CartResource|JsonResponse
    {
        $cart = $this->cartService->getOrCreateCart(request()->user());
        $cart->load(['items.product.translations', 'items.product.thumbnail', 'items.product.inventory']);

        return new CartResource($cart);
    }

    public function addItem(AddToCartRequest $request): JsonResponse
    {
        $cart = $this->cartService->getOrCreateCart($request->user());
        $product = Product::query()->findOrFail($request->validated('product_id'));
        $product->load(['translations', 'thumbnail', 'inventory']);

        try {
            $this->cartService->addItem($cart, $product, $request->validated('quantity'));
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        $cart->load(['items.product.translations', 'items.product.thumbnail', 'items.product.inventory']);

        return (new CartResource($cart->refresh()))->response()->setStatusCode(200);
    }

    public function updateItem(UpdateCartItemRequest $request, Product $product): JsonResponse
    {
        $cart = $this->cartService->getOrCreateCart($request->user());

        if (! $cart->items()->where('product_id', $product->id)->exists()) {
            return response()->json(['message' => 'Product not in cart.'], 404);
        }

        try {
            $this->cartService->updateQuantity($cart, $product, $request->validated('quantity'));
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        $cart->load(['items.product.translations', 'items.product.thumbnail', 'items.product.inventory']);

        return (new CartResource($cart->refresh()))->response()->setStatusCode(200);
    }

    public function removeItem(Product $product): JsonResponse
    {
        $cart = $this->cartService->getOrCreateCart(request()->user());
        $this->cartService->removeItem($cart, $product);
        $cart->load(['items.product.translations', 'items.product.thumbnail', 'items.product.inventory']);

        return (new CartResource($cart->refresh()))->response()->setStatusCode(200);
    }

    public function clear(): JsonResponse
    {
        $cart = $this->cartService->getOrCreateCart(request()->user());
        $this->cartService->clear($cart);

        return (new CartResource($cart->refresh()))->response()->setStatusCode(200);
    }
}
