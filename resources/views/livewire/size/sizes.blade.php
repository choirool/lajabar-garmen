<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Sizes') }}
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
                        @if (auth()->user()->isAbleTo('size-list'))
                        <x-link class="ml-2 float-right" href="{{ route('master-data.create-size') }}">
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
                
                <table class="table-auto w-full">
                    <thead>
                        <tr>
                            <th class="border px-4 py-2">Name</th>
                            <th class="border px-4 py-2">Level</th>
                            <th class="border px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sizes as $size)
                            <tr>
                                <td class="border px-4 py-2">{{ $size->name }}</td>
                                <td class="border px-4 py-2">{{ $size->sort }}</td>
                                <td class="border px-4 py-2">
                                    @if (auth()->user()->isAbleTo('size-update'))
                                    <x-link href="{{ route('master-data.update-size', ['id' => $size->id]) }}">{{ __('Edit') }}</x-link>
                                    @endif
                                    @if($confirming == $size->id)
                                        <x-button action="delete({{ $size->id }})" type="danger">
                                            Yes?
                                        </x-button>
                                        <x-button action="resetConfirm" type="success">
                                            No
                                        </x-button>
                                    @else
                                        @if (auth()->user()->isAbleTo('size-delete'))
                                        <x-button action="confirmDelete({{ $size->id }})">
                                            Delete
                                        </x-button>
                                        @endif
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
                {{ $sizes->links() }}
                </div>
            </div>        
        </div>
    </div>
</div>