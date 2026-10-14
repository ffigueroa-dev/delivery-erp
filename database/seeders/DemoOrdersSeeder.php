<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\Role;
use App\Modules\Clients\Models\Client;
use App\Modules\Clients\Enums\ClientType;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Enums\PriceType;
use App\Modules\Orders\Models\Order;
use App\Modules\Orders\Enums\OrderStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role as SpatieRole;
use Faker\Factory as FakerFactory;

class DemoOrdersSeeder extends Seeder
{
    public function run(): void
    {
        $faker = FakerFactory::create('es_AR');

        DB::transaction(function () use ($faker) {
            // 1. Crear o recuperar el rol de repartidor
            $deliveryRole = SpatieRole::firstOrCreate([
                'name' => Role::DELIVERY->value,
                'guard_name' => 'web',
            ]);

            // 2. Crear 3 repartidores
            $deliveries = collect();

            for ($i = 1; $i <= 3; $i++) {
                $user = User::factory()->create([
                    'name' => $faker->name(),
                    'email' => "repartidor{$i}_" . uniqid() . '@example.com',
                ]);

                $user->assignRole($deliveryRole);
                $deliveries->push($user);
            }

            // 3. Crear los productos y sus precios
            $productDefinitions = [
                [
                    'name' => 'Garrafa 10kg',
                    'commercial' => 27000,
                    'retail' => 30000,
                ],
                [
                    'name' => 'Garrafa 10kg YPF',
                    'commercial' => 30000,
                    'retail' => 33000,
                ],
                [
                    'name' => 'Garrafa 15kg',
                    'commercial' => 45000,
                    'retail' => 48000,
                ],
                [
                    'name' => 'Tubo 45kg',
                    'commercial' => 98000,
                    'retail' => 105000,
                ],
            ];

            $products = collect();

            foreach ($productDefinitions as $definition) {
                $product = Product::create([
                    'name' => $definition['name'],
                    'description' => 'Producto de prueba',
                    'active' => true,
                ]);

                $product->prices()->createMany([
                    [
                        'type' => PriceType::COMMERCIAL->value,
                        'amount' => $definition['commercial'],
                    ],
                    [
                        'type' => PriceType::RETAIL->value,
                        'amount' => $definition['retail'],
                    ],
                ]);

                // Cargar los precios para usarlos al generar órdenes
                $product->load('prices');
                $products->push($product);
            }

            // 4. Crear 21 clientes:
            // 7 particulares, 7 comerciales y 7 adicionales aleatorios
            $clientTypes = collect([
                ...array_fill(0, 7, ClientType::RETAIL),
                ...array_fill(0, 7, ClientType::COMMERCIAL),
            ]);

            for ($i = 0; $i < 7; $i++) {
                $clientTypes->push(
                    $faker->randomElement([
                        ClientType::RETAIL,
                        ClientType::COMMERCIAL,
                    ])
                );
            }

            $clients = collect();

            foreach ($clientTypes as $type) {
                $clients->push(Client::create([
                    'name' => $faker->name(),
                    'phone' => $faker->numerify('11########'),
                    'type' => $type->value,
                    'full_address' => $faker->streetAddress()
                        . ', ' . $faker->city(),
                    'address_reference' => $faker->optional()->sentence(4),
                    'active' => true,
                ]));
            }

            // 5. Crear 10 órdenes por cliente
            $today = now()->startOfDay();
            $totalOrders = 0;

            foreach ($clients as $client) {
                // Una cancelada entre las 9 órdenes anteriores
                $cancelledIndex = random_int(0, 8);

                for ($i = 0; $i < 10; $i++) {
                    $date = $today->copy()->subDays(9 - $i);

                    $status = match (true) {
                        $i === 9 => OrderStatus::PENDING,
                        $i === $cancelledIndex => OrderStatus::CANCELLED,
                        default => OrderStatus::DELIVERED,
                    };

                    $selectedProducts = $products->random(
                        random_int(1, min(3, $products->count()))
                    );

                    $items = [];
                    $total = 0;

                    foreach ($selectedProducts as $product) {
                        $priceType = $client->type === ClientType::COMMERCIAL
                            ? PriceType::COMMERCIAL
                            : PriceType::RETAIL;

                        $price = $product->prices->firstWhere(
                            'type',
                            $priceType->value
                        );

                        if (!$price) {
                            throw new \RuntimeException(
                                "Falta el precio {$priceType->value} "
                                . "para {$product->name}"
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

                    $order = Order::create([
                        'client_id' => $client->id,
                        'delivery_id' => $deliveries->random()->id,
                        'status' => $status->value,
                        'notes' => $faker->optional(0.4)->sentence(),
                        'total_amount' => $total,
                    ]);

                    // Fechas distribuidas durante los últimos 10 días
                    $orderDate = $date->copy()->addHours(
                        random_int(8, 19)
                    );

                    $order->created_at = $orderDate;
                    $order->updated_at = $orderDate;
                    $order->save();

                    $order->orderProducts()->createMany($items);

                    $totalOrders++;
                }
            }

            $this->command->info('Datos generados correctamente.');
            $this->command->info("Repartidores: {$deliveries->count()}");
            $this->command->info("Clientes: {$clients->count()}");
            $this->command->info("Productos: {$products->count()}");
            $this->command->info("Órdenes: {$totalOrders}");
        });
    }
}
