<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Customer products') }}
    </h2>
</x-slot>

<div class="py-12">
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

                @if (session()->has('message'))
                    <x-alert>
                        {{ session('message') }}
                    </x-alert>
                @endif

                @if ($errors->any())
                    {{-- <x-error-array /> --}}
                    <x-alert type="danger">
                        Please fille form with correct data.
                    </x-alert>
                @endif

                <x-jet-action-message class="mr-3" on="dataSaved">
                    <x-alert type="success">
                        Data saved
                    </x-alert>
                </x-jet-action-message>
                
                <div class="w-full flex">
                    <form wire:submit.prevent="saveData">
                        <table class="table-auto w-full text-xs">
                            <thead>
                                <tr>
                                    <th class="border" width="20%" rowspan="2">Item name</th>
                                    <th class="border" width="3%" rowspan="2">Unit</th>
                                    <th class="border" width="10%" colspan="{{ $sizes->count() }}">Price</th>
                                    <th class="border" width="10%" rowspan="2">Type</th>
                                    <th class="border" width="10%" rowspan="2">Material</th>
                                    <th class="border" width="7%" rowspan="2">Color</th>
                                    <th class="border" width="3%" rowspan="2">Sablon</th>
                                    <th class="border" width="25%" rowspan="2">Note</th>
                                    <th class="border" width="2%" rowspan="2"></th>
                                </tr>
                                <tr>
                                    @foreach ($sizes as $size)
                                        <th class="border" width="7%">{{ $size->name }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customerItems as $index => $customerItem)
                                    <tr>
                                        <td class="border align-top @if($errors->has('customerItems.'.$index.'.item_id')) border-red-700 @endif">
                                            <select
                                                class="w-full bg-white"
                                                id="customerItems.{{ $index }}.item_id"
                                                wire:change="itemSelected({{ $index }})" 
                                                wire:model="customerItems.{{ $index }}.item_id">
                                                <option value="null">Select item</option>
                                                @foreach ($items as $item)
                                                    <option value="{{ $item->id }}">
                                                        {{ $item->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            {{-- <x-jet-input-error for="customerItems.{{ $index }}.item_id" class="mt-2 text-xs" /> --}}
                                        </td>
                                        <td class="border align-top">
                                            <input id="customerItems.{{ $index }}.unit" type="text" wire:model.defer="customerItems.{{ $index }}.unit" class="w-full bg-white text-center">
                                        </td>
                                        @foreach ($sizes as $i => $size)
                                            <td class="border align-top @if($errors->has('customerItems.'.$index.'.price.'.$i.'.value')) border-red-700 @endif">
                                                <input 
                                                    id="customerItems.{{ $index }}.price.{{ $i }}.value" 
                                                    type="number" 
                                                    class="w-full bg-white text-right"
                                                    min="0"
                                                    wire:model.debounce.500ms="customerItems.{{ $index }}.price.{{ $i }}.value">
                                            </td>
                                        @endforeach
                                        {{-- <td class="border align-top @if($errors->has('customerItems.'.$index.'.price')) border-red-700 @endif">
                                            <input id="customerItems.{{ $index }}.price" type="text" wire:model.debounce.500ms="customerItems.{{ $index }}.price" class="w-full bg-white">
                                        </td> --}}
                                        <td class="border align-top @if($errors->has('customerItems.'.$index.'.type')) border-red-700 @endif">
                                            <select
                                                class="w-full bg-white"
                                                id="customerItems.{{ $index }}.type"
                                                wire:model="customerItems.{{ $index }}.type">
                                                <option>Select category</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}">
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="border align-top @if($errors->has('customerItems.'.$index.'.material')) border-red-700 @endif">
                                            <select
                                                class="w-full bg-white"
                                                id="customerItems.{{ $index }}.material" 
                                                wire:model="customerItems.{{ $index }}.material">
                                                <option>Select material</option>
                                                @foreach ($materials as $material)
                                                    <option value="{{ $material->id }}">
                                                        {{ $material->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="border align-top @if($errors->has('customerItems.'.$index.'.color')) border-red-700 @endif">
                                            <select
                                                class="w-full bg-white"
                                                id="customerItems.{{ $index }}.color"
                                                wire:model="customerItems.{{ $index }}.color">
                                                <option>Select color</option>
                                                @foreach ($colors as $color)
                                                    <option value="{{ $color->id }}">
                                                        {{ $color->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="border align-top @if($errors->has('customerItems.'.$index.'.sablon')) border-red-700 @endif">
                                            <input id="customerItems.{{ $index }}.sablon" type="checkbox" wire:model.debounce.500ms="customerItems.{{ $index }}.sablon" class="w-full bg-white">
                                        </td>
                                        <td class="border align-top @if($errors->has('customerItems.'.$index.'.note')) border-red-700 @endif">
                                            <input id="customerItems.{{ $index }}.note" type="text" wire:model.debounce.500ms="customerItems.{{ $index }}.note" class="w-full">
                                        </td>
                                        <td class="border align-top">
                                            @if (count($customerItems) > 1)
                                                <a href="javascript:;" wire:click="removeCustomerItem({{ $index }})">X</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="pt-2">
                                        <a href="javascript:;" 
                                            wire:click="addCustomerItem" 
                                            class="bg-blue-400 p-1">
                                            Add new item
                                        </a>
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                        <div class="mt-2 flex">
                            <x-button>
                                {{ __('Save') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</div>