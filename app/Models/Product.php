<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class Product extends Model
{
    //
    public function category(){
        // a product may belongs to many categories
        // but to be simple, set product would only belongs to one category
        return $this->belongsTo(Category::class);
    }
}