<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'billing_address_id' => AddressFactory::new(),
            'coupon_id' => null,
            'created_at' => now(),
            'discount_amount' => 0,
            'order_status_id' => OrderStatusFactory::new(),
            'shipping_address_id' => AddressFactory::new(),
            'shipping_cost' => 5,
            'shipping_method_id' => ShippingMethodFactory::new(),
            'subtotal' => 100,
            'tax_amount' => 5,
            'total' => 110,
            'user_id' => UserFactory::new(),
        ];
    }
}
