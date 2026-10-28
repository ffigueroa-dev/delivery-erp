<?php

namespace App\Modules\Product\Database\Factories;

use App\Modules\Product\Enums\PriceType;
use App\Modules\Product\Models\ProductPrice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductPrice>
 */
class ProductPriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = ProductPrice::class;
    public function definition(): array
    {

        return [
            'type' => $this->faker->randomElement(PriceType::cases())->value,
            'amount' => $this->faker->randomFloat(2, 0, 10000),
        ];
    }
}
