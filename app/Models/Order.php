<?php

namespace App\Models;

use App\Models\Payment;
use App\Models\Customer;
use App\Models\Salesman;
use App\Models\OrderItem;
use App\Models\Scopes\OrderScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;
    use OrderScope;

    protected $fillable = [
        'invoice_code',
        'customer_id',
        'invoice_date',
        'salesman_id',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function salesman()
    {
        return $this->belongsTo(Salesman::class)->withTrashed();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
