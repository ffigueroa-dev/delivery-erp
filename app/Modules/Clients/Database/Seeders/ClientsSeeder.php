<?php

namespace App\Modules\Clients\Database\Seeders;

use App\Modules\Clients\Models\Client;
use Illuminate\Database\Seeder;

class ClientsSeeder extends Seeder
{
    public function run(): void
    {
        Client::factory()->count(40)->create();
    }
}
