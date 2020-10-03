<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Colors') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-1 py-8 sm:px-8 bg-white border-b border-gray-200">
                <div class="w-full flex">
                    <div class="w-1/2 my-2">
                        <x-jet-input type="text" class="mt-1 block w-2/4" placeholder="{{ __('Search by name...') }}"
                        wire:model.debounce.500ms="search" />
                    </div>
                    <div class="w-1/2 my-2">
                        <x-link class="ml-2 float-right" href="{{ route('master-data.create-color') }}">
                            {{ __('Create new') }}
                        </x-link>
                    </div>
                </div>

                @if (session()->has('message'))
                    <x-alert>
                        {{ session('message') }}
                    </x-alert>
                @endif
                
                <table class="table-auto w-full">
                    <thead>
                        <tr>
                            <th class="border px-4 py-2">Name</th>
                            <th class="border px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($colors as $color)
                            <tr>
                                <td class="border px-4 py-2">{{ $color->name }}</td>
                                <td class="border px-4 py-2">
                                    <x-link href="{{ route('master-data.update-color', ['id' => $color->id]) }}">{{ __('Edit') }}</x-link>
                                    @if($confirming == $color->id)
                                        <x-button action="delete({{ $color->id }})" type="danger">
                                            Yes?
                                        </x-button>
                                        <x-button action="resetConfirm" type="success">
                                            No
                                        </x-button>
                                    @else
                                        <x-button action="confirmDelete({{ $color->id }})">
                                            Delete
                                        </x-button>
                                        
                                    @endif
                                </td>
                            </tr>
                        @empty
                        <tr>
                            <td class="border px-4 py-2" colspan="2">No data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="py-4">
                {{ $colors->links() }}
                </div>
            </div>        
        </div>
    </div>
</div>