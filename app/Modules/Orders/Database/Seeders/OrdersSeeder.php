<?php

namespace App\Modules\Orders\Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use App\Modules\Clients\Models\Client;
use App\Modules\Orders\Enums\OrderStatus;
use App\Modules\Orders\Models\Order;
use App\Modules\Product\Models\Product;
use Carbon\Carbon;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OrdersSeeder extends Seeder
{
    public function run(): void
    {
        $faker = FakerFactory::create('es_AR');

        $clients = Client::query()
            ->active()
            ->get();

        $deliveries = User::role(Role::DELIVERY->value)
            ->get();

        $products = Product::query()
            ->active()
            ->with('prices')
            ->get();

        if ($clients->isEmpty()) {
            throw new RuntimeException(
                'No hay clientes activos. Ejecutá ClientsSeeder primero.'
            );
        }

        if ($deliveries->isEmpty()) {
            throw new RuntimeException(
                'No hay deliveries disponibles. Ejecutá DeliveriesSeeder primero.'
            );
        }

        if ($products->isEmpty()) {
            throw new RuntimeException(
                'No hay productos activos. Ejecutá ProductsSeeder primero.'
            );
        }

        $now = now();
        $today = $now->copy()->startOfDay();
        $startDate = $today->copy()->subMonths(3);

        $totalOrders = 0;
        $todayOrders = 0;

        DB::transaction(function () use (
            $faker,
            $clients,
            $deliveries,
            $products,
            $now,
            $today,
            $startDate,
            &$totalOrders,
            &$todayOrders
        ) {
            $dayCount = (int) $startDate->diffInDays($today);

            for ($offset = 0; $offset <= $dayCount; $offset++) {
                $day = $startDate->copy()->addDays($offset);

                $this->command->info(
                    "ITERACIÓN {$offset}/{$dayCount} — FECHA: {$day->toDateString()}"
                );

                $isToday = $day->isSameDay($today);

                $ordersForDay = random_int(50, 120);

                $this->command->info(
                    "Generando {$ordersForDay} órdenes para {$day->toDateString()}..."
                );

                for ($i = 0; $i < $ordersForDay; $i++) {
                    $client = $clients->random();
                    $delivery = $deliveries->random();

                    $selectedProducts = $products
                        ->shuffle()
                        ->take(
                            $faker->numberBetween(1, min(5, $products->count()))
                        );

                    $items = [];
                    $total = 0;

                    foreach ($selectedProducts as $product) {
                        $price = $product->getPriceForClientType(
                            $client->type
                        );

                        if (!$price) {
                            throw new RuntimeException(
                                "Falta el precio para el producto {$product->id}"
                            );
                        }

                        $quantity = random_int(1, 5);
                        $subtotal = $price->amount * $quantity;

                        $items[] = [
                            'product_id' => $product->id,
                            'current_price' => $price->amount,
                            'quantity' => $quantity,
                            'subtotal' => $subtotal,
                        ];

                        $total += $subtotal;
                    }

                    // Fecha de la orden.
                    if ($isToday) {
                        $earliest = $today->copy()->setTime(8, 0);

                        $earliestTimestamp = min(
                            $earliest->timestamp,
                            $now->timestamp
                        );

                        $orderDate = Carbon::createFromTimestamp(
                            random_int(
                                $earliestTimestamp,
                                $now->timestamp
                            ),
                            $now->timezone
                        );
                    } else {
                        $orderDate = $day->copy()->setTime(
                            random_int(8, 20),
                            random_int(0, 59),
                            random_int(0, 59)
                        );
                    }

                    // Las órdenes de hoy pueden tener los tres estados.
                    // Las históricas solamente pueden estar entregadas
                    // o canceladas.
                    $status = $isToday
                        ? $faker->randomElement([
                            OrderStatus::PENDING,
                            OrderStatus::PENDING,
                            OrderStatus::PENDING,
                            OrderStatus::PENDING,
                            OrderStatus::PENDING,
                            OrderStatus::PENDING,
                            OrderStatus::PENDING,
                            OrderStatus::DELIVERED,
                            OrderStatus::DELIVERED,
                            OrderStatus::CANCELLED,
                        ])
                        : $faker->randomElement([
                            OrderStatus::DELIVERED,
                            OrderStatus::DELIVERED,
                            OrderStatus::DELIVERED,
                            OrderStatus::DELIVERED,
                            OrderStatus::DELIVERED,
                            OrderStatus::DELIVERED,
                            OrderStatus::DELIVERED,
                            OrderStatus::CANCELLED,
                            OrderStatus::CANCELLED,
                            OrderStatus::CANCELLED,
                        ]);

                    // Crear la orden directamente con sus fechas.
                    // Evitamos un UPDATE adicional por cada orden.
                    $order = new Order([
                        'client_id' => $client->id,
                        'delivery_id' => $delivery->id,
                        'status' => $status,
                        'notes' => $faker->optional(0.4)->sentence(),
                        'total_amount' => $total,
                    ]);

                    $order->timestamps = false;
                    $order->created_at = $orderDate;
                    $order->updated_at = $orderDate;
                    $order->save();

                    // Crear los detalles de la orden.
                    $order->orderProducts()->createMany($items);

                    $totalOrders++;

                    if ($isToday) {
                        $todayOrders++;
                    }
                }
            }
        });

        $this->command->info('Órdenes de demostración generadas.');
        $this->command->info(
            "Período: {$startDate->toDateString()} al {$today->toDateString()}"
        );
        $this->command->info("Órdenes totales: {$totalOrders}");
        $this->command->info("Órdenes de hoy: {$todayOrders}");
        $this->command->info("Clientes disponibles: {$clients->count()}");
        $this->command->info("Deliveries disponibles: {$deliveries->count()}");
        $this->command->info("Productos disponibles: {$products->count()}");
    }
}
