<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Productionss') }}
    </h2>
</x-slot>

<div class="py-12">
    <form wire:submit.prevent="saveData">
        <div class="w-full mx-auto sm:px-6 lg:px-1">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-1 py-1 sm:px-1 bg-white border-b border-gray-200">
                    <div class="w-full flex bg-blue-200 px-3 py-3 mb-5">
                        <div class="w-1/2 my-2">
                            <table>
                                <tr>
                                    <td>Customer name</td>
                                    <td>:</td>
                                    <td>
                                        <select id="" wire:model="form.customer_id" wire:change="customerSelected">
                                            <option value="">Select customer</option>
                                            @foreach ($customers as $customer)
                                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Date</td>
                                    <td>:</td>
                                    <td>
                                        <input type="date" wire:model="form.date">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Sales</td>
                                    <td>:</td>
                                    <td>
                                        <select id="" wire:model="form.salesman_id" wire:change="customerSelected">
                                            <option value="">Select salesman</option>
                                            @foreach ($salesmen as $salesman)
                                                <option value="{{ $salesman->id }}">{{ $salesman->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
    
                    @if ($showTable)
                        <div class="w-full flex overflow-x-auto">
                            <table class="table-auto text-xs">
                                <thead>
                                    <tr>
                                        <th class="border" rowspan="3">
                                            <div class="w-28">Item name</div>
                                        </th>
                                        <th class="border" rowspan="3">
                                            <div class="w-10">Unit</div>
                                        </th>
                                        <th class="border" rowspan="3">
                                            <div>Type</div>
                                        </th>
                                        <th class="border" rowspan="3">
                                            <div class="w-32">Material</div>
                                        </th>
                                        <th class="border" rowspan="3">
                                            <div class="w-32">Color</div>
                                        </th>
                                        <th class="border" rowspan="3">
                                            <div class="w-32">Sablon</div>
                                        </th>
                                        <th class="border" width="3%" colspan="{{ $sizes->count() * 2 }}">Price</th>
                                        <th class="border" rowspan="3">
                                            <div class="w-32">Sub Total</div>
                                        </th>
                                        <th class="border" rowspan="3">
                                            <div class="w-32">Note</div>
                                        </th>
                                    </tr>
                                    <tr>
                                        @foreach ($sizes as $size)
                                            <th class="border" colspan="2">{{ $size->name }}</th>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        @foreach ($sizes as $size)
                                            <th class="border">price</th>
                                            <th class="border">qty</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($form['order_lines'] as $i => $orderLines)
                                        <tr>
                                            <td>
                                                <select id="" wire:model="form.order_lines.{{ $i }}.item">
                                                    <option value="0">Select item</option>
                                                    @foreach ($items as $item)
                                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>{{ $orderLines['unit'] }}</td>
                                            <td>
                                                <select id="" wire:model="form.order_lines.{{ $i }}.type">
                                                    <option value="0">Select category</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select id="" wire:model="form.order_lines.{{ $i }}.material">
                                                    <option value="0">Select material</option>
                                                    @foreach ($materials as $material)
                                                        <option value="{{ $material->id }}">{{ $material->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select id="" wire:model="form.order_lines.{{ $i }}.color">
                                                    <option value="0">Select color</option>
                                                    @foreach ($colors as $color)
                                                        <option value="{{ $color->id }}">{{ $color->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="checkbox" wire:model="form.order_lines.{{ $i }}.printing">
                                            </td>
                                            @foreach ($sizes as $y => $size)
                                                <td class="border">
                                                    <input type="number" wire:model="form.order_lines.{{ $i }}.price.{{ $y }}.price">
                                                </td>
                                                <td class="border">
                                                    <input type="number" wire:model="form.order_lines.{{ $i }}.price.{{ $y }}.qty">
                                                </td>
                                            @endforeach
                                            <td class="border">
                                                <input type="number">
                                            </td>
                                            <td class="border">
                                                <input type="number" wire:model="form.order_lines.{{ $i }}.note">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>        
            </div>
        </div>
    </form>
</div>