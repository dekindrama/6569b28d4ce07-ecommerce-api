<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'order_id',
        'payer_name',
        'paid_amount',
        'change_amount',
        'payment_type',
    ];

    protected $table = 'order_payment';

    public function order() {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
