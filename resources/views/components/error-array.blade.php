<x-alert type="danger">
    <ul>
        @foreach ($errors->get('customerItems.*') as $index => $customerItemErrors)
            @foreach ($customerItemErrors as $i => $customerItemError)
                @if (Str::of($customerItemError)->contains('customerItems.'.$i.'.item_id'))
                    <li>{{ Str::of($customerItemError)->replace('customerItems.'.$i.'.item_id', 'item name '.($i+1)) }}</li>
                @else
                    <li>
                        @php
                            $errorIndex = Str::of($index)
                                                ->finish(' '.($i+1))
                                                ->replace('customerItems.'.$i, '');
                        @endphp
                        {{ Str::of($customerItemError)->replace($index, $errorIndex) }}
                    </li>
                @endif
            @endforeach
        @endforeach
    </ul>
</x-alert>