<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Update production (v3)') }}
        </h2>
    </x-slot>

    <div x-data="order()" x-init="initOrder($watch)" class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-1">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-1 py-1 sm:px-1 bg-white border-b border-gray-200">
                    <div class="w-full flex bg-blue-200 px-3 py-3 mb-5" style="background-color: {{ $customers->first()->invoice_color }};">
                        <div class="w-1/3 my-2">
                            <table>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td>Inv. Order #{{ $order->order_to }}</td>
                                </tr>
                                <tr>
                                    <td>Customer name</td>
                                    <td>:</td>
                                    <td>
                                        <select x-model="form.customer_id" class="w-full bg-white">
                                            @foreach ($customers as $customer)
                                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                            @endforeach
                                        </select>
                                        <template x-if="errors.customer_id">
                                            <p class="mt-2 text-sm text-red-600"
                                                x-text="errors.customer_id[0]"></p>
                                        </template>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Date</td>
                                    <td>:</td>
                                    <td>
                                        <input type="date" x-model="form.date" class="w-full">
                                        <template x-if="errors.date">
                                            <p class="mt-2 text-sm text-red-600"
                                                x-text="errors.date[0]"></p>
                                        </template>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Sales</td>
                                    <td>:</td>
                                    <td>
                                        <select x-model="form.salesman_id" class="w-full bg-white">
                                            <option value="">Select salesman</option>
                                            @foreach ($salesmen as $salesman)
                                                <option value="{{ $salesman->id }}">{{ $salesman->name }}</option>
                                            @endforeach
                                        </select>
                                        <template x-if="errors.salesman_id">
                                            <p class="mt-2 text-sm text-red-600"
                                                x-text="errors.salesman_id[0]"></p>
                                        </template>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="w-1/2 my-2">
                            <table>
                                <tr>
                                    <td>Phone</td>
                                    <td>:</td>
                                    <td>{{ $customer->phone }}</td>
                                </tr>
                                <tr>
                                    <td>Country</td>
                                    <td>:</td>
                                    <td>{{ $customer->country }}</td>
                                </tr>
                                <tr>
                                    <td>Inovice name</td>
                                    <td>:</td>
                                    <td>
                                        <textarea x-model="form.invoice_name"></textarea>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="w-full">
                        <template x-if="message != ''">
                            <x-alert type="success">
                                Data saved
                            </x-alert>
                        </template>
                    </div>

                    <template x-if="showTable">
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
                                        <th class="border" rowspan="2">Print</th>
                                        <th class="border" colspan="{{ $sizes->count() }}">Size</th>
                                        <th class="border" rowspan="2">Qty</th>
                                        <th class="border" rowspan="2">Price</th>
                                        @if (auth()->user()->isAbleTo('order-special-price'))
                                            <th class="border" rowspan="2">Special Price</th>
                                        @endif
                                        <th class="border" rowspan="2">Sub Total</th>
                                        <th class="border" rowspan="2">Image</th>
                                        <th class="border" rowspan="2">Note</th>
                                        @if (auth()->user()->isAbleTo('order-special-note'))
                                            <th class="border" rowspan="2">Special Note</th>
                                        @endif
                                        <th class="border" rowspan="2"></th>
                                    </tr>
                                    <tr>
                                        @foreach ($sizes as $size)
                                            <th class="border">{{ $size->name }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(order_line, index) in form.order_lines" :key="index">
                                        <tr>
                                            <td class="border" 
                                                :class="{ 'border-red-700': errors[`order_lines.${index}.item`] || errors[`order_lines.${index}.item_combination`] }">
                                                <select 
                                                    x-model="order_line.item"
                                                    x-on:change="
                                                        itemSelected(index, order_line.item), 
                                                        createItemCombination(index),
                                                        setPrice(index)
                                                    "
                                                    class="w-28 bg-white">
                                                    <option value="0">Select item</option>
                                                    @foreach ($customer->products->unique('item_id') as $item)
                                                        <option value="{{ $item->item->id }}">{{ $item->item->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="border" :class="{ 'border-red-700': errors[`order_lines.${index}.unit`] }">
                                                <input type="text" x-model="order_line.unit" class="w-10 text-center">
                                            </td>
                                            <td class="border" :class="{ 'border-red-700': errors[`order_lines.${index}.type`] }">
                                                <select x-model="order_line.type" class="w-28 bg-white" disabled>
                                                    <option value="0">Select category</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="border" 
                                                :class="{ 'border-red-700': errors[`order_lines.${index}.material`] || errors[`order_lines.${index}.item_combination`] }">
                                                <select 
                                                    x-model="order_line.material" 
                                                    class="w-28 bg-white"
                                                    @change="createItemCombination(index), setPrice(index)" disabled>
                                                    <option value="0">Select material</option>
                                                    @foreach ($materials as $material)
                                                        <option value="{{ $material->id }}">{{ $material->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="border"
                                                :class="{ 'border-red-700': errors[`order_lines.${index}.color`] || errors[`order_lines.${index}.item_combination`] }">
                                                <select 
                                                    x-model="order_line.color" 
                                                    class="bg-white"
                                                    @change="createItemCombination(index), setPrice(index)">
                                                    <option value="0">Select color</option>
                                                    @foreach ($colors as $color)
                                                        <option value="{{ $color->id }}">{{ $color->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="border text-center" :class="{ 'border-red-700': errors[`order_lines.${index}.printing`] }">
                                                <input type="checkbox" x-model="order_line.printing">
                                            </td>
                                            @foreach ($sizes as $y => $size)
                                                <td class="border" :class="{ 'border-red-700': errors[`order_lines.${index}.price{{ $y }}.qty`] }">
                                                    <input type="number" min="0" class="w-16" x-model="order_line.price[{{ $y }}].qty">
                                                </td>
                                            @endforeach
                                            <td class="border text-right">
                                                <div class="w-24" x-text="subQty(order_line)"></div>
                                            </td>
                                            <td class="border text-center">
                                                <input type="number" min="0" class="w-20" x-model="order_line.priceData" @change="priceDataChanged(order_line)">
                                            </td>
                                            @if (auth()->user()->isAbleTo('order-special-price'))
                                                <td class="border text-center">
                                                    <input type="number" min="0" class="w-20" x-model="order_line.specialPriceData">
                                                </td>
                                            @endif
                                            <td class="border text-right">
                                                <div class="w-24" x-text="subTotal(order_line)"></div>
                                            </td>
                                            <td class="border">
                                                <template x-if="order_line.image_url">
                                                    <a x-bind:href="order_line.image_url" target="_blank">View image</a>
                                                </template>
                                                <input type="file" :x-ref="`file_${index}`" :class="{ 'border-red-700': errors[`order_lines.${index}.image`] }">
                                            </td>
                                            <td class="border">
                                                <input type="text" x-model="order_line.note" :class="{ 'border-red-700': errors[`order_lines.${index}.note`] }">
                                            </td>
                                            @if (auth()->user()->isAbleTo('order-special-note'))
                                                <td class="border">
                                                    <input type="text" x-model="order_line.special_note" :class="{ 'border-red-700': errors[`order_lines.${index}.special_note`] }">
                                                </td>
                                            @endif
                                            <td class="border">
                                                <template x-if="form.order_lines.length > 1">
                                                    <span class="cursor-pointer" @click="removeLine(index)">X</span>
                                                </template>
                                            </td>
                                        </tr>
                                    </template>    
                                    <tr>
                                         <td class="pt-2">
                                            <a href="javascript:;" 
                                                @click="addNewLine()" 
                                                class="bg-blue-400 p-1">
                                                Add new item
                                            </a>
                                        </td>
                                        <td colspan="{{ ($sizes->count() + 9) }}" class="text-right">
                                            <span x-text="grandTotal()"></span>
                                        </td>
                                        <td class=""></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        @include('order.v3.dp-form')
                        <div class="w-full">
                            <template x-if="message != ''">
                                <x-alert type="success">
                                    Data saved
                                </x-alert>
                            </template>
                        </div>
                        <div class="mt-2 flex">
                            <template x-if="loading">
                                <button class="rounded p-2 bg-white hover:bg-gray-400 text-black">
                                    {{ __('Loading...') }}
                                </button>
                            </template>
                            <template x-if="!loading">
                                <button @click="saveOrder" class="rounded p-2 bg-white hover:bg-gray-400 text-black">
                                    {{ __('Save') }}
                                </button>
                            </template>
                        </div>
                    </template>
                    <div>
                        {{ $orderItems->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
    <script>
        function order() {
            return {
                errors: [],
                message: '',
                timeout: null,
                showTable: true,
                loading: false,
                availableItems: [],
                customerItems: [],
                sizes: @json($sizes),
                items: @json($items),
                form: {
                    id: {{ $order->id }},
                    customer_id: {{ $order->customer_id }},
                    date: '{{ $order->invoice_date }}',
                    salesman_id: {{ $order->salesman_id }},
                    invoice_name: '{{ $order->invoice_name }}',
                    order_lines: [],
                    deleted_items: [],
                    dp: {
                        has_dp: {{ $order->dp ? '1' : '0' }},
                        id: {{ $order->dp ? $order->dp->id : 'null' }},
                        payment_method: '{{ $order->dp ? str_replace(' ', '_', $order->dp->payment_method) : '' }}',
                        amount: {{ $order->dp ? $order->dp->amount : '0' }},
                        date: '{{ $order->dp ? $order->dp->payment_date : '' }}',
                        meta: {
                            bank_name: '',
                            account_number: '',
                            cc_name: '',
                            cc_number: '',
                            note: ''
                        }
                    }
                },
                addNewLine(data = null) {
                    var orderLine = {
                        id: '',
                        item: data ? data.item.id : '',
                        unit: data ? data.item.unit : '',
                        item_combination: '',
                        type: data ? data.item.category_id : '',
                        material: data ? data.material_id : '',
                        color: data ? data.color_id : '',
                        printing: data ? data.screen_printing == 1 : false,
                        image: '',
                        priceData: 0,
                        specialPriceData: 0,
                        note: '',
                        special_note: '',
                        price: [],
                    }

                    this.sizes.forEach(size => {
                        orderLine.price.push({
                            id: '',
                            size_id: size.id,
                            qty: 0,
                            price: 0,
                            special_price: 0
                        })
                    });
       
                    this.form.order_lines.push(orderLine)
                },
                removeLine(i) {
                    var deletedItem = this.form.order_lines[i]
                    if (deletedItem.id) {
                        this.form.deleted_items.push(deletedItem.id)
                    }

                    this.form.order_lines.splice(i, 1)
                },
                itemSelected(i, data) {
                    var selectedItem = this.items.find(item => item.id == data)
                    this.form.order_lines[i].unit = selectedItem.unit
                    this.form.order_lines[i].type = selectedItem.category_id
                    this.form.order_lines[i].material = selectedItem.material_id
                },
                createItemCombination(i) {
                    var formItem = this.form.order_lines[i]
                    this.form.order_lines[i].item_combination = `${formItem.item}_${formItem.material}_${formItem.color}`
                },
                setPrice(i) {
                    var formItem = this.form.order_lines[i]
                    if (formItem.item && formItem.material && formItem.color) {
                        var customerItem = this.customerItems.find(item => {
                            return item.item_id == formItem.item
                                && item.material_id == formItem.material
                                && item.color_id == formItem.color
                        })

                        if (customerItem) {
                            this.form.order_lines[i].priceData = customerItem.prices[0].price
                            this.form.order_lines[i].specialPriceData = customerItem.prices[0].special_price
                            this.form.order_lines[i].price.forEach(price => {
                                price.price = customerItem.prices[0].price
                                price.special_price = customerItem.prices[0].special_price
                            })
                        } else {
                            this.form.order_lines[i].priceData = 0
                            this.form.order_lines[i].price.forEach(price => {
                                price.price = 0
                                price.special_price = 0
                            })
                        }
                    }
                },
                subQty(data) {
                    var subQty = 0
                    data.price.forEach(qty => subQty += parseInt(qty.qty))
                    return subQty
                },
                subTotal(data) {
                    var subTotal = 0
                    data.price.forEach(price => subTotal += parseFloat(price.qty) * parseFloat(price.price))
                    return subTotal
                },
                grandTotal() {
                    var grandTotal = 0
                    this.form.order_lines.forEach(orderLine => {
                        orderLine.price.forEach(price => grandTotal += parseFloat(price.qty) * parseFloat(price.price))
                    });

                    return grandTotal
                },
                priceDataChanged(data) {
                    data.price.forEach(price => {
                        price.price = data.priceData
                    })
                },
                addDp() {
                    this.form.dp.has_dp = 1
                },
                removeDp() {
                    this.form.dp.has_dp = 0
                },
                checkDpAmount() {
                    if(this.form.dp.amount > this.grandTotal()) {
                        alert('Dp payment greater than total order amount')
                        this.form.dp.amount = 0
                    }
                },
                generateFormData() {
                    var formData = new FormData()
                    formData.append('method', '_patch')

                    for (const key in this.form) {
                        if (this.form.hasOwnProperty(key)) {
                            const element = this.form[key]
                            if (typeof element == 'object') {
                                if (key == 'dp') {
                                    for (const k in element) {
                                        if (element.hasOwnProperty(k)) {
                                            if (k !== 'meta') {
                                                formData.append(`dp[${k}]`, element[k])
                                            } else {
                                                formData.append(`dp[${k}]['bank_name']`, element[k]['bank_name'])
                                                formData.append(`dp[${k}]['account_number']`, element[k]['account_number'])
                                                formData.append(`dp[${k}]['cc_name']`, element[k]['cc_name'])
                                                formData.append(`dp[${k}]['cc_number']`, element[k]['cc_number'])
                                                formData.append(`dp[${k}]['note']`, element[k]['note'])
                                            }
                                        }
                                    }
                                }

                                if (key == 'order_lines') {
                                    for (const k in element) {
                                        if (element.hasOwnProperty(k)) {
                                            const orderLines = element[k];
                                            if (orderLines['id'] == undefined) {
                                                formData.append('deleted_items[]', orderLines)
                                            } else {
                                                formData.append(`order_lines[${k}][id]`, orderLines['id'])
                                                formData.append(`order_lines[${k}][item]`, orderLines['item'])
                                                formData.append(`order_lines[${k}][unit]`, orderLines['unit'])
                                                formData.append(`order_lines[${k}][item_combination]`, orderLines['item_combination'])
                                                formData.append(`order_lines[${k}][type]`, orderLines['type'])
                                                formData.append(`order_lines[${k}][material]`, orderLines['material'])
                                                formData.append(`order_lines[${k}][color]`, orderLines['color'])
                                                formData.append(`order_lines[${k}][printing]`, orderLines['printing'] == true ? '1' : '0')
                                                formData.append(`order_lines[${k}][note]`, orderLines['note'])
                                                formData.append(`order_lines[${k}][special_note]`, orderLines['special_note'])
                                                if (this.$refs[`file_${k}`].files[0]) {
                                                    formData.append(`order_lines[${k}][image]`, this.$refs[`file_${k}`].files[0])
                                                }

                                                orderLines['price'].forEach((price, i) => {
                                                    formData.append(`order_lines[${k}][price][${i}][id]`, price.id)
                                                    formData.append(`order_lines[${k}][price][${i}][size_id]`, price.size_id)
                                                    formData.append(`order_lines[${k}][price][${i}][qty]`, price.qty)
                                                    formData.append(`order_lines[${k}][price][${i}][price]`, price.price)
                                                    formData.append(`order_lines[${k}][price][${i}][special_price]`, price.special_price)
                                                })
                                            }
                                        }
                                    }
                                }

                                if (key == 'deleted_items') {
                                    element.forEach((el, i) => {
                                        formData.append(`deleted_items[${i}]`, el)
                                    })
                                }
                            } else {
                                formData.append(key, element)
                            }
                        }
                    }

                    return formData
                },
                saveOrder() {
                    let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    this.errors = []
                    this.loading = true
                    fetch('{{ route('transactions.v2.update-order', ['page' => request()->input('page')]) }}', {
                        method: 'POST',
                        headers: {
                            // 'Content-Type': 'multipart/form-data',
                            // 'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json, text-plain, */*',
                            'X-CSRF-TOKEN': token
                        },
                        body: this.generateFormData()
                    })
                    .then(response => response.json())
                    .then((response) => {
                        if (response.errors) {
                            this.errors = response.errors
                            this.loading = false
                        }

                        if (response.status) {
                            clearTimeout(this.timeout)
                            this.message = response.message
                            this.timeout = setTimeout(() => { 
                                this.message = '' 
                                window.location = response.redirect
                            }, 1000)
                            this.loading = false
                        }
                    }).catch((error) => {
                        console.log(error);
                        this.loading = false
                    })
                },
                initOrder($watch) {
                    @foreach($orderItems->items() as $i => $orderItem)
                        this.form.order_lines.push({
                            id: {{ $orderItem->id }},
                            item: {{ $orderItem->item_id }},
                            unit: '{{ $orderItem->item->unit }}',
                            item_combination: '{{ $orderItem->item_id }}_{{ $orderItem->material_id }}_{{ $orderItem->color_id }}',
                            type: {{ $orderItem->item->category_id }},
                            material: {{ $orderItem->material_id }},
                            color: {{ $orderItem->color_id }},
                            printing: {{ $orderItem->screen_printing }},
                            image: '',
                            image_url: '{{ $orderItem->image_url }}',
                            priceData: 0,
                            specialPriceData: 0,
                            note: '{{ $orderItem->note }}',
                            special_note: '{{ $orderItem->special_note }}',
                            price: []
                        })
                        
                        this.availableItems = []
                        var customerItems = @json($customer->products);
                        if (customerItems.length > 0) {
                            this.customerItems = customerItems
                            var itemIds = customerItems.map(item => item.item_id)
                            this.availableItems = this.items.filter(item => itemIds.includes(item.id))
                        }

                        @foreach($sizes as $size)
                            @php
                                $currentPirceData = $orderItem->prices->first(fn ($price) => $price->size_id == $size->id);
                                $nonZeroPriceData = $orderItem->prices->first(fn ($price) => $price->price > 0);
                            @endphp

                            this.form.order_lines[{{ $i }}].priceData = {{ $nonZeroPriceData ? $nonZeroPriceData->price : 0 }}
                            this.form.order_lines[{{ $i }}].specialPriceData = {{ $nonZeroPriceData ? $nonZeroPriceData->special_price : 0 }}

                            this.form.order_lines[{{ $i }}].price.push({
                                id: {{ $currentPirceData ? $currentPirceData->id : 'null' }},
                                size_id: {{ $size->id }},
                                qty: {{ $currentPirceData ? $currentPirceData->qty: 0 }},
                                price: {{ $nonZeroPriceData ? $nonZeroPriceData->price : 0 }},
                                special_price: {{ $nonZeroPriceData ? $nonZeroPriceData->special_price : 0 }}
                            })
                        @endforeach
                    @endforeach
                }
            }
        }
    </script>
@endpush
</x-app-layout>