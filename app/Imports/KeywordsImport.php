<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use App\Models\Keyword;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\NicheCategory;
use App\Models\RelatedKeyword;
use Illuminate\Database\QueryException;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
 
class KeywordsImport implements ToCollection, WithHeadingRow, WithChunkReading, WithValidation, SkipsOnFailure
{
    use SkipsFailures;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    // public function model(array $row)
    // {
    //     dd($row);
    //     return new Keyword([
    //         //
    //     ]);
    // }
    public function headingRow(): int
    {
        return 1;
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $category = Category::where('name', '=', $row["category"])->first();
            if (!$category) {
                $category = Category::create([
                    'name' => $row["category"],
                ]);
            }

            $sub_category = SubCategory::where('name', '=', $row["sub_category"])->first();
            if (!$sub_category) {
                $sub_category = SubCategory::create([
                    'name' => $row["sub_category"],
                ]);
            }

            $niche_category = NULL;
            if ($row["niche_category"] || strlen($row["niche_category"]) > 0) {
                $niche_category = NicheCategory::where('name', '=', $row["niche_category"])->first();
                if (!$niche_category) {
                    $niche_category = NicheCategory::create([
                        'name' => $row["niche_category"],
                    ]);
                }
            }
            

            $keyword = Keyword::where('name', '=', $row["keyword"])
                                ->where('category_id', '=', $category->id)
                                ->where('sub_category_id', '=', $sub_category->id)
                                ->first();
            if (!$keyword) {
                $keyword = Keyword::create([
                    'category_id' => $category->id,
                    'sub_category_id' => $sub_category->id,
                    'niche_category_id' => ($niche_category)?$niche_category->id:NULL,
                    'name' => $row["keyword"],
                    'game' => $row["game"],
                    'competition' => $row["competition"],
                    'traffic' => $row["traffic"],
                    'branded' => $row["branded"],
                ]);
            }else{
                $keyword->relatedKeywords()->delete();
            }

            $related_keywords = explode(", ", $row["related_keywords"]);

            foreach ($related_keywords as $index => $related_keyword) {
                $Keyword = Keyword::where('name', '=', $related_keyword)
                                ->where('category_id', '=', $category->id)
                                ->where('sub_category_id', '=', $sub_category->id)
                                ->first();
                if (!$Keyword) {
                   $Keyword = Keyword::create([
                        'category_id' => $category->id,
                        'sub_category_id' => $sub_category->id,
                        'niche_category_id' => ($niche_category)?$niche_category->id:NULL,
                        'name' => $related_keyword,
                        'game' => $row["game"],
                        'competition' => $row["competition"],
                        'traffic' => $row["traffic"],
                        'branded' => $row["branded"],
                    ]);
                }

                try {
                    $related_keyword = RelatedKeyword::create([
                        'keyword_id' => $keyword->id,
                        'related_keyword_id' => $Keyword->id,
                    ]);
                } catch (QueryException $e) {
                    continue;
                }
            }
        }
    }

    public function rules(): array
    {
        return [
            'keyword' => 'required|min:1|max:255',
            'category' => 'required|min:1|max:255',
            'sub_category' => 'required|min:1|max:255',
            'game' => 'in:yes,no',
            'competition' => 'required|integer|gte:0|lte: 20000',
            'traffic' => 'required|integer|gte:0|lte: 100',
            'branded' => 'in:yes,no',
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        // dd($failures[0]);
        // Handle the failures how you'd like.
        return $failures;
    }

    // public function customValidationMessages()
    // {
    //     return [
    //         'game' => 'The game column must be Yes or No only',
    //     ];
    // }
}
