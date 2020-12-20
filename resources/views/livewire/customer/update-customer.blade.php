<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Update Customer') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-1 py-8 sm:px-8 bg-white border-b border-gray-200">
                <x-form-section submit="saveCustomer">                
                    <x-slot name="form">
                        <div class="col-span-6 sm:col-span-4">
                            <x-jet-label for="name" value="{{ __('Name') }}" />
                            <x-jet-input id="name" type="text" class="mt-1 block w-full" wire:model.defer="name" />
                            <x-jet-input-error for="name" class="mt-2" />
                        </div>    
                        <div class="col-span-6 sm:col-span-4">
                            <x-jet-label for="address" value="{{ __('Address') }}" />
                            <x-jet-input id="address" type="text" class="mt-1 block w-full" wire:model.defer="address" />
                            <x-jet-input-error for="address" class="mt-2" />
                        </div> 
                        <div class="col-span-6 sm:col-span-4">
                            <x-jet-label for="phone" value="{{ __('Phone') }}" />
                            <x-jet-input id="phone" type="text" class="mt-1 block w-full" wire:model.defer="phone" />
                            <x-jet-input-error for="phone" class="mt-2" />
                        </div> 
                        <div class="col-span-6 sm:col-span-4">
                            <x-jet-label for="email" value="{{ __('Email') }}" />
                            <x-jet-input id="email" type="text" class="mt-1 block w-full" wire:model.defer="email" />
                            <x-jet-input-error for="email" class="mt-2" />
                        </div>   
                        <div class="col-span-6 sm:col-span-4">
                            <x-jet-label for="country" value="{{ __('Country') }}" />
                            <x-jet-input id="country" type="text" class="mt-1 block w-full" wire:model.defer="country" />
                            <x-jet-input-error for="country" class="mt-2" />
                        </div>
                        <div class="col-span-6 sm:col-span-4">
                            <x-jet-label for="country" value="{{ __('Invoice color') }}" />
                            {{-- <x-jet-input id="country" type="color" class="mt-1 block w-full" wire:model.defer="invoiceColor" /> --}}
                            <input id="country" type="color" class="mt-1" wire:model.defer="invoiceColor" />
                            <x-jet-input-error for="invoiceColor" class="mt-2" />
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