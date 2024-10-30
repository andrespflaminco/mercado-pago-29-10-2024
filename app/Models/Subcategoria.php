<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategoria extends Model
{
    use HasFactory;

    protected $table = 'subcategorias';

    protected $fillable = [
        'categoria_id', 
        'nombre', 
        'eliminado',
        'comercio_id'
    ];

    public function categoria()
    {
        return $this->belongsTo(Category::class);
    }
}
