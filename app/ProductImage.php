<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id', 'image_url'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
