<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\Production\ProductionIndexResponse;

class ProductionController extends Controller
{
    public function index(Request $request, $orderId)
    {
        return new ProductionIndexResponse;
    }
}
