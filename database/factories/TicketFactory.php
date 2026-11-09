<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Ticket> */
class TicketFactory extends Factory
{
    public function definition(): array
    {
        return ['customer_id' => Customer::factory(), 'subject' => fake()->sentence(6), 'description' => fake()->paragraph(), 'priority' => 'normal', 'status' => 'open', 'version' => 0];
    }
}
