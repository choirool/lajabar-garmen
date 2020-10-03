@props(['submit'])

<div {{ $attributes->merge(['class' => '']) }}>
    <div class="mt-5 md:mt-0 md:col-span-2">
        <form wire:submit.prevent="{{ $submit }}">
            <div class="shadow overflow-hidden sm:rounded-md">
                <div class="px-4 py-5 bg-white sm:p-6">
                    <div class="grid grid-cols-6 gap-6">
                        {{ $form }}
                    </div>
                </div>

                @if (isset($actions))
                    <div class="flex items-center px-4 py-3 bg-gray-50 sm:px-6">
                        {{ $actions }}
                    </div>
                @endif
            </div>
        </form>
    </div>
</div>