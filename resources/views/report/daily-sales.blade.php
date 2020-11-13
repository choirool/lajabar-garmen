<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daily sales') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-1">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="max-w-7xl mx-auto p-1 py-1 sm:px-1 bg-white border-b border-gray-200">
                    <div class="w-full text-center mb-2">
                        <p class="font-bold text-3xl">Daily Sales</p>
                        <p class="font-bold text-xl">Periode {{ now()->startOfMonth()->format('Y-m-d') }} to {{ now()->format('Y-m-d') }}</p>
                    </div>
                    <div>
                        <table class="table table-auto w-full">
                            <thead>
                                <tr>
                                    <th class="border" rowspan="2">Description</th>
                                    <th class="border" colspan="2">Sales</th>
                                </tr>
                                <tr>
                                    <th class="border">Today</th>
                                    <th class="border">Month to date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sales as $sale)
                                    <tr>
                                        <td class="border">{{ $sale->item_name }}</td>
                                        <td class="border text-right w-60">{{ $sale->today_sales ? : '0'}}</td>
                                        <td class="border text-right w-60">{{ $sale->month_to_date }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="border font-bold">Total</td>
                                    <td class="border font-bold text-right w-60">{{ $sales->sum('today_sales') }}</td>
                                    <td class="border font-bold text-right w-60">{{ $sales->sum('month_to_date') }}</td>
                                </tr>
                            </tfoot>
                        </table>

                        <a href="{{ route('reports.daily-sales', ['download']) }}">Download</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>