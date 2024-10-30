<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suscripcion extends Model
{
    protected $table = 'suscripcions';
    
    protected $fillable = [
        'user_id',
        'suscripcion_id',
        'plan_id',
        'payer_id',
        'payer_email',
        'nombre_comercio',
        'telefono',
        'init_point',
        'fecha',
        'monto_mensual',
        'modulos_id',
        'modulos_amount',
        'users_count',
        'users_amount',
        'suscripcion_status',
        'cobro_status',
        'plan_id_flaminco',
        'external_reference',
        'proximo_cobro'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
