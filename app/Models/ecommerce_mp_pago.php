<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ecommerce_mp_pago extends Model
{
    use HasFactory;

    protected $fillable = ['sale_id','comercio_id','cliente_id','mp_id','payer_id','payment_method_id','status'];
}
