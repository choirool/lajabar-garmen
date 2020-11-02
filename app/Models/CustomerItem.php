<?php

namespace App\Models;

use App\Models\Item;
use App\Models\Color;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'item_id',
        'material_id',
        'color_id',
        'price',
        'image',
        'note',
        'screen_printing',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function item()
    {
        return $this->belongsTo(Item::class)->withTrashed();
    }

    public function color()
    {
        return $this->belongsTo(Color::class)->withTrashed();
    }

    public function material()
    {
        return $this->belongsTo(Material::class)->withTrashed();
    }

    public function prices()
    {
        return $this->hasMany(CustomerItemPrice::class);
    }
}
