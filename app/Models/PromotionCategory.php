<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionCategory extends Model
{
    protected $fillable = [
        'promotion_id',
        'category_id',
    ];

    /**
     * Promotion
     */
    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    /**
     * Category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}