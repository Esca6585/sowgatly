<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $table = 'shops';

    protected $fillable = [
        'name',
        'email',
        'image',
        'address',
        'mon_fri_open',
        'mon_fri_close',
        'sat_sun_open',
        'sat_sun_close',
        'user_id',
    ];

    protected function fillableData()
    {
        return $this->fillable;
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
