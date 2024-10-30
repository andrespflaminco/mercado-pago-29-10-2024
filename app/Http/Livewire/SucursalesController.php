<?php

namespace App\Http\Livewire;


use App\Models\productos_ivas; 
use App\Models\Category;
use App\Models\permisos_listas;
use App\Models\permisos_sucursales;
use App\Models\Product;
use App\Models\User;
use App\Models\lista_precios;
use App\Models\lista_precios_muestra_sucursales;
use App\Models\productos_variaciones_datos;
use App\Models\productos_stock_sucursales;
use App\Models\sucursales;
use App\Models\provincias;
use App\Models\ClientesMostrador;
use App\Models\datos_facturacion;
use App\Models\insumos_stock_sucursales;
use App\Models\insumo;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

use App\Models\saldos_iniciales;


class SucursalesController extends Component
{

	use WithFileUploads;
	use WithPagination;



	public $wc_key, $permiso, $user_central, $pageTitle, $componentName, $descripcion;

	public $relacion_precio_iva,$name,$phone,$email, $detalle_facturacion, $pto_venta, $plan, $iva_defecto, $ciudad, $provincia, $cuit,$search, $search_sucursal, $domicilio_comercial, $selected_id_facturacion, $iibb, $condicion_iva, $razon_social, $status,$image,$password,$selected_id,$id_provincia, $profile, $comercio_id, $usuario_id, $fecha_inicio_actividades;

	private $pagination = 25;
	
	public $lista_precios = [];
	public $lista_precios_id;
	
	public $lista_defecto;
	
	public $tipo_local;
	
	public  $id_check;
	
	public $tipo_filtro;
	
	public $ver_listado_clientes;


	public function mount()
	{
	    
	    $this->ver_listado_clientes = 0;
	    $this->tipo_filtro = "all";
	    if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;
		
		$this->pageTitle = 'Listado';
		$this->componentName = 'Sucursales';
		$this->precio_interno = 0;
		$this->id_provincia = "Elegir";
        $this->relacion_precio_iva = 0;
        $this->iva_defecto = 0;
        $this->lista_precios_id = 1;
        $this->lista_defecto = 0;
        $this->tipo_local = "Sucursal propia";
	    
	    $this->permiso = [];


	}

	public function paginationView()
	{
		return 'vendor.livewire.bootstrap';
	}


	public function render()
	{
		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

        $this->casa_central_id = Auth::user()->casa_central_user_id;
        
        $this->lista_precios = lista_precios::where('comercio_id',$this->casa_central_id)->where('eliminado',0)->get();
        
		$data = sucursales::join('users','users.id','sucursales.sucursal_id')
		->join('clientes_mostradors','clientes_mostradors.sucursal_id','sucursales.id')
		->leftjoin('lista_precios','lista_precios.id','clientes_mostradors.lista_precio')
		->select('sucursales.*','users.*','lista_precios.nombre as nombre_lista','users.lista_defecto')
		->where('sucursales.eliminado',0)->where('casa_central_id', 'like', $comercio_id);
			
        	if(strlen($this->search_sucursal) > 0) {
        			$data = $data->where( function($query) {
        				 $query->where('users.name', 'like', '%' . $this->search . '%');
        				});
        	}
        	
        	if($this->tipo_filtro != "all"){
        	    $data = $data->where('sucursales.tipo',$this->tipo_filtro);
        	}
        	
        	
			$data = $data->orderBy('sucursales.tipo','desc')
			->orderBy('users.name','desc')
			->paginate($this->pagination);
			
		//	dd($data);

        $this->lista_permisos = permisos_listas::where('eliminado',0)->get();
        
        $this->user_central = User::find($this->casa_central_id);
        
		$this->count_sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')->where('sucursales.eliminado',0)->where('casa_central_id', 'like', $comercio_id)->count();

        //dd($this->user_central);

		return view('livewire.sucursales.component', [
			'data' => $data,
			'lista_permisos' => $this->lista_permisos,
		  'provincias' => provincias::select('*')->get(),
		])
		->extends('layouts.theme-pos.app')
		->section('content');
	}



	public function Edit($id)
	{
	    
		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;
		  
		$this->selected_id = $id;

		$user = User::find($id);

		$this->name = $user->name;
		$this->lista_defecto = $user->lista_defecto;
		$this->email = $user->email;
		$this->phone = $user->phone;
		$this->password ='';

		$datos_facturacion = datos_facturacion::where('comercio_id',$id)->where('predeterminado',1)->where('eliminado',0)->first();

        //dd($datos_facturacion);
        
		 $this->razon_social = $datos_facturacion->razon_social;
		 $this->domicilio_comercial = $datos_facturacion->domicilio_fiscal;
		 $this->ciudad = $datos_facturacion->localidad;
		 $this->iva_defecto = $datos_facturacion->iva_defecto;
		 $this->pto_venta = $datos_facturacion->pto_venta;
		 $this->relacion_precio_iva = $datos_facturacion->relacion_precio_iva;
		 $this->id_provincia = $datos_facturacion->provincia;
		 $this->fecha_inicio_actividades = $datos_facturacion->fecha_inicio_actividades;
		 $this->condicion_iva = $datos_facturacion->condicion_iva;
		 $this->iibb = $datos_facturacion->iibb;
		 $this->cuit = $datos_facturacion->cuit;
		 
		 $sucursales = sucursales::where('sucursal_id',$id)->first();
		 
		 $this->tipo_local = $sucursales->tipo;
		 $this->ver_listado_clientes = $sucursales->solo_ver_clientes_propios;
		   	
		 $cliente = ClientesMostrador::where('sucursal_id',$sucursales->id)->first();
		 $this->lista_precios_id = $cliente->lista_precio;
	
	//	 $this->precio_interno = $sucursales->precio_interno;
		 
		 $permisos = permisos_sucursales::where('comercio_id',$comercio_id)->where('sucursal_id',$id)->get();
	    
	    // dd($permisos);
	    
	     $this->permiso = [];
	   
    	   foreach($permisos as $llaves => $sucus) {
    			$this->permiso[$sucus['permiso_id']] = $sucus['status'];
    	    }

		$this->emit('show-modal', 'show modal!');
	}

    public function Agregar() {
        
        foreach($this->lista_permisos as $lp) {
        $this->permiso[$lp->id] = "Elegir";    
        }
        
        $this->resetUI();
		$this->selected_id = 0;
		$this->resetUI();

        $this->emit('show-modal', 'show modal!');
    }

	public function Store()
	{
	    
        $validacion_iva = $this->ValidarIVA();
	   	if($validacion_iva != false) {
    	$this->emit("msg-error",$validacion_iva);
    	return;
        }
        
	    $vacios = $this->DetectarPermisosVacios();
	   	if($vacios == true) {
    	$this->emit("msg-error","existen permisos no elegidos");
    	return;
        }
    
		$rules =[
				'email' => "required|email|unique:users,email,{$this->selected_id}",
				'name' => 'required|min:3',
				'cuit' => 'numeric',
				'iibb' => 'nullable|numeric',
				'condicion_iva' => 'not_in:Elegir',
				'pto_venta' => 'nullable|numeric',
				'ciudad' => 'required',
				'domicilio_comercial' => 'required',
				'id_provincia' => 'not_in:Elegir',
		];

		$messages =[
				'name.required' => 'Ingresa el nombre',
				'name.min' => 'El nombre del usuario debe tener al menos 3 caracteres',
				'email.required' => 'Ingresa el correo ',
				'email.email' => 'Ingresa un correo válido',
				'email.unique' => 'El email ya existe en sistema',
				'cuit.numeric' => 'El cuit debe ser un numero, sin guiones ni espacios.',
				'iibb.numeric' => 'Ingresos brutos debe ser un numero, sin guiones ni espacios.',
				'condicion_iva.not_in' => 'Elija la condicion del IVA. En caso de no corresponder seleccione "Sin IVA"',
				'pto_venta.numeric' => 'El punto de venta debe ser un numero',
				'ciudad.required' => 'La ciudad es requerida',
				'domicilio_comercial.required' => 'El domicilio es requerido',
				'id_provincia.not_in' => 'Debe elegir una provincia'
			];


		$this->validate($rules, $messages);

		  if(Auth::user()->comercio_id != 1)
		  $comercio_id = Auth::user()->comercio_id;
		  else
		  $comercio_id = Auth::user()->id;
		  
		  if($this->tipo_local == "Sucursal propia"){$tipo_comercio = "Comercio";}
		  if($this->tipo_local == "Franquicia"){$tipo_comercio = "Franquicia";}

		 

			$user = User::create([
			    'name' => $this->name,
			    'email' => $this->email,
			    'phone' => $this->phone,
			    'status' => "Activo",
			    'plan' => $this->user_central->plan,
				'sucursal' => 1,
			    'profile' => $tipo_comercio,
			    'comercio_id' => 1,
			    'email_verified_at' => Carbon::now(),
			    'confirmed' => 1,
			    'confirmed_at' => Carbon::now(),
			    'casa_central_user_id' => Auth::user()->casa_central_user_id,
			    'password' => bcrypt($this->password)
			]);
		

		  $user->syncRoles($tipo_comercio);


		  if($this->image != $user->image)
		  {
		    $customFileName = uniqid() . '_.' . $this->image->extension();
		    $this->image->storeAs('public/users', $customFileName);
		    $imageTemp = $user->image; // imagen temporal
		    $user->image = $customFileName;
		    $user->save();

		    if($imageTemp !=null)
		    {
		      if(file_exists('storage/users/' . $imageTemp )) {
		        unlink('storage/users/' . $imageTemp);
		      }
		    }
		  }

			//////////      SUCURSAL                ////////////////////


			$sucursales = sucursales::create([
				'casa_central_id' => $comercio_id,
				'sucursal_id' => $user->id,
				'precio_interno' => $this->precio_interno,
				'tipo' => $this->tipo_local,
				'solo_ver_clientes_propios' => $this->ver_listado_clientes
			]);

            //// PERMISOS /////
            
            foreach ($this->permiso as $key => $value) {

            $permiso_id = $key;
            $status = $value;
                    
            $ps = permisos_sucursales::create([
                'comercio_id' => $comercio_id,
                'sucursal_id' => $user->id,
                'permiso_id' => $permiso_id,
                'status' => $status
                ]);
            }

		  //////////         DATOS DE FACTURACION           /////////////


		  $datos_facturacion = datos_facturacion::create([
		     'razon_social' => $this->razon_social,
		     'domicilio_fiscal' => $this->domicilio_comercial,
		     'localidad' => $this->ciudad,
		     'relacion_precio_iva' => $this->relacion_precio_iva,
		     'iva_defecto' => $this->iva_defecto,
		     'pto_venta' => $this->pto_venta,
		     'provincia' => $this->id_provincia,
		     'fecha_inicio_actividades' => $this->fecha_inicio_actividades,
		     'condicion_iva' => $this->condicion_iva,
		     'iibb' => $this->iibb,
		     'cuit' => $this->cuit,
		     'comercio_id' => $user->id
		  ]);

		  //////             CLIENTE               ////////
		  
		$ultimo_id = ClientesMostrador::where('comercio_id',$comercio_id)->max('id');
        $ultimo_cliente = ClientesMostrador::find($ultimo_id);
        
        if($ultimo_cliente != null){
        $this->id_cliente = $ultimo_cliente->id_cliente + 1;
        } else {
        $this->id_cliente = 1;    
        }
          
		  $cliente = ClientesMostrador::create([
		      'id_cliente' => $this->id_cliente,
              'nombre' => $this->name,
              'telefono' => $this->phone,
              'email' => $this->email,
              'direccion' => $this->domicilio_comercial,
              'barrio' => null,
              'localidad' => $this->ciudad,
              'provincia' => $this->provincia,
              'status' => $this->status,
              'dni' => $this->cuit,
              'observaciones' => '',
              'lista_precio' => $this->lista_precios_id,
              'comercio_id' => $comercio_id,
              'creador_id' => $comercio_id,
              'sucursal_id' => $sucursales->id
              ]);
		  
		    $user->update([
		        'cliente_id' => $cliente->id,
		        'lista_defecto' => $this->lista_defecto
		        ]);
		  
		  
		  ///////               PRODUCTOS                    //////////
		  
		  
// Si el producto es simple
        
		$productos_simples = Product::where('comercio_id',$comercio_id)->where('producto_tipo','s')->where('eliminado',0)->get();
        
        foreach($productos_simples as $p) {

        productos_stock_sucursales::create([
			'sucursal_id' => $user->id,
			'product_id' => $p->id,
			'stock' => 0,
			'referencia_variacion' => 0,
			'comercio_id' => $comercio_id
		]);
		
		productos_stock_sucursales::create([
			'sucursal_id' => $user->id,
			'product_id' => $p->id,
			'stock' => 0,
			'referencia_variacion' => 0,
			'comercio_id' => $comercio_id
		]);

        } 
        
        // Si el producto es variable
        
        $productos_variaciables = productos_variaciones_datos::leftjoin('products','products.id','productos_variaciones_datos.product_id')
        ->select('productos_variaciones_datos.*')
        ->where('products.comercio_id',$comercio_id)
        ->where('products.eliminado',0)
        ->where('products.producto_tipo','v')
        ->get();
        
        foreach($productos_variaciables as $pv) {

        productos_stock_sucursales::create([
			'sucursal_id' => $user->id,
			'product_id' => $pv->product_id,
			'stock' => 0,
			'referencia_variacion' => $pv->referencia_variacion,
			'comercio_id' => $comercio_id
		]);

        } 

        // Si tiene insumos crea los stocks
        
		$insumos = insumo::where('comercio_id',$comercio_id)->get();
        
        foreach($insumos as $i) {

        insumos_stock_sucursales::create([
			'sucursal_id' => $user->id,
			'insumo_id' => $i->id,
			'stock' => 0,
			'comercio_id' => $comercio_id
		]);

        } 		

        
        
        $this->StoreSaldosIniciales($comercio_id,$user->id);        
        
        // Si tiene iva crea los IVA segun el iva por defecto
        $this->StoreIVA($comercio_id,$user->id);
        
        $this->StoreListaPrecios($comercio_id,$user->id);

        
        
        
		$this->resetUI();
		$this->emit('data-added','Sucursal Registrada');

		}

	public function Update()
	{
	    $validacion_iva = $this->ValidarIVA();
	   	if($validacion_iva != false) {
    	$this->emit("msg-error",$validacion_iva);
    	return;
        }
        
	   if(Auth::user()->comercio_id != 1)
	   $comercio_id = Auth::user()->comercio_id;
	   else
	   $comercio_id = Auth::user()->id;

	    
	    $rules = [];

        // Recorre los valores de permiso y agrega reglas para cada uno
       foreach ($this->permiso as $key => $value) {
            $rules["permiso.$key"] = 'not_in:Elegir';
        }
        
        // Mensajes de error personalizados
        $messages = [
            'permiso.*.not_in' => 'Los permisos no pueden quedar en "Elegir".',
        ];
        
        $this->validate($rules, $messages);
        
		$rules =[
				'email' => "required|email|unique:users,email,{$this->selected_id}",
				'name' => 'required|min:3',
				'cuit' => 'numeric',
				'iibb' => 'nullable|numeric',
				'iva_defecto' => 'not_in:Elegir',
				'condicion_iva' => 'not_in:Elegir',
				'pto_venta' => 'nullable|numeric'
		];

		$messages =[
				'name.required' => 'Ingresa el nombre',
				'name.min' => 'El nombre del usuario debe tener al menos 3 caracteres',
				'email.required' => 'Ingresa el correo ',
				'email.email' => 'Ingresa un correo válido',
				'email.unique' => 'El email ya existe en sistema',
				'cuit.numeric' => 'El cuit debe ser un numero, sin guiones ni espacios.',
				'iibb.numeric' => 'Ingresos brutos debe ser un numero, sin guiones ni espacios.',
				'iva_defecto.not_in' => 'Elija el IVA por defecto. En caso de no corresponder seleccione "Sin IVA"',
				'condicion_iva.not_in' => 'Elija la condicion del IVA. En caso de no corresponder seleccione "Sin IVA"',
				'pto_venta.numeric' => 'El punto de venta debe ser un numero'
			];

		$this->validate($rules, $messages);

 
	 $user = User::find($this->selected_id);


		    $user->update([
		        'name' => $this->name,
		        'email' => $this->email,
		        'plan' => $this->user_central->plan,
		        'phone' => $this->phone,
		        'password' => strlen($this->password) > 0 ? bcrypt($this->password) : $user->password,
		        'lista_defecto' => $this->lista_defecto
		    ]);

			$datos_facturacion = datos_facturacion::where('comercio_id',$this->selected_id)->first();
            
            //dd($this->domicilio_comercial);
            
            $datos_facturacion->update([
		       'razon_social' => $this->razon_social,
		       'domicilio_fiscal' => $this->domicilio_comercial,
		       'fecha_inicio_actividades' => $this->fecha_inicio_actividades,
		       'relacion_precio_iva' => $this->relacion_precio_iva,
		       'condicion_iva' => $this->condicion_iva,
		       'iibb' => $this->iibb,
		       'iva_defecto' => $this->iva_defecto,
		       'pto_venta' => $this->pto_venta,
		       'provincia' => $this->id_provincia,
		       'localidad' => $this->ciudad,
		       'cuit' => $this->cuit
		    ]);
		    
		    //dd($datos_facturacion);
		    
		   	$sucursales = sucursales::where('sucursal_id',$user->id)->first();
		   	
		   	$sucursales->update([
		   	'tipo' => $this->tipo_local,
		   	'solo_ver_clientes_propios' => $this->ver_listado_clientes
		   	    ]);
            
			foreach ($this->permiso as $key => $value) {

            $permiso_id = $key;
            $status = $value;
            
            $ps = permisos_sucursales::where('comercio_id',$comercio_id)->where('sucursal_id',$user->id)->where('permiso_id',$permiso_id)->first();
            
            if($ps != null) {       
            $ps->update([
                'status' => $status
                ]);
            } else {
                 $ps = permisos_sucursales::create([
                'comercio_id' => $comercio_id,
                'sucursal_id' => $user->id,
                'permiso_id' => $permiso_id,
                'status' => $status
                ]);    
            }
            
            
            }

		  //////             CLIENTE               ////////
		  
		  // dd($user->id);
		  
         	 $cliente = ClientesMostrador::where('sucursal_id',$sucursales->id)->first();

         // dd($cliente);
		 

		  $cliente->update([
              'nombre' => $this->name,
              'telefono' => $this->phone,
              'email' => $this->email,
              'direccion' => $this->domicilio_comercial,
              'barrio' => null,
              'localidad' => $this->ciudad,
              'provincia' => $this->provincia,
              'status' => $this->status,
              'dni' => $this->cuit,
              'observaciones' => '',
              'lista_precio' => $this->lista_precios_id
              ]);

        
        
		$this->resetUI();
		$this->emit('data-updated', 'Lista de precios Actualizada');



	}


	public function resetUI()
	{
	    $this->ver_listado_clientes = 0;
	    $this->tipo_local = "Sucursal propia";
	    $this->lista_precios_id = 1;
	    $this->lista_defecto = 0;
	    $this->condicion_iva = null;
		$this->name ='';
		$this->image = null;
		$this->search_sucursal ='';
		$this->selected_id =0;
		$this->wc_key = '';
        $this->phone = '';
        $this->email = '';
        $this->domicilio_comercial = '';
        $this->ciudad = '';
        $this->id_provincia = 'Elegir';
        $this->status = '';
        $this->cuit = '';
        $this->iibb = '';
        $this->relacion_precio_iva = 0;
        $this->iva_defecto = 0;
        $this->pto_venta = null;
        $this->fecha_inicio_actividades = null;
        

	}

	protected $listeners =[
		'deleteRow' => 'Destroy',
		'accion-lote' => 'AccionEnLote',
	];


	public function Destroy($id)
	{

        $sucursal = sucursales::where('sucursal_id',$id)->first();
		
	//	dd($sucursal);

		$sucursal->eliminado = 1;
		$sucursal->save();


		$this->resetUI();
		$this->emit('data-deleted', 'La sucursal ha sido Eliminada');

	}


    public function DetectarPermisosVacios() {
        $hayValorVacioONulo = false;

        $arreglo = $this->permiso;
    
        $nuevoArray = array_values($arreglo);
        
        foreach ($nuevoArray as $valor) {
            if ($valor == "Elegir") {
                $hayValorVacioONulo = true;
                break;
            }
        }
        
        //dd($hayValorVacioONulo);
        
        if ($hayValorVacioONulo) {
            $hay_vacios = true;
        } else {
            $hay_vacios = false;
        }
        
        return $hay_vacios;
    }


    public function ValidarIVA(){
    //dd($this->condicion_iva);
    
      $response = false;
      
      if($this->relacion_precio_iva == 0 && $this->iva_defecto != 0) {
          $response = "Chequea el iva por defecto y la relacion precio iva.";
      }
      
      if($this->relacion_precio_iva != 0 && $this->iva_defecto == 0) {
          $response = "Chequea el iva por defecto y la relacion precio iva.";
      }

 // dd($this->condicion_iva); ---> si es monotributista no se debe dejar poner IVA.

    if($this->condicion_iva == "Monotributo"){
      if($this->relacion_precio_iva != 0 || $this->iva_defecto != 0) {
          $response = "Chequea el iva por defecto y la relacion precio iva. No se correponde a Monotributo.";
      }
        
    }
  // dd($this->condicion_iva); ---> si es otro que no sea monotributista no se debe dejar poner sin iva.

    if($this->condicion_iva == "IVA Responsable inscripto"){
      
      if($this->relacion_precio_iva == 0 || $this->iva_defecto == 0) {
          $response = "Chequea el iva por defecto y la relacion precio iva.";
      }
        
    }

      return $response;
    }
    
    public function StoreSaldosIniciales($comercio_id,$sucursal_id){
        
        $clientes = ClientesMostrador::where('comercio_id',$comercio_id)->get();
        
        foreach($clientes as $cliente){
            
        saldos_iniciales::create([
            'tipo' => 'cliente',
            'concepto' => 'Saldo inicial',
            'referencia_id' => $cliente->id,
            'comercio_id' => $comercio_id,
            'sucursal_id' => $sucursal_id,
            'monto' => 0,
            'eliminado' => 0,
            'fecha' => $cliente->fecha_inicial_cuenta_corriente
        ]);
                    
        }

    }
    public function StoreIVA($comercio_id,$sucursal_id){

	    $productos_ivas = productos_ivas::where('comercio_id',$comercio_id)
        ->select('productos_ivas.product_id')
        ->groupBy('productos_ivas.product_id')
        ->distinct()
        ->get();
        
        foreach($productos_ivas as $producto_iva){
        productos_ivas::create(
        [
            'product_id' => $producto_iva->product_id, 
            'comercio_id' => $comercio_id, 
            'sucursal_id' => $sucursal_id,
            'iva' => $this->iva_defecto
        ]);            
        }
    }
    
        public function StoreListaPrecios($comercio_id,$sucursal_id){
        $this->lista_precios = lista_precios::where('comercio_id',$this->casa_central_id)->where('eliminado',0)->get();
        
        foreach($this->lista_precios as $lista){
            $registro = lista_precios_muestra_sucursales::create([
                'sucursal_id' => $sucursal_id,
                'lista_id' => $lista->id,
                'muestra' => 1
            ]);
        
        }
    }
}
