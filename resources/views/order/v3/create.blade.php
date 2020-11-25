<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Productions') }}
        </h2>
    </x-slot>

    <div x-data="order()" x-init="initOrder($watch)" class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-1">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-1 py-1 sm:px-1 bg-white border-b border-gray-200">
                    @include('order.v3.customer-form')

                    <template x-if="showTable">
                        <div class="w-full flex" :class="{'overflow-x-scroll overflow-y-hidden' :overflow}">
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
                                                    <template x-for="item in availableItems">
                                                        <option :value="item.id" x-text="item.name"></option>
                                                    </template>
                                                    {{-- @foreach ($items as $item)
                                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                    @endforeach --}}
                                                </select>
                                            </td>
                                            <td class="border" :class="{ 'border-red-700': errors[`order_lines.${index}.unit`] }">
                                                <input type="text" x-model="order_line.unit" class="w-10 text-center">
                                            </td>
                                            <td class="border" :class="{ 'border-red-700': errors[`order_lines.${index}.type`] }">
                                                <select x-model="order_line.type" class="w-28 bg-white text-black" disabled>
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
                                            <td class="border">
                                                <input type="number" :x-ref="`price_${index}`" min="0" class="w-20 text-right" x-model="order_line.priceData" @change="priceDataChanged(order_line)">
                                            </td>
                                            @if (auth()->user()->isAbleTo('order-special-price'))
                                                <td class="border text-right">
                                                    <input type="number" min="0" class="w-20 text-right" x-model="order_line.specialPriceData">
                                                </td>
                                            @endif
                                            <td class="border text-right">
                                                <div class="w-24" x-text="subTotal(order_line)"></div>
                                            </td>
                                            <td class="border">
                                                <template x-if="order_line.image_url">
                                                    <a 
                                                        href="javascript:;"
                                                        x-on:mouseleave="order_line.show_image = false, overflow = true"
                                                        x-on:mouseover="overflow = true, order_line.show_image = true">
                                                        View image
                                                    </a>
                                                    <div class="relative" x-cloak x-show.transition.origin.top="order_line.show_image">
                                                        <div class="absolute w-auto h-auto top-0 z-10 p-1 -mt-5 transform -translate-x-1/2 -translate-y-full">
                                                            <img :src="order_line.image_url">
                                                        </div>
                                                    </div>
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
                </div>
            </div>
        </div>
    </div>

@push('scripts')
    <script>
        function order() {
            return {
                errors: [],
                showTable: false,
                overflow: true,
                loading: false,
                sizes: @json($sizes),
                items: @json($items),
                categories: @json($categories),
                customers: @json($customers),
                availableItems: [],
                customerItems: [],
                selectedCustomer: '',
                form: {
                    customer_id: '',
                    date: '',
                    salesman_id: '',
                    order_lines: [],
                    dp: {
                        has_dp: 0,
                        payment_method: '',
                        amount: '',
                        date: '',
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
                        item: data ? data.item.id : '',
                        unit: data ? data.item.unit : '',
                        item_combination: '',
                        type: data ? data.item.category_id : '',
                        material: data ? data.material_id : '',
                        color: data ? data.color_id : '',
                        printing: data ? data.screen_printing == 1 : false,
                        note: '',
                        special_note: '',
                        image: '',
                        show_image: false,
                        priceData: 0,
                        specialPriceData: 0,
                        price: [],
                    }

                    this.sizes.forEach(size => {
                        if (data) {
                            var currentPrice = data.prices.find(price => price.size_id == size.id)
                            console.log({currentPrice});
                            orderLine.priceData = currentPrice ? currentPrice.price : 0
                            orderLine.specialPriceData = currentPrice ? currentPrice.special_price : 0

                            orderLine.price.push({
                                size_id: size.id,
                                qty: 0,
                                price: currentPrice ? currentPrice.price : 0,
                                special_price: currentPrice ? currentPrice.special_price : 0
                            })
                        } else {
                            orderLine.price.push({
                                size_id: size.id,
                                qty: 0,
                                price: 0,
                                special_price: 0
                            })
                        }
                    });
       
                    this.form.order_lines.push(orderLine)
                },
                removeLine(i) {
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
                            this.form.order_lines[i].note = customerItem.note
                            this.form.order_lines[i].special_note = customerItem.special_note
                            this.form.order_lines[i].image_url = customerItem.image_url
                            this.form.order_lines[i].price.forEach(price => {
                                price.price = customerItem.prices[0].price
                                price.special_price = customerItem.prices[0].special_price
                            })
                        } else {
                            this.form.order_lines[i].priceData = 0
                            this.form.order_lines[i].specialPriceData = 0
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
                    data.price.forEach(price => subTotal += price.qty * price.price)
                    return subTotal
                },
                grandTotal() {
                    var grandTotal = 0
                    this.form.order_lines.forEach(orderLine => {
                        orderLine.price.forEach(price => grandTotal += price.qty * price.price)
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
                customerSelected(customerId) {
                    this.selectedCustomer = this.customers.find(customer => customer.id == customerId)
                },
                generateFormData() {
                    var formData = new FormData()

                    for (const key in this.form) {
                        if (this.form.hasOwnProperty(key)) {
                            const element = this.form[key]
                            if (typeof element == 'object') {
                                if (key == 'dp') {
                                    for (const k in element) {
                                        if (element.hasOwnProperty(k)) {
                                            formData.append(`dp[${k}]`, element[k])
                                        }
                                    }
                                }

                                if (key == 'order_lines') {
                                    for (const k in element) {
                                        if (element.hasOwnProperty(k)) {
                                            const orderLines = element[k];
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
                                            } else {
                                                formData.append(`order_lines[${k}][image]`, orderLines['image'])
                                            }

                                            orderLines['price'].forEach((price, i) => {
                                                formData.append(`order_lines[${k}][price][${i}][size_id]`, price.size_id)
                                                formData.append(`order_lines[${k}][price][${i}][qty]`, price.qty)
                                                formData.append(`order_lines[${k}][price][${i}][price]`, price.price)
                                                formData.append(`order_lines[${k}][price][${i}][special_price]`, price.special_price)
                                            })
                                        }
                                    }
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

                    fetch('{{ route('transactions.v2.store-order') }}', {
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
                            window.location = response.redirect
                        }
                    }).catch((error) => {
                        console.log(error);
                        this.loading = false
                    });
                },
                useVesion2(data) {
                    var result = []
                    data.forEach(item => {
                        var pricesList = []
                        item.prices.forEach(price => pricesList.push(price.price))
                        var unique = pricesList.filter((pricesListItem, i, ar) => ar.indexOf(pricesListItem) === i)
                        result.push(unique.length > 1)
                    })

                    return result.includes(true)
                },
                initOrder($watch) {
                    $watch('form.customer_id', (value) => {
                        this.form.order_lines = []
                        
                        fetch(`{{ route('master-data.customer.customer-items') }}?id=${value}`)
                            .then(response => response.json())
                            .then(data => {
                                this.availableItems = []
                                if (data.length > 0) {
                                    this.customerItems = data
                                    var itemIds = data.map(item => item.item_id)
                                    this.availableItems = this.items.filter(item => itemIds.includes(item.id))
                                }

                                this.addNewLine()
                            })
                        
                        this.showTable = true
                    })
                }
            }
        }
    </script>
@endpush
</x-app-layout>