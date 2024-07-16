<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = ['seller_id', 'token'];
    protected $table = 'devices';


    public function sellers()
    {
        return $this->belongsTo(Seller::class);
    }
}
