<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\Report\SalesChartResponse;
use App\Http\Responses\Report\DailySalesReportResponse;

class ReportController extends Controller
{
    public function salesChart(Request $request)
    {
        return new SalesChartResponse;
    }

    public function dailySales(Request $request)
    {
        return new DailySalesReportResponse;
    }
}
