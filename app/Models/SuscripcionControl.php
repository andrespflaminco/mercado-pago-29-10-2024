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

        'collector_id_mp',
        'application_id_mp',
        'reason_mp',
        'date_created_mp',
        'last_modified_mp',
        'frequency_mp',
        'frequency_type_mp',
        'transaction_amount_mp',
        'currency_id_mp',
        'start_date_mp',
        'end_date_mp',
        'free_trial_mp',
        'quotas_mp',
        'charged_quantity_mp',
        'pending_charge_quantity_mp',
        'charged_amount_mp',
        'pending_charge_amount_mp',
        'semaphore_mp',
        'last_charged_date_mp',
        'last_charged_amount_mp',
        'next_payment_date_mp',
        'payment_method_id_mp',
        'payment_method_id_secondary_mp',
        'first_invoice_offset_mp',
        'billing_day_proportional_mp',
        'has_billing_day_mp',
        'back_url_mp',
        'status_mp',
        'payer_first_name_mp',
        'payer_last_name_mp',
        'observaciones_mp',
        'proceso_asociado',
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
