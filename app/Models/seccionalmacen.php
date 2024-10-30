<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class seccionalmacen extends Model
{
  use HasFactory;


      protected $fillable = ['nombre','comercio_id','eliminado'];

      public function products()
      {
        return $this->hasMany(Product::class);
      }
}
