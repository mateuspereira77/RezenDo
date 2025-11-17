<?php

namespace Database\Factories;

use App\Models\Todo;
use App\Priority;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Todo>
 */
class TodoFactory extends Factory
{
    protected $model = Todo::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'text' => fake()->sentence(3),
            'description' => fake()->optional()->paragraph(2),
            'completed' => fake()->boolean(30), // 30% de chance de estar concluída
            'priority' => fake()->randomElement(Priority::values()),
            'day' => fake()->optional()->randomElement(['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo']),
            'date' => fake()->optional(0.6)->date(),
        ];
    }

    /**
     * Indicate that the todo is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'completed' => true,
        ]);
    }

    /**
     * Indicate that the todo is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'completed' => false,
        ]);
    }

    /**
     * Indicate that the todo is urgent.
     */
    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => Priority::URGENT,
        ]);
    }

    /**
     * Indicate that the todo has a medium priority.
     */
    public function medium(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => Priority::MEDIUM,
        ]);
    }

    /**
     * Indicate that the todo has a simple priority.
     */
    public function simple(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => Priority::SIMPLE,
        ]);
    }

    /**
     * Indicate that the todo has a date.
     */
    public function withDate(string $date = null): static
    {
        return $this->state(fn (array $attributes) => [
            'date' => $date ?? fake()->date(),
        ]);
    }
}
