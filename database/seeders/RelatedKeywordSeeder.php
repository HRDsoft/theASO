<?php

namespace Database\Seeders;

use App\Models\RelatedKeyword;
use Illuminate\Database\Seeder;

class RelatedKeywordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RelatedKeyword::factory()->count(5)->create();
    }
}
