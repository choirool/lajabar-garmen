<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Sales report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-1">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-1 py-1 sm:px-1 bg-white border-b border-gray-200">
                    <div class="flex">
                        <div class="w-1/2">
                            <div id="sales-chart"></div>
                        </div>
                        <div class="w-1/2">
                            Chart 1
                        </div>
                    </div>
                    <div class="flex">
                        <div class="w-1/2">
                            Chart 1
                        </div>
                        <div class="w-1/2">
                            Chart 1
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);
        function drawChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Topping');
            data.addColumn('number', 'Slices');

            var chartData = []

            @foreach($item_sales as $item_sale)
                chartData.push([
                    '{{ $item_sale->item_name }}',
                    {{ $item_sale->count_item }}
                ]);
            @endforeach

            data.addRows(chartData);
            var options = {
                'title': "Top 5 item order form {{ now()->subMonth()->format('Y-m-d') }} to {{ now()->format('Y-m-d') }}",
                'width':500, 'height':400
            };
            var chart = new google.visualization.PieChart(document.getElementById('sales-chart'));
            chart.draw(data, options);
        }
    </script>
@endpush
</x-app-layout>