<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Customers') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-1 py-8 sm:px-8 bg-white border-b border-gray-200">
                <div class="w-full flex">
                    <div class="w-1/2 my-2">
                        <x-jet-input type="text" class="mt-1 block w-2/4" placeholder="{{ __('Search by name, address, phone or email...') }}"
                        wire:model.debounce.500ms="search" />
                        <label><input type="checkbox" wire:model="deleted"> Inactive</label>
                    </div>
                    <div class="w-1/2 my-2">
                        @if (auth()->user()->isAbleTo('customer-create'))
                        <x-link class="ml-2 float-right" href="{{ route('master-data.create-customer') }}">
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
                            <th class="border">Name</th>
                            <th class="border">Address</th>
                            <th class="border">Phone</th>
                            <th class="border">Email</th>
                            <th class="border">Country</th>
                            <th class="border">Invoice color</th>
                            <th class="border">Status</th>
                            <th class="border"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customers as $customer)
                            <tr>
                                <td class="border align-top truncate">
                                    <span>{{ $customer->name }}</span>
                                </td>
                                <td class="border align-top truncate">{{ $customer->address }}</td>
                                <td class="border align-top truncate">{{ $customer->phone }}</td>
                                <td class="border align-top truncate">{{ $customer->email }}</td>
                                <td class="border align-top truncate">{{ $customer->country }}</td>
                                <td class="border align-top truncate">
                                    <div class="p-2" style="background-color: {{ $customer->invoice_color }};"></div>
                                </td>
                                <td class="border align-top truncate">
                                    @if($confirming == $customer->id)
                                        <x-button action="toggleActive({{ $customer->id }}, {{ $customer->deleted_at ? true : false}})" type="danger" size="small">
                                            Yes?
                                        </x-button>
                                        <x-button action="resetConfirm" type="success" size="small">
                                            No
                                        </x-button>
                                    @else
                                        @if (auth()->user()->isAbleTo('customer-delete'))
                                            @if ($customer->deleted_at)
                                                <x-button action="confirmDelete({{ $customer->id }})" size="small">
                                                    Inactive
                                                </x-button>
                                            @else
                                                <x-button action="confirmDelete({{ $customer->id }})" size="small">
                                                    Acitive
                                                </x-button>    
                                            @endif
                                        @else
                                            {{ $customer->deleted_at ? 'Inactive' : 'Active' }}
                                        @endif
                                    @endif
                                </td>
                                <td class="border align-top">
                                    @if (auth()->user()->isAbleTo('customer-update'))
                                    <x-link href="{{ $customer->deleted_at ? '#' : route('master-data.update-customer', ['id' => $customer->id]) }}" size="small">{{ __('Edit') }}</x-link>
                                    @endif
                                    @if (auth()->user()->isAbleTo('customer-manage-item'))
                                    <x-link href="{{ $customer->deleted_at ? '#' : route('master-data.manage-products-customer-v3', ['id' => $customer->id]) }}" size="small">{{ __('Product') }}</x-link>
                                    @endif
                                </td>
                            </tr>
                        @empty
                        <tr>
                            <td class="border px-4 py-2" colspan="5">No data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="py-4">
                {{ $customers->links() }}
                </div>
            </div>        
        </div>
    </div>
</div>