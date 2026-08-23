<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement([Transaction::TYPE_INCOME, Transaction::TYPE_EXPENSE]);

        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory()->state(['type' => $type]),
            'date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'type' => $type,
            'amount' => fake()->randomFloat(2, 100, 50000),
            'memo' => fake()->optional()->sentence(),
        ];
    }

    public function income(): static
    {
        return $this->state(fn () => ['type' => Transaction::TYPE_INCOME]);
    }

    public function expense(): static
    {
        return $this->state(fn () => ['type' => Transaction::TYPE_EXPENSE]);
    }
}
