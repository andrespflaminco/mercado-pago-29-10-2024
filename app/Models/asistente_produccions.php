<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class asistente_produccions extends Model
{
    use HasFactory;

    protected $fillable = ['product_id','product_name','product_barcode','referencia_variacion','cantidad','sale_id','estado'];

}
