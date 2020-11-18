<?php

use App\Models\Order;

if (!function_exists('generate_invoice_code')) {
    function generate_invoice_code()
    {
        $invoiceNumber = 0;
        $lastToday = Order::query()
            ->latest()
            ->max('invoice_code');

        if ($lastToday) {
            $invoiceNumber = substr($lastToday, 8, 8);
        }

        return date('Ymd') . str_pad((int) $invoiceNumber + 1, 8, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('format_number')) {
    function format_number($number)
    {
        return number_format($number, 0, ',', '.');
    }
}
