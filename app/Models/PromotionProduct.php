<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionProduct extends Model
{
    protected $fillable = [
        'promotion_id',
        'product_id',
    ];

    /**
     * Promotion
     */
    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    /**
     * Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}