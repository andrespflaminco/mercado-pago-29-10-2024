<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComisionUsuario extends Model
{
    use HasFactory;
    
    protected $table = 'comision_usuarios';
    protected $fillable = ['user_id', 'comercio_id','casa_central_id','porcentaje_comision','eliminado'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
