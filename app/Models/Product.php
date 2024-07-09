<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    
    protected $table = 'products';

    protected $casts = [
        'name' => 'string',
        'description' => 'string',
        'price' => 'string',
        'discount' => 'string',
        'attributes' => 'array',
        'category_id' => 'integer',
        'status' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'description',
        'price',
        'discount',
        'attributes',
        'code',
        'category_id',
        'status',
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

    public function getDiscountPrice()
    {
        return $this->price - ($this->price*$this->discount) / 100;
    }
}
