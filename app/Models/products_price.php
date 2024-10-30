<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class products_price extends Model
{
    use HasFactory;

    protected $fillable = ['product_id','price','stock','alerts','comercio_id','seccionalmacen_id','stock_descubierto','eliminado','sucursal_id','lista'];

}
