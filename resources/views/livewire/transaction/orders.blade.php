<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Productions') }}
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
                        <div class="w-full flex overflow-x-scroll overflow-y-hidden">
                            <table class="table-auto text-xs">
                                <thead>
                                    <tr>
                                        <th class="border" rowspan="3">Item name</th>
                                        <th class="border" rowspan="3">
                                            <div class="w-10">Unit</div>
                                        </th>
                                        <th class="border" rowspan="3">Type</th>
                                        <th class="border" rowspan="3">Material</th>
                                        <th class="border" rowspan="3">Color</th>
                                        <th class="border" rowspan="3">Sablon</th>
                                        <th class="border" colspan="{{ $sizes->count() * 2 }}">Price</th>
                                        <th class="border" rowspan="3">Sub Total</th>
                                        <th class="border" rowspan="3">Note</th>
                                        <th class="border" rowspan="3"></th>
                                    </tr>
                                    <tr>
                                        @foreach ($sizes as $size)
                                            <th class="border" colspan="2">{{ $size->name }}</th>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        @foreach ($sizes as $size)
                                            <th class="border">qty</th>
                                            <th class="border">price</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $subTotal = [];
                                    @endphp
                                    @foreach ($form['order_lines'] as $i => $orderLines)
                                        <tr>
                                            <td class="border @if($errors->has('customerItems.'.$i.'.item')) border-red-700 @endif">
                                                <select 
                                                    wire:model="form.order_lines.{{ $i }}.item"
                                                    wire:change="itemSelected({{ $i }})" 
                                                    class="w-28 bg-white">
                                                    <option value="0">Select item</option>
                                                    @foreach ($items as $item)
                                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="border text-center">{{ $orderLines['unit'] }}</td>
                                            <td class="border @if($errors->has('customerItems.'.$i.'.type')) border-red-700 @endif">
                                                <select wire:model="form.order_lines.{{ $i }}.type" class="w-28 bg-white">
                                                    <option value="0">Select category</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="border @if($errors->has('customerItems.'.$i.'.material')) border-red-700 @endif">
                                                <select wire:model="form.order_lines.{{ $i }}.material" class="w-28 bg-white">
                                                    <option value="0">Select material</option>
                                                    @foreach ($materials as $material)
                                                        <option value="{{ $material->id }}">{{ $material->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="border @if($errors->has('customerItems.'.$i.'.color')) border-red-700 @endif">
                                                <select wire:model="form.order_lines.{{ $i }}.color" class="bg-white">
                                                    <option value="0">Select color</option>
                                                    @foreach ($colors as $color)
                                                        <option value="{{ $color->id }}">{{ $color->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="border text-center @if($errors->has('customerItems.'.$i.'.printing')) border-red-700 @endif">
                                                <input type="checkbox" wire:model="form.order_lines.{{ $i }}.printing">
                                            </td>
                                            @foreach ($sizes as $y => $size)
                                                <td class="border @if($errors->has('customerItems.'.$i.'.price.'.$y.'.qty')) border-red-700 @endif">
                                                    <input type="number" wire:model.debounce.500ms="form.order_lines.{{ $i }}.price.{{ $y }}.qty" class="w-16">
                                                </td>
                                                <td class="border @if($errors->has('customerItems.'.$i.'.price.'.$y.'.price')) border-red-700 @endif">
                                                    <input type="number" wire:model.debounce.500ms="form.order_lines.{{ $i }}.price.{{ $y }}.price" class="w-20 text-right">
                                                </td>
                                            @endforeach
                                            <td class="border text-right">
                                                <div class="w-24">
                                                    @php
                                                        $subTotal[$i] = 0;
                                                    @endphp
                                                    @foreach ($sizes as $y => $size)
                                                        @php
                                                            $subTotal[$i] += $orderLines['price'][$y]['price'] * $orderLines['price'][$y]['qty'];
                                                        @endphp
                                                    @endforeach
                                                    
                                                    {{ $subTotal[$i] }}
                                                </div>
                                            </td>
                                            <td class="border @if($errors->has('customerItems.'.$i.'.price.'.$y.'.note')) border-red-700 @endif">
                                                <input type="text" wire:model="form.order_lines.{{ $i }}.note">
                                            </td>
                                            <td class="border">
                                                @if (count($form['order_lines']) > 1)
                                                    <span class="cursor-pointer" wire:click="deleteItem({{ $i }})">X</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td class="pt-2">
                                            <a href="javascript:;" 
                                                wire:click="addItem" 
                                                class="bg-blue-400 p-1">
                                                Add new item
                                            </a>
                                        </td>
                                        <td colspan="{{ ($sizes->count() * 2 + 6) }}" class="text-right">
                                            @php
                                                $total = 0;
                                            @endphp
                                            @foreach ($subTotal as $st)
                                                @php
                                                    $total += $st;
                                                @endphp
                                            @endforeach
                                            {{ $total }}
                                        </td>
                                        <td class=""></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    
                                </tfoot>
                            </table>
                        </div>
                    @endif
                </div>        
            </div>
        </div>
    </form>
</div>