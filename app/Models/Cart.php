<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\CartItem;

class Cart extends Model
{
    //
    protected $fillable = [
        'user_id',
        'checked_out'
    ];

    public function user(){
        // identify the cart belongs to which user
        return $this->belongsTo(User::class);
    }

    public function items(){
        return $this->hasMany(CartItem::class);
    }
}