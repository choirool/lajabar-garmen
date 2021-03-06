<?php

namespace App\Models;

use App\Models\Item;
use App\Models\Color;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

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
        'special_note',
        'screen_printing',
    ];

    protected $appends = [
        'image_url',
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

    public function getImageUrlAttribute()
    {
        if ($this->image && Storage::exists('orders/' . $this->image)) {
            return Storage::url('orders/' . $this->image);
        }

        return null;
    }
}
