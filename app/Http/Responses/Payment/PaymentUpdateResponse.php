<?php

namespace App\Http\Responses\Payment;

use App\Models\Payment;
use Illuminate\Support\Str;
use Illuminate\Contracts\Support\Responsable;

class PaymentUpdateResponse implements Responsable
{
    public function toResponse($request)
    {
        $this->saveData($request);
        session()->flash('message', 'Data successfully created.');

        return response()->json([
            'status' => true,
            'redirect' => route('transactions.orders'),
        ]);
    }

    protected function saveData($request)
    {
        return Payment::where('id', $request->payment_id)
            ->update([
                'order_id' => $request->order_id,
                'payment_date' => $request->date,
                'payment_method' => Str::of($request->payment_method)->replace('_', ' '),
                'payment_type' => 'payment',
                'amount' => $request->amount,
                'meta' => $request->meta,
            ]);
    }
}
