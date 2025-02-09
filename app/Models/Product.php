<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    
    protected $table = 'products';

    protected $fillable = [
        'name', 'description', 'price', 'discount', 'code', 
        'category_id', 'shop_id', 'brand_ids', 'status',
        'gender', 'sizes', 'separated_sizes', 'color', 'manufacturer',
        'width', 'height', 'weight', 'production_time', 'min_order',
        'seller_status'
    ];
    
    protected $casts = [
        'price' => 'decimal:2',
        'discount' => 'integer',
        'brand_ids' => 'array',
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

    public function brands()
    {
        return $this->belongsToMany(Brand::class)
                    ->whereIn('brands.id', $this->brand_ids);
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

    // Scope to filter products by brand
    public function scopeWithBrand($query, $brandId)
    {
        return $query->whereJsonContains('brand_ids', $brandId);
    }

    public function scopeWithFullDetails($query)
    {
        return $query->with(['category', 'shop', 'brands', 'images', 'compositions']);
    }
}