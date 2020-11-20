<?php

namespace App\Models;

use App\Models\Order;
use App\Models\OrderItemPrice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
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
        'special_note',
        'screen_printing',
    ];

    protected $appends = [
        'image_url',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class)->withTrashed();
    }

    public function material()
    {
        return $this->belongsTo(Material::class)->withTrashed();
    }

    public function color()
    {
        return $this->belongsTo(Color::class)->withTrashed();
    }

    public function prices()
    {
        return $this->hasMany(OrderItemPrice::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function getImageUrlAttribute()
    {
        if ($this->image && Storage::exists('orders/' . $this->image)) {
            return Storage::url('orders/' . $this->image);
        }

        return null;
    }
}
