<?php

namespace App\Http\Responses\Customer;

use App\Models\Customer;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomerProductExport;
use Illuminate\Contracts\Support\Responsable;

class ExportExcelManageProductResponse implements Responsable
{
    public function toResponse($request)
    {
        $customer = Customer::query()
            ->where('id', $request->route('id'))
            ->first();

        $filename = str_replace(' ', '_', $customer->name) . '_products.xlsx';

        return Excel::download(
            new CustomerProductExport($customer),
            $filename
        );
    }
}
