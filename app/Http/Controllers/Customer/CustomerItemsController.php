<?php

namespace App\Http\Controllers\Customer;

use App\Models\CustomerItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CustomerItemsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return CustomerItem::query()
            ->where('customer_id', $request->id)
            ->with('item', 'prices')
            ->get();
    }
}
