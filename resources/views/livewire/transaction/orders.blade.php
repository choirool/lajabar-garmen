<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Orders') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-1 py-8 sm:px-8 bg-white border-b border-gray-200">
                <div class="w-full flex">
                    <div class="w-4/5 my-2 flex">
                        <div class="w-auto">
                            <div>Query</div>
                            <x-jet-input type="text" 
                            class="mt-1 block" 
                            placeholder="{{ __('Search by code or customer name...') }}"
                            wire:model.debounce.500ms="search" />
                        </div>
                        <div>
                            <div>Start date</div>
                            <input type="date" wire:model.defer="startDate" class="mt-1 block form-input rounded-md shadow-sm">
                        </div>
                        <div>
                            <div>End date</div>
                            <input type="date" wire:model.defer="endDate" class="mt-1 block form-input rounded-md shadow-sm">
                        </div>
                        <div>
                            <button 
                                wire:click="searchData" 
                                class="mt-7 p-3 bg-green-200 hover:bg-gray-400 text-black text-xs ite uppercase tracking-widest rounded transition ease-in-out duration-150">
                                Search
                            </button>
                        </div>
                    </div>
                    <div class="w-1/5 my-2">
                        <x-link class="ml-2 float-right" href="{{ route('transactions.v3.create-order') }}">
                            {{ __('Create new') }}
                        </x-link>
                    </div>
                </div>

                @if (session()->has('message'))
                    <x-alert>
                        {{ session('message') }}
                    </x-alert>
                @endif
                
                <table class="table-auto w-full text-xs">
                    <thead>
                        <tr>
                            <th class="border">Date</th>
                            <th class="border">No</th>
                            <th class="border">Customer name</th>
                            <th class="border">Phone</th>
                            <th class="border">Email</th>
                            <th class="border">Country</th>
                            <th class="border"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td class="border align-top truncate">
                                    <span>{{ $order->invoice_date }}</span>
                                </td>
                                <td class="border align-top truncate">{{ $order->invoice_code }}</td>
                                <td class="border align-top truncate">{{ $order->customer->name }}</td>
                                <td class="border align-top truncate">{{ $order->customer->phone }}</td>
                                <td class="border align-top truncate">{{ $order->customer->email }}</td>
                                <td class="border align-top truncate">{{ $order->customer->country }}</td>
                                <td class="border">
                                    <x-link href="{{ route('transactions.v3.edit-order', ['id' => $order->id]) }}" size="small">{{ __('Edit') }}</x-link>
                                    <x-link href="{{ route('master-data.update-customer', ['id' => $order->id]) }}" size="small">{{ __('Check') }}</x-link>
                                </td>
                            </tr>
                        @empty
                        <tr>
                            <td class="border px-4 py-2" colspan="7">No data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="py-4">
                {{ $orders->links() }}
                </div>
            </div>        
        </div>
    </div>
</div>