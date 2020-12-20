<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\Order\Prebilling\PrebillingShowResponse;
use App\Http\Responses\Order\Prebilling\PrebillingExportToPdfResponse;
use App\Http\Responses\Order\Prebilling\PrebillingExportToExcelResponse;

class PrebillingController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        return new PrebillingShowResponse($id);
    }

    public function export(Request $request, $id)
    {
        if($request->export == 'excel') {
            return new PrebillingExportToExcelResponse($id);
        }

        if($request->export == 'pdf') {
            return new PrebillingExportToPdfResponse($id);
        }
    }
}
