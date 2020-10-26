<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ManageProductRequest;
use App\Http\Responses\Customer\ManageProductResponse;
use App\Http\Responses\Customer\StoreManageProductResponse;

class CustomerController extends Controller
{
    public function manageProduct(Request $request, $id)
    {
        return new ManageProductResponse($id);
    }

    public function storeManageProduct(ManageProductRequest $request)
    {
        return new StoreManageProductResponse;
    }
}
