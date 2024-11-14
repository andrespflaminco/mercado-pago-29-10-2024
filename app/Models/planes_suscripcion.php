<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class planes_suscripcion extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'origen',
        'monto',
        'preapproval_plan_id',
        'eliminado',
        'plan_id',
        'frequency',
        'frequency_type',
        'trial_frequency',
        'trial_frequency_type',
        'billing_day',
        'billing_day_proportional',
    ];
}
