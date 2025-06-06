<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;

// Route::get('/',function (){
//     return view('welcome');
// });
Route::get('/', [ProductController::class, 'index'])->name('products.index');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::delete('cart/item/{item}', [CartController::class, 'remove'])->name('cart.remove');
    Route::put('cart/update/{item}', [CartController::class, 'update'])->name('cart.update');
    Route::get('cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');

    Route::get('checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::get('checkout/confirm', [CheckoutController::class, 'confirm'])->name('checkout.confirm');
});

require __DIR__.'/auth.php';