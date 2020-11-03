<?php

namespace App\Models;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItemPrice extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'order_item_id',
        'size_id',
        'qty',
        'price',
        'special_price',
    ];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function productions()
    {
        return $this->hasMany(Production::class);
    }
}
