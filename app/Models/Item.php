<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'picture',
        'stock',
        'unit',
        'unit_price',
    ];
}
