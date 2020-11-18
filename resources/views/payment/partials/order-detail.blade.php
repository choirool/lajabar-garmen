<div class="w-full flex overflow-x-scroll overflow-y-hidden">
    <table class="table-auto text-xs">
        <thead>
            <tr>
                <th class="border w-32" rowspan="2">Item name</th>
                <th class="border w-10" rowspan="2">Unit</th>
                <th class="border w-56" rowspan="2">Type</th>
                <th class="border w-32" rowspan="2">Material</th>
                <th class="border" rowspan="2">Color</th>
                <th class="border" rowspan="2">Sablon</th>
                <th class="border" colspan="{{ $sizes->count() }}">Size</th>
                <th class="border w-10" rowspan="2">Qty</th>
                <th class="border w-24" rowspan="2">Price</th>
                @if (auth()->user()->isAbleTo('order-special-price'))
                    <th class="border w-24" rowspan="2">Special Price</th>
                @endif
                <th class="border w-24" rowspan="2">Sub Total</th>
                <th class="border" rowspan="2">Note</th>
                @if (auth()->user()->isAbleTo('order-special-note'))
                    <th class="border" rowspan="2">Special Note</th>
                @endif
            </tr>
            <tr>
                @foreach ($sizes as $size)
                    <th class="border w-10">{{ $size->name }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($order->orderItems as $orderItem)
                <tr>
                    <td class="border">{{ $orderItem->item->name }}</td>
                    <td class="border">{{ $orderItem->item->unit }}</td>
                    <td class="border">{{ $orderItem->item->category->name }}</td>
                    <td class="border">{{ $orderItem->material->name }}</td>
                    <td class="border">{{ $orderItem->color->name }}</td>
                    <td class="border text-center">
                        {{ $orderItem->screen_printing ? 'Yes' : 'No' }}
                    </td>
                    @foreach ($sizes as $y => $size)
                        @php
                            $price = $orderItem->prices->first(fn($p) => $p->size_id == $size->id)
                        @endphp
                        <td class="border text-center">{{ $price ? $price->qty : 0 }}</td>
                    @endforeach
                    <td class="border text-center">
                        {{ $orderItem->prices->sum('qty') }}
                    </td>
                    <td class="border text-right">
                        @php
                            $price = $orderItem->prices->first(fn($p) => $p->price > 0)
                        @endphp
                        {{ format_number($price->price) }}
                    </td>
                    @if (auth()->user()->isAbleTo('order-special-price'))
                        <td class="border text-right">
                            {{ format_number($price->special_price) }}
                        </td>
                    @endif
                    <td class="border text-right">
                        @php
                            $subTotal = $orderItem->prices->reduce(function ($carry, $item) {
                                return $carry + ($item->price * $item->qty);
                            });
                        @endphp
                        {{ format_number($subTotal) }}
                    </td>
                    <td class="border">{{ $orderItem->note }}</td>
                    @if (auth()->user()->isAbleTo('order-special-note'))
                        <td class="border">{{ $orderItem->special_note }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td class="text-right" colspan="{{ $sizes->count() + 9 }}">Total</td>
                <td class="text-right">
                    {{ format_number($order->order_amount) }}
                </td>
            </tr>
            <tr>
                <td class="text-right" colspan="{{ $sizes->count() + 9 }}">DP</td>
                <td class="text-right">
                    {{ $order->dp ? format_number($order->dp->amount) : 0 }}
                </td>
            </tr>
            @foreach ($order->payments as $payment)
                <tr>
                    <td class="text-right" colspan="{{ $sizes->count() + 9 }}">Payment {{ $payment->payment_date }}</td>
                    <td class="text-right">
                        {{ format_number($payment->amount) }}
                    </td>
                    <td>
                        <a href="{{ route('transactions.payment.create', ['orderId' => $order->id, 'paymentId' => $payment->id]) }}">Edit</a>
                    </td>
                </tr>
            @endforeach
            <tr>
                <td class="border-t-2 text-right text-lg" colspan="{{ $sizes->count() + 9 }}">Amount due</td>
                <td class="border-t-2 text-right text-lg">
                    {{ format_number($order->order_amount - $order->paid_amount)  }}
                </td>
            </tr>
        </tfoot>
    </table>
</div>