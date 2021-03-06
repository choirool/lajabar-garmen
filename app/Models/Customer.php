<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'country',
        'phone',
        'email',
        'invoice_color',
    ];

    public function products()
    {
        return $this->hasMany(CustomerItem::class);
    }
}
