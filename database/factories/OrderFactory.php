<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 50, 1000);
        $shippingCost = fake()->randomFloat(2, 5, 30);
        $vatAmount = round($subtotal * 0.15, 2);
        $totalAmount = $subtotal + $shippingCost + $vatAmount;

        return [
            'user_id' => User::factory(),
            'address_id' => Address::factory(),
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'vat_amount' => $vatAmount,
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'payment_status' => 'pending',
            'notes' => fake()->optional(0.2)->sentence(),
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'payment_status' => 'paid',
        ]);
    }

    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
            'address_id' => Address::factory()->for($user)->create()->id,
        ]);
    }
}
