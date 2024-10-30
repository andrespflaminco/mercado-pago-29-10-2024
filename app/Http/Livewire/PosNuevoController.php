<?php

namespace App\Http\Livewire;

use Darryldecode\Cart\Facades\CartFacade as Cart;
use App\Models\Denomination;
use Illuminate\Support\Facades\Auth;
use App\Models\SaleDetail;
use App\Models\Category;
use App\Models\errores;
use App\Models\facturacion;
use App\Models\seccionalmacen;
// ACA ENVIO
use App\Models\ecommerce_envio;
use App\Models\provincias;
//

// AGREGADO 6-6-2023
use App\Models\bancos_muestra_sucursales;
use App\Models\metodo_pagos_muestra_sucursales;
//
use App\Models\asistente_produccions;
use Automattic\WooCommerce\Client;
use App\Models\wocommerce;
use App\Models\recordatorios;
use App\Models\atributos;
use App\Models\variaciones;
use App\Services\CartVariaciones;
use App\Models\actualizacion_precios;
use App\Models\proveedores;
use App\Models\productos_variaciones_datos;
use App\Models\productos_lista_precios;
use App\Models\lista_precios;
use App\Models\datos_facturacion;
use Afip;
use App\Models\cajas;
use App\Models\sucursales;
use App\Models\bancos;
use App\Models\productos_stock_sucursales;
use App\Models\formulario_respuesta;
use App\Models\beneficios;
use App\Models\User;
use App\Models\historico_stock;
use App\Models\pagos_facturas;
use App\Models\productos_variaciones;
use App\Models\hoja_ruta;
use App\Models\ClientesMostrador;
use App\Models\metodo_pago;
use App\Models\cheques;
use Livewire\Component;
use Carbon\Carbon;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\Sale;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Notification;
use App\Notifications\NotificarCambios;

// Trait
use App\Traits\FacturacionNuevoAfip;
use App\Traits\ProductsTrait;
//use App\Traits\CartNuevoTrait;
use App\Traits\WocommerceTrait;
use App\Traits\ClientesTrait;
//

// 9-1-2024
use App\Models\compras_proveedores;
//
use Illuminate\Support\Facades\Redirect;

//use App\CartTrait\Pos;



class PosNuevoController extends Component
{
//	use CartNuevoTrait;
	use WithFileUploads;
	use FacturacionNuevoAfip;
	use WocommerceTrait;
	use ClientesTrait;
	// 27-12-2023
    use ProductsTrait;
    //



	public $product_id,$nro_paso,$checked_retiro_sucursal,$checked_envio_cliente,$search_lista_productos,$search_categorias_lista_productos,$paso1_style,$paso2_style,$paso3_style,$total,$itemsQuantity,$cliente_cuit, $descuento_gral_mostrar, $producto_tipo, $cuit,$tipo_persona,  $apellido_cliente, $created_at, $tipo_clave, $mensaje, $created_at_cambiado, $nombre_cliente , $productos_variaciones1,$productos_variaciones ,$productos_variaciones2 ,$recordatorio, $tipo_pago, $metodos, $caja_abierta, $mail_ingresado, $beneficios, $total_pago, $metodo_pago_agregar_pago, $comprobante, $efectivo2, $change_div, $ventaIdEmail, $recargo_total, $ventaId, $id_venta_email, $id_venta , $efectivo, $change, $metodo_pago, $comercio_id, $usuario_id, $tipo_comprobante, $tipo_documento, $comentarios,$canal_venta, $coment, $desc, $metodo_pago_ap_div1, $descuento, $descuento_ratio, $nota_interna, $usuario_activo, $componentName, $selected_id, $pago_parcial, $check, $estado_pedido, $rec, $id_pedido, $estado_estado, $NroVenta, $suma_monto, $suma_cash, $tot, $hojar, $hoja_ruta, $monto, $efectivo1, $estado, $inputs, $estado2, $nombre_hr, $tipo, $fecha_hr, $turno, $hr_elegida, $observaciones_hr, $id_pago, $fecha_pedido, $monto_inicial, $descuento_total, $caja, $formato_modal ,$fecha_editar, $monto_ap_div,$monto_ap_div2, $metodo_pago_ap_div, $metodo_pago_ap_div2, $pago_dividido, $recargo_div1, $recargo_div2, $metodo_pagodiv2,$pago_total, $prueba1, $tipo_click, $tipos_pago, $condicion_iva, $name,$barcode,$cost,$price,$stock,$alerts,$categoryid, $almacen, $stock_descubierto, $cantidad_empleados, $cuenta_con_sistema, $inv_ideal, $proveedor, $cod_proveedor, $proveedor_elegido, $precio_lista, $stock_sucursal, $lista_precios,$tipo_producto,$sucursales, $ecommerce_canal, $mostrador_canal, $descripcion, $importancia, $urgencia, $subtotal, $producto_casa_central, $relacion_precio_iva, $nro_cheque_ch, $emisor_ch, $banco_ch, $fecha_cobro_ch, $fecha_emision_ch,$iva, $wc_canal;
    public $efectivo_pago_dividido;
    
    public $noEncontrados1 = [];
    // ACA ENVIO 
    public $telefono_envio, $cantidad_promo_max_form, $check_envio,$check_envio_cliente, $nombre_envio, $direccion_envio, $calle_envio,$altura_envio,$depto_envio,$piso_envio,$cod_postal_envio,$ciudad_envio, $provincia_envio;
    //

    
    // 2-5-2024
    
    public $puntos_venta_listado,$punto_venta_elegido,$datos_punto_venta_elegido;
    
    //
    
    public $producto_casa_central_pasar;
	public $nro_venta;
	public $mail = [];
	public $variacion_elegida = [];
	public $productos_variaciones_datos = [];
	public $lista_cajas_dia = [];
	public $listado_hojas_ruta = [];
	public $pagos1 = [];
	public $pagos2 = [];
	public $total_total = [];
	public $usuario = [];
	public $fecha = [];
	public $detalle_cliente = [];
	public $detalle_venta = [];
	public $atributos = [];
    public $variaciones = [];
    public $lista_productos = [];

    // 27-12-2023
    public $mostrarDivProductos = false;

	public $Id_cart;
	public $query;
	public $date;
	public $query_id;
	public $products_s;
	public $query_product;
	public $recargo;
	public $metodo_pago_nuevo;
	public $contacts;
	public $sum_descuento_promo;
	public $observaciones;
	public $highlightIndex;
    
    public $nombre_metodo_pago;
    public $recargo_metodo_pago;
    public $cuenta_metodo_pago;
    public $categoria_metodo_pago;
    public $motivo_si = [];
    public $motivo_no = [];
    public $stock_sucursales = [];
    public $stock_sucursales_pasar = [];
    
    // AGREGADO 6-6-2023
    public $muestra_sucursales = [];
    //
    // 7-6-2023
    
    public $a_recargar;
    
    //
    
    //Id venta
    public $idVenta, $stateSaveSale;
            
	public $nombre,$telefono,$email,$status,$image,$provincia,$localidad,$fileLoaded,$direccion,$barrio,$dni, $cliente, $clientes;
	public $pageTitle, $search, $fecha_ap, $monto_ap, $metodo_pago_ap;

	public $CBU, $cuit_banco, $nombre_banco, $tipo_banco;

    public function ModalAgregarDescuento($id){

    		$this->Id_cart = $id;
    		$item = Cart::get($id);
            
     		if(1 < $item->attributes['id_promo'] && 0 < $item->attributes['cantidad_promo']) {
    		    $this->emit("msg-error","No puede agregar descuentos a productos con promociones vigentes");
    		    return;
    		}
    		
    		           
    		$this->descuento_promo_form = $item->attributes['descuento_promo']/($item->price)*100;
    		$this->cantidad_promo_form = $item->attributes['cantidad_promo'] ?? 1;
    		$this->cantidad_promo_max_form = $item->quantity;
    
    		$this->emit('show-modal-descuentos','details loaded');
    		
    }
    
    public function CerrarModalAgregarDescuento(){
    
    		$this->emit('show-modal-descuentos','details loaded');
    		
    }
    
// MODIFICADO 6-6-2023

public function StoreBanco()
	{
	    
	    if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;


      $this->tipo_usuario = User::find(Auth::user()->id);

      if($this->tipo_usuario->sucursal != 1) {
        $this->casa_central_id = $comercio_id;
      } else {

        $this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
        $this->casa_central_id = $this->casa_central->casa_central_id;
      }


    		$rules  =[
    		'nombre_banco' => 'required|min:3',
            'tipo_banco' => 'required|not_in:Elegir'
    		];
    
    		$messages = [
    		'nombre_banco.required' => 'Nombre del metodo de pago requerido',
    		'tipo_banco.required' => 'Debe seleccionar un tipo de cuenta',
            'nombre_banco.min' => 'El nombre debe tener al menos 3 caracteres',
            'tipo_banco.not_in' => 'Debe seleccionar un tipo de cuenta'
    		];
    
    		$this->validate($rules, $messages);
    

    	$banco = bancos::create([
    	    'nombre' => $this->nombre_banco,
            'tipo' => $this->tipo_banco,
    		'CBU' => $this->CBU,
            'cuit' => $this->cuit_banco,
    		'comercio_id' => $this->casa_central_id,
            'muestra_sucursales' => 1,
            'creador_id' => $comercio_id
    		]);
        		
    
        if($this->muestra_sucursales != 1) {
    
          foreach ($this->muestra_sucursales as $key => $value) {
            
            bancos_muestra_sucursales::create([
              'banco_id' => $banco->id,
              'sucursal_id' => $key,
              'muestra' =>  $this->muestra_sucursales[$key]
            ]);
    
        }
    
        }

        $this->tipos_pago = $this->getTipoPago($this->comercio_id);
		$this->resetUIBanco();
		$this->emit('scan-ok', 'Banco/Plataforma de pago Registrado');


		$this->tipo_pago = $banco->id;

			$this->emit('banco-hide', '');

	}

// MODIFICADO 6-6-2023

public function StoreMetodoPago()
	{
	    
		$this->cuenta_banco =  bancos::find($this->tipo_pago);

	    $rules  =[
		'nombre_metodo_pago' => 'required',
		'recargo_metodo_pago' => 'required|numeric'
		];

		$messages = [
		'nombre_metodo_pago.required' => 'Nombre del metodo de pago requerido',
        'recargo_metodo_pago.required' => 'El recargo es requerido, en caso de ser nulo coloque 0.',
        'recargo_metodo_pago.numeric' => 'El recargo debe ser un numero.'
    
		];

		$this->validate($rules, $messages);  

        if(Auth::user()->comercio_id != 1)
        $comercio_id = Auth::user()->comercio_id;
        else
        $comercio_id = Auth::user()->id;
    
    	$metodo = metodo_pago::create([
    			'nombre' => $this->nombre_metodo_pago,
    			'recargo' => $this->recargo_metodo_pago,
    			'categoria' => $this->cuenta_banco->tipo,
    			'cuenta' => $this->cuenta_banco->id,
            	'comercio_id' => $this->casa_central_id,
                'muestra_sucursales' => 1,
                'creador_id' => $comercio_id
    		]);
    
        if($this->muestra_sucursales != 1) {
    
          foreach ($this->muestra_sucursales as $key => $value) {
    
            metodo_pagos_muestra_sucursales::create([
              'metodo_id' => $metodo->id,
              'sucursal_id' => $key,
              'muestra' =>  $this->muestra_sucursales[$key]
            ]);
    
        }
    
        }
        
        $value = $metodo;
        
        $this->metodo_pago = $value->id;
		session(['MetodoPago' => $value->id]);
        $this->MetodoPago($value->id);
        
        $this->metodos = $this->getMetodosPago($this->comercio_id);
        
		$this->resetUIMetodoPago();
		$this->emit('scan-ok', 'Metodo de pago registrado');

		$this->emit('metodo-pago-hide', '');
	}



	public function mount()
	{
    	$this->fecha_inicial_cuenta_corriente = Carbon::now()->format('Y-m-d');
            
        $this->paso1_style = "block;";
        $this->paso2_style = "none;";
        $this->paso3_style = "none;";
        
        $this->style_pago_dividido = "display:none;";
        $this->style_metodo_pago = "display:block;";

	    // Pago dividido   
    	$this->SetPagoDivididoMount();

        
        $this->nro_paso = 1;
        $this->check_retiro_sucursal = "checked";
        $this->checked_retiro_sucursal = "checked";
        
        // Seteamos el comercio, usuario y casa central
        $this->comercio_id = $this->setComercioId();
        $this->sucursal_id = $this->comercio_id;
		$this->usuario_id = Auth::user()->id;
		$this->casa_central_id = $this->setCasaCentral($this->comercio_id);
        $this->setSucursalesMount();

        session(['sucursal_id' => $this->sucursal_id]);
        session(['casa_central_id' => $this->casa_central_id]);
        session(['sucursales' => $this->sucursales]);
            
		// 			

	    // Seteamos la forma de envio
        $this->SetEnvioMount();

	    $this->created_at_cambiado = '';
		$this->iva = 0;
		$this->tipo_producto = "Elegir";
		$this->wc_canal = false;
		$this->ecommerce_canal = false;
		$this->mostrador_canal = false;
		$this->cuenta_con_sistema = 'Elegir';
		$this->cantidad_empleados = 'Elegir';
		$this->urgencia = 'Elegir';
		$this->importancia = 'Elegir';
        $this->tipo_banco ='Elegir';
		$this->lista_cajas_dia = [];
		$this->metodos = [];
		$this->motivo_no = [];
		$this->motivo_si = [];
		$this->metodo_pago_agregar_pago = 1;
		$this->tipo_pago = 1;
		$this->tipos_pago = [];
		$this->tipo_click = 0;

		$this->prueba1 = 0;
	    
	    $this->comprobante = Auth::user()->comprobante;
		$this->pago_parcial = Auth::user()->pago_parcial;
		$this->efectivo =0;
		$this->canal_venta = 'Mostrador';
        $this->itemsQuantity = Cart::getTotalQuantity();
		$this->subtotal = Cart::getTotal();
		$this->query = '';
		$this->recargo = 0;
		$this->monto_ap = 0;
		$this->metodo_pago_ap = 'Elegir';
		$this->contacts = [];
		$this->proveedor = '1';
		$this->categoryid = 'Elegir';
		$this->almacen = 'Elegir';
		$this->stock_descubierto = 'Elegir';
		$this->highlightIndex = 0;
		$this->usuario_activo = Auth::user()->id;
		$this->observaciones = '';
		$this->date = Carbon::parse(Carbon::now())->format('Y-m-d');
		$this->fecha_pedido = Carbon::parse(Carbon::now())->format('d-m-Y H:i');

		
		$this->metodo_pago = session('MetodoPago');
		$descuento_gral_mostrar = session('DescuentoGral');
		$this->descuento_gral_mostrar = session('DescuentoGral');

        // Aca cliente mount 
        $this->SetClienteMount();
        
        // Aca metodo de pago 
        $this->SetMetodoPagoMount();
        
        // Aca seteamos el iva elegido
		$this->SetIvaElegido();
        
        // Setear el pago parcial 
        $this->SetPagoParcialMount();

        // ACA LISTA PRECIOS
        $this->SetListaPreciosDefecto();
        $this->SetNombreLista($this->lista_precios_elegida);			
			
    	$this->caja_abierta = cajas::where('estado',0)->where('eliminado',0)->where('comercio_id',$this->comercio_id)->max('id');
		$this->GetCajaElegida();

		$this->ultimas_cajas = cajas::where('comercio_id', $this->comercio_id)->where('eliminado',0)->orderby('created_at','desc')->limit(5)->get();
			
			
		$caja = cajas::where('estado',0)->where('eliminado',0)->where('comercio_id',$this->comercio_id)->max('id');
		
		// Setear el tipo de comprobante
		$this->tipo_comprobante = $this->setTipoComprobante($this->comercio_id, $this->tipo_comprobante);
		
        // Setear todos los bancos y metodos de pago
        $this->SetMetodosPagosYBancosMount();
        
		$this->completa_formulario = User::find(Auth::user()->id);
		$this->setWooCommerce($this->comercio_id);

		$this->atributos_var = atributos::where('comercio_id', $this->comercio_id)->get();	
					
		$cart_variaciones = new CartVariaciones;
		$this->cart_variaciones = $cart_variaciones->getContent();
		$this->usuario = Auth::user()->name;	
			
        $this->df = $this->getDatosFacturacion($this->comercio_id); //ok

		//Flag SaveSale			
		$this->stateSaveSale = false;

    	
        // 9-1-2024
        $this->SetCliente($this->query_id);
        if($this->cliente->sucursal_id != 0){
        $sucursal_id_cliente = $this->GetSucursalUserId($this->cliente->sucursal_id);    
        $this->bancos_sucursal = $this->getTipoPago($sucursal_id_cliente); 
        }else {
        $this->bancos_sucursal = [];
        } 
            
        $this->tipo_pago_sucursal = 1;
    	//dd($this->bancos_sucursal);

        // 2-5-2024
        
        $this->puntos_venta_listado = $this->setPuntosVentaMount();
        $this->datos_punto_venta_elegido = $this->puntos_venta_listado->first() ? $this->puntos_venta_listado->first() : null;
        $this->punto_venta_elegido = $this->puntos_venta_listado->first() ? $this->puntos_venta_listado->first()->id : null;


        
        //
        
        $this->CalcularTotales();		
    		
			
	}

    protected function redirectLogin()
    {
        return \Redirect::to("login");
    }
    
	public function render()
	{
        if (!Auth::check()) {
            // Redirigir al inicio de sesi¨®n y retornar una vista vac¨ªa
            $this->redirectLogin();
            return view('auth.login');
        }
     
        $this->metodos_todos = $this->getMetodosPagoTodos($this->comercio_id);
        
        $this->idVenta = session('idVenta');
        $this->sucursal_id = session('sucursal_id');
        $this->casa_central_id = session('casa_central_id');
        $this->sucursales = session('sucursales');
        $this->provincias = provincias::orderBy('provincia','asc')->get();
        if($this->es_pago_dividido == null){$this->es_pago_dividido = 0;}
        
		return view('livewire.pos_nuevo.component', [
			// ACA ENVIO 
		    'provincias' => $this->provincias,
		    //
			'categories' => Category::orderBy('name','asc')->where('comercio_id', 'like', $this->comercio_id)->where('eliminado',0)->get(),
			'almacenes' => seccionalmacen::orderBy('nombre','asc')->where('comercio_id', 'like', $this->comercio_id)->get(),
			'prov' => proveedores::orderBy('nombre','asc')->where('comercio_id', 'like', $this->comercio_id)->get(),
			'metodos' => $this->metodos,
			'tipos' => $this->tipos_pago,
			'sucursales' => $this->sucursales,
			'variaciones' => $this->variaciones,
			'atributos_var' => $this->atributos_var,
			'productos_variaciones' => $this->productos_variaciones,
			'completa_formulario' => $this->completa_formulario,
			'bancos_metodo_pago' => $this->bancos_metodo_pago,
			'plataformas_metodo_pago' => $this->plataformas_metodo_pago,
			'user' => User::where('comercio_id', 'like', $this->comercio_id)
			->orWhere('id', 'like', $this->usuario_id)->orderBy('id','asc')->get(),
			'cart' => Cart::getContent()->sortByDesc(function ($cart) {
          		return $cart->attributes->get('added_at');
       		 })
		])
		->extends('layouts.theme-pos.app')
		->section('content');
	}



public function ChequearVentas() {
    
    $venta = Sale::where('comercio_id',$this->comercio_id)->orderBy('id','desc')->first();
    
    if($venta != null) {
    
    $detalle_venta = SaleDetail::where('sale_id',$venta->id)->first();
    
    $pago = pagos_facturas::where('id_factura',$venta->id)->get();
    
    if($venta != null && $detalle_venta == null) {
    $venta->delete();
    
    foreach($pago as $id) {
    
    $pagos = pagos_facturas::find($id->id);
    $pagos->eliminado = 1;
    $pagos->save();
    
    }
    
    }
    
    }

}

public function ChequearVentas2() {
    
    $venta = Sale::where('comercio_id',$this->comercio_id)->orderBy('id','desc')->get();
    
    $error = [];
   foreach($venta as $v) { 
       
    $detalle_venta = SaleDetail::where('sale_id',$v->id)->where('eliminado',0)->first();
    
  //  $pago = pagos_facturas::where('id_factura',$v->id)->first();
    
    if($detalle_venta == null) {
    
    $sale = Sale::find($v->id);
    
    $sale->status = "Cancelado";
    $sale->save();
    
    array_push($error, $v->id);    
    }
    
    }
    

}

// ............ FORMULARIO ........... //

	public function CuentaConSistema($value)
	{

		$this->emit('modal-formulario','form');

		$this->cuenta_con_sistema = $value;
	}

	public function GuardarInfoFormulario()
	{


		if($this->cantidad_empleados == "Elegir")
		{
			$this->emit('formulario','');
			$this->emit('sale-error','DEBE ELEGIR LA CANTIDAD DE EMPLEADOS');
			return;
		}

		if($this->cuenta_con_sistema == "Elegir")
		{
			$this->emit('formulario','');
			$this->emit('sale-error','DEBE ELEGIR SI CUENTA CON UN SISTEMA');
			return;
		}







		if($this->cuenta_con_sistema == "Si") {

			if($this->urgencia == "Elegir")
			{

				$this->emit('sale-error','DEBE ELEGIR LA URGENCIA');
				$this->emit('formulario','');
				return;
			}

			if($this->motivo_si == [])
			{

				$this->emit('sale-error','DEBE ELEGIR EL MOTIVO');
				$this->emit('formulario','');
				return;
			}





		$this->motivo_si = json_encode($this->motivo_si);

		$respuesta = new formulario_respuesta;

		$respuesta->numero_formulario = 1;
		$respuesta->numero_pregunta = 1;
		$respuesta->user_id = Auth::user()->id;
		$respuesta->respuesta = $this->cantidad_empleados;
		$respuesta->save();

		$respuesta = new formulario_respuesta;

		$respuesta->numero_formulario = 1;
		$respuesta->numero_pregunta = 2;
		$respuesta->user_id = Auth::user()->id;
		$respuesta->respuesta = $this->cuenta_con_sistema;
		$respuesta->save();

		$respuesta = new formulario_respuesta;

		$respuesta->numero_formulario = 1;
		$respuesta->numero_pregunta = 6;
		$respuesta->user_id = Auth::user()->id;
		$respuesta->respuesta = $this->urgencia;
		$respuesta->save();

		$respuesta = new formulario_respuesta;

		$respuesta->numero_formulario = 1;
		$respuesta->numero_pregunta = 5;
		$respuesta->user_id = Auth::user()->id;
		$respuesta->respuesta = $this->motivo_si;
		$respuesta->save();

        $user = User::find(Auth::user()->id);
		$user->completo_formulario = 1;
		$user->save();

		$this->emit('formulario-hide', 'Gracias por su respuesta!');
		}

		if($this->cuenta_con_sistema == "No") {

			if($this->importancia == "Elegir")
			{
				$this->emit('sale-error','DEBE ELEGIR LA IMPORTANCIA');
				$this->emit('formulario','');
				return;
			}

			if($this->motivo_no == [])
			{

				$this->emit('sale-error','DEBE ELEGIR EL MOTIVO');
				$this->emit('formulario','');
				return;
			}

		$this->motivo_no = json_encode($this->motivo_no);

		$respuesta = new formulario_respuesta;

		$respuesta->numero_formulario = 1;
		$respuesta->numero_pregunta = 1;
		$respuesta->user_id = Auth::user()->id;
		$respuesta->respuesta = $this->cantidad_empleados;
		$respuesta->save();

		$respuesta = new formulario_respuesta;

		$respuesta->numero_formulario = 1;
		$respuesta->numero_pregunta = 2;
		$respuesta->user_id = Auth::user()->id;
		$respuesta->respuesta = $this->cuenta_con_sistema;
		$respuesta->save();

		$respuesta = new formulario_respuesta;

		$respuesta->numero_formulario = 1;
		$respuesta->numero_pregunta = 3;
		$respuesta->user_id = Auth::user()->id;
		$respuesta->respuesta = $this->importancia;
		$respuesta->save();

		$respuesta = new formulario_respuesta;

		$respuesta->numero_formulario = 1;
		$respuesta->numero_pregunta = 4;
		$respuesta->user_id = Auth::user()->id;
		$respuesta->respuesta = $this->motivo_no;
		$respuesta->save();

		$user = User::find(Auth::user()->id);
		$user->completo_formulario = 1;
		$user->save();

		$this->emit('formulario-hide', 'Gracias por su respuesta!');

		}

	}

// ......................................... //





	// agregar efectivo / denominations
	public function ACash($value)
	{
			$this->metodo_pago = session('MetodoPago');

			if($this->metodo_pago  != null) {
			$this->metodo_pago = metodo_pago::find($this->metodo_pago);
            $this->metodo_pago_acash = $this->metodo_pago->recargo;
            $this->recargo = $this->metodo_pago->recargo;
			} else {
			$this->metodo_pago = 1;
            $this->metodo_pago_acash = 0;
            $this->recargo = 0;
			}



		    $this->carro = Cart::getContent();
                //
            $this->sum_iva = $this->carro->sum(function($item){
              return (($item->price * $item->quantity) - ( ($item->price * $item->quantity) * ( ($item->attributes['descuento']/100) ) )  + (($item->price * $item->quantity)* ($this->recargo/100) )) * $item->attributes['iva']  ;
            });
            
            // 7-6-2023
           // $this->sum_descuento = $this->sumarDescuento($this->carro,$this->recargo);

            $this->sum_descuento = $this->carro->sum(function($item){
                return $item->price * $item->quantity * ($item->attributes['descuento']/100)  ;
            });

            $this->subtotal = Cart::getTotal();

            $this->total = Cart::getTotal() + $this->sum_iva - $this->sum_descuento;

            $this->efectivo = $this->total;
            $this->recargo_total = ($this->subtotal - $this->sum_descuento)  * ($this->metodo_pago_acash/100);
            $this->itemsQuantity = Cart::getTotalQuantity();

            $this->change = 0;


	}

    
    // 8-1-2024
	public function cambio($value)
	{
        if ($value === "") {
            $value = 0;
        }
        
		$this->efectivo = $value;
        $this->metodo_pago = session('MetodoPago');
        
        
        // Seteamos el metodo de pago
		if($this->metodo_pago == null) {

			$this->metodo_pago = 1;
			$this->metodo_pago_nuevo = 1;
			$this->recargo = 0;

		} else {
			$this->metodo_pago = session('MetodoPago');
			$this->metodo_pago_nuevo = session('MetodoPago');

        }
		
		// aca esta mal cuando es cambio 
        $this->CalcularTotales();
        
        //dd($this->change);
		return $this->change;   
            
	}


	public function descuento($value)
	{
		if(empty($value)) {
			$this->descuento_ratio = 0;
		} else {
		$this->descuento = $value;

		$this->descuento_ratio = $value/100;


		$this->pago_total = $this->efectivo + $this->recargo_total;

		$this->descuento_total = $this->pago_total * ($this->descuento_ratio);

		$this->a_cobrar = 	$this->pago_total - 	$this->descuento_total;

		if($this->pago_parcial == 0) {

			$this->efectivo = $this->total;

		}

        // ver este
		$this->change = $this->total - $this->descuento_total - $this->a_cobrar;


	}

	}

	public function EliminarMoneda($value)
	{
		$this->efectivo = $value;
		$this->change = 0;
		$this->recargo_total = 0;
	}


	public function UpdateEstado($estado_id)
	{

	$this->estado_pedido = $estado_id;

	 session(['EstadoPedido' => $this->estado_pedido]);

	 	$this->emit('modal-estado-hide','close');

	}

	public function CerrarModalEstado()
	{



		$this->emit('modal-estado-hide','close');

			$this->tipo_click = 0;

	}


	public function TipoComprobante($value)
	{
session(['TipoComprobante' => $value]);
}
//
public function PagoDividido(){
    
    $this->CheckPagoParcial(1);
    
    $this->metodo_pago = 2;
    $this->tipo_pago = 2;
    session(['MetodoPago' => $this->metodo_pago]);
    
    $this->es_pago_dividido = 1;
    session(['PagoDividido' => $this->es_pago_dividido]);
    
    $this->style_pago_dividido = "display:block;";
    $this->style_metodo_pago = "display:none;";
    
    
}

public function TipoPago($value)
{

// 9-1-2024
// si el cliente es una sucursal y el tipo de cobro es en efectivo el tipo de pago de la sucursal es el efectivo
if($this->cliente != null){
if($this->cliente->sucursal_id != null) {
    if($value == 1) {$this->tipo_pago_sucursal = 1;}
}
}


//    dd($value);
	if($value == 'OTRO') {
	
	$this->muestra_sucursales[$this->comercio_id] = true;    
	

	$this->emit('tipo-pago-nuevo-show','Sales');
	}


if($value == '3') {
$this->metodo_pago = $value;
}

if($value == '1' || $value == '2' || $value == '3') {

$this->metodo_pago = $value;

$this->metodo_pago = $value;
$this->metodo_pago_nuevo = $value;
session(['MetodoPago' => $value]);


if($value == '1') {
    $this->recargo = 0;
    $this->recargo_total = 0;
    $this->metodo_pago = 1;
    $this->metodo_pago_nuevo = 1;
    session(['MetodoPago' => 1]);
	$this->MetodoPago(1);
}



} else {
	$this->metodo_pago = 'Elegir';
}

    
// AGREGADO 6-6-2023
$this->metodos = $this->getMetodosPago($this->comercio_id);
//

}




		public function MetodoComprobante($value)
	{

//update metodo comprobante
					$user = User::find(Auth::user()->id);
					$user->comprobante = $value;
					$user->save();

					$this->comprobante = $value;


	}


public function SwitchFormaCobro($es_pago_dividido_original,$es_pago_dividido_nuevo,$valor_pago_parcial){
    
    $this->es_pago_dividido = $es_pago_dividido_nuevo;
    session(['PagoDividido' => $es_pago_dividido_nuevo]);
    
    
    // CUANDO ES PAGO DIVIDIDO
    if($es_pago_dividido_nuevo == 1) {
    $this->monto_ap_div = [];
    $this->iva_pago_dividido = [];
    $this->metodos_pago_dividido = [];
    $this->efectivo_dividido = [];
    $this->a_cobrar = [];
    $this->recargo_div = [];
    $this->recargo_total_div = [];
    $this->TipoPago("1"); 
    $this->tipo_pago = 1;
    $this->PagoDividido();  
    $this->guardarPagoDividido();
    } 
    
    // CUANDO ES PAGO TOTAL O PARCIAL
    if($es_pago_dividido_nuevo == 0) {
    
    session(['metodos_pago_dividido' => null]);
    $this->metodos_pago_dividido = [];

    if($es_pago_dividido_original == 1) {
    $this->TipoPago("1"); 
    $this->tipo_pago = 1;
    } 

    $this->style_pago_dividido = "display:none;";
    $this->style_metodo_pago = "display:block;";
    $this->CheckPagoParcial($valor_pago_parcial);  
    } 

}


public function CheckPagoParcial($value)
{

if($value == "1") {
	$user = User::find(Auth::user()->id);
	$user->pago_parcial = "0";
	$user->save();
	$this->pago_parcial = "0";
	$this->check = '';
	if($this->total != null) { $this->efectivo = $this->total;}
    $this->CalcularTotales();
}

if($value == "0") {
	$user = User::find(Auth::user()->id);
	$user->pago_parcial = "1";
	$user->save();
	$this->pago_parcial = "1";
	$this->check = 'checked';
	$this->efectivo = 0;
    $this->CalcularTotales();

}

}



	// escuchar eventos
	protected $listeners = [
		'deletePago' => 'DeletePago',
		'scan-code'  =>  'ScanCode',
		'scan-code-sucursal'  =>  'ScanCodeSucursal',
		'set-barcode'  =>  'SetBarcode',
		'removeItem' => 'removeItem',
		'clearCart'  => 'clearCart',
		'saveSale'   => 'saveSale',
		'update-cliente'   => 'UpdateListaPrecio',
		'refresh' => '$refresh',
		'info-producto' => 'InfoProducto',
		'print-last' => 'printLast',
		'print' => 'Print',
		'emitir-factura' => 'EmitirFactura',
		'QuitarPromo',
		'IrPasoPosterior' => 'IrPasoPosterior',
		'IrPasoAnterior' => 'IrPasoAnterior'
	];

public function IrPasoAnterior(){
    if($this->nro_paso == 1){
        $this->clearCart();
    }
    if($this->nro_paso == 2){
        $this->IrPaso1();
    }

}

public function IrPasoPosterior(){
//    dd($this->nro_paso);
    if($this->nro_paso == 2){
        $this->saveSale();
    }
    if($this->nro_paso == 1){
        $this->IrPaso2();
    }

}

    
    public function IrPaso3() {
        
            $this->paso3_style = "block;";
            $this->paso2_style = "none;";
            $this->paso1_style = "none;";
            $this->nro_paso = 3;
            
    }
    
    public function IrPaso2() {
        
            $this->paso3_style = "none;";
            $this->paso2_style = "block;";
            $this->paso1_style = "none;";
            $this->nro_paso = 2;
            
    }
    
    
    public function IrPaso1() {
        
            $this->paso1_style = "block;";
            $this->paso2_style = "none;";
            $this->paso3_style = "none;";
            $this->nro_paso = 1;
            
    }
    
public function MetodoPago($value)
{

	if($value == 'OTRO') {
		$this->emit('metodo-pago-nuevo-show','Sales');
		return;
	}
	elseif($value == '2') {
		$this->emit('pago-dividido','Sales');
	}
	else 
	{
	 // setea metodos de pago
	 $metodo_pago = metodo_pago::find($value);
	 session(['MetodoPago' => $value]);
	 $this->metodo_pago_nuevo = $metodo_pago->id;

     $this->CalcularTotales();

	    
	}
}

	// Ver el stock en sucursales
	public function InfoProducto($id)
	{
		$array = explode("-",$id);

		$product_id = $array[0];
		$variacion = $array[1];

		if($variacion != 0) {
			$variacion2 = $array[2];
			$variacion = $variacion."-".$variacion2;
		} else {
		$variacion = $variacion;
		}

		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		$this->tipo_usuario = User::find(Auth::user()->id);

		if($this->tipo_usuario->sucursal != 1) {


			$this->casa_central_id = $comercio_id;

			$this->stock_sucursales_pasar = productos_stock_sucursales::join('products','products.id','productos_stock_sucursales.product_id')
			->join('sucursales','sucursales.sucursal_id','productos_stock_sucursales.sucursal_id')
			->join('users','users.id', 'sucursales.sucursal_id')
			->select('products.*','productos_stock_sucursales.stock as stock_sucursal','productos_stock_sucursales.referencia_variacion','productos_stock_sucursales.sucursal_id','users.name as nombre_sucursal')
			->where('productos_stock_sucursales.comercio_id', $this->casa_central_id)
			->where('productos_stock_sucursales.product_id', $product_id)
			->where('productos_stock_sucursales.referencia_variacion', $variacion)
			->get();



		} else {

			$this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
			$this->casa_central_id = $this->casa_central->casa_central_id;

			// STOCK EN EL RESTO DE LAS SUCURSALES MENOS LA ACTUAL //

			$this->stock_sucursales_pasar = productos_stock_sucursales::where('comercio_id', $this->casa_central_id)
			->where('sucursal_id','<>',$comercio_id)
			->where('product_id', $product_id)
			->where('productos_stock_sucursales.referencia_variacion', $variacion)
			->get();

				// STOCK EN LA CASA CENTRAL //

				$this->producto_casa_central_pasar  = productos_stock_sucursales::join('products','products.id','productos_stock_sucursales.product_id')
				->join('sucursales','sucursales.sucursal_id','productos_stock_sucursales.sucursal_id')
				->join('users','users.id', 'sucursales.sucursal_id')
				->select('products.*','productos_stock_sucursales.stock as stock_sucursal','productos_stock_sucursales.referencia_variacion','productos_stock_sucursales.sucursal_id','users.name as nombre_sucursal')
				->where('productos_stock_sucursales.comercio_id', $this->casa_central_id)
				->where('productos_stock_sucursales.product_id', $product_id)
				->where('productos_stock_sucursales.referencia_variacion', $variacion)
				->get();

				///////////////////////////////

		}


			$this->emit('info-prod','');


	}

	// buscar y agregar producto por escaner y/o manual
	public function ScanCode($barcode, $cant = 1)
	{

		$this->cliente_lp_id = $this->query_id;

		$this->ScanearCode($barcode, $cant);

	}


	// buscar y agregar producto por escaner y/o manual

	public function ScanearVariacion($barcode)
	{

	    $cant = 1;
		//$this->ScanearCodeVariacion($barcode, $cant);

		$this->ScanearCode($barcode, $cant, true);

		//return dd($barcode);

		$this->emit('variacion-elegir-hide','');

        $this->CalcularTotales();

	}




	// buscar y agregar producto por escaner y/o manual
	public function ScanCodeSucursal($product, $cant = 1)
	{

		$this->cliente_lp_id = $this->query_id;

		$this->ScanearCodeSucursal($product, $cant);
		//$this->ScanearCode(null, $cant, $product);

		$this->emit('hide-info-prod','');


        $this->CalcularTotales();
	}

	// incrementar cantidad item en carrito
	public function increaseQty($id, $product, $variacion, $sucursal, $cant = 1)
	{
	    
		$this->IncreaseQuantity($id, $product, $variacion, $sucursal, $cant);

        $this->CalcularTotales();
	}

	// actualizar cantidad item en carrito
	public function removeItem($productId)
	{
            //dd($productId);
            
			$this->removeItems($productId);
			
            $this->CalcularTotales();
	}

	// actualizar cantidad item en carrito
	public function updateQty($id, $product, $variacion, $sucursal, $cant = 1)
	{

		if($cant <=0)
			$this->removeItem($id);
		else
			//$this->UpdateQuantity($id, $product,$variacion ,$sucursal, $cant);
			$this->updateQuantity($id, $product,$variacion ,$sucursal, $cant);
			

            $this->CalcularTotales();
            

	}

	// actualizar el precio del item en carrito
	public function updatePrice($id, $product, $variacion, $sucursal, $price = 1)
	{

			$this->UpdatePrecio($id, $product, $variacion, $sucursal, $price);

			$this->descuento_total = ($this->total + $this->recargo_total)* ($this->descuento_ratio);

            $this->CalcularTotales();

	}

	// actualizar el iva del item en carrito (no se usa por ahora)
	public function UpdateIva($id, $product,$variacion, $sucursal, $cant = 1)
	{
			$this->Update_Iva($id, $product,$variacion ,$sucursal, $cant);

			$this->descuento_total = ($this->total + $this->recargo_total)* ($this->descuento_ratio);

        $this->CalcularTotales();

	}

    // actualizar el iva en todo el carrito 
    
    public function UpdateIvaGral($iva) {
        	
        	
        	session(['IvaElegido' => $iva]);
        	$this->iva_elegido  = $iva;
        	
        	$this->Update_Iva_Gral($iva);

			$this->descuento_total = ($this->total + $this->recargo_total)* ($this->descuento_ratio);

            $this->CalcularTotales();
    
            //dd($this->iva_elegido);
    }


	// actualizar el precio del item en carrito
	public function updateDescuento($id, $product, $variacion,$sucursal, $cant = 1)
	{
	        if(empty($cant)) { $cant = 0; }        
	        $cant = str_replace(",",".",$cant);
	        
			$this->Update_descuento($id, $product,$variacion ,$sucursal, $cant);

			$this->descuento_total = ($this->total + $this->recargo_total)* ($this->descuento_ratio);

        $this->CalcularTotales();

	}

	// actualizar el precio del item en carrito
	public function updateDescuentoGral($descuento)
	{
	    
	    if(empty($descuento)) { $descuento = 0; }
        $descuento = str_replace(",",".",$descuento);
            
			$descuento_gral_mostrar = $descuento;
			session(['DescuentoGral' => $descuento]);
        
			$this->Update_descuento_gral($descuento);

			$this->descuento_total = ($this->total + $this->recargo_total)* ($this->descuento_ratio);

        $this->CalcularTotales();

	}

	public function UpdateListaPrecio(){
	$this->UpdateCliente();
	// recalcular deuda
	$this->deuda = $this->calcularDeuda($this->total,$this->recargo_total, $this->descuento_total, $this->efectivo, $this->pago_parcial,$this->sum_iva_pago,$this->sum_iva_recargo,$this->relacion_precio_iva);
    $this->CalcularTotales();
	}


	// decrementar cantidad item en carrito
	public function decreaseQty($id, $product, $variacion, $sucursal, $cant = 1)
	{
		$this->decreaseQuantity($id, $product, $variacion, $sucursal, $cant);

        $this->CalcularTotales();
	}



	// vaciar carrito
	public function clearCart()
	{
		$this->trashCart();
	}


	public function saveSale(){
	    
	    
	    if($this->es_pago_dividido == 1) {
	      $r = 0;
	  //    $r = $this->ValidarPagoDivididoSaveSale();
	      if($r == 1){return;}
	    }
	    
	    // 17-01-2024
		//Agregar en tabla sale, columna idVenta
		$IdVentasCheck = sale::where('id_venta', $this->idVenta)->first();
		
		if($IdVentasCheck !== null){
			return; //dd('el codigo de venta ya existe');
		}else{
		    
		// Validacion consumidor final con tipo de comprobante B
		$this->ValidacionComprobanteB();
		
		//Validacion
		if($this->validacionDatosSalesSave() === true){
			return;
		}
	
	    // Set nro venta 
	    $this->nro_venta = $this->SetNroVenta();
		//Set estado pedido
		$this->estado_pedido = $this->setEstadoPedido($this->estado_pedido);
		
		//Set caja
		//dd($this->caja);
		$this->caja = $this->GetCajaElegida();
		//dd($this->caja);
		//$this->caja = $this->setCaja($this->caja, $this->comercio_id);

		//Set recordatorio
		$this->recordatorio = $this->setRecordatorioFecha($this->recordatorio);
		
		//Set deuda
		$this->deuda = $this->calcularDeuda($this->total,$this->recargo_total, $this->descuento_total, $this->efectivo, $this->pago_parcial,$this->sum_iva_pago,$this->sum_iva_recargo,$this->relacion_precio_iva);

        //Set total
		$this->total = $this->calcularTotalSale($this->total, $this->recargo_total );
        // Set recargo
		//$this->recargo_total = $this->calcularRecargoSaveSale($this->recargo_total, $this->sum_iva_recargo,$this->relacion_precio_iva);
        
		//Set  fecha creacion
		$this->created_at = $this->setDateCreate($this->created_at_cambiado);

		// obtenemos el cliente	
		$this->cliente = ClientesMostrador::find($this->query_id);	
					
        // Set canal de ventas
        $this->canal_venta = $this->setCanalVentas($this->canal_venta,$this->cliente->sucursal_id);

	    
	    
		DB::beginTransaction();
		try {
				$sale = $this->SetSaleDB();	
                
				if($this->recordatorio)	{							
						//Setea en DB pago recordatorio
						$pagos = $this->setPagosRecordatorioDB($sale);		
				}	
	
				if($sale)
				{					
					// Poner el metodo de envio 
					if($this->check_envio == true){				
						$this->checkEnvio($this->check_envio, $sale);
					}
				  
					// Poner el metodo de envio 
					
					
					if($this->check_envio_cliente == true)
					{				
						$this->checkEnvio($this->check_envio_cliente, $sale);
					}
					
					
                    // Setear si hay pago dividido
					if($this->metodo_pago_nuevo == 2) {
    					foreach($this->metodos_pago_dividido as $value){    
    					  //  dd($value['efectivo'],$value['recargo_total_div'],$value['iva_pago_dividido'], $value['iva_recargo_dividido']);
    					    
    						$pagos = $this->setPagosFacturasDB( ($value['efectivo']+$value['recargo_total_div']),$this->total, $value['recargo_total_div'], null, $sale, $value['metodo_pago_ap_div'],$this->comercio_id,$this->relacion_precio_iva,$value['iva_pago_dividido'], $value['iva_recargo_dividido']);
    					}
					}
					else
					{
						
						$pago = $this->setPagosFacturasDB($this->efectivo, $this->total, $this->recargo_total, $this->change, $sale, $this->metodo_pago_nuevo,$this->comercio_id,$this->relacion_precio_iva,$this->sum_iva_pago,$this->sum_iva_recargo);
					    
					    
					}
		
					if($this->metodo_pago_nuevo == '3') {
						$cheques =  $this->setChequesDB($sale);	
					}	

				    //9-1-2024
				    if($this->cliente->sucursal_id != 0) {
				    $compra_id = $this->SetCompraEnSucursal($sale);
				    $sucursal_id_cliente = $this->GetSucursalUserId($this->cliente->sucursal_id); 
				    $this->sucursal_id_cliente = $sucursal_id_cliente;
				    $this->efectivo_real = $this->setEfectivoReal($this->efectivo, $this->total);
					$pago_sucursal = $this->SetPagosFacturasSucursalDB($pago,$this->efectivo_real, $this->recargo_total, $this->change, $compra_id, $this->tipo_pago_sucursal,$sucursal_id_cliente);
				    } else {
				    $compra_id = null;
				    }
				    //
				
					$items = Cart::getContent();	
	
					foreach ($items as  $item) {							
						$this->porcentaje = $this->calcularPorcentage($item, $this->total);
						$this->descuento_item = $this->calcularDescuentoItem($item);
						$this->recargo_item = $this->calcularRecargoItem($item, $this->descuento_item, $this->recargo);
						$sale_details_id = $this->setSaleDetailsDB($sale, $item,$compra_id);	
						
						// hasta aca no se actualizo stock
						//$this->ChequearCoincidencias($sale,$sale_details_id,$pago);
						//
						
						
						$this->tipo_usuario = User::find($item->attributes->sucursal_id);
						$product = Product::find($item->attributes->product_id);
						
				    	$productos_stock_sucursales =  $this->getProductStockSucursales($item, $this->tipo_usuario->sucursal);
						$productos_stock_sucursales_nuevo =  $this->calcularProductStockSucursalesNuevo($item, $productos_stock_sucursales);		
						$productos_stock_real_sucursales_nuevo =  $this->calcularProductStockRealSucursalesNuevo($item, $productos_stock_sucursales,$this->estado_pedido);
						
					
						$this->SSA = $productos_stock_sucursales->stock;
							
						$r = $this->setUpdateStockDB($this->tipo_usuario->sucursal, $item, $productos_stock_sucursales_nuevo,$productos_stock_real_sucursales_nuevo);	
						
						// ACA TENEMOS QUE SETEAR EL STOCK DE LA SUCURSAL 
						 if($this->cliente->sucursal_id != 0) {
						$sucursal_id_venta = sucursales::find($this->cliente->sucursal_id)->sucursal_id;
						$productos_stock_sucursales_venta_sucursal =  $this->getProductStockSucursalesVentaSucursal($item, $sucursal_id_venta);
						
						$productos_stock_sucursales_nuevo_venta_sucursal =  $this->calcularProductStockSucursalesNuevoVentaSucursal($item, $productos_stock_sucursales_venta_sucursal,$this->estado_pedido);		
						$productos_stock_real_sucursales_nuevo_venta_sucursal =  $this->calcularProductStockRealSucursalesNuevoVentaSucursal($item, $productos_stock_sucursales_venta_sucursal,$this->estado_pedido);
				       	
				       	// 9-1-2024
				       	$compra = compras_proveedores::find($compra_id);
				       	$r = $this->setUpdateStockDBVentaSucursal($compra,$this->tipo_usuario->sucursal, $item, $productos_stock_sucursales_nuevo_venta_sucursal,$productos_stock_real_sucursales_nuevo_venta_sucursal,$this->estado_pedido,$sucursal_id_venta);    
					    //$this->SetDetalleCompraEnSucursal($compra,$item);
					    //
				    	
						     
						 }
						// VERLO A TODO ESTO 
						
						$this->stock_historico = $productos_stock_sucursales->stock;
						
						if($this->tipo_usuario->sucursal != 1 && ($productos_stock_sucursales->stock < 0 && $product->tipo_producto == 2)) {	
						  //   dd($productos_stock_sucursales);
						  if($productos_stock_sucursales != null) {
						    $this->SSD = $productos_stock_sucursales->stock;  
						     $cantidad_asistente = $this->setCantidadAsistente($item, $this->SSD);	
							 $this->setAsistenteProduccionDB($product, $item, $cantidad_asistente, $sale);
							}

						}					
						$historico_stock = $this->setHistoricoStockDB($item, $sale, $productos_stock_real_sucursales_nuevo,$this->estado_pedido);		
						
						//dd($historico_stock);
						////////// WooCommerce ////////////	
						
					/*	$noEncontrados1 = $this->wooCommerce($product,$item,$productos_stock_sucursales_nuevo);	
					// aca almacenarlo en la base de datos
					// dd($noEncontrados);
					
					if($noEncontrados1 != []) {
					    
					   $noEncontrados1 = json_encode($noEncontrados1);
					   
					    errores::create([
					        'errores' => $noEncontrados1,
					        'tipo_error' => 'pos_variaciones',
					        'comercio_id' => $this->comercio_id
					        ]);
					}
					
					*/
					}	
					
					$this->cliente->update(['last_sale' => now()]);	
				}
	            
	            //dd($items);
	            
	            if($this->estado_pedido == "Entregado") {
	            $this->wooCommerceUpdateStockGlobal($sale,1);
	            }
				

				DB::commit();	
				Cart::clear();
	
				

				$this->saveSalesReset();
	
	
				if(Auth::user()->comprobante == 1) {	
					$this->emit('sale-ok','Venta registrada con Exito');	
					$this->ventaId = $sale;				
					$this->mail = $this->getMail($this->ventaId);		
					$this->emit('imprimir-show', $ventaId);		
				} 
				else 
				{	
					$this->emit('sale-ok','Venta registrada con Exito');	
					$this->emit('cheque-hide','Sales');	
					$this->emit('mensaje-facturar', $sale);	
				}
	
	
		} catch (\Exception $e) {
				DB::rollback();
				$this->emit('sale-error', $e->getMessage());
		}	
		
		}
		
	}
	
	
public function SwitchImprimir($accion, $venta_id) {
    $url = '';
    $f = facturacion::where('sale_id', $venta_id)->first();  

    if ($f == null) {
        if ($accion == 1) {
            $url = 'https://app.flamincoapp.com.ar/ticket' . '/' . $venta_id;
        } elseif ($accion == 2) {
            $url = 'https://app.flamincoapp.com.ar/report-factura' . '/' . $venta_id;
        }
    } else {
        if ($accion == 1) {
            $url = 'https://app.flamincoapp.com.ar/ticket-factura' . '/' . $f->id;
        } elseif ($accion == 2) {
            $url = 'https://app.flamincoapp.com.ar/imprimir-factura/pdf' . '/' . $f->id;
        }
    }

    $this->emit("imprimir-nueva-ventana",$url);
}


public function EmitirFactura($ventaId) {

    // 2-5-2024
    
    $pto_venta_elegido = $this->punto_venta_elegido;
    
	$this->ventaId = $ventaId;

	$this->FacturarAfip($ventaId,$pto_venta_elegido);

	$this->mail = Sale::join('clientes_mostradors as c','c.id','sales.cliente_id')
	->select('c.email', 'sales.cash','sales.status','sales.id')
	->where('sales.id', $ventaId)
	->get();

	$this->emit('imprimir-show', $ventaId);


}


public function EmitirFacturaOLD($ventaId) {

	$this->ventaId = $ventaId;

	$this->FacturarAfip($ventaId);

	$this->mail = Sale::join('clientes_mostradors as c','c.id','sales.cliente_id')
	->select('c.email', 'sales.cash','sales.status')
	->where('sales.id', $ventaId)
	->get();

	$this->emit('imprimir-show', $ventaId);


}

public function Print($ventaId) {

	$this->ventaId = $ventaId;

	$this->mail = Sale::join('clientes_mostradors as c','c.id','sales.cliente_id')
	->select('c.email', 'sales.cash','sales.status')
	->where('sales.id', $ventaId)
	->get();

	$this->emit('imprimir-show', $ventaId);


}


	public function printTicket($ventaId)
	{
		return \Redirect::to("print://$ventaId");

	}

	public function Factura($ventaId)
	{

		$this->RenderFactura($ventaId);

	}

	public function resetUI()
	{
		$this->comentario = '';
		$this->recordatorio = '';
		$this->nota_interna = '';
		$this->paso1_style = "block;";
        $this->paso2_style = "none;";
        $this->paso3_style = "none;";
        $this->nro_paso = 1;
		
	}

	public function resetUICliente()
	{

	$this->name ='';
	$this->barcode ='';
	$this->cost ='';
	$this->price ='';
	$this->stock ='';
	$this->alerts ='';
	$this->almacen ='';
	$this->proveedor ='';
	$this->search ='';
	$this->inv_ideal ='';
	$this->categoryid ='Elegir';
	$this->image = null;
	$this->selected_id = 0;
	$this->stock_descubierto = 'si';

}

public function resetUIBanco()
{

$this->nombre_banco ='';
$this->tipo_banco ='Elegir';
$this->CBU ='';
$this->cuit_banco ='';


$this->emit('tipo-pago-nuevo-hide', '');

}



public function resetUIMetodoPago()
{

$this->nombre_metodo_pago = '';
$this->recargo_metodo_pago = '';
$this->cuenta_metodo_pago = '';
$this->categoria_metodo_pago = '';


$this->emit('metodo-pago-nuevo-hide', '');

}




	public function printLast()
	{
		$lastSale = Sale::latest()->first();

		if($lastSale)
			$this->emit('print-last-id', $lastSale->id);
	}


	    public function resetCliente()
	    {
	      $this->contacts = [];
	    }

          // 1-12-2023
          
	      public function selectContact(ClientesMostrador $cliente)
	      {
	          
	        //  dd($cliente);

	                    $this->query = $cliente->nombre;
						$this->query_id = $cliente->id;

						session(['NombreCliente' => $this->query]);
						session(['IdCliente' => $this->query_id]);

                        $this->SetCliente($this->query_id);
                        
                        
                        if($this->cliente != null) {						
                            
						    //dd($this->cliente->lista_precio != $this->lista_precios_elegida);
						    
						    if($this->check_envio_cliente == true){
						        $this->EnviosCliente();
						    }
						    
							if($this->cliente->lista_precio != $this->lista_precios_elegida) {
							    if($this->lista_precios_elegida == 0){
							        $this->emit('update-cliente-modal', ['query_id' => $this->query_id,'titulo' => "CLIENTE CON LISTA DE PRECIOS DISTINTA" ,'mensaje' => "Desea modificar los precios de los productos en el carrito? Esto eliminara tambien las promociones."]);

							    } else {
							    	$this->emit('update-cliente-modal', ['query_id' => $this->query_id,'titulo' => "CLIENTE CON LISTA DE PRECIOS DISTINTA" ,'mensaje' => "Desea modificar los precios de los productos en el carrito? En caso de haber descuento por promociones estas se calcularan."]);        
							    }
							
							}	
							
							// Sucursales
							if($this->cliente->sucursal_id != 0) {
							
					        $this->emit('update-cliente-modal', ['query_id' => $this->query_id,'titulo' => "SUCURSAL ELEGIDA" ,'mensaje' => "Desea modificar los precios del carrito a precio interno de venta a sucursal? Esto eliminara las promociones"]);
							
							// 9-1-2024
							$sucursal_id_cliente = $this->GetSucursalUserId($this->cliente->sucursal_id);    
							$this->bancos_sucursal = $this->getTipoPago($sucursal_id_cliente);
							//
							}	

                        }
                        
	          $this->resetCliente();
	          
	          $this->contacts = [];
	      }

	      public function updatedQuery()
	      {
					if(Auth::user()->comercio_id != 1)
					$comercio_id = Auth::user()->comercio_id;
					else
					$comercio_id = Auth::user()->id;

	          $this->contacts = ClientesMostrador::where('eliminado',0)->where('nombre', 'like', '%' . $this->query . '%')
								->where('comercio_id', 'like', $this->casa_central_id)
								->orWhere('comercio_id', 'like', 1)
	              ->get()
	              ->toArray();
	      }

				public function resetProduct()
			 {
				 $this->products_s = [];
			 }

				public function selectProduct()
	      {
	          $this->query_product = '';

	          $this->resetProduct();
	      }

	      public function updatedQueryProduct()
	      {
					if(Auth::user()->comercio_id != 1)
					$comercio_id = Auth::user()->comercio_id;
					else
					$comercio_id = Auth::user()->id;

					$this->tipo_usuario = User::find($comercio_id);



					if($this->tipo_usuario->sucursal != 1) {
						$this->casa_central_id = $comercio_id;
					} else {

						$this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
						$this->casa_central_id = $this->casa_central->casa_central_id;
					}



	          $this->products_s = 	Product::where('comercio_id', 'like', $this->casa_central_id)->where( function($query) {
							    $query->where('name', 'like', '%' . $this->query_product . '%')->orWhere('barcode', 'like', $this->query_product . '%');
							})
							->where('eliminado',0)
				->limit(25)
	              ->get()
	              ->toArray();


	      }


									 public function selectEstado()
									 {
										 $this->tipo_click = 0;

											 $this->emit('modal-estado','details loaded');

									 }


									 public function CerrarFactura() {
										 $this->emit('cerrar-factura','details loaded');
									 }




                                
                                	public function ElegirCaja($caja_id)
                                	{
                                    session(['CajaElegida' => $caja_id]);
                                	$this->caja_elegida = $caja_id;
                                	$this->nro_caja_elegida = cajas::find($this->caja_elegida)->nro_caja;
                                	$this->fecha_pedido = $this->fecha_ap;
                                	
                                	$this->emit("modal-estado-hide","");
                                
                                	}

								   public function AbrirCaja() {


									if(Auth::user()->comercio_id != 1)
									$comercio_id = Auth::user()->comercio_id;
									else
									$comercio_id = Auth::user()->id;

									$ultimo = cajas::where('cajas.comercio_id', 'like', $comercio_id)->select('cajas.nro_caja')->latest('nro_caja')->first();

									if($ultimo != null)
									$nro = $ultimo->nro_caja + 1;
									else
									$nro = 1;



									$cajas = cajas::create([
									  'user_id' => Auth::user()->id,
									  'comercio_id' => $comercio_id,
									  'nro_caja' => $nro,
									  'monto_inicial' => $this->monto_inicial,
									  'estado' => '0',
									  'fecha_inicio' => Carbon::now()

									]);

								   $this->caja_elegida = cajas::where('estado',0)->where('eliminado',0)->where('comercio_id',$this->comercio_id)->max('id');
								   $this->caja_abierta = $this->caja_elegida;
								   $this->nro_caja_elegida = cajas::find($this->caja_elegida)->nro_caja;
								   $this->ultimas_cajas = cajas::where('comercio_id', $this->comercio_id)->where('eliminado',0)->orderby('created_at','desc')->limit(5)->get();
								  }


public function CambioCaja() {


	$this->tipo_click = 1;

	if(Auth::user()->comercio_id != 1)
	$comercio_id = Auth::user()->comercio_id;
	else
	$comercio_id = Auth::user()->id;
    
    
    $from = Carbon::parse($this->fecha_ap)->format('Y-m-d').' 00:00:00';
    $to = Carbon::parse($this->fecha_ap)->format('Y-m-d').' 23:59:59';

    $this->lista_cajas_dia = cajas::where('comercio_id', $comercio_id)
    ->where('eliminado', 0)
    ->whereBetween('cajas.created_at', [$from, $to])
    ->get();
   //dd($this->lista_cajas_dia);
    
    $this->created_at_cambiado = $from;

    $this->emit('modal-estado','details loaded');


}

// Facturar con el trait de AFIP

public function FacturarAfip($ventaIdFactura,$pto_venta_elegido) {
   $this->EmitirFacturaTrait($ventaIdFactura,$pto_venta_elegido);
}

public function CodigoBarrasAfip($ventaId) {

  /////////////// CODIGO DE BARRAS AFIP ///////////////////

  $this->total_total2 = Sale::join('metodo_pagos as m','m.id','sales.metodo_pago')
  ->select('sales.recargo','sales.tipo_comprobante','sales.created_at','sales.descuento','sales.total','sales.created_at as fecha','sales.observaciones','sales.nota_interna', 'm.nombre as metodo_pago','sales.cae','sales.vto_cae','sales.nro_factura')
  ->where('sales.id', $ventaId)
  ->first();

  if(Auth::user()->comercio_id != 1)
  $comercio_id = Auth::user()->comercio_id;
  else
  $comercio_id = Auth::user()->id;

  $this->detalle_facturacion2 = datos_facturacion::where('datos_facturacions.comercio_id', $comercio_id)->first();

  /**
   * CUIT de la persona/empresa emitio la factura (11 caracteres)
   **/

  /**
   * Tipo de comprobante (2 caracteres, completado con 0's)
   **/
  if ($this->total_total2->tipo_comprobante == "A") {
   $tipo_de_comprobante = '01';
  }
  if ($this->total_total2->tipo_comprobante == "B") {
   $tipo_de_comprobante = '06';
  }
  if ($this->total_total2->tipo_comprobante == "C") {
   $tipo_de_comprobante = '011';
  }


  $cuit = $this->detalle_facturacion2->cuit;

  /**
   * Punto de venta (4 caracteres, completado con 0's)
   **/
   $porciones = explode("-", $this->total_total2->nro_factura);
   $tipo_factura = $porciones[0]; // porciÃ³n1
   $pto_venta = $porciones[1]; // porciÃ³n2
   $nro_factura_ = $porciones[2]; // porciÃ³n2
   $this->pto_venta = str_pad($pto_venta, 4, "0", STR_PAD_LEFT);


  $punto_de_venta = $this->pto_venta;

  /**
   * CAE (14 caracteres)
   **/
  $cae = $this->total_total2->cae;

  /**
   * Fecha de expiracion del CAE (8 caracteres, formato aaaammdd)
   **/
  $this->vto_cae = Carbon::parse($this->total_total2->vto_cae)->format('Ymd');

  $vencimiento_cae = $this->vto_cae;


  $barcode = $cuit.$tipo_de_comprobante.$punto_de_venta.$cae.$vencimiento_cae;

  $code = $cuit.$tipo_de_comprobante.$punto_de_venta.$cae.$vencimiento_cae;

  //Step one
  $number_odd = 0;
  for ($i=0; $i < strlen($code); $i+=2) {
    $number_odd += $code[$i];
  }

  //Step two
  $number_odd *= 3;

  //Step three
  $number_even = 0;
  for ($i=1; $i < strlen($code); $i+=2) {
    $number_even += $code[$i];
  }

  //Step four
  $sum = $number_odd+$number_even;

  //Step five
  $checksum_char = 10 - ($sum % 10);

  $this->barcode_ultimo = $checksum_char == 10 ? 0 : $checksum_char;

  $barcode .= $this->barcode_ultimo;

  /**
   * Mostramos por pantalla el numero del codigo de barras de 40 caracteres
   **/
  $this->codigo_barra_afip = $barcode;


}


public function MailModal($ventaId) {
    $this->ventaId = $ventaId;
     $this->emit('mail-modal', '');

}


public function EnviarMail() {


      return redirect('report-email/pdf' . '/' . $this->ventaId  . '/' . $this->mail_ingresado);

}


public function enviarNotificacion() {

	if(Auth::user()->comercio_id != 1)
	$comercio_id = Auth::user()->comercio_id;
	else
	$comercio_id = Auth::user()->id;

			 $esquema = User::all();

			 $notificacion = [
					 'titulo' => 'Nivel de stock bajo',
					 'contenido' => 'Vianda de pollo'
			 ];

			 Notification::sendNow($esquema, new NotificarCambios($notificacion));

			 dd('funciono');

	 }


public function Cheques(){
	$this->emit('cheque','Sales');
}


  public function BuscarClienteAFIP() {

  $cuit = $this->query;
  
  if(is_numeric($cuit)) {

  /**
  * Obtenemos los datos del contribuyente
  **/

  if(Auth::user()->comercio_id != 1)
  $comercio_id = Auth::user()->comercio_id;
  else
  $comercio_id = Auth::user()->id;

  $this->datos_facturacion = datos_facturacion::where('comercio_id', $comercio_id)->first();

// CAMBIO 6-6-2023

  $afip = new Afip(array('CUIT' => '20358072101', 'production' => true));


  /**
   * Obtenemos los datos del contribuyente
   **/
  $datos = $afip->RegisterScopeFive->GetTaxpayerDetails($cuit);

  if($datos === NULL){
  	$this->mensaje = 'El contribuyente no existe.';
  }
  else{
  	/**


  	 * Mostramos por pantalla los datos del contribuyente
  	 **/
  	 
if($datos->datosGenerales->tipoPersona == "JURIDICA") {
    
        $this->cuit = $datos->datosGenerales->idPersona;
       $this->nombre_cliente = $datos->datosGenerales->razonSocial;
       $this->tipo_clave = $datos->datosGenerales->tipoClave;
       $this->tipo_persona = $datos->datosGenerales->tipoPersona;


       $this->provincia = $datos->datosGenerales->domicilioFiscal->descripcionProvincia;
       $this->direccion = $datos->datosGenerales->domicilioFiscal->direccion;
       
       
       if($this->provincia != "CIUDAD AUTONOMA BUENOS AIRES") {
           $this->localidad = $datos->datosGenerales->domicilioFiscal->localidad;
       } else {
           $this->localidad = "";
       }
       
    
    
    
} else {
    
        $this->cuit = $datos->datosGenerales->idPersona;
        $this->nombre_cliente = $datos->datosGenerales->nombre;
        $this->apellido_cliente = $datos->datosGenerales->apellido;
        $this->tipo_clave = $datos->datosGenerales->tipoClave;
        $this->tipo_persona = $datos->datosGenerales->tipoPersona;


       $this->provincia = $datos->datosGenerales->domicilioFiscal->descripcionProvincia;
       $this->direccion = $datos->datosGenerales->domicilioFiscal->direccion;
       
       if($this->provincia != "CIUDAD AUTONOMA BUENOS AIRES") {
           $this->localidad = $datos->datosGenerales->domicilioFiscal->localidad;
       } else {
           $this->localidad = "";
       }
       
}


    $this->emit('datos-cliente','');

  }

//


}

}


  public function AgregarCliente()
  {
	if(Auth::user()->comercio_id != 1)
	$comercio_id = Auth::user()->comercio_id;
	else
    $comercio_id = Auth::user()->id;

    $cliente = ClientesMostrador::where('dni',$this->cuit)->where('comercio_id',$comercio_id)->first();

    if($cliente == null) {
    $this->nombre_cliente = $this->nombre_cliente." ".$this->apellido_cliente;
    $this->dni_cliente = $this->cuit;
    $this->pais_cliente = 1;
    $this->provincia_cliente = provincias::where('provincia',$this->provincia)->first()->provincia ?? '';
    $this->localidad_cliente = $this->localidad;
    $this->calle_cliente = $this->direccion;
    
    $this->ModalAgregarCliente();
    } else {
    $this->selectContact($cliente);
    $this->resetUICliente();    
    }

    $this->emit('datos-cliente-hide','');

  }
  
  
      // ACA ENVIO 
    
    // PARA ENVIOS A DOMICILIO 
    
    // ABRIR MODAL
    public function Envios2() {
    
    if($this->check_envio == true){
    
    $this->check_envio_cliente = false;
    $this->check_envio_cliente = "";
    
    $this->envio_visible = "block;";    
    session(['EnvioVisible' => $this->envio_visible]);
    $this->checked_envio = "checked";
    
    $this->nombre_envio = "";
    $this->ciudad_envio = "";
    $this->direccion_envio = "";
    $this->telefono_envio = "";
    $this->provincia_envio = "";
    
    } else {
    $this->envio_visible = "none;" ;    
    session(['EnvioVisible' => $this->envio_visible]);
    $this->checked_envio = "";
    }
    
    }

    
    public function RetiroSucursal() {
    
    if($this->check_retiro_sucursal == true){
    $this->check_envio = false;
    $this->check_envio_cliente = false;
    
    $this->checked_envio = "";
    $this->checked_envio_cliente = "";
    $this->checked_retiro_sucursal = "checked";
    
    
    $this->envio_visible = "none;" ;    
    session(['EnvioElegido' => 1]);
    session(['EnvioVisible' => $this->envio_visible]);
    

    } 
    
    }


    // ABRIR SECCION ENVIOS AL CLIENTE
    public function EnviosCliente() {
        
    if($this->query_id == 1) {
        $this->emit('msg-error','Debe incluir primero el cliente');
        $this->check_envio_cliente = false;
        $this->checked_envio_cliente = "";
        return;
    }
    
    if($this->check_envio_cliente == true){
    $this->check_envio = "";
    $this->check_envio = false;
    $this->check_retiro_sucursal = false;
    $this->check_envio_cliente = true;
    
    $this->checked_envio_cliente = "checked";
    $this->checked_envio = "";
    $this->checked_retiro_sucursal = "";
    
    $this->envio_visible = "block;" ;    
    session(['EnvioElegido' => 2]);
    session(['EnvioVisible' => $this->envio_visible]);
    
    //dd($this->cliente);
    
    if($this->cliente != null) {
    $this->nombre_envio  = $this->cliente->nombre;
    $this->telefono_envio = $this->cliente->telefono;
    $this->calle_envio = $this->cliente->direccion;
    $this->altura_envio = $this->cliente->altura;
    $this->piso_envio = $this->cliente->piso;
    $this->depto_envio = $this->cliente->depto;
    $this->cod_postal_envio = $this->cliente->codigo_postal;
    $this->ciudad_envio = $this->cliente->localidad;
    $this->provincia_envio = provincias::where('provincia',$this->cliente->provincia)->first()->id ?? '';
    } else {
    $this->nombre_envio  = "";
    $this->telefono_envio = "";
    $this->calle_envio = "";
    $this->altura_envio = "";
    $this->piso_envio = "";
    $this->depto_envio = "";
    $this->cod_postal_envio = "";
    $this->ciudad_envio = "";
    $this->provincia_envio = "";    
    }

    
    } 
    
    }

    
    public function Envios() {
    
    if($this->check_envio == true){
    $this->check_retiro_sucursal = false;
    $this->check_envio_cliente = false;
    
    $this->checked_envio = "checked";
    $this->checked_envio_cliente = "";
    $this->checked_retiro_sucursal = "";
    
    $this->envio_visible = "block;" ; 
    
    session(['EnvioElegido' => 3]);
    session(['EnvioVisible' => $this->envio_visible]);

    $this->nombre_envio  = "";
    $this->telefono_envio = "";
    $this->direccion_envio = "";
    $this->ciudad_envio = "";
    $this->provincia_envio = "";
        
    } 
    
    }


public function VerCatalogo() {
    $this->lista_productos = $this->GetProductosTodos();
    $this->emit('ver-catalogo','');
}


public function CloseCatalogo() {
    $this->emit('ver-catalogo-hide','');
}

public function CerrarFormulario(){
    $this->emit('modal-formulario-hide','');
}

public function ErrorFaltaMonto(){

//dd("hola");

$this->emit('falta-monto','');    
}


//10-12

public function ModalAgregarCliente(){
    $this->lista_precios = lista_precios::where('comercio_id',$this->casa_central_id)->get();
    $this->emit('modal-agregar-cliente','');
}

public function StoreCliente(){
    $this->sucursal_agregar_cliente = $this->comercio_id;
    $cliente = $this->StoreClienteTrait();
    $this->selectContact($cliente);
    //dd($cliente);
    $this->resetUICliente();
    $this->emit("modal-agregar-cliente-hide","Cliente agregado");
}


// 27-12-2023

public function SetBarcode($barcode)
{

$this->name = null;
$this->barcode = $barcode;
$this->categoryid = 1;
$this->alerts = 0;
$this->stock_descubierto = "no";
$this->mostrador_canal = true;
$this->cost = 0;
$this->precio_interno = 0;
$this->producto_tipo = "s";
$this->imagen_seleccionada = null;
$this->getVariaciones($this->casa_central_id); 
$this->GetDatosFacturacionDefectoProducto(1);

$this->emit('add-product', '');

}


public function StoreProducto($barcode){

    $product = $this->StoreProduct();
    
 //   dd($product);
//    $product = Product::where('barcode',$barcode)->where('comercio_id',$this->comercio_id)->first();

    $this->emit('add-product-hide', '');
    
    $this->ScanCode($barcode, $cant = 1);
    
    $this->almacen_id = null;
    $this->mostrarDivProductos = false;

    $this->resetUIProducto();
    
}

    public function toggleDivProductos()
    {
       // dd("hola");
        $this->mostrarDivProductos = !$this->mostrarDivProductos;
    }
    
    
    



}
