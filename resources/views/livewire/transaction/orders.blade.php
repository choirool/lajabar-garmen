<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Productionss') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="w-full mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-1 py-8 sm:px-8 bg-white border-b border-gray-200">
                <div class="w-full flex bg-blue-200 px-3 py-3 mb-5">
                    <div class="w-1/2 my-2">
                        <table>
                            <tr>
                                <td>Customer name</td>
                                <td>:</td>
                                <td>
                                    <select id="" wire:model="customer" wire:change="customerSelected">
                                        <option value="">Select customer</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>        
        </div>
    </div>
</div>