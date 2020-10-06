<?php

namespace App\Models;

use App\Models\Order;
use App\Models\OrderItemPrice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'item_id',
        'material_id',
        'color_id',
        'image',
        'note',
        'screen_printing',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function price()
    {
        return $this->hasMany(OrderItemPrice::class);
    }
}
