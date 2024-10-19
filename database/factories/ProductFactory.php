<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    // Define the model that this factory is for
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => [
                "en"=>$this->faker->word(),
                "ar"=>$this->faker->word()
            ],
            'price' => $this->faker->numberBetween(10, 100),
            'stock_quantity' => $this->faker->numberBetween(1, 100),
        ];
    }
}
