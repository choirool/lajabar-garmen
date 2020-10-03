@props(['type' => '', 'action'])

@php
    $typeClass = 'bg-white bg-gray-200 hover:bg-gray-400 text-black';

    if ($type == 'danger') {
        $typeClass = 'bg-red-200 hover:bg-red-700 text-black';
    }

    if ($type == 'success') {
        $typeClass = 'bg-green-200 hover:bg-green-700 text-black';
    }

    if ($type == 'warning') {
        $typeClass = 'bg-yellow-200 hover:bg-yellow-700 text-black';
    }
@endphp

<button class="mr-5 {{ $typeClass }} px-4 py-2 text-xs ite uppercase tracking-widest rounded transition ease-in-out duration-150" wire:click="{{ $action }}">
    {{ $slot }}
</button>