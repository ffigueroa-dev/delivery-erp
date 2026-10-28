<?php

namespace App\Modules\Delivery\Database\Factories;

use App\Enums\Role;
use App\Models\User;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeliveryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = User::class;

    public function definition(): array
    {
        $faker = FakerFactory::create('es_AR');
        return [
            'name' => $faker->name(),
            'email' => $faker->email(),
            'password' => $faker->password(),
        ];
    }

    public function configure(): static
    {
        $faker = FakerFactory::create('es_AR');

        return $this->afterCreating(function (User $user) use ($faker) {
            $user->assignRole(Role::DELIVERY->value);
        });
    }
}
