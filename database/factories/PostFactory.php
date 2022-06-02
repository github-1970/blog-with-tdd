<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory()->create(),
            'title' => $this->faker->sentence(Arr::random([2, 3, 4, 5])),
            'description' => $this->faker->text(Arr::random([120, 140, 170])),
            'body' => $this->faker->text(Arr::random([600, 700, 1500])),
        ];
    }
}
