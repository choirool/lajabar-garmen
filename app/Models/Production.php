<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Production extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'order_item_price_id',
        'value',
        'size_id',
    ];

    public function orderItemPrice()
    {
        return $this->belongsTo(OrderItemPrice::class);
    }
}
