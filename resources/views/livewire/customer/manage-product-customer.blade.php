<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Customer products') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-1 py-8 sm:px-8 bg-white border-b border-gray-200">
                <div class="w-full flex">
                    <div class="w-1/2 my-2">
                        Customer name: {{ $customer->name }}
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
                        <table class="table-auto w-full text-base">
                            <thead>
                                <tr>
                                    <th class="border" width="20%">Item name</th>
                                    <th class="border" width="3%">Unit</th>
                                    <th class="border" width="10%">Price</th>
                                    <th class="border" width="15%">Type</th>
                                    <th class="border" width="15%">Material</th>
                                    <th class="border" width="10%">Color</th>
                                    <th class="border" width="3%">Sablon</th>
                                    <th class="border" width="25%">Note</th>
                                    <th class="border" width="2%"></th>
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
                                                <option>Select item</option>
                                                @foreach ($items as $item)
                                                    <option value="{{ $item->id }}">
                                                        {{ $item->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            {{-- <x-jet-input-error for="customerItems.{{ $index }}.item_id" class="mt-2 text-xs" /> --}}
                                        </td>
                                        <td class="border align-top">
                                            <input id="customerItems.{{ $index }}.unit" type="text" wire:model.defer="customerItems.{{ $index }}.unit" class="w-full bg-white">
                                        </td>
                                        <td class="border align-top @if($errors->has('customerItems.'.$index.'.price')) border-red-700 @endif">
                                            <input id="customerItems.{{ $index }}.price" type="text" wire:model.debounce.500ms="customerItems.{{ $index }}.price" class="w-full bg-white">
                                        </td>
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
                                    <td>
                                        <a href="javascript:;" wire:click="addCustomerItem">Add new aitem</a>
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                        <div class="mt-2">
                            <x-button>
                                {{ __('Save') }}
                            </x-button>
                        </div>
                        {{-- <button type="submit">Save</button> --}}
                    </form>
                </div>
            </div>        
        </div>
    </div>
</div>