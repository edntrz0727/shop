<table class="table">
    <thead><th>商品</th><th>數量</th><th>小計</th></thead>
    <tbody>
        @foreach($cart->items as $item)
        <tr>
            <td>{{ $item->product->name }}</td>
            <td>{{ $item->quantity }}</td>
            <td>${{ $item->product->price * $item->quantity }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr><th colspan="2">總額</th><th>${{ $total }}</th></tr>
    </tfoot>
</table>
<form method="POST" action="{{ route('checkout.confirm') }}">
    @csrf
    <button class="btn btn-success btn-lg w-100">確認結帳</button>
</form>