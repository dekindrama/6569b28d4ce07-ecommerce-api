<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'order_code',
        'total_all_price',
    ];

    protected $table = 'orders';

    public function payment() {
        return $this->hasOne(OrderPayment::class, 'order_id', 'id');
    }

    public function item() {
        return $this->hasOne(OrderItem::class, 'order_id', 'id');
    }
}
