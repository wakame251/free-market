<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'seller_id'   => User::factory(),
            'item_name'   => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'price'       => 1000,
            'brand_name'  => $this->faker->optional()->word(),
            'image_path'  => null,
            'condition'   => 'good',
            'status'      => 'on_sale',
        ];
    }
}
