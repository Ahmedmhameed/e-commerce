<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        $constraints = [
            "ALTER TABLE brands ADD CONSTRAINT ck_brands_description CHECK (description IN ('Sold here since launch', 'Exclusive to this store', 'Ships from the brand', 'Made in the USA', 'Imported'))",
            "ALTER TABLE categories ADD CONSTRAINT ck_categories_description CHECK (description IN ('Free shipping over $35', 'Bestsellers', 'New arrivals weekly', 'Clearance'))",
            "ALTER TABLE categories ADD CONSTRAINT ck_categories_name CHECK (name IN ('Books', 'Movies', 'Music', 'Games', 'Electronics', 'Computers', 'Home', 'Garden', 'Tools', 'Grocery', 'Health', 'Beauty', 'Toys', 'Kids', 'Baby', 'Clothing', 'Shoes', 'Jewelry', 'Sports', 'Outdoors', 'Automotive', 'Office'))",
            'ALTER TABLE discount_coupons ADD CONSTRAINT ck_discount_coupons_discount_value CHECK (discount_value BETWEEN 5 AND 30)',
            "ALTER TABLE order_statuses ADD CONSTRAINT ck_order_statuses_description CHECK (description IN ('Waiting on the customer', 'In the warehouse', 'With the carrier', 'Closed'))",
            "ALTER TABLE order_statuses ADD CONSTRAINT ck_order_statuses_status_name CHECK (status_name IN ('pending', 'paid', 'shipped', 'delivered', 'cancelled', 'refunded'))",
            "ALTER TABLE payment_methods ADD CONSTRAINT ck_payment_methods_name CHECK (name IN ('credit_card', 'debit_card', 'digital_wallet', 'gift_card', 'bank_transfer'))",
            "ALTER TABLE product_attributes ADD CONSTRAINT ck_product_attributes_name CHECK (name IN ('color', 'finish', 'accent_color', 'trim_color'))",
            "ALTER TABLE products ADD CONSTRAINT ck_products_description CHECK (description IN ('Ships in one business day', 'Limited stock', 'Customer favourite', 'Eligible for free returns', 'New this season'))",
            'ALTER TABLE products ADD CONSTRAINT ck_products_price CHECK (price BETWEEN 3 AND 160)',
            "ALTER TABLE product_images ADD CONSTRAINT ck_product_images_alt_text CHECK (alt_text IN ('Front view', 'Side view', 'In use', 'Packaging', 'Close-up detail'))",
            'ALTER TABLE product_images ADD CONSTRAINT ck_product_images_sort_order CHECK (sort_order BETWEEN 1 AND 6)',
            "ALTER TABLE return_reasons ADD CONSTRAINT ck_return_reasons_reason_text CHECK (reason_text IN ('Arrived damaged', 'Wrong item sent', 'Does not fit', 'Not as described', 'Changed my mind', 'Arrived too late'))",
            'ALTER TABLE shipping_methods ADD CONSTRAINT ck_shipping_methods_base_cost CHECK (base_cost BETWEEN 0 AND 35)',
            "ALTER TABLE shipping_methods ADD CONSTRAINT ck_shipping_methods_carrier CHECK (carrier IN ('UPS', 'FedEx', 'USPS', 'DHL', 'In store'))",
            'ALTER TABLE shipping_methods ADD CONSTRAINT ck_shipping_methods_estimated_days CHECK (estimated_days BETWEEN 1 AND 7)',
            "ALTER TABLE shipping_methods ADD CONSTRAINT ck_shipping_methods_name CHECK (name IN ('standard', 'expedited', 'two_day', 'overnight', 'store_pickup'))",
            'ALTER TABLE tax_rates ADD CONSTRAINT ck_tax_rates_rate CHECK (rate BETWEEN 0.02 AND 0.1)',
            "ALTER TABLE audit_logs ADD CONSTRAINT ck_audit_logs_action CHECK (action IN ('insert', 'update', 'delete'))",
            'ALTER TABLE audit_logs ADD CONSTRAINT ck_audit_logs_record_id CHECK (record_id BETWEEN 1 AND 360000)',
            "ALTER TABLE audit_logs ADD CONSTRAINT ck_audit_logs_table_affected CHECK (table_affected IN ('orders', 'products', 'users', 'payments', 'shipments'))",
            'ALTER TABLE order_items ADD CONSTRAINT ck_order_items_quantity CHECK (quantity BETWEEN 1 AND 3)',
            "ALTER TABLE order_returns ADD CONSTRAINT ck_order_returns_status CHECK (status IN ('refunded', 'approved', 'requested', 'rejected'))",
            "ALTER TABLE payments ADD CONSTRAINT ck_payments_status CHECK (status IN ('completed', 'failed', 'refunded'))",
            "ALTER TABLE product_reviews ADD CONSTRAINT ck_product_reviews_comment CHECK (comment IN ('Fast delivery and well packed.', 'Quality is fine for the price.', 'Sizing runs small.', 'Stopped working after a month.', 'Looks just like the photos.', 'Instructions were unclear.', 'Bought a second one as a gift.', 'Returned it for a different colour.'))",
            'ALTER TABLE product_reviews ADD CONSTRAINT ck_product_reviews_rating CHECK (rating BETWEEN 1 AND 5)',
            "ALTER TABLE product_reviews ADD CONSTRAINT ck_product_reviews_title CHECK (title IN ('Does the job', 'Better than expected', 'Not for me', 'Great value', 'Would buy again', 'Arrived damaged', 'Smaller than it looks', 'Exactly as described'))",
            'ALTER TABLE cart_items ADD CONSTRAINT ck_cart_items_quantity CHECK (quantity BETWEEN 1 AND 3)',
            "ALTER TABLE support_tickets ADD CONSTRAINT ck_support_tickets_priority CHECK (priority IN ('low', 'medium', 'high'))",
            "ALTER TABLE support_tickets ADD CONSTRAINT ck_support_tickets_status CHECK (status IN ('open', 'pending', 'resolved', 'closed'))",
            "ALTER TABLE support_tickets ADD CONSTRAINT ck_support_tickets_subject CHECK (subject IN ('Where is my order?', 'Item arrived damaged', 'Change my shipping address', 'Refund not received', 'Coupon did not apply', 'Cannot sign in'))",
            "ALTER TABLE support_messages ADD CONSTRAINT ck_support_messages_message CHECK (message IN ('Thanks, that worked.', 'Can you check the tracking number?', 'I have issued a refund to your card.', 'Please send a photo of the damage.', 'Your replacement ships today.', 'I still have not received it.'))",
            "ALTER TABLE support_messages ADD CONSTRAINT ck_support_messages_sender_type CHECK (sender_type IN ('customer', 'agent'))",
            "ALTER TABLE warehouses ADD CONSTRAINT ck_warehouses_name CHECK (name IN ('East Coast DC', 'West Coast DC', 'Midwest DC', 'Southeast DC', 'Texas DC', 'Mountain DC'))",
            'ALTER TABLE product_inventories ADD CONSTRAINT ck_product_inventories_quantity_on_hand CHECK (quantity_on_hand BETWEEN 0 AND 500)',
            'ALTER TABLE product_inventories ADD CONSTRAINT ck_product_inventories_reorder_level CHECK (reorder_level BETWEEN 5 AND 50)',
            "ALTER TABLE wishlists ADD CONSTRAINT ck_wishlists_name CHECK (name IN ('Wish list', 'Birthday', 'Holiday gifts', 'Home office', 'Save for later'))",
        ];

        foreach ($constraints as $constraint) {
            DB::statement($constraint);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        $constraints = [
            'ck_brands_description' => 'brands',
            'ck_categories_description' => 'categories',
            'ck_categories_name' => 'categories',
            'ck_discount_coupons_discount_value' => 'discount_coupons',
            'ck_order_statuses_description' => 'order_statuses',
            'ck_order_statuses_status_name' => 'order_statuses',
            'ck_payment_methods_name' => 'payment_methods',
            'ck_product_attributes_name' => 'product_attributes',
            'ck_products_description' => 'products',
            'ck_products_price' => 'products',
            'ck_product_images_alt_text' => 'product_images',
            'ck_product_images_sort_order' => 'product_images',
            'ck_return_reasons_reason_text' => 'return_reasons',
            'ck_shipping_methods_base_cost' => 'shipping_methods',
            'ck_shipping_methods_carrier' => 'shipping_methods',
            'ck_shipping_methods_estimated_days' => 'shipping_methods',
            'ck_shipping_methods_name' => 'shipping_methods',
            'ck_tax_rates_rate' => 'tax_rates',
            'ck_audit_logs_action' => 'audit_logs',
            'ck_audit_logs_record_id' => 'audit_logs',
            'ck_audit_logs_table_affected' => 'audit_logs',
            'ck_order_items_quantity' => 'order_items',
            'ck_order_returns_status' => 'order_returns',
            'ck_payments_status' => 'payments',
            'ck_product_reviews_comment' => 'product_reviews',
            'ck_product_reviews_rating' => 'product_reviews',
            'ck_product_reviews_title' => 'product_reviews',
            'ck_cart_items_quantity' => 'cart_items',
            'ck_support_tickets_priority' => 'support_tickets',
            'ck_support_tickets_status' => 'support_tickets',
            'ck_support_tickets_subject' => 'support_tickets',
            'ck_support_messages_message' => 'support_messages',
            'ck_support_messages_sender_type' => 'support_messages',
            'ck_warehouses_name' => 'warehouses',
            'ck_product_inventories_quantity_on_hand' => 'product_inventories',
            'ck_product_inventories_reorder_level' => 'product_inventories',
            'ck_wishlists_name' => 'wishlists',
        ];

        foreach ($constraints as $name => $table) {
            DB::statement("ALTER TABLE {$table} DROP CONSTRAINT {$name}");
        }
    }
};
