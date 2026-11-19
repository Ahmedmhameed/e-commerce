<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->constraintRenames() as $table => $renames) {
            foreach ($renames as $currentName => $sourceName) {
                DB::statement("ALTER TABLE \"{$table}\" RENAME CONSTRAINT \"{$currentName}\" TO \"{$sourceName}\"");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->constraintRenames() as $table => $renames) {
            foreach ($renames as $currentName => $sourceName) {
                DB::statement("ALTER TABLE \"{$table}\" RENAME CONSTRAINT \"{$sourceName}\" TO \"{$currentName}\"");
            }
        }
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function constraintRenames(): array
    {
        return [
            'addresses' => [
                'addresses_country_id_foreign' => 'fk_addresses_country_id',
                'addresses_user_id_foreign' => 'fk_addresses_user_id',
            ],
            'audit_logs' => [
                'audit_logs_user_id_foreign' => 'fk_audit_logs_user_id',
            ],
            'cart_items' => [
                'cart_items_cart_id_foreign' => 'fk_cart_items_cart_id',
                'cart_items_product_id_foreign' => 'fk_cart_items_product_id',
            ],
            'categories' => [
                'categories_parent_category_id_foreign' => 'fk_categories_parent_category_id',
                'categories_name_unique' => 'categories_name_key',
                'categories_slug_unique' => 'categories_slug_key',
            ],
            'discount_coupons' => [
                'discount_coupons_code_unique' => 'discount_coupons_code_key',
            ],
            'order_items' => [
                'order_items_order_id_foreign' => 'fk_order_items_order_id',
                'order_items_product_id_foreign' => 'fk_order_items_product_id',
            ],
            'order_returns' => [
                'order_returns_order_item_id_foreign' => 'fk_order_returns_order_item_id',
                'order_returns_reason_id_foreign' => 'fk_order_returns_reason_id',
                'order_returns_order_item_id_unique' => 'order_returns_order_item_id_key',
            ],
            'order_statuses' => [
                'order_statuses_status_name_unique' => 'order_statuses_status_name_key',
            ],
            'orders' => [
                'orders_billing_address_id_foreign' => 'fk_orders_billing_address_id',
                'orders_coupon_id_foreign' => 'fk_orders_coupon_id',
                'orders_order_status_id_foreign' => 'fk_orders_order_status_id',
                'orders_shipping_address_id_foreign' => 'fk_orders_shipping_address_id',
                'orders_shipping_method_id_foreign' => 'fk_orders_shipping_method_id',
                'orders_user_id_foreign' => 'fk_orders_user_id',
            ],
            'payment_methods' => [
                'payment_methods_name_unique' => 'payment_methods_name_key',
            ],
            'payments' => [
                'payments_order_id_foreign' => 'fk_payments_order_id',
                'payments_payment_method_id_foreign' => 'fk_payments_payment_method_id',
                'payments_order_id_unique' => 'payments_order_id_key',
                'payments_transaction_reference_unique' => 'payments_transaction_reference_key',
            ],
            'product_attribute_values' => [
                'product_attribute_values_attribute_id_foreign' => 'fk_product_attribute_values_attribute_id',
                'product_attribute_values_product_id_foreign' => 'fk_product_attribute_values_product_id',
            ],
            'product_attributes' => [
                'product_attributes_name_unique' => 'product_attributes_name_key',
            ],
            'product_images' => [
                'product_images_product_id_foreign' => 'fk_product_images_product_id',
                'product_images_url_unique' => 'product_images_url_key',
            ],
            'product_inventories' => [
                'product_inventories_product_id_foreign' => 'fk_product_inventories_product_id',
                'product_inventories_warehouse_id_foreign' => 'fk_product_inventories_warehouse_id',
            ],
            'product_reviews' => [
                'product_reviews_product_id_foreign' => 'fk_product_reviews_product_id',
                'product_reviews_user_id_foreign' => 'fk_product_reviews_user_id',
            ],
            'products' => [
                'products_brand_id_foreign' => 'fk_products_brand_id',
                'products_category_id_foreign' => 'fk_products_category_id',
                'products_sku_unique' => 'products_sku_key',
                'products_slug_unique' => 'products_slug_key',
            ],
            'return_reasons' => [
                'return_reasons_reason_text_unique' => 'return_reasons_reason_text_key',
            ],
            'shipments' => [
                'shipments_order_id_foreign' => 'fk_shipments_order_id',
                'shipments_order_id_unique' => 'shipments_order_id_key',
                'shipments_tracking_number_unique' => 'shipments_tracking_number_key',
            ],
            'shipping_methods' => [
                'shipping_methods_name_unique' => 'shipping_methods_name_key',
            ],
            'shopping_carts' => [
                'shopping_carts_user_id_foreign' => 'fk_shopping_carts_user_id',
            ],
            'support_messages' => [
                'support_messages_ticket_id_foreign' => 'fk_support_messages_ticket_id',
            ],
            'support_tickets' => [
                'support_tickets_user_id_foreign' => 'fk_support_tickets_user_id',
            ],
            'tax_rates' => [
                'tax_rates_country_id_foreign' => 'fk_tax_rates_country_id',
            ],
            'users' => [
                'users_email_unique' => 'users_email_key',
            ],
            'warehouses' => [
                'warehouses_country_id_foreign' => 'fk_warehouses_country_id',
                'warehouses_name_unique' => 'warehouses_name_key',
            ],
            'wishlist_items' => [
                'wishlist_items_product_id_foreign' => 'fk_wishlist_items_product_id',
                'wishlist_items_wishlist_id_foreign' => 'fk_wishlist_items_wishlist_id',
            ],
            'wishlists' => [
                'wishlists_user_id_foreign' => 'fk_wishlists_user_id',
            ],
        ];
    }
};
