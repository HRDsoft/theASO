<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Keyword;
use App\Models\NicheCategory;
use App\Models\SubCategory;

class KeywordFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Keyword::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'category_id' => Category::factory(),
            'sub_category_id' => SubCategory::factory(),
            'niche_category_id' => NicheCategory::factory(),
            'name' => $this->faker->name,
            'game' => $this->faker->randomElement(["yes","no"]),
            'competition' => $this->faker->numberBetween(-10000, 10000),
            'traffic' => $this->faker->numberBetween(-10000, 10000),
            'branded' => $this->faker->randomElement(["yes","no"]),
        ];
    }
}
