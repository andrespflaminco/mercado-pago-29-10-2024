<?php

namespace App\Models;

//use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable //implements MustVerifyEmail
{
    use HasFactory, Notifiable;
    use HasRoles;


    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'token_pass',
        'intento_pago',
        'autogestion',
        'flujo',
        'prefijo_pais',
        'name',
        'costo_igual_precio',
        'sucursal',
        'email',
        'password',
        'profile',
        'phone',
        'completo_formulario',
        'status',
        'external_auth',
        'external_id',
        'email_verified_at',
        'confirmation_code',
        'nro_operacion_mp',
        'confirmed_at',
        'confirmed',
        'plan',
        'last_login',
        'cantidad_login',
        'comercio_id',
        'cliente_id',
        'lista_defecto',
        'usuario_nuevo',
        'rubro',
        'image',
        'casa_central_user_id',
        'origen',
        'url_origen',
        'intencion_compra',
        'prueba_hasta',
        'usuarios_extra',
        'configuracion_codigos',
        
        'nombre_usuario',
        'apellido_usuario',
        'cantidad_sucursales',
        'cantidad_empleados',
        
        'lead_soho_id'
        
        
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function suscripcion()
    {
        return $this->hasOne(Suscripcion::class, 'user_id');
    }

}
