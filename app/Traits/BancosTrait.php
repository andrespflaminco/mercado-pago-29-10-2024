<?php
namespace App\Traits;


// Trait


// Modelos


// services 

use App\Services\CartVariaciones;

// Otros

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


trait BancosTrait {

public function GetBancosTrait($comercio_id) {
    
    $bancos = bancos::join('bancos_muestra_sucursales','bancos_muestra_sucursales.banco_id','bancos.id')
    ->where('bancos_muestra_sucursales.muestra', 1)
    ->where('bancos_muestra_sucursales.sucursal_id', $comercio_id)
    ->select('bancos.*')
    ->orderBy('bancos.nombre','asc')
    ->get();
    
    return $bancos;
}


// Estos son los metodos de pago que tienen los comercios con proveedores 

public function GetFormaPagoTrait($comercio_id) {
    
    $forma_pagos = forma_pagos::where('forma_pagos.comercio_id', $comercio_id)
    ->orderBy('forma_pagos.nombre','asc')
    ->get();
    
    return $forma_pagos;
}

public function GetFormaPagoTraitJson($comercio_id) {
    
    $forma_pagos = forma_pagos::where('forma_pagos.comercio_id', $comercio_id)
    ->orderBy('forma_pagos.nombre','asc')
    ->get()
    ->map(function ($forma) {
                return [
                    'id' => $forma->nombre,
                    'text' => $forma->nombre,
                ];
    });
    
    return $forma_pagos;
}



public function SetFormaPago($nombre,$comercio_id,$casa_central_id){
        
        if(!empty($nombre)){
            
        $nombre = implode('', $nombre);
        
        $forma_pago = forma_pagos::where('nombre',$nombre)->where('comercio_id',$comercio_id)->first();
        
        if($forma_pago != null){
        $forma_pego_elegida = $forma_pago->nombre;    
        } else {
        $forma_pago = forma_pagos::create([
            'nombre' => $nombre,
            'comercio_id' => $comercio_id,
            'casa_central_id' => $casa_central_id
            ]);    
        $forma_pego_elegida = $forma_pago->nombre;
        }
        
        
        } else {
        $forma_pego_elegida = null;    
        }
        
        return $forma_pego_elegida;
}

public function GetFormaPagoEditTrait($nombre, $comercio_id) {



$formas_pago = $this->GetFormaPagoTraitJson($comercio_id);
if($nombre !== 0){
    $nombre = $nombre;
} else {
    $nombre = [];
}

//dd($nombre);

$this->emit('FormasPagoCargadasEdit', $formas_pago->toArray(),$nombre);
    
        
}

}
