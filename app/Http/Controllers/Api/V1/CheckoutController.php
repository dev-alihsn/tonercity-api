<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Models\Address;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class CheckoutController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    public function checkout(CheckoutRequest $request): JsonResponse
    {
        $user = $request->user();

        $address = Address::query()
            ->where('id', $request->input('address_id'))
            ->where('user_id', $user->id)
            ->firstOrFail();

        try {
            $order = $this->orderService->createFromCart($user, $address->id);

            return response()->json(['order' => $order], 201);
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
