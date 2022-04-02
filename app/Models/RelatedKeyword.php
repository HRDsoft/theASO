<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelatedKeyword extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'keyword_id',
        'related_keyword_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'keyword_id' => 'integer',
        'related_keyword_id' => 'integer',
    ];

    public function keyword()
    {
        return $this->belongsTo(Keyword::class);
    }

    public function relatedKeyword()
    {
        return $this->belongsTo(RelatedKeyword::class);
    }
}
