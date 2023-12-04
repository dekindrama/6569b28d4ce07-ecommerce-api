<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'item_id',
        'order_id',
        'name',
        'unit',
        'unit_price',
        'qty',
        'subtotal_price',
    ];

    protected $table = 'order_items';

    public function order() {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function item() {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }
}
