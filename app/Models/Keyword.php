<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Keyword extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'category_id',
        'sub_category_id',
        'niche_category_id',
        'game',
        'competition',
        'traffic',
        'branded',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'category_id' => 'integer',
        'sub_category_id' => 'integer',
        'niche_category_id' => 'integer',
    ];

    public function relatedKeywords()
    {
        return $this->hasMany(RelatedKeyword::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function nicheCategory()
    {
        return $this->belongsTo(NicheCategory::class);
    }
    //  public function keywords()
    // {
    //     return $this;
    // }
}
