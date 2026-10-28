<?php

namespace App\Modules\Order\Database\Factories;

use App\Models\User;
use App\Modules\Clients\Models\Client;
use App\Modules\Orders\Enums\OrderStatus;
use App\Modules\Orders\Models\Order;
use App\Modules\Product\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory;
use RuntimeException;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;
    public function definition(): array
    {
        $faker = FakerFactory::create('es_AR');

        return [
            'client_id' => Client::query()
                ->active()
                ->inRandomOrder()
                ->firstOrFail()
                ->id,

            'delivery_id' => User::query()
                ->where('type', 'delivery')
                ->inRandomOrder()
                ->firstOrFail()
                ->id,

            'status' => OrderStatus::PENDING,
            'notes' => $faker->optional()->sentence(),
            'total_amount' => 0,
        ];
    }

    public function configure(): static
    {
        $faker = FakerFactory::create('es_AR');

        return $this->afterCreating(function (Order $order) use ($faker) {
            $client = $order->client;

            $products = Product::query()
                ->active()
                ->with('prices')
                ->get();

            if ($products->isEmpty()) {
                throw new RuntimeException(
                    'No active products available to create the order.'
                );
            }

            $selectedProducts = $products
                ->shuffle()
                ->take(
                    $faker->numberBetween(
                        1,
                        min(5, $products->count())
                    )
                );

            $total = 0;

            foreach ($selectedProducts as $product) {
                $price = $product->getPriceForClientType($client->type);

                if (!$price) {
                    throw new RuntimeException(
                        "No price configured for product {$product->id}."
                    );
                }

                $quantity = $faker->numberBetween(1, 5);
                $subtotal = $price->amount * $quantity;

                $order->orderProducts()->create([
                    'product_id' => $product->id,
                    'current_price' => $price->amount,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal,
                ]);

                $total += $subtotal;
            }

            $order->update([
                'total_amount' => $total,
            ]);
        });
    }
}
