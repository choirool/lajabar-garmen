<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Productions') }}
        </h2>
    </x-slot>

    <div x-data="production()" x-init="initOrder($watch)" class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-1">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-1 py-1 sm:px-1 bg-white border-b border-gray-200">
                    <div class="w-full flex bg-blue-200 px-3 py-3 mb-5">
                        <div class="w-1/2 my-2">
                            <table>
                                <tr>
                                    <td>Customer name</td>
                                    <td>:</td>
                                    <td x-text="order.customer.name"></td>
                                </tr>
                                <tr>
                                    <td>Order Date</td>
                                    <td>:</td>
                                    <td>
                                        {{ $order->invoice_date }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Created on</td>
                                    <td>:</td>
                                    <td>
                                        {{ $order->created_at }}
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

                    <div class="w-full flex overflow-x-scroll overflow-y-hidden">
                        <table class="table-auto text-xs">
                            <thead>
                                <tr>
                                    <th class="border" rowspan="2">Item name</th>
                                    <th class="border" rowspan="2">
                                        <div class="w-10">Unit</div>
                                    </th>
                                    <th class="border" rowspan="2">Type</th>
                                    <th class="border" rowspan="2">Material</th>
                                    <th class="border" rowspan="2">Color</th>
                                    <th class="border" rowspan="2">Sablon</th>
                                    <th class="border" colspan="{{ $sizes->count() }}">Size</th>
                                    <th class="border" rowspan="2">Qty</th>
                                    <th class="border" rowspan="2">Price</th>
                                    <th class="border" rowspan="2">Sub Total</th>
                                    <th class="border" rowspan="2">Image</th>
                                    <th class="border" rowspan="2">Note</th>
                                    <th class="border" rowspan="2">Percentage</th>
                                </tr>
                                <tr>
                                    @foreach ($sizes as $size)
                                        <th class="border">{{ $size->name }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(orderItem, index) in order.order_items">
                                    <tr>
                                        <td class="border" x-text="orderItem.item.name"></td>
                                        <td class="border" x-text="orderItem.item.unit"></td>
                                        <td class="border" x-text="orderItem.item.category.name"></td>
                                        <td class="border" x-text="orderItem.material.name"></td>
                                        <td class="border" x-text="orderItem.color.name"></td>
                                        <td class="border" x-text="printing(orderItem.screen_printing)"></td>
                                        <template x-for="(size, i) in sizes">
                                            <td class="border">
                                                <template x-if="!form[index].values[i].disabled">
                                                    <input 
                                                        type="number" 
                                                        min="0" 
                                                        max="5" 
                                                        class="w-16" 
                                                        x-model="form[index].values[i].value"
                                                        @change="valueChanged(orderItem, index, i)">
                                                </template>
                                                <template x-if="form[index].values[i].disabled">
                                                    <input type="number" min="0" class="w-16 bg-gray-300" disabled>
                                                </template>
                                            </td>
                                        </template>
                                        <td class="border" x-text="subTotalQty(orderItem)"></td>
                                        <td class="border" x-text="orderItem.prices[0].price"></td>
                                        <td class="border" x-text="subTotal(orderItem)"></td>
                                        <td class="border">
                                            <a :href="imageUrl(orderItem.image_url)">View image</a>
                                        </td>
                                        <td class="border" x-text="orderItem.note"></td>
                                        <td class="border" x-text="percentage(orderItem, index)"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-2 flex">
                        <template x-if="loading">
                            <button class="rounded p-2 bg-white hover:bg-gray-400 text-black">
                                {{ __('Loading...') }}
                            </button>
                        </template>
                        <template x-if="!loading">
                            <button @click="saveData" class="rounded p-2 bg-white hover:bg-gray-400 text-black">
                                {{ __('Save') }}
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function production() {
            return {
                errors: [],
                howTable: false,
                loading: false,
                order: @json($order),
                sizes: @json($sizes),
                form: [],
                printing(data) {
                    return data ? 'Yes' : 'no'
                },
                imageUrl(data) {
                    return data ? data : '#'
                },
                subTotalQty(data) {
                    return data.prices.reduce((carry, price) => {
                       return carry += parseInt(price.qty)
                    }, 0)
                },
                subTotal(data) {
                    return data.prices.reduce((carry, price) => {
                       return carry += parseFloat(price.price)
                    }, 0)
                },
                progress(formIndex) {
                    return this.form[formIndex].values.reduce((carry, v) => {
                        return carry + parseInt(v.value)
                    }, 0)
                },
                percentage(orderItem, formIndex) {
                    var totalQty = this.subTotalQty(orderItem)
                    var percentage = 0
                    
                    percentage = Math.floor(this.progress(formIndex) / totalQty * 100)
                    return `${percentage}%`;
                },
                valueChanged(orderItem, index, i) {
                    var totalQty = this.subTotalQty(orderItem)
                    var progress = this.progress(index)
                    if (progress > totalQty) {
                        alert('Value too large')
                        this.form[index].values[i].value = this.form[index].values[i].old_value
                    } else {
                        this.form[index].values[i].old_value = this.form[index].values[i].value
                    }
                },
                saveData() {
                    let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    this.errors = []
                    this.loading = true

                    fetch('{{ route('transactions.production.store', ['orderId' => $order->id]) }}', {
                        method: 'POST',
                        headers: {
                            // 'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json, text-plain, */*',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify(this.form)
                    })
                    .then(response => response.json())
                    .then((response) => {
                        this.loading = false
                        console.log(response);
                        // if (response.errors) {
                        //     this.errors = response.errors
                        //     this.loading = false
                        // }

                        // if (response.status) {
                        //     window.location = response.redirect
                        // }
                    }).catch((error) => {
                        console.log(error);
                        this.loading = false
                    })
                },
                initOrder($watch) {
                    this.order.order_items.forEach((orderItem, index) => {
                        this.form.push({
                            id: orderItem.id,
                            values: []
                        })

                        this.sizes.forEach(size => {
                            var currentSize = orderItem.prices.find(p => p.size_id == size.id)

                            this.form[index].values.push({
                                id: currentSize ? currentSize.id : null,
                                size_id: size.id,
                                value: 0,
                                old_value: 0,
                                disabled: currentSize == undefined
                            })
                        })
                    });
                }
            }
        }
    </script>
    @endpush
</x-app-layout>