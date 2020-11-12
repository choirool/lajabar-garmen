<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|in:cash,cc,bank_transfer',
            'amount' => 'required|numeric',
            'date' => 'required|date|before_or_equal:today',
            'meta.bank_name' => '',
            'meta.account_number' => '',
            'meta.cc_name' => '',
            'meta.cc_number' => '',
            'meta.note' => '',
        ];
    }
}
