<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class planes_suscripcion extends Model
{
    use HasFactory;
    
    protected $fillable = ['nombre','origen','monto','preapproval_plan_id','eliminado','plan_id'];
}
