<?php

namespace Database\Seeders;

use App\Modules\Clients\Database\Seeders\ClientsSeeder;
use App\Modules\Delivery\Database\Seeders\DeliveriesSeeder;
use App\Modules\Orders\Database\Seeders\OrdersSeeder;
use Illuminate\Database\Seeder;
use App\Modules\Product\Database\Seeders\ProductsSeeder;
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminUserSeeder::class,
            ProductsSeeder::class,
            ClientsSeeder::class,
            DeliveriesSeeder::class,
            OrdersSeeder::class,
        ]);
    }
}
