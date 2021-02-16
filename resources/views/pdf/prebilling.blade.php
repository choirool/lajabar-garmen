<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="stylesheet" href="http://aljabar.test/css/app.css">
</head>
<body>
    <div>
        <div class="w-full">
            <p class="h1 text-center">AL - JABARS</p>
            <p class="text-center">Jl. Pulau Batanta Gang VII A No. 4</p>
        </div>
        <div class="w-full justify-between">
            <table class="w-full">
                <tr>
                    <td>
                        <p class="text-xs">Name: {{ $order->customer->name }}</p>
                        <p class="text-xs">Date: {{ $order->invoice_date }}</p>
                        <p class="text-xs">Order ID: {{ $order->invoice_code }}</p>
                    </td>
                    <td>
                        <p class="pr-5 pl-5" style="background-color: {{ $order->customer->invoice_color }};">
                            Inv. Order #{{ $order->order_to }}
                        </p>
                    </td>
                </tr>
            </table>
            {{-- <div>
                <p class="text-xs">Name: {{ $order->customer->name }}</p>
                <p class="text-xs">Date: {{ $order->invoice_date }}</p>
                <p class="text-xs">Order ID: {{ $order->invoice_code }}</p>
            </div>
            <div>
                <p class="pr-5 pl-5" style="background-color: {{ $order->customer->invoice_color }};">
                Inv. Order #{{ $order->order_to }}
                </p>
            </div> --}}
        </div>
    </div>
    <table class="table-auto w-full text-xs">
        <thead>
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
        </thead>
        <tbody>
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
                    <td class="border text-right">{{ $orderItem->prices->first() ? format_number($orderItem->prices->first()->price) : 0 }}</td>
                    <td class="border text-right">
                        {{ format_number($orderItem->prices->sum(fn ($price) => $price->qty * $price->price)) }}
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
                <td class="border text-right">{{ format_number($order->order_amount) }}</td>
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
                    {{ format_number($dp) }}
                </td>
            </tr>
            @if ($order->payments)
                @foreach ($order->payments as $payment)
                    <tr>
                        <td colspan="{{ $sizes->count() + 5 }}"></td>
                        <td class="border">Payment {{ $payment->payment_date }}</td>
                        <td class="border text-right">
                            {{ format_number($payment->amount) }}
                        </td>
                    </tr>
                @endforeach
            @endif  
            <tr>
                <td colspan="{{ $sizes->count() + 5 }}"></td>
                <td class="border">Balance</td>
                <td class="border text-right">
                    {{ format_number($order->order_amount - $order->paid_amount) }}
                </td>
            </tr>
            @endif
        </tfoot>
    </table> 
</body>
</html>
