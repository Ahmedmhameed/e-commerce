<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'description' => fake()->randomElement(Category::ALLOWED_DESCRIPTION),
            'name' => fake()->unique()->randomElement(Category::ALLOWED_NAME),
            'parent_category_id' => null,
            'slug' => fake()->unique()->regexify('[a-z0-9]{12}'),
        ];
    }
}
