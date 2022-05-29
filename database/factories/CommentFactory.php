<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class CommentFactory extends Factory
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
            'commentable_id' => Post::factory()->create(),
            'commentable_type' => Post::class,
            'body' => $this->faker->text(Arr::random([100, 500])),
        ];
    }
}
