<table class="table-auto w-full text-xs">
    <tbody>
        <tr>
            <td colspan="{{ $sizes->count() + 7}}">AL - JABARS</td>
        </tr>
        <tr>
            <td colspan="{{ $sizes->count() + 7}}">Jl. Pulau Batanta Gang VII A No. 4</td>
        </tr>
        <tr>
            <td>Name:</td>
            <td>{{ $order->customer->name }}</td>
        </tr>
        <tr>
            <td>Date:</td>
            <td>{{ $order->invoice_date }}</td>
        </tr>
        <tr>
            <td>Order ID:</td>
            <td>#{{ $order->invoice_code }}</td>
        </tr>
        <tr>
            <td colspan="{{ $sizes->count() + 7}}"></td>
        </tr>
        <tr>
            <th class="border" rowspan="2">Name</th>
            <th class="border" rowspan="2">Type</th>
            <th class="border" rowspan="2">Material</th>
            <th class="border" rowspan="2">Color</th>
            <th class="border" colspan="{{ $sizes->count() }}">Size</th>
            <th class="border" rowspan="2">QTY</th>
            @if ((int)$request->hide_price == 0)
            <th class="border" rowspan="2">Price</th>
            <th class="border" rowspan="2">Total Price</th>
            @endif
        </tr>
        <tr>
            @foreach ($sizes as $size)
                <th class="border">{{ $size->name }}</th>
            @endforeach
        </tr>
        @foreach ($order->orderItems as $orderItem)
            <tr>
                <td class="border">{{ $orderItem->item->name }}</td>
                <td class="border">{{ $orderItem->item->category->name }}</td>
                <td class="border">{{ $orderItem->material->name }}</td>
                <td class="border">{{ $orderItem->color->name }}</td>
                @foreach ($sizes as $size)
                    @php
                        $currentPrice = $orderItem->prices->first(fn ($price) => $price->size_id == $size->id);
                    @endphp
                    <td class="border text-center">
                        {{ $currentPrice ? $currentPrice->qty : 0 }}
                    </td>
                @endforeach
                <td class="border text-center">{{ $orderItem->prices->sum('qty') }}</td>
                @if ((int)$request->hide_price == 0)
                <td class="border text-right">{{ $orderItem->prices->first()->price }}</td>
                <td class="border text-right">
                    {{ $orderItem->prices->sum(fn ($price) => $price->qty * $price->price ) }}
                </td>
                @endif
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="{{ $sizes->count() + 4 }}"></td>
            <td class="border text-center">
                {{ $order->orderItems->reduce(fn ($carry, $item) => $carry + $item->prices->sum('qty')) }}
            </td>
            @if ((int)$request->hide_price == 0)
            <td class="border">Total</td>
            <td class="border text-right">{{ $order->order_amount }}</td>
            @endif
        </tr>
        @if ((int)$request->hide_price == 0)
        <tr>
            <td colspan="{{ $sizes->count() + 5 }}"></td>
            <td class="border">DP</td>
            <td class="border text-right">
                @php
                    $dp = $order->dp ? $order->dp->amount : 0;
                @endphp
                {{ $dp }}
            </td>
        </tr>
        @if ($order->payments)
            @foreach ($order->payments as $payment)
                <tr>
                    <td colspan="{{ $sizes->count() + 5 }}"></td>
                    <td class="border">Payment {{ $payment->payment_date }}</td>
                    <td class="border text-right">
                        {{ $payment->amount }}
                    </td>
                </tr>
            @endforeach
        @endif  
        <tr>
            <td colspan="{{ $sizes->count() + 5 }}"></td>
            <td class="border">Balance</td>
            <td class="border text-right">
                {{ $order->order_amount - $order->paid_amount }}
            </td>
        </tr>
        @endif
    </tfoot>
</table>