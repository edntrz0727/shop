<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    //
    public function index(Request $req)
    {
        $categories = Category::all();
        $products = Product::with('category')
            ->when($req->category_id, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->paginate(12);
        return view('products.index', compact('products', 'categories'));
    }           
}