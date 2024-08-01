<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    
    protected $table = 'products';

    protected $fillable = [
        'name',
        'description',
        'price',
        'discount',
        'attributes',
        'code',
        'category_id',
        'shop_id',
        'status',
    ];
    
    protected $casts = [
        'name' => 'string',
        'description' => 'string',
        'price' => 'float',
        'discount' => 'float',
        'attributes' => 'array',
        'category_id' => 'integer',
        'shop_id' => 'integer',
        'status' => 'boolean',
    ];

    protected function fillableData()
    {
        return $this->fillable;
    }

    public function images()
    {
        return $this->hasMany(Image::class,'product_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function getDiscountPrice()
    {
        return $this->price - ($this->price*$this->discount) / 100;
    }
}
