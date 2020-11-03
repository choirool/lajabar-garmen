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
                                    <td>Customer name </td>
                                    <td>:</td>
                                    <td>{{ $customer->name }}</td>
                                </tr>
                                <tr>
                                    <td>Phone</td>
                                    <td>:</td>
                                    <td>{{ $customer->phone }}</td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td>:</td>
                                    <td>{{ $customer->email }}</td>
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
                    <div class="w-full flex overflow-x-scroll overflow-y-hidden">
                        <table class="table-auto text-xs" x-cloak>
                            <thead>
                                <tr>
                                    <th class="border" width="20%">Item name</th>
                                    <th class="border" width="3%">Unit</th>
                                    <th class="border" width="10%">Type</th>
                                    <th class="border" width="10%">Material</th>
                                    <th class="border" width="7%">Color</th>
                                    <th class="border" width="10%">Price</th>
                                    <th class="border" width="3%">Sablon</th>
                                    <th class="border" width="25%">Note</th>
                                    <th class="border" width="2%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, i) in form.items">
                                    <tr>
                                        <td class="border align-top"
                                            :class="{ 'border-red-700': errors[`items.${i}.item_id`] || errors[`items.${i}.item_combination`] }">
                                            <select class="w-full bg-white"
                                                x-model="item.item_id"
                                                @change="itemSelected(i, item.item_id), createCombination(i, item)">
                                                <option value="null">Select item</option>
                                                @foreach ($items as $item)
                                                    <option value="{{ $item->id }}">
                                                        {{ $item->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="border align-top">
                                            <input type="text" x-model="item.unit" class="w-full bg-white text-center">
                                        </td>
                                        <td class="border align-top" :class="{ 'border-red-700': errors[`items.${i}.type`] }">
                                            <select class="w-full bg-white" x-model="item.type">
                                                <option>Select category</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}">
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="border align-top" 
                                            :class="{ 'border-red-700': errors[`items.${i}.material_id`]  || errors[`items.${i}.item_combination`] }">
                                            <select 
                                                class="w-full bg-white" 
                                                x-model="item.material_id"
                                                @change="createCombination(i, item)"
                                                disabled>
                                                <option value="">Select material</option>
                                                @foreach ($materials as $material)
                                                    <option value="{{ $material->id }}">
                                                        {{ $material->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="border align-top" 
                                            :class="{ 'border-red-700': errors[`items.${i}.color_id`] || errors[`items.${i}.item_combination`] }">
                                            <select 
                                                class="w-full bg-white" 
                                                x-model="item.color_id"
                                                @change="createCombination(i, item)">
                                                <option value="">Select color</option>
                                                @foreach ($colors as $color)
                                                    <option value="{{ $color->id }}">
                                                        {{ $color->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="border align-top" :class="{ 'border-red-700': errors[`items.${i}.price`] }">
                                            <input type="number" 
                                                class="w-full bg-white" 
                                                x-model="item.price"
                                                :disabled="item.color_id == '' && item.material_id == ''">
                                        </td>
                                        <td class="border align-top" :class="{ 'border-red-700': errors[`items.${i}.screen_printing`] }">
                                            <input type="checkbox" class="w-full bg-white" x-model="item.screen_printing">
                                        </td>
                                        <td class="border align-top" :class="{ 'border-red-700': errors[`items.${i}.note`] }">
                                            <input type="text" class="w-full" x-model="item.note">
                                        </td>
                                        <td class="border align-top">
                                            <a href="javascript:;" @click="removeItem(i)">X</a>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="pt-2">
                                        <a href="javascript:;" 
                                            @click="addItem"
                                            class="bg-blue-400 p-1">
                                            Add new item
                                        </a>
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
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
                timeout: null,
                message: '',
                items: @json($items),
                customerItems: @json($customer->products),
                loading: false,
                form: {
                    customer_id: {{ request()->route('id') }},
                    items: [{
                        item_id: '',
                        item_combination: '',
                        unit: '',
                        type: '',
                        price: '',
                        material_id: '',
                        color_id: '',
                        image: '',
                        note: '',
                        screen_printing: false
                    }]
                },
                addItem() {
                    this.form.items.push({
                        item_id: '',
                        item_combination: '',
                        unit: '',
                        type: '',
                        price: '',
                        material_id: '',
                        color_id: '',
                        image: '',
                        note: '',
                        screen_printing: false
                    })
                },
                removeItem(i) {
                    this.form.items.splice(i, 1)
                },
                itemSelected(i, data) {
                    var selectedItem = this.items.find(item => item.id == data)
                    this.form.items[i].unit = selectedItem.unit
                    this.form.items[i].type = selectedItem.category_id
                    this.form.items[i].material_id = selectedItem.material_id
                },
                createCombination(i, item) {
                    form = this.form.items[i]
                    this.form.items[i].item_combination = `${form.item_id}_${form.material_id}_${form.color_id}`
                },
                saveData() {
                    let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    this.errors = []
                    this.loading = true

                    fetch('{{ route('master-data.manage-products-customer-store-data-v3') }}', {
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
                        if (response.errors) {
                            this.errors = response.errors
                        }

                        if (response.status) {
                            clearTimeout(this.timeout)
                            this.message = response.message
                            this.timeout = setTimeout(() => { this.message = '' }, 3000)
                        }
                        this.loading = false
                    }).catch((error) => {
                        console.log(error);
                        this.loading = false
                    });
                },
                initOrder($watch) {
                    if (this.customerItems.length) {
                        this.form.items = []
                        this.customerItems.forEach(customerItem => {
                            this.form.items.push({
                                item_id: customerItem.item_id,
                                item_combination: `${customerItem.item_id}_${customerItem.item.material_id}_${customerItem.color_id}`,
                                unit: customerItem.item.unit,
                                type: customerItem.item.category_id,
                                price: customerItem.prices[0].price,
                                material_id: customerItem.item.material_id,
                                color_id: customerItem.color_id,
                                image: customerItem.images,
                                note: customerItem.note,
                                screen_printing: customerItem.screen_printing
                            })
                        })
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>