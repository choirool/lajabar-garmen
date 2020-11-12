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
                    <div class="w-4/5 my-2" x-data="{show: false}">
                        <a href="javascript:;" @click="show = !show">Show/hide Filter</a>
                    
                        <div x-show="show">
                            <div class="w-auto">
                                <div>Query</div>
                                <input 
                                    type="text" 
                                    wire:model.debounce.500ms="search"
                                    class="border border-gray-500 border-solid">
                            </div>
                            <di class="flex">
                                <div>
                                    <div>Start date</div>
                                    <input 
                                        type="date" 
                                        wire:model.defer="startDate"
                                        class="border border-gray-500 border-solid">
                                </div>
                                <div>
                                    <div>End date</div>
                                    <input 
                                        type="date" 
                                        wire:model.defer="endDate"
                                        class="border border-gray-500 border-solid">
                                </div>
                            </di>
                            <div class="pt-1">
                                <label>
                                    <input type="checkbox" wire:model="unpaid">Not paid off yet
                                </label>
                            </div>
                            <div>
                                <button 
                                    wire:click="searchData" 
                                    class="mt-7 p-3 bg-green-200 hover:bg-gray-400 text-black text-xs ite uppercase tracking-widest rounded transition ease-in-out duration-150">
                                    Search
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="w-1/5 my-2">
                        @if (auth()->user()->isAbleTo('order-create'))
                        <x-link class="ml-2 float-right" href="{{ route('transactions.v3.create-order') }}">
                            {{ __('Create new') }}
                        </x-link>
                        @endif
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
                            <th class="border">Amount</th>
                            <th class="border">Paid</th>
                            <th class="border">Amount due</th>
                            <th class="border">Customer name</th>
                            <th class="border">Phone</th>
                            <th class="border">Email</th>
                            <th class="border">Country</th>
                            <th class="border"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr 
                                class="
                                {{ ($order->order_amount - $order->paid_amount) == 0 ? ' bg-teal-300' : '' }}
                                {{ ($order->order_amount - $order->paid_amount) > 0 ? ' bg-red-300' : '' }}
                                "
                            >
                                <td class="border align-top truncate">
                                    <span>{{ $order->invoice_date }}</span>
                                </td>
                                <td class="border align-top truncate">{{ $order->invoice_code }}</td>
                                <td class="border align-top truncate text-right">{{ $order->order_amount }}</td>
                                <td class="border align-top truncate text-right">{{ $order->paid_amount }}</td>
                                <td class="border align-top truncate text-right">{{ $order->order_amount - $order->paid_amount }}</td>
                                <td class="border align-top truncate">{{ $order->customer->name }}</td>
                                <td class="border align-top truncate">{{ $order->customer->phone }}</td>
                                <td class="border align-top truncate">{{ $order->customer->email }}</td>
                                <td class="border align-top truncate">{{ $order->customer->country }}</td>
                                <td class="border">
                                    @if (auth()->user()->isAbleTo('order-update'))
                                    <x-link href="{{ route('transactions.v3.edit-order', ['id' => $order->id]) }}" size="small">{{ __('Edit') }}</x-link>
                                    @endif
                                    @if (auth()->user()->isAbleTo('order-check'))
                                    <x-link href="{{ route('transactions.production.index', ['orderId' => $order->id]) }}" size="small">{{ __('Check') }}</x-link>
                                    @endif
                                    @if (auth()->user()->isAbleTo('order-create-payment'))
                                    <x-link href="{{ route('transactions.payment.create', ['orderId' => $order->id]) }}" size="small">{{ __('Payment') }}</x-link>
                                    @endif
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