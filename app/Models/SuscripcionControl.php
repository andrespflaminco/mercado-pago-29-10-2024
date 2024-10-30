<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuscripcionControl extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'suscripcion_id',   
        'plan_id',
        'payer_id',
        'payer_email',
        'suscripcion_status',
        'cobro_status',        
        'nombre_comercio',
        'fecha_suscripcion',
        'monto_mensual',
        'monto_plan',
        'users_amount',        
        'users_count',     
        'init_point',           
        'external_reference',           
        'plan_id_flaminco',
        'proximo_cobro',
        'pagado',
        'reintentos',
        'action',
        'modulos_id',
        'modulos_amount',
    ];

    //relations
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function planFlaminco()
    {
        return $this->belongsTo(planes_suscripcion::class, 'plan_id_flaminco');
    }
}
