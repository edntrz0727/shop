@extends('layouts.app')
@section('content')
<div class="max-w-6xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <form method="GET" action="{{ route('products.index') }}">
        <select name="category_id" onchange="this.form.submit()">
            <option value="">全部分類</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>
        </form>
        
        <a href="{{ route('cart.index') }}" class="text-sm">購物車</a>
    </div>
    @if ($products->isEmpty())
        <div class="text">目前此分類沒有商品。</div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($products as $product)
                <div class="border rounded p-4 shadow hover:shadow-lg transition">
                    <h3 class="text">{{ $product->name }}</h3>
                    <p class="text-sm">{{ $product->category->name ?? '未分類' }}</p>
                    <p class="mt-2 text">${{ number_format($product->price, 0) }}</p>
                    <form method="POST" action="{{ route('cart.add', $product->id) }}" class="mt-2">
                        @csrf
                        <div class="flex items-center space-x-2">
                            <select name="quantity" class="px-4 py-1 text-sm">
                                @for ($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                            <button type="submit" class="text-sm">
                                加入購物車
                            </button>
                        </div>
                    </form>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $products->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection