<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Keyword;
use App\Models\RelatedKeyword;

class RelatedKeywordFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RelatedKeyword::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'keyword_id' => Keyword::factory(),
            'related_keyword_id' => RelatedKeyword::factory(),
        ];
    }
}
