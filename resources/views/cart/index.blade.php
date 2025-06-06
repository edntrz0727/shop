@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text">購物車</h2>
        <a href="{{ route('products.index') }}" class="text">
            回到商品目錄
        </a>
    </div>

    @if ($cart->items->isEmpty())
        <p class="text">你的購物車是空的。</p>
    @else
            <table class="w-full border-collapse border border-gray-300 mb-6">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 p-2 text-left">商品名稱</th>
                        <th class="border border-gray-300 p-2 text-right">單價</th>
                        <th class="border border-gray-300 p-2 text-center">數量</th>
                        <th class="border border-gray-300 p-2 text-right">小計</th>
                        <th class="border border-gray-300 p-2 text-center">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cart->items as $item)
                    <tr>
                        <td class="p-2">{{ $item->product->name }}</td>
                        <td class="p-2 text-right">${{ number_format($item->product->price) }}</td>
                        <td class="p-2 text-center">
                            {{ $item->quantity }}
                        </td>
                        <td class="p-2 text-right">
                            ${{ number_format($item->product->price * $item->quantity) }}
                        </td>
                        <td class="p-2 text-center">
                            <form action="{{ route('cart.remove', $item->id) }}" method="POST" onsubmit="return confirm('確定要移除該商品嗎？')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600">移除</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="text-right">
                <span class="text-lg">
                    總計：$
                    {{
                        number_format(
                            $cart->items->reduce(function ($carry, $item) {
                                return $carry + $item->product->price * $item->quantity;
                            }, 0)
                        )
                    }}
                </span>
            </div>

            <div class="flex justify-end space-x-4">
                <form action="{{ route('cart.checkout') }}" method="POST" onsubmit="return confirm('確定要結帳並清空購物車嗎？')">
                    @csrf
                    <button type="submit" class="px-4 py-2">結帳</button>
                </form>
            </div>
    @endif
</div>
@endsection