<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

class TagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->text(20),
            'slug' => $this->faker->slug(),
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function (Tag $tag) {
            //
        })->afterCreating(function (Tag $tag) {
            // $tag->posts()->attach(Post::factory(5)->create());
        });
    }
}
