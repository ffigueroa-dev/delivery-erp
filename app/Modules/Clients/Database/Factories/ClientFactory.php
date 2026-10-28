<?php

namespace App\Modules\Clients\Database\Factories;

use App\Modules\Clients\Models\Client;
use App\Modules\Clients\Enums\ClientType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory;
class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        $faker = FakerFactory::create('es_AR');
        return [
            'name' => $faker->name(),
            'phone' => $faker->phoneNumber(),
            'type' => $faker->randomElement(ClientType::cases()),
            'full_address' => $faker->address(),
            'address_reference' => $faker->sentence(),
            'active' => $faker->boolean(80),
        ];
    }
}
