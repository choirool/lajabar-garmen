@props(['type' => '', 'action', 'size' => ''])

@php
    $sizeClass = 'px-4 py-2 font-semibold';
    if($size == 'small') {
        $sizeClass = 'px-0.5 py-0.5';
    }
@endphp

<a {{ $attributes->merge(['href' => '#', 'class' => "inline-flex items-center {$sizeClass} bg-gray-800 border border-transparent rounded-md  text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150"]) }}>
    {{ $slot }}
</a>