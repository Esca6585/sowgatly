<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
    use SoftDeletes;
    
    protected $table = 'carts';
    
    protected $dates = ['deleted_at'];
    
    protected $fillable = [
        'user_id',
        'ip_address',
        'standart_id',
    ];

    public function standart()
    {
        return $this->hasOne(Standart::class, 'id', 'standart_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    protected function fillableData()
    {
        return $this->fillable;
    }
}
