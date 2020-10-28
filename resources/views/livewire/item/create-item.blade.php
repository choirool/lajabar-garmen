<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Create a New Item') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-1 py-8 sm:px-8 bg-white border-b border-gray-200">
                <x-form-section submit="saveItem">                
                    <x-slot name="form">
                        <div class="col-span-6 sm:col-span-4">
                            <x-jet-label for="name" value="{{ __('Name') }}" />
                            <x-jet-input id="name" type="text" class="mt-1 block w-full" wire:model.defer="name" />
                            <x-jet-input-error for="name" class="mt-2" />
                        </div>
                        <div class="col-span-6 sm:col-span-4">
                            <x-jet-label for="unit" value="{{ __('Unit') }}" />
                            <x-jet-input id="unit" type="text" class="mt-1 block w-full" wire:model.defer="unit" />
                            <x-jet-input-error for="unit" class="mt-2" />
                        </div>
                        <div class="col-span-6 sm:col-span-4">
                            <x-jet-label for="category" value="{{ __('Type') }}" />
                            <select id="category" wire:model="category" class="mt-1 block w-full form-input rounded-md shadow-sm">
                                <option>Select type</option>
                                @foreach ($categories as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                            <x-jet-input-error for="category" class="mt-2" />
                        </div>      
                        <div class="col-span-6 sm:col-span-4">
                            <x-jet-label for="category" value="{{ __('Matrial') }}" />
                            <select id="category" wire:model="material" class="mt-1 block w-full form-input rounded-md shadow-sm">
                                <option>Select matrial</option>
                                @foreach ($materials as $material)
                                    <option value="{{ $material->id }}">{{ $material->name }}</option>
                                @endforeach
                            </select>
                            <x-jet-input-error for="category" class="mt-2" />
                        </div> 
                    </x-slot>
                
                    <x-slot name="actions">
                        <x-jet-action-message class="mr-3" on="saved">
                            {{ __('Saved.') }}
                        </x-jet-action-message>
                
                        <x-jet-button>
                            {{ __('Save') }}
                        </x-jet-button>
                    </x-slot>
                </x-form-section>
            </div>        
        </div>
    </div>
</div>