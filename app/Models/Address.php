<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
    */
    protected $fillable = [
        'street',
        'settlement',
        'district',
        'province',
        'region',
        'country',
        'postal_code',
    ];

    /**
     * Get the shop associated with the address.
     */
    public function shop()
    {
        return $this->hasOne(Shop::class);
    }

    /**
     * Get the full address as a string.
     *
     * @return string
     */
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->street,
            $this->settlement,
            $this->district,
            $this->province,
            $this->region,
            $this->country,
            $this->postal_code
        ]);

        return implode(', ', $parts);
    }
}