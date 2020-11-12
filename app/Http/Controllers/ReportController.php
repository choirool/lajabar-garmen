<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\Report\SalesChartResponse;

class ReportController extends Controller
{
    public function salesChart(Request $request)
    {
        return new SalesChartResponse;
    }
}
