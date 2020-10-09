<?php

namespace App\Http\Controllers;

use App\Models\CustomerItem;
use Illuminate\Http\Request;

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
