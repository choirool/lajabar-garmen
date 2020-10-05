<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <!-- component -->
                <div class="flex flex-wrap mb-2">
                    <div class="w-full md:w-1/2 xl:w-1/4 pt-3 px-3 md:pr-2">
                        <a href="{{ route('master-data.customers') }}">
                            <div class="bg-green-600 border rounded shadow p-2 h-48">
                                <div class="flex flex-row items-center">
                                    <div class="flex-1 text-center pt-14">
                                        <h5 class="text-white text-3xl">Customer</h5>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="w-full md:w-1/2 xl:w-1/4 pt-3 px-3 md:pl-2">
                        <a href="#">
                            <div class="bg-blue-600 border rounded shadow p-2 h-48">
                                <div class="flex flex-row items-center">
                                    <div class="flex-1 text-center pt-14">
                                        <h5 class="text-white text-3xl">Productions</h5>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="w-full md:w-1/2 xl:w-1/4 pt-3 px-3 md:pr-2 xl:pr-3 xl:pl-1">
                        <a href="#">
                            <div class="bg-orange-600 border rounded shadow p-2 h-48">
                                <div class="flex flex-row items-center">
                                    <div class="flex-1 text-center pt-14">
                                        <h5 class="text-white text-3xl">Statuses</h5>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    {{-- <div class="w-full md:w-1/2 xl:w-1/4 pt-3 px-3 md:pl-2 xl:pl-3 xl:pr-2">
                        <a href="#">
                            <div class="bg-purple-600 border rounded shadow p-2 h-48">
                                <div class="flex flex-row items-center">
                                    <div class="flex-1 text-center pt-14">
                                        <h5 class="text-white text-3xl">Invoices</h5>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div> --}}
                </div>

                {{-- <div class="container px-4 py-4 mx-auto">
                    <h3 class="block text-xl text-gray-700 font-semibold mb-3"></h3>
                    <div class="flex">
                        <div class="w-1/2">
                            <div class="rounded-md p-6 bg-white shadow">
                                <div class="mb-2 pb-2">
                                    <h3 class="font-semibold text-lg text-gray-600">Bar Chart</h3>
                                    <p class="text-sm text-gray-500">Profit Share between customers</p>
                                </div>
                                <div id="chartdiv" class="w-full" style="height: 240px"></div>
                            </div>
                        </div>
                        <div class="w-1/2 ml-4">
                            <div class="rounded-md p-6 bg-white shadow">
                                <div class="mb-2 pb-2">
                                    <h3 class="font-semibold text-lg text-gray-600">Bar Chart</h3>
                                    <p class="text-sm text-gray-500">Profit Share between customers</p>
                                </div>
                                <div id="chartdiv2" class="w-full" style="height: 240px"></div>
                            </div>
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>

    {{-- @push('scripts')

    <script src="https://www.amcharts.com/lib/4/core.js"></script>
    <script src="https://www.amcharts.com/lib/4/charts.js"></script>
    <script src="https://www.amcharts.com/lib/4/themes/animated.js"></script>

    <script>
        function bar_chart(selector, data) {
            // Themes begin
            // Themes end
            am4core.useTheme(am4themes_animated);

            // Create chart instance
            var chart = am4core.create(selector, am4charts.XYChart);

            // Add data
            chart.data = data;

            // Create axes

            var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
            categoryAxis.dataFields.category = "country";
            categoryAxis.renderer.grid.template.location = 0;
            categoryAxis.renderer.minGridDistance = 30;

            categoryAxis.renderer.labels.template.adapter.add("dy", function (dy, target) {
                if (target.dataItem && target.dataItem.index & 2 == 2) {
                    return dy + 25;
                }
                return dy;
            });

            var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

            // Create series
            var series = chart.series.push(new am4charts.ColumnSeries());
            series.dataFields.valueY = "visits";
            series.dataFields.categoryX = "country";
            series.name = "Visits";
            series.columns.template.tooltipText = "{categoryX}: [bold]{valueY}[/]";
            series.columns.template.fillOpacity = .8;

            var columnTemplate = series.columns.template;
            columnTemplate.strokeWidth = 2;
            columnTemplate.strokeOpacity = 1;
        }

        function radar_chart(selector, data) {
            // Themes begin
            // Themes end
            am4core.useTheme(am4themes_animated);

            // Create chart instance
            var chart = am4core.create(selector, am4charts.RadarChart);


            chart.data = data;

            // Make chart not full circle
            chart.startAngle = -90;
            chart.endAngle = 180;
            chart.innerRadius = am4core.percent(20);

            // Set number format
            chart.numberFormatter.numberFormat = "#.#'%'";

            // Create axes
            var categoryAxis = chart.yAxes.push(new am4charts.CategoryAxis());
            categoryAxis.dataFields.category = "category";
            categoryAxis.renderer.grid.template.location = 0;
            categoryAxis.renderer.grid.template.strokeOpacity = 0;
            categoryAxis.renderer.labels.template.horizontalCenter = "right";
            categoryAxis.renderer.labels.template.fontWeight = 500;
            categoryAxis.renderer.labels.template.adapter.add("fill", function (fill, target) {
                return (target.dataItem.index >= 0) ? chart.colors.getIndex(target.dataItem.index) : fill;
            });
            categoryAxis.renderer.minGridDistance = 10;

            var valueAxis = chart.xAxes.push(new am4charts.ValueAxis());
            valueAxis.renderer.grid.template.strokeOpacity = 0;
            valueAxis.min = 0;
            valueAxis.max = 100;
            valueAxis.strictMinMax = true;

            // Create series
            var series1 = chart.series.push(new am4charts.RadarColumnSeries());
            series1.dataFields.valueX = "full";
            series1.dataFields.categoryY = "category";
            series1.clustered = false;
            series1.columns.template.fill = new am4core.InterfaceColorSet().getFor("alternativeBackground");
            series1.columns.template.fillOpacity = 0.08;
            series1.columns.template.cornerRadiusTopLeft = 20;
            series1.columns.template.strokeWidth = 0;
            series1.columns.template.radarColumn.cornerRadius = 20;

            var series2 = chart.series.push(new am4charts.RadarColumnSeries());
            series2.dataFields.valueX = "value";
            series2.dataFields.categoryY = "category";
            series2.clustered = false;
            series2.columns.template.strokeWidth = 0;
            series2.columns.template.tooltipText = "{category}: [bold]{value}[/]";
            series2.columns.template.radarColumn.cornerRadius = 20;

            series2.columns.template.adapter.add("fill", function (fill, target) {
                return chart.colors.getIndex(target.dataItem.index);
            });

            // Add cursor
            chart.cursor = new am4charts.RadarCursor();
        }

        am4core.ready(function () {
            bar_chart("chartdiv", [{
                "country": "USA",
                "visits": 2025
            }, {
                "country": "China",
                "visits": 1882
            }, {
                "country": "Japan",
                "visits": 1809
            }]);

            radar_chart("chartdiv2", [{
                "category": "Research",
                "value": 80,
                "full": 100
            }, {
                "category": "Human Resources",
                "value": 68,
                "full": 100
            }]);
        }); // end am4core.ready()

    </script>
@endpush --}}
</x-app-layout>
