<?php

namespace App\Modules\Product\Database\Factories;

use App\Modules\Product\Enums\PriceType;
use App\Modules\Product\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Product::class;

    public function definition(): array
    {
        $faker = fake('es_AR');

        $productos = [
            'billetera',
            'caja',
            'bolso',
            'mochila',
            'botella',
            'recipiente',
            'manguera',
            'regulador',
            'envase',
            'termo',
            'balde',
            'canasto',
            'contenedor',
            'estuche',
            'maletín',
            'silla',
            'mesa',
            'lámpara',
            'cable',
            'adaptador',
        ];

        $adjetivos = [
            'negro',
            'blanco',
            'gris',
            'rojo',
            'azul',
            'verde',
            'grande',
            'chico',
            'mediano',
            'reforzado',
            'liviano',
            'resistente',
            'plástico',
            'metálico',
            'plegable',
            'térmico',
            'industrial',
            'compacto',
        ];

        $producto = $faker->randomElement($productos);
        $adjetivo = $faker->randomElement($adjetivos);

        return [
            'name' => ucfirst("{$producto} {$adjetivo}"),
            'description' => $faker->paragraph(),
            'active' => $faker->boolean(70),
        ];
    }


    public function configure(): static
    {
        return $this->afterCreating(function (Product $product) {
            $product->prices()->createMany([
                [
                    'type' => PriceType::RETAIL,
                    'amount' => $this->faker->randomFloat(2, 0, 10000),
                ],
                [
                    'type' => PriceType::COMMERCIAL,
                    'amount' => $this->faker->randomFloat(2, 0, 10000),
                ],
            ]);
        });
    }
}
