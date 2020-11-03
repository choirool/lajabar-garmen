<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerItemPrice extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'customer_item_id',
        'size_id',
        'price',
        'special_price',
    ];

    public function customerItem()
    {
        return $this->belongsTo(CustomerItem::class);
    }
}
