<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class SuscripcionCobros extends Model
{
    protected $fillable = [
        
        'user_id',
        'status_ticket',
        'suscripcion_id',
        'authorized_payment_id',
        //'suscripcion_id',
        'payer_id',
        'collector_id',
        'payer_email',
        'pago_id',
        'monto_mensual',  
        'intento_cobro',           
        'status',
        'status_detail',
        'external_reference',
        'description',
        'date_approved',      
        'date_last_updated',
        'date_created',
        'external_reference',
        'payment_method'
    ];
}
