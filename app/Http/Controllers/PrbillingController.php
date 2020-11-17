<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\Order\PrebillingResponse;

class PrbillingController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, $id)
    {
        return new PrebillingResponse($id);
    }
}
