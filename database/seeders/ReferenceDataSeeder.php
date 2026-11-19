<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\OrderStatus;
use App\Models\PaymentMethod;
use App\Models\ProductAttribute;
use App\Models\ReturnReason;
use App\Models\ShippingMethod;
use App\Models\TaxRate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReferenceDataSeeder extends Seeder
{
    // Reference values from databasesample.com, e-commerce 1K PostgreSQL.

    public function run(): void
    {
        DB::transaction(function (): void {
            $this->seedLookups();
            $this->seedTaxRates();
        });
    }

    private function seedLookups(): void
    {
        foreach ([
            ['description' => 'With the carrier', 'status_name' => 'shipped'],
            ['description' => 'In the warehouse', 'status_name' => 'paid'],
            ['description' => 'Waiting on the customer', 'status_name' => 'pending'],
            ['description' => 'With the carrier', 'status_name' => 'delivered'],
            ['description' => 'With the carrier', 'status_name' => 'refunded'],
            ['description' => 'Closed', 'status_name' => 'cancelled'],
        ] as $attributes) {
            OrderStatus::updateOrCreate(['status_name' => $attributes['status_name']], $attributes);
        }

        foreach ([
            ['name' => 'credit_card'],
            ['name' => 'gift_card'],
            ['name' => 'bank_transfer'],
            ['name' => 'debit_card'],
            ['name' => 'digital_wallet'],
        ] as $attributes) {
            PaymentMethod::updateOrCreate(['name' => $attributes['name']], $attributes);
        }

        foreach ([
            ['base_cost' => '16.91', 'carrier' => 'USPS', 'estimated_days' => 2, 'name' => 'expedited'],
            ['base_cost' => '29.10', 'carrier' => 'USPS', 'estimated_days' => 5, 'name' => 'store_pickup'],
            ['base_cost' => '11.48', 'carrier' => 'In store', 'estimated_days' => 7, 'name' => 'two_day'],
            ['base_cost' => '7.66', 'carrier' => 'UPS', 'estimated_days' => 3, 'name' => 'overnight'],
            ['base_cost' => '24.08', 'carrier' => 'FedEx', 'estimated_days' => 5, 'name' => 'standard'],
        ] as $attributes) {
            ShippingMethod::updateOrCreate(['name' => $attributes['name']], $attributes);
        }

        foreach ([
            ['reason_text' => 'Does not fit'],
            ['reason_text' => 'Not as described'],
            ['reason_text' => 'Arrived too late'],
            ['reason_text' => 'Arrived damaged'],
            ['reason_text' => 'Changed my mind'],
            ['reason_text' => 'Wrong item sent'],
        ] as $attributes) {
            ReturnReason::updateOrCreate(['reason_text' => $attributes['reason_text']], $attributes);
        }

        foreach ([
            ['name' => 'color', 'type' => 'text'],
            ['name' => 'accent_color', 'type' => 'text'],
            ['name' => 'finish', 'type' => 'text'],
            ['name' => 'trim_color', 'type' => 'text'],
        ] as $attributes) {
            ProductAttribute::updateOrCreate(['name' => $attributes['name']], $attributes);
        }
    }

    private function seedTaxRates(): void
    {
        $country = Country::updateOrCreate(['code' => 'US'], ['name' => 'United States']);

        // A state can have several distinct rates in the sample.
        foreach ([
            ['state_province' => 'GA', 'rate' => '0.0402'],
            ['state_province' => 'CO', 'rate' => '0.0393'],
            ['state_province' => 'TX', 'rate' => '0.0510'],
            ['state_province' => 'TX', 'rate' => '0.0577'],
            ['state_province' => 'TX', 'rate' => '0.0955'],
            ['state_province' => 'TX', 'rate' => '0.0976'],
            ['state_province' => 'IL', 'rate' => '0.0905'],
            ['state_province' => 'TX', 'rate' => '0.0273'],
            ['state_province' => 'CA', 'rate' => '0.0833'],
            ['state_province' => 'NY', 'rate' => '0.0513'],
            ['state_province' => 'CA', 'rate' => '0.0877'],
            ['state_province' => 'NY', 'rate' => '0.0486'],
        ] as $attributes) {
            TaxRate::updateOrCreate(['country_id' => $country->id, ...$attributes]);
        }
    }
}
