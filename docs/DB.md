# Database spec: e-commerce sample (PostgreSQL, 31 tables)

Source of truth: postgres.sql (1K). Build the schema to match this exactly. Do not redesign.

Rules for the agent:

- Keep table names, column names, types, nullability, unique keys and FKs exactly as listed.
- Keep the CHECK constraints (they encode allowed values).
- Use `numeric(14,2)` for money, `timestamp` for dates, `integer` identity PKs.
- Note the one oddity: `order_items.discount_amount` is `integer` in the source, not numeric. Keep it as source unless told otherwise, and flag it.
- Verify after building: compare table/column/constraint lists against this spec, and load sample data with no FK errors.

## Table index

`brands`, `categories`, `countries`, `discount_coupons`, `order_statuses`, `payment_methods`, `product_attributes`, `products`, `product_attribute_values`, `product_images`, `return_reasons`, `shipping_methods`, `suppliers`, `tax_rates`, `users`, `addresses`, `audit_logs`, `orders`, `order_items`, `order_returns`, `payments`, `product_reviews`, `shipments`, `shopping_carts`, `cart_items`, `support_tickets`, `support_messages`, `warehouses`, `product_inventories`, `wishlists`, `wishlist_items`

## brands

| column      | type         | key / constraint | nullable |
| ----------- | ------------ | ---------------- | -------- |
| id          | integer      | PK (identity)    | no       |
| description | varchar(23)  | -                | no       |
| logo_url    | varchar(39)  | -                | no       |
| name        | varchar(114) | -                | no       |

CHECK constraints:

- `ck_brands_description`: "description" IN ('Sold here since launch', 'Exclusive to this store', 'Ships from the brand', 'Made in the USA', 'Imported')

Relationships:

- has many `products` via `products.brand_id`

## categories

| column             | type         | key / constraint    | nullable |
| ------------------ | ------------ | ------------------- | -------- |
| id                 | integer      | PK (identity)       | no       |
| description        | varchar(22)  | -                   | no       |
| name               | varchar(11)  | UNIQUE              | no       |
| parent_category_id | integer      | FK -> categories.id | yes      |
| slug               | varchar(120) | UNIQUE              | no       |

CHECK constraints:

- `ck_categories_description`: "description" IN ('Free shipping over $35', 'Bestsellers', 'New arrivals weekly', 'Clearance')
- `ck_categories_name`: "name" IN ('Books', 'Movies', 'Music', 'Games', 'Electronics', 'Computers', 'Home', 'Garden', 'Tools', 'Grocery', 'Health', 'Beauty', 'Toys', 'Kids', 'Baby', 'Clothing', 'Shoes', 'Jewelry', 'Sports', 'Outdoors', 'Automotive', 'Office')

Relationships:

- belongs to `categories` via `parent_category_id` (inverse: `categories` has many `categories`)
- has many `categories` via `categories.parent_category_id`
- has many `products` via `products.category_id`

## countries

| column | type         | key / constraint | nullable |
| ------ | ------------ | ---------------- | -------- |
| id     | integer      | PK (identity)    | no       |
| code   | varchar(120) | -                | no       |
| name   | varchar(120) | -                | no       |

Relationships:

- has many `tax_rates` via `tax_rates.country_id`
- has many `addresses` via `addresses.country_id`
- has many `warehouses` via `warehouses.country_id`

## discount_coupons

| column              | type          | key / constraint | nullable |
| ------------------- | ------------- | ---------------- | -------- |
| id                  | integer       | PK (identity)    | no       |
| code                | varchar(9)    | UNIQUE           | no       |
| discount_type       | varchar(120)  | -                | no       |
| discount_value      | numeric(14,2) | -                | no       |
| ends_at             | timestamp     | -                | no       |
| is_active           | boolean       | -                | no       |
| min_purchase_amount | integer       | -                | no       |
| starts_at           | timestamp     | -                | no       |

CHECK constraints:

- `ck_discount_coupons_discount_value`: "discount_value" BETWEEN 5 AND 30

Relationships:

- has many `orders` via `orders.coupon_id`

## order_statuses

| column      | type        | key / constraint | nullable |
| ----------- | ----------- | ---------------- | -------- |
| id          | integer     | PK (identity)    | no       |
| description | varchar(23) | -                | no       |
| status_name | varchar(9)  | UNIQUE           | no       |

CHECK constraints:

- `ck_order_statuses_description`: "description" IN ('Waiting on the customer', 'In the warehouse', 'With the carrier', 'Closed')
- `ck_order_statuses_status_name`: "status_name" IN ('pending', 'paid', 'shipped', 'delivered', 'cancelled', 'refunded')

Relationships:

- has many `orders` via `orders.order_status_id`

## payment_methods

| column | type        | key / constraint | nullable |
| ------ | ----------- | ---------------- | -------- |
| id     | integer     | PK (identity)    | no       |
| name   | varchar(14) | UNIQUE           | no       |

CHECK constraints:

- `ck_payment_methods_name`: "name" IN ('credit_card', 'debit_card', 'digital_wallet', 'gift_card', 'bank_transfer')

Relationships:

- has many `payments` via `payments.payment_method_id`

## product_attributes

| column | type         | key / constraint | nullable |
| ------ | ------------ | ---------------- | -------- |
| id     | integer      | PK (identity)    | no       |
| name   | varchar(12)  | UNIQUE           | no       |
| type   | varchar(120) | -                | no       |

CHECK constraints:

- `ck_product_attributes_name`: "name" IN ('color', 'finish', 'accent_color', 'trim_color')

Relationships:

- has many `product_attribute_values` via `product_attribute_values.attribute_id`

## products

| column           | type          | key / constraint    | nullable |
| ---------------- | ------------- | ------------------- | -------- |
| id               | integer       | PK (identity)       | no       |
| brand_id         | integer       | FK -> brands.id     | no       |
| category_id      | integer       | FK -> categories.id | no       |
| compare_at_price | numeric(14,2) | -                   | yes      |
| created_at       | timestamp     | -                   | no       |
| description      | varchar(25)   | -                   | no       |
| is_active        | boolean       | -                   | no       |
| name             | varchar(59)   | -                   | no       |
| price            | numeric(14,2) | -                   | no       |
| sku              | varchar(12)   | UNIQUE              | no       |
| slug             | varchar(10)   | UNIQUE              | no       |

CHECK constraints:

- `ck_products_description`: "description" IN ('Ships in one business day', 'Limited stock', 'Customer favourite', 'Eligible for free returns', 'New this season')
- `ck_products_price`: "price" BETWEEN 3 AND 160

Relationships:

- belongs to `brands` via `brand_id` (inverse: `brands` has many `products`)
- belongs to `categories` via `category_id` (inverse: `categories` has many `products`)
- has many `product_attribute_values` via `product_attribute_values.product_id`
- has many `product_images` via `product_images.product_id`
- has many `order_items` via `order_items.product_id`
- has many `product_reviews` via `product_reviews.product_id`
- has many `cart_items` via `cart_items.product_id`
- has many `product_inventories` via `product_inventories.product_id`
- has many `wishlist_items` via `wishlist_items.product_id`

## product_attribute_values

| column       | type        | key / constraint            | nullable |
| ------------ | ----------- | --------------------------- | -------- |
| id           | integer     | PK (identity)               | no       |
| attribute_id | integer     | FK -> product_attributes.id | no       |
| product_id   | integer     | FK -> products.id           | no       |
| value        | varchar(30) | -                           | no       |

Relationships:

- belongs to `product_attributes` via `attribute_id` (inverse: `product_attributes` has many `product_attribute_values`)
- belongs to `products` via `product_id` (inverse: `products` has many `product_attribute_values`)

## product_images

| column     | type        | key / constraint  | nullable |
| ---------- | ----------- | ----------------- | -------- |
| id         | integer     | PK (identity)     | no       |
| alt_text   | varchar(15) | -                 | no       |
| product_id | integer     | FK -> products.id | no       |
| sort_order | integer     | -                 | no       |
| url        | varchar(38) | UNIQUE            | no       |

CHECK constraints:

- `ck_product_images_alt_text`: "alt_text" IN ('Front view', 'Side view', 'In use', 'Packaging', 'Close-up detail')
- `ck_product_images_sort_order`: "sort_order" BETWEEN 1 AND 6

Relationships:

- belongs to `products` via `product_id` (inverse: `products` has many `product_images`)

## return_reasons

| column      | type         | key / constraint | nullable |
| ----------- | ------------ | ---------------- | -------- |
| id          | integer      | PK (identity)    | no       |
| reason_text | varchar(255) | UNIQUE           | no       |

CHECK constraints:

- `ck_return_reasons_reason_text`: "reason_text" IN ('Arrived damaged', 'Wrong item sent', 'Does not fit', 'Not as described', 'Changed my mind', 'Arrived too late')

Relationships:

- has many `order_returns` via `order_returns.reason_id`

## shipping_methods

| column         | type          | key / constraint | nullable |
| -------------- | ------------- | ---------------- | -------- |
| id             | integer       | PK (identity)    | no       |
| base_cost      | numeric(14,2) | -                | no       |
| carrier        | varchar(8)    | -                | no       |
| estimated_days | integer       | -                | no       |
| name           | varchar(12)   | UNIQUE           | no       |

CHECK constraints:

- `ck_shipping_methods_base_cost`: "base_cost" BETWEEN 0 AND 35
- `ck_shipping_methods_carrier`: "carrier" IN ('UPS', 'FedEx', 'USPS', 'DHL', 'In store')
- `ck_shipping_methods_estimated_days`: "estimated_days" BETWEEN 1 AND 7
- `ck_shipping_methods_name`: "name" IN ('standard', 'expedited', 'two_day', 'overnight', 'store_pickup')

Relationships:

- has many `orders` via `orders.shipping_method_id`

## suppliers

| column       | type         | key / constraint | nullable |
| ------------ | ------------ | ---------------- | -------- |
| id           | integer      | PK (identity)    | no       |
| contact_name | varchar(62)  | -                | no       |
| email        | varchar(78)  | -                | no       |
| name         | varchar(114) | -                | no       |
| phone        | varchar(26)  | -                | no       |
| website      | varchar(75)  | -                | no       |

## tax_rates

| column         | type          | key / constraint   | nullable |
| -------------- | ------------- | ------------------ | -------- |
| id             | integer       | PK (identity)      | no       |
| country_id     | integer       | FK -> countries.id | no       |
| rate           | numeric(14,4) | -                  | no       |
| state_province | varchar(3)    | -                  | no       |

CHECK constraints:

- `ck_tax_rates_rate`: "rate" BETWEEN 0.02 AND 0.1

Relationships:

- belongs to `countries` via `country_id` (inverse: `countries` has many `tax_rates`)

## users

| column        | type        | key / constraint | nullable |
| ------------- | ----------- | ---------------- | -------- |
| id            | integer     | PK (identity)    | no       |
| created_at    | timestamp   | -                | no       |
| email         | varchar(78) | UNIQUE           | no       |
| first_name    | varchar(30) | -                | no       |
| last_name     | varchar(44) | -                | no       |
| password_hash | varchar(60) | -                | no       |
| phone         | varchar(26) | -                | no       |
| updated_at    | timestamp   | -                | no       |

Relationships:

- has many `addresses` via `addresses.user_id`
- has many `audit_logs` via `audit_logs.user_id`
- has many `orders` via `orders.user_id`
- has many `product_reviews` via `product_reviews.user_id`
- has many `shopping_carts` via `shopping_carts.user_id`
- has many `support_tickets` via `support_tickets.user_id`
- has many `wishlists` via `wishlists.user_id`

## addresses

| column              | type         | key / constraint   | nullable |
| ------------------- | ------------ | ------------------ | -------- |
| id                  | integer      | PK (identity)      | no       |
| city                | varchar(40)  | -                  | no       |
| country_id          | integer      | FK -> countries.id | no       |
| extended_address    | varchar(7)   | -                  | yes      |
| is_default_billing  | boolean      | -                  | no       |
| is_default_shipping | boolean      | -                  | no       |
| postal_code         | varchar(10)  | -                  | no       |
| state_province      | varchar(3)   | -                  | no       |
| street_address      | varchar(101) | -                  | no       |
| user_id             | integer      | FK -> users.id     | no       |

Relationships:

- belongs to `countries` via `country_id` (inverse: `countries` has many `addresses`)
- belongs to `users` via `user_id` (inverse: `users` has many `addresses`)
- has many `orders` via `orders.billing_address_id`
- has many `orders` via `orders.shipping_address_id`

## audit_logs

| column         | type       | key / constraint | nullable |
| -------------- | ---------- | ---------------- | -------- |
| id             | integer    | PK (identity)    | no       |
| action         | varchar(6) | -                | no       |
| performed_at   | timestamp  | -                | no       |
| record_id      | integer    | -                | no       |
| table_affected | varchar(9) | -                | no       |
| user_id        | integer    | FK -> users.id   | no       |

CHECK constraints:

- `ck_audit_logs_action`: "action" IN ('insert', 'update', 'delete')
- `ck_audit_logs_record_id`: "record_id" BETWEEN 1 AND 360000
- `ck_audit_logs_table_affected`: "table_affected" IN ('orders', 'products', 'users', 'payments', 'shipments')

Relationships:

- belongs to `users` via `user_id` (inverse: `users` has many `audit_logs`)

## orders

| column              | type          | key / constraint          | nullable |
| ------------------- | ------------- | ------------------------- | -------- |
| id                  | integer       | PK (identity)             | no       |
| billing_address_id  | integer       | FK -> addresses.id        | no       |
| coupon_id           | integer       | FK -> discount_coupons.id | yes      |
| created_at          | timestamp     | -                         | no       |
| discount_amount     | numeric(14,2) | -                         | no       |
| order_status_id     | integer       | FK -> order_statuses.id   | no       |
| shipping_address_id | integer       | FK -> addresses.id        | no       |
| shipping_cost       | numeric(14,2) | -                         | no       |
| shipping_method_id  | integer       | FK -> shipping_methods.id | no       |
| subtotal            | numeric(14,2) | -                         | no       |
| tax_amount          | numeric(14,2) | -                         | no       |
| total               | numeric(14,2) | -                         | no       |
| user_id             | integer       | FK -> users.id            | no       |

Relationships:

- belongs to `addresses` via `billing_address_id` (inverse: `addresses` has many `orders`)
- belongs to `discount_coupons` via `coupon_id` (inverse: `discount_coupons` has many `orders`)
- belongs to `order_statuses` via `order_status_id` (inverse: `order_statuses` has many `orders`)
- belongs to `addresses` via `shipping_address_id` (inverse: `addresses` has many `orders`)
- belongs to `shipping_methods` via `shipping_method_id` (inverse: `shipping_methods` has many `orders`)
- belongs to `users` via `user_id` (inverse: `users` has many `orders`)
- has many `order_items` via `order_items.order_id`
- has many `payments` via `payments.order_id`
- has many `shipments` via `shipments.order_id`

## order_items

| column          | type          | key / constraint  | nullable |
| --------------- | ------------- | ----------------- | -------- |
| id              | integer       | PK (identity)     | no       |
| discount_amount | integer       | -                 | no       |
| order_id        | integer       | FK -> orders.id   | no       |
| product_id      | integer       | FK -> products.id | no       |
| quantity        | integer       | -                 | no       |
| tax_amount      | numeric(14,2) | -                 | no       |
| unit_price      | numeric(14,2) | -                 | no       |

CHECK constraints:

- `ck_order_items_quantity`: "quantity" BETWEEN 1 AND 3

Relationships:

- belongs to `orders` via `order_id` (inverse: `orders` has many `order_items`)
- belongs to `products` via `product_id` (inverse: `products` has many `order_items`)
- has many `order_returns` via `order_returns.order_item_id`

## order_returns

| column        | type       | key / constraint             | nullable |
| ------------- | ---------- | ---------------------------- | -------- |
| id            | integer    | PK (identity)                | no       |
| order_item_id | integer    | UNIQUE, FK -> order_items.id | no       |
| processed_at  | timestamp  | -                            | no       |
| quantity      | integer    | -                            | no       |
| reason_id     | integer    | FK -> return_reasons.id      | no       |
| requested_at  | timestamp  | -                            | no       |
| status        | varchar(9) | -                            | no       |

CHECK constraints:

- `ck_order_returns_status`: "status" IN ('refunded', 'approved', 'requested', 'rejected')

Relationships:

- belongs to `order_items` via `order_item_id` (inverse: `order_items` has many `order_returns`)
- belongs to `return_reasons` via `reason_id` (inverse: `return_reasons` has many `order_returns`)

## payments

| column                | type          | key / constraint         | nullable |
| --------------------- | ------------- | ------------------------ | -------- |
| id                    | integer       | PK (identity)            | no       |
| amount                | numeric(14,2) | -                        | no       |
| order_id              | integer       | UNIQUE, FK -> orders.id  | no       |
| payment_method_id     | integer       | FK -> payment_methods.id | no       |
| processed_at          | timestamp     | -                        | no       |
| status                | varchar(9)    | -                        | no       |
| transaction_reference | varchar(16)   | UNIQUE                   | no       |

CHECK constraints:

- `ck_payments_status`: "status" IN ('completed', 'failed', 'refunded')

Relationships:

- belongs to `orders` via `order_id` (inverse: `orders` has many `payments`)
- belongs to `payment_methods` via `payment_method_id` (inverse: `payment_methods` has many `payments`)

## product_reviews

| column     | type        | key / constraint  | nullable |
| ---------- | ----------- | ----------------- | -------- |
| id         | integer     | PK (identity)     | no       |
| comment    | varchar(35) | -                 | yes      |
| created_at | timestamp   | -                 | no       |
| product_id | integer     | FK -> products.id | no       |
| rating     | integer     | -                 | no       |
| title      | varchar(21) | -                 | no       |
| user_id    | integer     | FK -> users.id    | no       |

CHECK constraints:

- `ck_product_reviews_comment`: "comment" IN ('Fast delivery and well packed.', 'Quality is fine for the price.', 'Sizing runs small.', 'Stopped working after a month.', 'Looks just like the photos.', 'Instructions were unclear.', 'Bought a second one as a gift.', 'Returned it for a different colour.')
- `ck_product_reviews_rating`: "rating" BETWEEN 1 AND 5
- `ck_product_reviews_title`: "title" IN ('Does the job', 'Better than expected', 'Not for me', 'Great value', 'Would buy again', 'Arrived damaged', 'Smaller than it looks', 'Exactly as described')

Relationships:

- belongs to `products` via `product_id` (inverse: `products` has many `product_reviews`)
- belongs to `users` via `user_id` (inverse: `users` has many `product_reviews`)

## shipments

| column          | type        | key / constraint        | nullable |
| --------------- | ----------- | ----------------------- | -------- |
| id              | integer     | PK (identity)           | no       |
| delivered_at    | timestamp   | -                       | no       |
| order_id        | integer     | UNIQUE, FK -> orders.id | no       |
| shipped_at      | timestamp   | -                       | no       |
| tracking_number | varchar(18) | UNIQUE                  | no       |

Relationships:

- belongs to `orders` via `order_id` (inverse: `orders` has many `shipments`)

## shopping_carts

| column     | type      | key / constraint | nullable |
| ---------- | --------- | ---------------- | -------- |
| id         | integer   | PK (identity)    | no       |
| created_at | timestamp | -                | no       |
| updated_at | timestamp | -                | no       |
| user_id    | integer   | FK -> users.id   | no       |

Relationships:

- belongs to `users` via `user_id` (inverse: `users` has many `shopping_carts`)
- has many `cart_items` via `cart_items.cart_id`

## cart_items

| column     | type      | key / constraint        | nullable |
| ---------- | --------- | ----------------------- | -------- |
| id         | integer   | PK (identity)           | no       |
| added_at   | timestamp | -                       | no       |
| cart_id    | integer   | FK -> shopping_carts.id | no       |
| product_id | integer   | FK -> products.id       | no       |
| quantity   | integer   | -                       | no       |

CHECK constraints:

- `ck_cart_items_quantity`: "quantity" BETWEEN 1 AND 3

Relationships:

- belongs to `shopping_carts` via `cart_id` (inverse: `shopping_carts` has many `cart_items`)
- belongs to `products` via `product_id` (inverse: `products` has many `cart_items`)

## support_tickets

| column     | type        | key / constraint | nullable |
| ---------- | ----------- | ---------------- | -------- |
| id         | integer     | PK (identity)    | no       |
| created_at | timestamp   | -                | no       |
| priority   | varchar(6)  | -                | no       |
| status     | varchar(8)  | -                | no       |
| subject    | varchar(26) | -                | no       |
| user_id    | integer     | FK -> users.id   | no       |

CHECK constraints:

- `ck_support_tickets_priority`: "priority" IN ('low', 'medium', 'high')
- `ck_support_tickets_status`: "status" IN ('open', 'pending', 'resolved', 'closed')
- `ck_support_tickets_subject`: "subject" IN ('Where is my order?', 'Item arrived damaged', 'Change my shipping address', 'Refund not received', 'Coupon did not apply', 'Cannot sign in')

Relationships:

- belongs to `users` via `user_id` (inverse: `users` has many `support_tickets`)
- has many `support_messages` via `support_messages.ticket_id`

## support_messages

| column      | type        | key / constraint         | nullable |
| ----------- | ----------- | ------------------------ | -------- |
| id          | integer     | PK (identity)            | no       |
| message     | varchar(36) | -                        | no       |
| sender_type | varchar(8)  | -                        | no       |
| sent_at     | timestamp   | -                        | no       |
| ticket_id   | integer     | FK -> support_tickets.id | no       |

CHECK constraints:

- `ck_support_messages_message`: "message" IN ('Thanks, that worked.', 'Can you check the tracking number?', 'I have issued a refund to your card.', 'Please send a photo of the damage.', 'Your replacement ships today.', 'I still have not received it.')
- `ck_support_messages_sender_type`: "sender_type" IN ('customer', 'agent')

Relationships:

- belongs to `support_tickets` via `ticket_id` (inverse: `support_tickets` has many `support_messages`)

## warehouses

| column     | type         | key / constraint   | nullable |
| ---------- | ------------ | ------------------ | -------- |
| id         | integer      | PK (identity)      | no       |
| address    | varchar(101) | -                  | no       |
| city       | varchar(40)  | -                  | no       |
| country_id | integer      | FK -> countries.id | no       |
| name       | varchar(13)  | UNIQUE             | no       |

CHECK constraints:

- `ck_warehouses_name`: "name" IN ('East Coast DC', 'West Coast DC', 'Midwest DC', 'Southeast DC', 'Texas DC', 'Mountain DC')

Relationships:

- belongs to `countries` via `country_id` (inverse: `countries` has many `warehouses`)
- has many `product_inventories` via `product_inventories.warehouse_id`

## product_inventories

| column            | type    | key / constraint    | nullable |
| ----------------- | ------- | ------------------- | -------- |
| id                | integer | PK (identity)       | no       |
| product_id        | integer | FK -> products.id   | no       |
| quantity_on_hand  | integer | -                   | no       |
| quantity_reserved | integer | -                   | no       |
| reorder_level     | integer | -                   | no       |
| warehouse_id      | integer | FK -> warehouses.id | no       |

CHECK constraints:

- `ck_product_inventories_quantity_on_hand`: "quantity_on_hand" BETWEEN 0 AND 500
- `ck_product_inventories_reorder_level`: "reorder_level" BETWEEN 5 AND 50

Relationships:

- belongs to `products` via `product_id` (inverse: `products` has many `product_inventories`)
- belongs to `warehouses` via `warehouse_id` (inverse: `warehouses` has many `product_inventories`)

## wishlists

| column     | type        | key / constraint | nullable |
| ---------- | ----------- | ---------------- | -------- |
| id         | integer     | PK (identity)    | no       |
| created_at | timestamp   | -                | no       |
| name       | varchar(14) | -                | no       |
| user_id    | integer     | FK -> users.id   | no       |

CHECK constraints:

- `ck_wishlists_name`: "name" IN ('Wish list', 'Birthday', 'Holiday gifts', 'Home office', 'Save for later')

Relationships:

- belongs to `users` via `user_id` (inverse: `users` has many `wishlists`)
- has many `wishlist_items` via `wishlist_items.wishlist_id`

## wishlist_items

| column      | type      | key / constraint   | nullable |
| ----------- | --------- | ------------------ | -------- |
| id          | integer   | PK (identity)      | no       |
| added_at    | timestamp | -                  | no       |
| product_id  | integer   | FK -> products.id  | no       |
| wishlist_id | integer   | FK -> wishlists.id | no       |

Relationships:

- belongs to `products` via `product_id` (inverse: `products` has many `wishlist_items`)
- belongs to `wishlists` via `wishlist_id` (inverse: `wishlists` has many `wishlist_items`)

## Index notes

Indexes in the dump: none beyond PK/UNIQUE constraints.

Columns with a plain UNIQUE constraint are listed above; multi-column pairs such as (order_id, product_id) are NOT unique in the source (10 duplicate pairs exist in the 1K data).
