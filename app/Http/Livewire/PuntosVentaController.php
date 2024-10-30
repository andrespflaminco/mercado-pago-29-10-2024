<?php

namespace App\Http\Livewire;

// Trait

use App\Traits\WocommerceTrait;

use App\Models\facturacion;
use App\Models\Sale;

use App\Models\provincias;

use App\Models\Product;
use App\Models\datos_facturacion;
use App\Models\User;
use App\Models\sucursales;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Automattic\WooCommerce\Client;
use App\Models\wocommerce;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Darryldecode\Cart\Facades\CartFacade as Cart;


use Afip;

use Mail;

class PuntosVentaController extends Component
{

	use WithFileUploads;
	use WithPagination;
	
	use WocommerceTrait;


    public $usuario;
	public $name, $search, $image, $agregar,$id_check,$selected_id, $pageTitle, $componentName, $wc_category_id;
	private $pagination = 25;
	private $wc_category;

    public $razon_social;
    public $domicilio_comercial;
    public $localidad;
    public $pto_venta;
    public $relacion_precio_iva;
    public $id_provincia;
    public $ciudad;
    public $fecha_inicio_actividades;
    public $condicion_iva;
    public $iibb;
    public $cuit;
    public $iva_defecto;
    public $sucursales;
    

	public function paginationView()
	{
		return 'vendor.livewire.bootstrap';
	}
	
    protected function redirectLogin()
    {
        return \Redirect::to("login");
    }

    public function mount()
    {
        $this->estado_filtro = 0;
        $this->accion_lote = 'Elegir';
        $this->pageTitle = 'Listado';
        $this->componentName = 'Categorías';
        $this->condicion_iva = 'Elegir';
        $this->iva_defecto = 0;
        $this->relacion_precio_iva = 0;
    
        if(Auth::user()->comercio_id != 1) {
            $this->comercio_id = Auth::user()->comercio_id;
        } else {
            $this->comercio_id = Auth::user()->id;
        }
    
        // Establecer la sucursal_id predeterminada
        $this->sucursal_id = $this->comercio_id;
    }

    public function ElegirSucursal($value){
    //    dd($value);
        $this->sucursal_id = $value;
        $this->render();
    }
    
	public function render()
	{
	   
	//   $this->SetearDatosAnteriores();
	   
        if (!Auth::check()) {
            // Redirigir al inicio de sesión y retornar una vista vacía
            $this->redirectLogin();
            return view('auth.login');
        }
        
        $this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')
        ->select('users.name','sucursales.sucursal_id')
        ->where('sucursales.eliminado',0)
        ->where('casa_central_id', $this->comercio_id )
        ->get();

        $this->usuario = User::find($this->comercio_id);
        
		$data = datos_facturacion::where('comercio_id', $this->sucursal_id);
			
	    if(strlen($this->search) > 0){
	     $data = $data->where('razon_social', 'like', '%' . $this->search . '%');   
	    }	
	    $data = $data->where('eliminado', $this->estado_filtro)
		->paginate($this->pagination);

        $provincias = provincias::get();
        
		return view('livewire.puntos_venta.component', [
		    'datos' => $data,
		    'provincias' => $provincias
		    ])
		->extends('layouts.theme-pos.app')
		->section('content');
	}



	public function Edit($id)
	{
	    
	    $this->agregar = 1;
	    $this->id_datos = $id;
	    $this->selected_id = $id;
	    
		$record = datos_facturacion::find($id);
        $this->razon_social = $record->razon_social;
        $this->domicilio_comercial = $record->domicilio_fiscal;
        $this->ciudad = $record->localidad;
        $this->iva_defecto = $record->iva_defecto;
        $this->pto_venta = $record->pto_venta;
        $this->relacion_precio_iva = $record->relacion_precio_iva;
        $this->id_provincia = $record->provincia;
        $this->ciudad = $record->localidad;
        $this->fecha_inicio_actividades = Carbon::parse($record->fecha_inicio_actividades)->format('Y-m-d');
        $this->condicion_iva = $record->condicion_iva;
        $this->iibb = $record->iibb;
        $this->cuit = $record->cuit;
        
		$this->emit('show-modal', 'show modal!');
	}


    public function Agregar() {
        $this->agregar = 1;
    }

    
    public function Validar(){
    //dd($this->condicion_iva);
    
      $response = true;
      
      if($this->relacion_precio_iva == 0 && $this->iva_defecto != 0) {
          $this->emit("msg-error","Chequee el iva por defecto y la relacion precio iva.");
          $response = false;
      }
      
      if($this->relacion_precio_iva != 0 && $this->iva_defecto == 0) {
          $this->emit("msg-error","Chequee el iva por defecto y la relacion precio iva.");
          $response = false;
      }

 // dd($this->condicion_iva); ---> si es monotributista no se debe dejar poner IVA.

    if($this->condicion_iva == "Monotributo"){
      if($this->relacion_precio_iva != 0 || $this->iva_defecto != 0) {
          $this->emit("msg-error","Chequee el iva por defecto y la relacion precio iva, ya que no se correponde a monotributo.");
          $response = false;
      }
        
    }
  // dd($this->condicion_iva); ---> si es otro que no sea monotributista no se debe dejar poner sin iva.

    if($this->condicion_iva == "IVA Responsable inscripto"){
      if($this->relacion_precio_iva == 0 || $this->iva_defecto == 0) {
          $this->emit("msg-error","Chequee el iva por defecto y la relacion precio iva, ya que no se correponde a IVA responsable inscripto.");
          $response = false;
      }
        
    }
 
      $datos_facturacion = datos_facturacion::find($this->selected_id);
      
      if($datos_facturacion != null) {
      
      if($datos_facturacion->iva_defecto != $this->iva_defecto) {
          
          $itemsQuantity = Cart::getTotalQuantity();
          if(0 < $itemsQuantity){
          $this->emit("msg-error","Es imposible actualizar el IVA por defecto porque existen productos en su carrito de ventas, porfavor finalice la venta o limpie el carrito.");
          $response = false;
          }
      }
        if($datos_facturacion->relacion_precio_iva != $this->relacion_precio_iva) {
     
          $itemsQuantity = Cart::getTotalQuantity();
          if(0 < $itemsQuantity){
          $this->emit("msg-error","Es imposible actualizar la relacion precio -  IVA, porque existen productos en su carrito de ventas, porfavor finalice la venta o limpie el carrito.");
          $response = false;
          }
      }
        
      }      
      return $response;
    }
    
	public function Store()
	{

		$response_validar = $this->Validar();
		if($response_validar == false){
		    return;
		}
		
		$rules = [
		    'razon_social' => 'required',
		    'pto_venta' => ['required','max:5'],
		    'condicion_iva' => 'not_in:Elegir',
			'cuit' => ['required','max:11'],
		];

		$messages = [
			'pto_venta.required' => 'El punto de venta es requerido',
			'pto_venta.max' => 'El punto de venta tiene que tener maximo 5 caracteres',
			'razon_social.required' => 'La razon social es requerida',
			'condicion_iva.not_in' => 'La condicion ante IVA es requerida',
			'cuit.max' => 'El cuit no debe tener mas de 11 caracteres',
			'cuit.unique' => 'El cuit ya existe.',
			'cuit.required' => 'El cuit es requerido'
		];
        
        $cantidad = datos_facturacion::where('comercio_id',$this->sucursal_id)->where('eliminado',0)->get();
        if(0 == $cantidad->count()){$predeterminado = 1;} else {$predeterminado = 0;}
        
		$this->validate($rules, $messages);

          datos_facturacion::create([
             'razon_social' => $this->razon_social,
             'domicilio_fiscal' => $this->domicilio_comercial,
             'localidad' => $this->ciudad,
             'iva_defecto' => $this->iva_defecto,
             'pto_venta' => $this->pto_venta,
             'relacion_precio_iva' => $this->relacion_precio_iva,
             'provincia' => $this->id_provincia,
             'fecha_inicio_actividades' => $this->fecha_inicio_actividades,
             'condicion_iva' => $this->condicion_iva,
             'iibb' => $this->iibb,
             'cuit' => $this->cuit,
             'comercio_id' => $this->sucursal_id,
             'predeterminado' => $predeterminado
          ]);


            $data["email"] = "andrespasquetta@gmail.com";
            $data["title"] = "Alta en AFIP";
            $data["body"] = "Solicitud de incorporacion de AFIP del usuario ".Auth::user()->name." , con email: ".Auth::user()->email.". CUIT: ".$this->cuit;
        
            Mail::send('mail', $data, function ($message) use ($data) {
                $message->to($data["email"], $data["email"])
                    ->subject($data["title"]);
            });
            
		$this->resetUI();
		$this->emit('category-added','Punto de venta Registrado');

	}

    public function EstablecerPredeterminado($id){
        $datos = datos_facturacion::find($id);
        $datos->update([
            'predeterminado' => 1
            ]);
		$datos_sin = datos_facturacion::where('comercio_id',$this->sucursal_id)->where('id','<>',$id)->get();
		foreach($datos_sin as $ds){
		$ds->predeterminado = 0;
		$ds->save();
		}
    }
    
    public function SetearDatosAnteriores(){
        $datos = datos_facturacion::get();
        
        foreach($datos as $d){
            $facturacion = facturacion::where('comercio_id',$d->comercio_id)->get();
    		foreach($facturacion as $f){
    		$f->datos_facturacion_id = $d->id;
    		$f->save();
    		}
    		$sale = Sale::where('comercio_id',$d->comercio_id)->get();
    		foreach($sale as $s){
    		$s->datos_facturacion_id = $d->id;
    		$s->save();
    		}
        }
        
    }
    
	public function Update()
	{
	    if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;
		
		$response_validar = $this->Validar();
		if($response_validar == false){
		    return;
		}
		
		$rules = [
		    'razon_social' => 'required',
		    'pto_venta' => ['required','max:5'],
		    'condicion_iva' => 'not_in:Elegir',
			'cuit' => ['required','max:11'],
        ];

		$messages = [
		    'pto_venta.max' => 'El cuit no debe tener mas de 5 caracteres',
			'pto_venta.required' => 'El punto de venta es requerido',
			'razon_social.required' => 'La razon social es requerida',
			'condicion_iva.not_in' => 'La condicion ante IVA es requerida',
			'cuit.max' => 'El cuit no debe tener mas de 11 caracteres',
			'cuit.unique' => 'El cuit ya existe.',
			'cuit.required' => 'El cuit es requerido'
		];
		
		$this->validate($rules, $messages);

        ////////////////////////////////////////////////
        
        $datos = datos_facturacion::find($this->selected_id);
		
		$datos->update([
             'razon_social' => $this->razon_social,
             'domicilio_fiscal' => $this->domicilio_comercial,
             'localidad' => $this->ciudad,
             'iva_defecto' => $this->iva_defecto,
             'pto_venta' => $this->pto_venta,
             'relacion_precio_iva' => $this->relacion_precio_iva,
             'provincia' => $this->id_provincia,
             'localidad' => $this->ciudad,
             'fecha_inicio_actividades' => $this->fecha_inicio_actividades,
             'condicion_iva' => $this->condicion_iva,
             'iibb' => $this->iibb,
             'cuit' => $this->cuit
             ]);

		$this->resetUI();
		$this->emit('category-updated', 'Punto de venta Actualizado');

	}


	public function resetUI()
	{
     $this->agregar = 0;
     $this->razon_social = null;
     $this->domicilio_comercial = null;
     $this->localidad = null;
     $this->ciudad = null;
     $this->pto_venta = null;
     $this->relacion_precio_iva = 0;
     $this->id_provincia = null;
     $this->ciudad = null;
     $this->fecha_inicio_actividades = null;
     $this->iibb = null;
     $this->cuit = null;
     $this->selected_id = 0;
     $this->condicion_iva = 'Elegir';
	 $this->iva_defecto = 0;
	}

	protected $listeners =[
		'deleteRow' => 'Destroy',
		'RestaurarCategoria' => 'RestaurarCategoria',
        'accion-lote' => 'AccionEnLote'
	];


	public function Destroy(datos_facturacion $datos)
	{
        $es_predeterminado = $datos->predeterminado;
        
        $datos->eliminado = 1;
		$datos->predeterminado = 0;
		$datos->save();
		
		// si era predeterminado, ponemos como predeterminado al primero que encontramos
		if($es_predeterminado == "1"){
        $datos_nvo = datos_facturacion::where('comercio_id', $this->sucursal_id)->where('eliminado',0)->first();
        
        if($datos_nvo != null){
        $this->EstablecerPredeterminado($datos_nvo->id);
		     
        }
		   
		}
		
		$this->resetUI();
		$this->emit('category-updated', 'Punto de venta Eliminado');

	}


	public function Filtro($estado)
	{
	   $this->estado_filtro = $estado;
	   
	}
	
	    // Eliminar en lote 
    
		
	public function RestaurarCategoria(datos_facturacion $datos)
	{
		$datos->update([
			'eliminado' => 0
		]);

		$this->resetUI();
		$this->emit('category-updated', 'Punto de venta Restaurado');
	}
	
	
	public function AccionEnLote($ids, $id_accion)
    {
    
    if($id_accion == 1) {
        $estado = 0;
        $msg = 'RESTAURADOS';
    } else {
        $estado = 1;
        $msg = 'ELIMINADOS';
    }
    
    $checked = datos_facturacion::select('datos_facturacions.id','datos_facturacions.comercio_id')->whereIn('datos_facturacions.id',$ids)->get();

    $this->id_check = [];


    foreach($checked as $pc) {
    $pc->eliminado = $estado;
    $pc->save();
    }
    
    
    $this->id_check = [];
    $this->accion_lote = 'Elegir';
    
     $this->emit('global-msg',"PUNTOS DE VENTA ".$msg);
    
    }
    

    public function ObtenerTributos(){
        $afip = new Afip(array('CUIT' => '20358072101', 'production' => true));
        $tax_types = $afip->ElectronicBilling->GetTaxTypes();
        dd($tax_types);
    }


}
