<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'address_1',
        'address_2',
        'postal_code',
    ];

    protected $casts = [
        'address_1' => 'string',
        'address_2' => 'string',
        'postal_code' => 'string',
    ];

    /**
     * Get the shop that owns the address.
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Get the full address as a string.
     *
     * @return string
     */
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address_1,
            $this->address_2,
            $this->postal_code
        ]);

        return implode(', ', $parts);
    }
}