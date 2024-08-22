<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    
    protected $table = 'products';

    protected $fillable = [
        'name', 'description', 'price', 'discount', 'attributes', 'code', 
        'category_id', 'shop_id', 'brand_id', 'status',
        'gender', 'sizes', 'separated_sizes', 'color', 'manufacturer',
        'width', 'height', 'weight', 'production_time', 'min_order',
        'seller_status'
    ];
    
    protected $casts = [
        'price' => 'decimal:2',
        'discount' => 'integer',
        'attributes' => 'array',
        'sizes' => 'array',
        'separated_sizes' => 'array',
        'width' => 'double',
        'height' => 'double',
        'weight' => 'double',
        'production_time' => 'integer',
        'min_order' => 'integer',
        'seller_status' => 'boolean',
        'status' => 'boolean',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function compositions()
    {
        return $this->belongsToMany(Composition::class, 'product_compositions')
                    ->withPivot('qty', 'qty_type')
                    ->withTimestamps();
    }

    // Helper method
    public function getDiscountedPrice()
    {
        return $this->price - ($this->price * $this->discount / 100);
    }
}