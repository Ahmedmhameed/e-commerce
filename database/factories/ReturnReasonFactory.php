<?php

namespace Database\Factories;

use App\Models\ReturnReason;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReturnReason>
 */
class ReturnReasonFactory extends Factory
{
    public function definition(): array
    {
        return [
            'reason_text' => fake()->unique()->randomElement(ReturnReason::ALLOWED_REASON_TEXT),
        ];
    }
}
