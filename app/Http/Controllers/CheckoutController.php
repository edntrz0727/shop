<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    //
    public function show(){
        //show結帳畫面
        //show購物車，購物車的結帳狀態false，且裡面有product，不然就fail
        $cart = auth()->user()->cart()->where('checked_out',false)
            ->with('items.product')->firstOrFail();

        //計算總額，$i->各個商品，取它的數量和價格相乘，最後sum
        $total = $cart->items->sum(fn($i)=>$i->product->price * $i->quantity);
        return view('checkout.show',compact('cart','total'));
    }

    public function confirm(){
        //確認購買
        $cart = auth()->user()->cart()->where('checked_out',false)->firstOrFail();
        //更新購物車狀態為已結帳
        $cart->update(['checked_out',true]);
        return redirect()->route('product.index')->with('msg','購買完成！'); //回到商品首頁
    }
}