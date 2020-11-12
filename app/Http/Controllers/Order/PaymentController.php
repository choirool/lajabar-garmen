<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentStoreRequest;
use App\Http\Requests\PaymentUpdateRequest;
use App\Http\Responses\Payment\PaymentStoreResponse;
use App\Http\Responses\Payment\PaymentCreateresponse;
use App\Http\Responses\Payment\PaymentUpdateResponse;

class PaymentController extends Controller
{
    public function create(Request $request, $orderId)
    {
        return new PaymentCreateresponse($orderId);
    }

    public function store(PaymentStoreRequest $request)
    {
        return new PaymentStoreResponse;
    }

    public function update(PaymentUpdateRequest $request)
    {
        return new PaymentUpdateResponse;
    }
}
