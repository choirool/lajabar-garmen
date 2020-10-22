<?php
namespace App\Http\Responses\Production;

use Illuminate\Contracts\Support\Responsable;

class ProductionIndexResponse implements Responsable
{
    public function toResponse($request)
    {
        return view('production.index');
    }
}
