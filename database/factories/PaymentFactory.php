<?php

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'amount' => fake()->randomFloat(2, 3, 160),
            'order_id' => OrderFactory::new(),
            'payment_method_id' => PaymentMethodFactory::new(),
            'processed_at' => now(),
            'status' => fake()->randomElement(Payment::ALLOWED_STATUS),
            'transaction_reference' => fake()->unique()->regexify('[A-Z0-9]{16}'),
        ];
    }
}
