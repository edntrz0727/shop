<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\CartItem;

class CartController extends Controller
{
    //
    public function index(){
        //load屬於哪個使用者->還沒結帳的購物車
        $cart = auth()->user()->cart()->where('checked_out',false)->firstOrCreate(
            ['checked_out'=>false]
        );
        return view('cart.index',['cart'=>$cart->load('items.product')]);
    }

    public function add(Product $product, Request $req){
        //新增商品進購物車
        //load購物車，加到最新or新建購物車，設定購物車還沒結帳
        // dd($req->input('quantity'));
        $qty = (int) $req->input('quantity', 1);

        $cart = auth()->user()->cart()->firstOrCreate(
            ['checked_out' => false]
        );
        $item = $cart->items()->firstOrCreate(['product_id'=>$product->id]);
        $item->quantity = ($item->quantity ?? 0) + $qty;
        // dd($item->quantity);
        $item->save();

        return back()->with('msg','已加入購物車！');
    }

    // public function update(Request $request, CartItem $itemId)
    // {
    //     // dd($item->id);
    //     // dd($request->all(), $itemId->toArray());
    //     $cart = auth()->user()->cart()->where('checked_out', false)->firstOrFail();
    //     $item = $cart->items()->find($itemId);

    //     if($item->cart->user_id !== auth()->id()) {
    //         abort(403, '你沒有權限修改這個購物車項目');
    //     }

    //     $item->quantity = max(1, (int) $request->input('quantity', 1));
    //     $item->save();

    //     return back()->with('msg', '購物車已更新');
    // }

    public function remove(CartItem $item){
        //移除購物車裡的商品
        //確保只能刪到該購物車內的商品
        if ($item->cart->user_id !== auth()->id()) {
            abort(403, '無權限執行此操作');
        }
        $item->delete();
        return back()->with('msg','已從購物車中移除');
    }

    public function checkout(){
        $cart = auth()->user()->cart()->where('checked_out',false)->firstOrFail();
        $cart->checked_out = true;
        $cart->save();

        return redirect()->route('cart.index')->with('msg', '感謝購買，購物車已清空！');
    }
}