<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class productos_ivas extends Model
{
    use HasFactory;
    
    protected $fillable = ['product_id','comercio_id','sucursal_id','iva'];
}
