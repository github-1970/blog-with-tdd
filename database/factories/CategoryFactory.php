<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $this->firstSeed();
        $parentId = ($this->getParent()->id ?? null);
        return [
            'name' => $this->faker->sentence(Arr::random([2, 3])),
            'parent_id' => Arr::random([null, $parentId])
        ];
    }

    private function getParent()
    {
        if(Category::count() == 0) {
            return null;
        }

        return Category::inRandomOrder()->first();
    }

    public function withParent()
    {
        $this->state(function (array $attributes) {
            $parent = $this->getParent();
            return array_merge($attributes, [
                'parent_id' => $parent->id
            ]);

            // return [
            //     'parent_id' => $this->getParent()->id,
            // ];
        });
    }

    private function firstSeed()
    {
        if(Category::count() == 0) {
            Category::create([
                'name' => 'root',
                'parent_id' => null
            ]);
        }
    }
}
