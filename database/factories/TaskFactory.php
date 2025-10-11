<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use App\Models\AutoDealership;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'comment' => fake()->optional()->sentence(),
            'creator_id' => User::factory(),
            'dealership_id' => AutoDealership::factory(),
            'appear_date' => null,
            'deadline' => fake()->optional()->dateTimeBetween('now', '+30 days'),
            'recurrence' => null,
            'task_type' => fake()->randomElement(['individual', 'group']),
            'response_type' => fake()->randomElement(['notification', 'execution']),
            'tags' => fake()->optional()->randomElements(['важное', 'срочное', 'рутина'], rand(0, 2)),
            'is_active' => true,
            'postpone_count' => 0,
            'archived_at' => null,
        ];
    }

    public function daily(): static
    {
        return $this->state(fn (array $attributes) => [
            'recurrence' => 'daily',
        ]);
    }

    public function weekly(): static
    {
        return $this->state(fn (array $attributes) => [
            'recurrence' => 'weekly',
        ]);
    }

    public function monthly(): static
    {
        return $this->state(fn (array $attributes) => [
            'recurrence' => 'monthly',
        ]);
    }

    public function notification(): static
    {
        return $this->state(fn (array $attributes) => [
            'response_type' => 'notification',
        ]);
    }

    public function execution(): static
    {
        return $this->state(fn (array $attributes) => [
            'response_type' => 'execution',
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'archived_at' => now(),
            'is_active' => false,
        ]);
    }
}
