<?php

namespace Database\Factories;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AuditLog>
 */
class AuditLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'action' => fake()->randomElement(AuditLog::ALLOWED_ACTION),
            'performed_at' => now(),
            'record_id' => fake()->numberBetween(1, 360000),
            'table_affected' => fake()->randomElement(AuditLog::ALLOWED_TABLE_AFFECTED),
            'user_id' => UserFactory::new(),
        ];
    }
}
