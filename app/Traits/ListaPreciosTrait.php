<?php
namespace App\Traits;


// Trait


// Modelos


// services 
use App\Models\User;
use App\Models\sucursales;
use App\Models\lista_precios_muestra_sucursales;
use App\Models\lista_precios;
use App\Models\configuracion_lista_precios;

// Otros

use App\Models\ClientesMostrador;

use Illuminate\Support\Facades\Storage;
use Notification;
use App\Notifications\NotificarCambios;
use Illuminate\Validation\Rule;
use DB;
use Intervention\Image\Facades\Image;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Automattic\WooCommerce\Client;
use Carbon\Carbon;

use App\Models\bancos;
use App\Models\forma_pagos;


trait ListaPreciosTrait {

public $mapeoListaMuestra;
public $precio_interno_defecto;
    // 14-8-2024
    public function GetConfiguracionListaPrecios() {
    
    
    $user = sucursales::where('casa_central_id', $this->casa_central_id)
        ->pluck('sucursal_id')
        ->toArray();
    array_push($user, $this->casa_central_id);
    
    // Obtén los registros que coincidan con cualquiera de los `sucursal_id` en `$user`
    //$lista_precios_muestra_sucursales = lista_precios_muestra_sucursales::whereIn('sucursal_id', $user)->get();
    //dd($lista_precios_muestra_sucursales);

    // Obtén los registros que coincidan con el `sucursal_id` actual
    $lista_precios_muestra_sucursales = lista_precios_muestra_sucursales::where('sucursal_id', $this->comercio_id)
    ->get();


    $configuracion = configuracion_lista_precios::where('casa_central_id', $this->casa_central_id)->first();
    $forma_mostrar = $configuracion ? $configuracion->forma_mostrar : 1;
    //dd($forma_mostrar);
    
    $mapeoListaMuestra = [];

    if ($forma_mostrar == 0) {
        // Si la forma de mostrar es 0, mapea los valores de lista_id => muestra
        $mapeoListaMuestra = $lista_precios_muestra_sucursales
            ->pluck('muestra', 'lista_id')
            ->toArray();
    } else {
        
        $lista_precios = lista_precios::where('comercio_id',$this->casa_central_id)->get();
        
        // Si la forma de mostrar es 1, asigna a todos los lista_id el valor 1
        $mapeoListaMuestra = $lista_precios
            ->pluck('id')
            ->mapWithKeys(function($id) {
                return [$id => 1];
            })
            ->toArray();
            
        $mapeoListaMuestra[0] = 1;    
            
        //  dd($mapeoListaMuestra);  
    }
    
    //dd($mapeoListaMuestra);
    $this->mapeoListaMuestra = $mapeoListaMuestra;


    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;
    
    $user = User::find($comercio_id);
 
    if($user->sucursal == 1){
        $cliente = ClientesMostrador::join('sucursales','sucursales.id','clientes_mostradors.sucursal_id')->where('sucursales.sucursal_id',$user->id)->first();
        if($cliente != null){$costo_defecto = $cliente->lista_precio;} else {$costo_defecto = 0;}
        $this->precio_interno_defecto = $costo_defecto;
    } else {
        $this->precio_interno_defecto = 0;
    }
    
   // dd($this->mapeoListaMuestra,$this->forma_mostrar);
}


    
}
