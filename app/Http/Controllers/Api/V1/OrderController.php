<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(private readonly InventoryService $inventoryService) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $orders = Order::query()
            ->where('user_id', $user->id)
            ->with(['items.product'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json($orders);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        $user = $request->user();

        if ($order->user_id !== $user->id) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        $order->load(['items.product', 'address', 'payment', 'shipment', 'invoice']);

        return response()->json($order);
    }

    public function cancel(Request $request, Order $order): JsonResponse
    {
        $user = $request->user();

        if ($order->user_id !== $user->id) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        if ($order->status !== 'pending') {
            return response()->json(['message' => 'Only pending orders can be cancelled.'], 422);
        }

        return DB::transaction(function () use ($order): JsonResponse {
            // restock items
            foreach ($order->items as $item) {
                $product = $item->product;

                if ($product) {
                    $this->inventoryService->restock($product, $item->quantity);
                }
            }

            $order->update(['status' => 'cancelled']);

            return response()->json(['message' => 'Order cancelled.', 'order' => $order->fresh()]);
        });
    }
}
