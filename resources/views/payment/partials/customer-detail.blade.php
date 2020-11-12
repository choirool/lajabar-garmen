<div class="w-full flex bg-blue-200 px-3 py-3 mb-5">
    <div class="w-1/2 my-2">
        <table>
            <tr>
                <td>Customer name</td>
                <td>:</td>
                <td>
                    {{ $order->customer->name }}
                </td>
            </tr>
            <tr>
                <td>Date</td>
                <td>:</td>
                <td>
                    {{ $order->invoice_date }}
                </td>
            </tr>
            <tr>
                <td>Sales</td>
                <td>:</td>
                <td>
                    {{ $order->salesman->name }}
                </td>
            </tr>
        </table>
    </div>
</div>