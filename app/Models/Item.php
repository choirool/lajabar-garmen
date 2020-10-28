<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'unit',
        'category_id',
        'material_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function customerItems()
    {
        return $this->hasMany(CustomerItem::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
