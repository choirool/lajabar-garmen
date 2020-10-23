<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\Production\ProductionIndexResponse;
use App\Http\Responses\Production\ProductionStoreResponse;

class ProductionController extends Controller
{
    public function index(Request $request, $orderId)
    {
        return new ProductionIndexResponse($orderId);
    }

    public function store(Request $request, $orderId)
    {
        return new ProductionStoreResponse($orderId);
    }
}
