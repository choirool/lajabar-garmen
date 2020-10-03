@props(['type' => 'default'])

@php
    $classType = 'text-gray-600 bg-gray-200 border border-gray-400';
    if ($type == 'primary') {
        $classType = 'text-purple-600 bg-purple-200 border border-purple-400';
    }

    if ($type == 'success') {
        $classType = 'text-indigo-600 bg-indigo-200 border border-indigo-400';
    }

    if ($type == 'info') {
        $classType = 'text-blue-600 bg-blue-200 border border-blue-400';
    }

    if ($type == 'warning') {
        $classType = 'text-yellow-600 bg-yellow-200 border border-yellow-400';
    }

    if ($type == 'danger') {
        $classType = 'text-red-600 bg-red-200 border border-red-400';
    }
    
@endphp

<div class="block text-sm text-left {{ $classType }} h-12 flex items-center p-4 my-4 rounded-sm" role="alert">
    {{ $slot }}
</div>