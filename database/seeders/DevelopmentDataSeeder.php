<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Country;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\PaymentMethod;
use App\Models\ProductAttribute;
use App\Models\ReturnReason;
use App\Models\ShippingMethod;
use App\Models\User;
use App\Models\Warehouse;
use Database\Factories\AddressFactory;
use Database\Factories\AuditLogFactory;
use Database\Factories\BrandFactory;
use Database\Factories\CartItemFactory;
use Database\Factories\DiscountCouponFactory;
use Database\Factories\OrderFactory;
use Database\Factories\OrderItemFactory;
use Database\Factories\OrderReturnFactory;
use Database\Factories\PaymentFactory;
use Database\Factories\ProductAttributeValueFactory;
use Database\Factories\ProductFactory;
use Database\Factories\ProductImageFactory;
use Database\Factories\ProductInventoryFactory;
use Database\Factories\ProductReviewFactory;
use Database\Factories\ShipmentFactory;
use Database\Factories\ShoppingCartFactory;
use Database\Factories\SupplierFactory;
use Database\Factories\SupportMessageFactory;
use Database\Factories\SupportTicketFactory;
use Database\Factories\UserFactory;
use Database\Factories\WishlistFactory;
use Database\Factories\WishlistItemFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DevelopmentDataSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'development'])) {
            throw new RuntimeException('DevelopmentDataSeeder can only run in the local or development environment.');
        }

        DB::transaction(function (): void {
            if (User::query()->exists()) {
                throw new RuntimeException('Development data already exists. Run php artisan migrate:fresh --seed to rebuild.');
            }

            $country = Country::where('code', 'US')->firstOrFail();
            $users = UserFactory::new()->count(70)->create();
            $addresses = $users->map(fn ($user) => AddressFactory::new()->create([
                'country_id' => $country->id,
                'user_id' => $user->id,
                'is_default_billing' => true,
                'is_default_shipping' => true,
            ]));
            AddressFactory::new()->count(20)->state(fn () => [
                'country_id' => $country->id,
                'user_id' => $users->random()->id,
            ])->create();

            $products = $this->seedCatalog($country);
            $this->seedOrders($addresses, $products);
            $this->seedCustomerActivity($users, $products);
        });
    }

    private function seedCatalog(Country $country): Collection
    {
        foreach (Category::ALLOWED_NAME as $name) {
            Category::firstOrCreate(['name' => $name], [
                'description' => fake()->randomElement(Category::ALLOWED_DESCRIPTION),
                'slug' => fake()->unique()->regexify('[a-z0-9]{12}'),
            ]);
        }

        foreach (Warehouse::ALLOWED_NAME as $name) {
            Warehouse::firstOrCreate(['name' => $name], [
                'address' => mb_substr(fake()->streetAddress(), 0, 101),
                'city' => mb_substr(fake()->city(), 0, 40),
                'country_id' => $country->id,
            ]);
        }

        $brands = BrandFactory::new()->count(40)->create();
        $categories = Category::whereIn('name', Category::ALLOWED_NAME)->get();
        $warehouses = Warehouse::whereIn('name', Warehouse::ALLOWED_NAME)->get();
        $attributes = ProductAttribute::whereIn('name', ProductAttribute::ALLOWED_NAME)->get();

        $products = ProductFactory::new()->count(400)->state(fn () => [
            'brand_id' => $brands->random()->id,
            'category_id' => $categories->random()->id,
        ])->create();

        foreach ($products as $index => $product) {
            foreach ($attributes->random(3) as $attribute) {
                ProductAttributeValueFactory::new()->create([
                    'attribute_id' => $attribute->id,
                    'product_id' => $product->id,
                ]);
            }

            foreach ($warehouses->random(3) as $warehouse) {
                ProductInventoryFactory::new()->create([
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouse->id,
                ]);
            }

            $imageCount = $index < 200 ? 3 : 2;
            for ($position = 1; $position <= $imageCount; $position++) {
                ProductImageFactory::new()->create([
                    'product_id' => $product->id,
                    'sort_order' => $position,
                ]);
            }
        }

        SupplierFactory::new()->count(30)->create();

        return $products;
    }

    private function seedOrders(Collection $addresses, Collection $products): void
    {
        DiscountCouponFactory::new()->count(80)->create();
        $statuses = OrderStatus::whereIn('status_name', ['delivered', 'paid', 'pending'])->get()->keyBy('status_name');
        $shippingMethods = ShippingMethod::whereIn('name', ShippingMethod::ALLOWED_NAME)->get();
        $paymentMethods = PaymentMethod::whereIn('name', PaymentMethod::ALLOWED_NAME)->get();
        $returnReasons = ReturnReason::whereIn('reason_text', ReturnReason::ALLOWED_REASON_TEXT)->get();

        $createdAt = now()->subDays(7);

        for ($index = 0; $index < 360; $index++) {
            $address = $addresses->random();
            $shippingMethod = $shippingMethods->random();
            $status = $index < 340 ? 'delivered' : ($index < 350 ? 'paid' : 'pending');
            $order = OrderFactory::new()->create([
                'billing_address_id' => $address->id,
                'created_at' => $createdAt,
                'order_status_id' => $statuses[$status]->id,
                'shipping_address_id' => $address->id,
                'shipping_cost' => $shippingMethod->base_cost,
                'shipping_method_id' => $shippingMethod->id,
                'user_id' => $address->user_id,
                'tax_amount' => 0,
            ]);

            $subtotalCents = 0;
            foreach ($products->random($index < 280 ? 3 : 2)->values() as $position => $product) {
                $item = OrderItemFactory::new()->create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'unit_price' => $product->price,
                ]);
                $subtotalCents += (int) round((float) $item->unit_price * 100) * $item->quantity;

                if ($index < 20 && $position === 0) {
                    OrderReturnFactory::new()->create([
                        'order_item_id' => $item->id,
                        'reason_id' => $returnReasons->random()->id,
                        'status' => 'approved',
                    ]);
                }
            }

            $shippingCents = (int) round((float) $shippingMethod->base_cost * 100);
            $order->update([
                'subtotal' => $subtotalCents / 100,
                'total' => ($subtotalCents + $shippingCents) / 100,
            ]);

            $this->seedFulfillment($index, $order, $paymentMethods);
        }
    }

    private function seedFulfillment(int $index, Order $order, Collection $paymentMethods): void
    {
        if ($index < 350) {
            PaymentFactory::new()->create([
                'amount' => $order->total,
                'order_id' => $order->id,
                'payment_method_id' => $paymentMethods->random()->id,
                'processed_at' => $order->created_at,
                'status' => 'completed',
            ]);
        }

        if ($index < 340) {
            ShipmentFactory::new()->create([
                'order_id' => $order->id,
                'shipped_at' => $order->created_at->copy()->addDay(),
                'delivered_at' => $order->created_at->copy()->addDays(3),
            ]);
        }

        if ($index < 20 && $order->id <= 360000) {
            AuditLogFactory::new()->create([
                'action' => 'insert',
                'record_id' => $order->id,
                'table_affected' => 'orders',
                'user_id' => $order->user_id,
            ]);
        }
    }

    private function seedCustomerActivity(Collection $users, Collection $products): void
    {
        ProductReviewFactory::new()->count(60)->state(fn () => [
            'product_id' => $products->random()->id,
            'user_id' => $users->random()->id,
        ])->create();

        foreach ($users->take(60) as $index => $user) {
            $cart = ShoppingCartFactory::new()->create(['user_id' => $user->id]);
            foreach ($products->random($index < 40 ? 2 : 1) as $product) {
                CartItemFactory::new()->create(['cart_id' => $cart->id, 'product_id' => $product->id]);
            }
        }

        foreach ($users->take(30) as $user) {
            $wishlist = WishlistFactory::new()->create(['user_id' => $user->id]);
            foreach ($products->random(2) as $product) {
                WishlistItemFactory::new()->create(['wishlist_id' => $wishlist->id, 'product_id' => $product->id]);
            }
        }

        foreach ($users->take(10) as $index => $user) {
            $ticket = SupportTicketFactory::new()->create(['user_id' => $user->id]);
            SupportMessageFactory::new()->count($index < 5 ? 4 : 3)->create(['ticket_id' => $ticket->id]);
        }
    }
}
