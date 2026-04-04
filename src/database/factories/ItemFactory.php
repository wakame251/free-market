<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        $category = Category::factory()->create();

        return [
            'seller_id' => User::factory(),
            'item_name' => $this->faker->words(2, true),
            'description' => $this->faker->sentence(),
            'price' => 1000,
            'brand_name' => $this->faker->optional()->word(),
            'image_path' => 'images/sample/test-item.jpg',
            'condition' => '良好',
            'status' => 'on_sale',
            'category_id' => $category->id,
        ];
    }
}