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
                    </div>
                    <div class="w-1/2 my-2">
                        <x-link class="ml-2 float-right" href="{{ route('master-data.create-customer') }}">
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
                            <th class="border">Name</th>
                            <th class="border">Address</th>
                            <th class="border">Phone</th>
                            <th class="border">Email</th>
                            <th class="border">Country</th>
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
                                <td class="border align-top">
                                    <x-link href="{{ route('master-data.update-customer', ['id' => $customer->id]) }}" size="small">{{ __('Edit') }}</x-link>
                                    @if($confirming == $customer->id)
                                        <x-button action="delete({{ $customer->id }})" type="danger" size="small">
                                            Yes?
                                        </x-button>
                                        <x-button action="resetConfirm" type="success" size="small">
                                            No
                                        </x-button>
                                    @else
                                        <x-button action="confirmDelete({{ $customer->id }})" size="small">
                                            Delete
                                        </x-button>
                                    @endif
                                    <x-link href="{{ route('master-data.manage-products-customer', ['id' => $customer->id]) }}" size="small">{{ __('Product') }}</x-link>
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