<?php

namespace App\Http\Livewire;


// Trait
use App\Traits\FacturacionAfip;
use App\Traits\WocommerceTrait;
use App\Traits\ClientesTrait;
//

use Livewire\Component;
use App\Models\User;
use App\Models\ColumnConfiguration;
use App\Models\historico_stock;
use App\Models\cajas;
use App\Models\hoja_ruta;
use App\Models\beneficios;
use App\Models\bancos;
use App\Models\nota_credito;
use App\Models\productos_variaciones_datos;
use App\Models\productos_variaciones;
use App\Models\productos_stock_sucursales;
use App\Models\productos_lista_precios;
use App\Models\variaciones;
use App\Models\detalle_compra_proveedores;
use App\Models\metodo_pago;
use App\Models\pagos_facturas;
use Livewire\WithPagination;
use App\Models\datos_facturacion;
use App\Models\Sale;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;
use Afip;
use App\Models\ClientesMostrador;
use App\Models\Product;
use App\Models\sucursales;
use App\Models\lista_precios;
use App\Models\compras_proveedores;
use App\Models\wocommerce;
use App\Models\ecommerce_envio;
use Automattic\WooCommerce\Client;
use Illuminate\Support\Facades\Auth;
use App\Models\SaleDetail;
use Carbon\Carbon;


class Reports2Controller extends Component
{

    use WithPagination;
    use FacturacionAfip;
    use WocommerceTrait;
    use ClientesTrait;

    public $componentName, $datos_cliente,$nombre_cliente_elegido,$relacion_precio_iva,$alicuota_iva, $cliente_id,$ecommerce_envio_form, $id_cliente_elegido, $caja, $lista_cajas_dia,$monto_inicial, $query_cliente, $estado_facturacion,  $sucursal_stock, $detalle_facturacion, $iva_agregar, $data,  $tipo_pago, $tipos_pago, $details, $sumDetails, $countDetails, $sum, $totales_ver, $cantidad_tickets, $ticket_promedio, $sucursal_id,
    $reportType, $userId, $dateFrom, $dateTo, $rec,$tipo_pago_sucursal, $saleId,$tipo_factura, $codigo_barra_afip, $suma_deuda, $ultimas_cajas, $mail_ingresado, $comercio_id,$status, $codigo_qr, $search, $codigoQR, $clienteId, $selected_id, $suma_totales, $suma_cantidades, $id_pedido, $product_agregar, $estado_estado, $NroVenta, $hr_elegida, $desc, $ventaId, $suma_monto, $suma_cash, $tot, $hojar, $hoja_ruta, $monto, $estado, $estado2, $nombre_hr, $tipo, $fecha_hr, $turno, $observaciones_hr, $dateHojaRuta, $estado_pago, $metodo_pago_sale_detail, $recargo_mp, $nombre_mp, $recargo, $id_pago, $monto_ap, $fecha_ap, $metodo_pago_ap, $fecha_editar,$nro_hoja_elegido, $product_wc, $formato_modal, $total_pago, $recargo_total, $recargo_nvo_venta, $style, $style2, $estado_original, $id_checked, $accion_lote;
    
    public $Nro_Venta;
    public $metodo_pago_agregar_pago = [];
    public $id_check = [];


  public $productos_variaciones_datos = [];

  	private $pagination = 25;

    public $lista_precios;
    public $venta_form;
  	public $query_id;
  	public $columns;
  	public $products_s;
    public $mail = [];
    public $listado_hojas_ruta = [];
    public $pagos1 = [];
    public $pagos2 = [];
    public $total_total = [];
    public $usuario = [];
    public $fecha = [];
    public $detalle_cliente = [];
    public $detalle_venta = [];
    public $query_product;
    public $clientesSelectedId;
    public $UsuarioSelectedName;
    public $EstadoSelectedName;
    public $MetodoPagoSelectedName;
    public $MetodoPagoSeleccionado;

    public $Usuario_SelectedValues;
    public $Estado_SelectedValues;

    public $usuarioSeleccionado;
    public $ClienteSeleccionado;
    public $EstadoSeleccionado;
    public $clientesSelectedName = [];

    public array $locationUsers = [];
    public array $usuario_seleccionado = [];
    public array $estado_seleccionado = [];
    public array $metodo_pago_seleccionado = [];
    
    public $tipo_entrega,$nombre_destinatario,$direccion,$ciudad,$nombre_provincia,$telefono;

    public function paginationView()
    {
      return 'vendor.livewire.bootstrap';
    }

    protected $listeners = [
        'RestaurarVenta' => 'RestaurarVenta', 
        'AnularFactura' => 'AnularFactura',
        'EliminarVenta' => 'EliminarVenta',
        'CancelarVenta' => 'Update',
        'accion-lote' => 'AccionEnLote',
        'cancelar-pagos' => 'CancelarPagos',
        'deletePago' => 'DeletePago',
        'deleteRow' => 'EliminarProductoPedido', 
        'FacturarVenta' => 'FacturarAfip' ,
        'locationUsersSelected',
        'UsuarioSelected',
        'EstadoSelected',
        'Usuario_Selected',
        'MetodoPagoSelected'
        ];



    public function UsuarioSelected($UsuarioSelectedValues)
    {
      $this->usuario_seleccionado = $UsuarioSelectedValues;


    }

    public function MetodoPagoSelected($MetodoPagoSelectedValues)
    {
      $this->metodo_pago_seleccionado = $MetodoPagoSelectedValues;



    }




    public function EstadoSelected($EstadoSelectedValues)
    {
      $this->estado_seleccionado = $EstadoSelectedValues;

    }


    public function locationUsersSelected($locationUsersValues)
    {
      $this->locationUsers = $locationUsersValues;
    }

    public function mount()
    {
        $this->tipo_pago_sucursal = 1;
        $this->columns = ColumnConfiguration::where('user_id', Auth::id())->where('table_name','reports')->pluck('column_name', 'is_visible')->toArray();
        $this->MostrarOcultar = "none";
        $this->columnaOrden = "nro_venta";
        $this->direccionOrden = "desc";
        $this->estado_filtro = 0;
        $this->id_cliente_elegido = null;
        $this->nombre_cliente_elegido = null;
        $this->caja = cajas::select('*')->where('estado',0)->where('eliminado',0)->where('user_id',Auth::user()->id)->max('id');
        $this->lista_cajas_dia = [];
        $this->componentName ='Reportes de Ventas';
        $this->detalle_facturacion =[];
        $this->editar_cliente_show = 0;
        $this->tipo_pago = 1;
        $this->tipos_pago = [];
        $this->tipos_pago_sucursal = [];
        $this->style = 'none';
        $this->style2 = 'block';
        $this->hr_elegida = 0;
        $this->monto_ap = 0;
        $this->details =[];
        $this->sumDetails =0;
        $this->countDetails =0;
        $this->reportType =0;
        $this->userId =0;
        $this->recargo = 0;
        $this->recargo_total =0;
        $this->saleId =0;
        $this->estado_pago = '';
        $this->estado_estado = [];
        $this->estado_facturacion = 'all';
        $this->usuarioSeleccionado = 0;
        $this->ClienteSeleccionado = 0;
        $this->clienteId =0;
        $this->metodo_pago_agregar_pago = 1;
        $this->clientesSelectedName = [];
        $this->dateFrom = Carbon::parse('2000-01-01 00:00:00')->format('d-m-Y');
        $this->dateTo = Carbon::now()->format('d-m-Y');
        $this->dateHojaRuta = Carbon::now()->format('d-m-Y');
        $fecha_editar = Carbon::now()->format('d-m-Y');
        $this->fecha_ap = Carbon::now();
        $this->metodos = [];
        $this->metodo_pago_agregar = [];
        $product_wc = null;



    }


    public function resetUI()
    {
      $this->id_pago = '';
      $this->monto = '';


      $this->monto_ap = '';
      $this->fecha_ap = Carbon::now()->format('d-m-Y');
      $this->metodo_pago_ap = '';
    }


    public function render()
    {
        
        
      //  dd($this->columnaOrden,$this->direccionOrden);
       
            if($this->dateFrom !== '' || $this->dateTo !== '')
            {
              $from = Carbon::parse($this->dateFrom)->format('Y-m-d').' 00:00:00';
              $to = Carbon::parse($this->dateTo)->format('Y-m-d').' 23:59:59';

            }

            if($this->estado_pago !== '' )
            {
              if($this->estado_pago !== 'Pago' )
              {

                $this->estado_pago_buscar = ' sales.deuda > 0 ';
              } else {
                $this->estado_pago_buscar = ' sales.deuda = 0';
              }

            }
            
           if($this->estado_facturacion !== 'all' )
            {
              if($this->estado_facturacion == 1 )
              {

                $this->estado_facturacion_buscar = ' sales.cae IS NOT NULL ';
                
                
              } else {
                $this->estado_facturacion_buscar = ' sales.cae IS NULL ';
              }

            }


            if(Auth::user()->comercio_id != 1)
            $comercio_id = Auth::user()->comercio_id;
            else
            $comercio_id = Auth::user()->id;

            $this->datos_facturacion_elegidos = datos_facturacion::where('comercio_id',$comercio_id)->first();
            
            //dd($this->datos_facturacion_elegidos);
            
            $this->tipo_usuario = User::find(Auth::user()->id);

        		if($this->tipo_usuario->sucursal != 1) {
        		$this->casa_central_id = $comercio_id;
        		} else {

        		$this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
        		$this->casa_central_id = $this->casa_central->casa_central_id;
        		}


            if($this->sucursal_id != null) {
              $this->sucursal_id = $this->sucursal_id;
            } else {
              $this->sucursal_id = $comercio_id;
            }


            $this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')->select('users.name','sucursales.sucursal_id')->where('sucursales.eliminado',0)->where('casa_central_id', $comercio_id)->get();


            $reportes = Sale::leftjoin('users as u', 'u.id', 'sales.user_id')
            ->leftjoin('metodo_pagos as m','m.id','sales.metodo_pago')
            ->leftjoin('bancos as b','b.id','m.cuenta')
            ->leftjoin('clientes_mostradors as cm','cm.id','sales.cliente_id')
            ->leftjoin('nota_creditos as nc','nc.venta_id','sales.id')
            ->select('sales.*', 'nc.nro_nota_credito','u.name as user','m.nombre as nombre_metodo_pago','cm.nombre as nombre_cliente','cm.email','sales.cash','sales.canal_venta','sales.deuda','b.nombre as nombre_banco');


          if($this->usuario_seleccionado) {

            $reportes = $reportes->whereIn('sales.user_id', $this->usuario_seleccionado); // el usuario


          }

              if($this->locationUsers) {

            $reportes = $reportes->whereIn('sales.cliente_id', $this->locationUsers); //  el cliente


          }

          if($this->estado_pago !== '') {
            $reportes = $reportes->whereRaw($this->estado_pago_buscar);

          }
          
        if($this->estado_facturacion !== 'all') {
            $reportes = $reportes->whereRaw($this->estado_facturacion_buscar);

          }

          if($this->estado_seleccionado)
        {
          $reportes = $reportes->whereIn('sales.status', $this->estado_seleccionado);

      }

      if($this->metodo_pago_seleccionado)
      {

      $reportes = $reportes->whereIn('sales.metodo_pago', $this->metodo_pago_seleccionado);

      }


          $reportes =  $reportes->whereBetween('sales.created_at', [$from, $to])
            ->where('sales.comercio_id', $this->sucursal_id)
            ->where('sales.id', 'like',$this->search . '%')
            ->where('sales.eliminado', 'like', $this->estado_filtro)
            ->orderBy($this->columnaOrden, $this->direccionOrden)
       //     ->orderBy('sales.created_at','desc')
            ->paginate($this->pagination);
            
        
		  // REPORTES TOTALES 
            $reportes_totales = Sale::select(Sale::raw('SUM(sales.total) as total'),Sale::raw('SUM(sales.recargo) as recargo'),Sale::raw('SUM(sales.items) as items'),Sale::raw('SUM(sales.deuda) as deuda'),Sale::raw('COUNT(sales.id) as count_id'))
             ->whereBetween('sales.created_at', [$from, $to])
             ->where('sales.comercio_id', $this->sucursal_id)
             ->where('sales.status','<>', 'Cancelado')
            ->where('sales.eliminado', 'like', $this->estado_filtro)
             ->where('sales.id', 'like',$this->search . '%');

             if($this->usuario_seleccionado) {

               $reportes_totales = $reportes_totales->whereIn('sales.user_id', $this->usuario_seleccionado); // el usuario


             }
             
            if($this->estado_facturacion !== 'all') {
            $reportes_totales = $reportes_totales->whereRaw($this->estado_facturacion_buscar);

          }

                 if($this->locationUsers) {

               $reportes_totales = $reportes_totales->whereIn('sales.cliente_id', $this->locationUsers); //  el cliente


             }

             if($this->estado_pago !== '') {
               $reportes_totales = $reportes_totales->whereRaw($this->estado_pago_buscar);

             }

             if($this->estado_seleccionado)
           {
             $reportes_totales = $reportes_totales->whereIn('sales.status', $this->estado_seleccionado);

         }

         if($this->metodo_pago_seleccionado)
         {

         $reportes_totales = $reportes_totales->whereIn('sales.metodo_pago', $this->metodo_pago_seleccionado);

         }

        $reportes_totales = $reportes_totales->first();


            $this->suma_totales = $reportes_totales->total;
            $this->recargos_totales = $reportes_totales->recargo;
            $this->suma_cantidades = $reportes_totales->items;
            $this->suma_deuda = $reportes_totales->deuda;
            $this->cantidad_tickets = $reportes_totales->count_id;
            if ($this->cantidad_tickets == 0) {
              $this->ticket_promedio = 0;
            } else {
            $this->ticket_promedio = $this->suma_totales/$this->cantidad_tickets;
          }



        $this->estado = "display: block;";



        if(($this->usuario_seleccionado == []) && ($this->locationUsers == []) && ($this->metodo_pago_seleccionado == []) && ($this->estado_pago == '') && ($this->estado_facturacion == 'all') && ($this->estado_seleccionado == []))
         {



                 if(Auth::user()->comercio_id != 1)
                 $comercio_id = Auth::user()->comercio_id;
                 else
                 $comercio_id = Auth::user()->id;

                 if($this->sucursal_id != null) {
                   $this->sucursal_id = $this->sucursal_id;
                 } else {
                   $this->sucursal_id = $comercio_id;
                 }



               $reportes = Sale::join('users as u', 'u.id', 'sales.user_id')
                   ->join('metodo_pagos as m','m.id','sales.metodo_pago')
                   ->join('bancos as b','b.id','m.cuenta')
                   ->join('clientes_mostradors as cm','cm.id','sales.cliente_id')
                   ->leftjoin('nota_creditos as nc','nc.venta_id','sales.id')
                   ->select('sales.*','nc.nro_nota_credito', 'u.name as user','m.nombre as nombre_metodo_pago','cm.nombre as nombre_cliente','cm.email','sales.cash','sales.estado_pago','sales.deuda','b.nombre as nombre_banco')
                   ->whereBetween('sales.created_at', [$from, $to])
                   ->where('sales.comercio_id', $this->sucursal_id)
                   ->where('sales.id', 'like',$this->search . '%')
                    ->where('sales.eliminado', 'like', $this->estado_filtro)
                    ->orderBy($this->columnaOrden, $this->direccionOrden)
                   ->paginate($this->pagination);


                   $reportes_totales = Sale::select(Sale::raw('SUM(sales.total) as total'),Sale::raw('SUM(sales.recargo) as recargo'),Sale::raw('SUM(sales.deuda) as deuda'),Sale::raw('SUM(sales.items) as items'),Sale::raw('COUNT(sales.id) as count_id'))
                    ->whereBetween('sales.created_at', [$from, $to])
                    ->where('sales.comercio_id', $this->sucursal_id)
                    ->where('sales.status','<>', 'Cancelado')
                    ->where('sales.eliminado', 'like', $this->estado_filtro)
                    ->where('sales.id', 'like',$this->search . '%')
                    ->first();


                   $this->suma_totales = $reportes_totales->total;
                   $this->recargos_totales = $reportes_totales->recargo;
                   $this->total_con_recargo = $this->suma_totales + $this->recargo;
                   $this->suma_cantidades = $reportes_totales->items;
                   $this->suma_deuda = $reportes_totales->deuda;
                   $this->cantidad_tickets = $reportes_totales->count_id;
                   if ($this->cantidad_tickets == 0) {
                     $this->ticket_promedio = 0;
                   } else {
                   $this->ticket_promedio = $this->total_con_recargo/$this->cantidad_tickets;
                 }

                     $this->estado = "display: none;";

             }





      $usuario_id = Auth::user()->id;

      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;

      if($this->sucursal_id != null) {
        $this->sucursal_id = $this->sucursal_id;
      } else {
        $this->sucursal_id = $comercio_id;
      }

  $this->comercio_id = $comercio_id;

      $this->tipos_pago = bancos::join('bancos_muestra_sucursales','bancos_muestra_sucursales.banco_id','bancos.id')
      ->select('bancos.*')
      ->where('bancos_muestra_sucursales.sucursal_id', $this->sucursal_id)
       ->where('bancos_muestra_sucursales.muestra', 1)
      ->orderBy('bancos.nombre','asc')->get();
      
      
      $this->detalle_facturacion = datos_facturacion::join('users','users.id','datos_facturacions.comercio_id')->leftjoin('provincias','provincias.id','datos_facturacions.provincia')->select('datos_facturacions.*','users.email','users.phone','provincias.provincia')->where('datos_facturacions.comercio_id', $this->sucursal_id)->get();


      /////////// METODOS DE PAGO ///////////

      $this->metodos =  metodo_pago::join('bancos','bancos.id','metodo_pagos.cuenta')
      ->join('metodo_pagos_muestra_sucursales','metodo_pagos_muestra_sucursales.metodo_id','metodo_pagos.id')
      ->select('metodo_pagos.*','bancos.nombre as nombre_banco')
      ->where('metodo_pagos_muestra_sucursales.sucursal_id', 'like', $this->sucursal_id)
      ->where('metodo_pagos_muestra_sucursales.muestra', 1)
      ->orderBy('metodo_pagos.nombre','asc')
      ->get();

      /////////// METODOS DE PAGO ///////////


      $this->metodo_pago_agregar =  metodo_pago::join('bancos','bancos.id','metodo_pagos.cuenta')
       ->join('metodo_pagos_muestra_sucursales','metodo_pagos_muestra_sucursales.metodo_id','metodo_pagos.id')
      ->select('metodo_pagos.*','bancos.nombre as nombre_banco')
      ->where('metodo_pagos_muestra_sucursales.sucursal_id', 'like', $this->sucursal_id)
      ->where('metodo_pagos_muestra_sucursales.muestra', 1)
      ->where('metodo_pagos.cuenta', 'like', $this->tipo_pago)
      ->get();

    //// ECOMMERCE ENVIOS ////
    
    $item_ids = $reportes->items();
		
	$items_id = [];
			
	foreach ($item_ids as $i_id) {
	$id_id = $i_id->id;
	array_push($items_id, $id_id);
		    
	} 
	
	
	$ecommerce_envios = ecommerce_envio::whereIn('sale_id',$items_id)->join('provincias','provincias.id','ecommerce_envios.provincia')
	->select('ecommerce_envios.*','provincias.provincia as nombre_provincia')
	->get();


      //////////////////////////////////////////////

      $wc = wocommerce::where('comercio_id', $comercio_id)->first();

      if($wc == null){
        $this->wc_yes = 0;
      } else {
          $this->wc_yes = $wc->id;
      }

        $this->caja_seleccionada = cajas::find($this->caja);

      $this->ultimas_cajas = cajas::where('comercio_id', $comercio_id)->where('eliminado',0)->orderby('created_at','desc')->limit(5)->get();

        return view('livewire.reports2.component', [
          'users' => User::orderBy('name','asc')
          ->where('users.comercio_id', $this->sucursal_id)
          ->orWhere('users.id', $usuario_id)
          ->get(),
          'ultimas_cajas' => $this->ultimas_cajas,
          'estados' => Sale::select('sales.status')
          ->where('sales.comercio_id', $this->sucursal_id)
          ->groupBy('sales.status')
          ->get(),
          'ecommerce_envios' => $ecommerce_envios,
          'data_reportes' => $reportes,
          'wc_yes' => $this->wc_yes,
          'tipos_pago' => $this->tipo_pago,
          'metodo_pago_filtro' => $this->metodos,
          'metodo_pago_agregar' => $this->metodo_pago_agregar,
          'sucursales' => $this->sucursales,
          'comercio_id' => $this->comercio_id,
          'clientes' => ClientesMostrador::where('comercio_id', $this->sucursal_id)
          ->where('eliminado',0)
          ->orderBy('nombre','asc')
          ->get()
        ])
        ->extends('layouts.theme-pos.app')
    ->section('content');



    }



    function AgregarPago($id_pago) {

      $this->emit('cerrar-factura','details loaded');

      $this->emit('agregar-pago','details loaded');

      $this->caja_pago = cajas::select('*')->where('id', $this->caja)->get();

      $this->id_pago = $id_pago;


    }



    function AgregarPago2($id_pago) {

      $this->CerrarFactura();


      $this->emit('agregar-pago','details loaded');


      $this->id_pago = $id_pago;

      $this->formato_modal = 0;


    }

    function EditPago($id_pago) {


      $this->CerrarFactura();

      $this->emit('agregar-pago','details loaded');

      $this->formato_modal = 1;


      $this->id_pago = $id_pago;

      $pagos = pagos_facturas::find($id_pago);

      $this->caja = $pagos->caja;

      $this->metodo_pago_agregar_pago = $pagos->metodo_pago;

      $metodo_pago = metodo_pago::find($this->metodo_pago_agregar_pago);

      $this->recargo = $metodo_pago->recargo/100;

      $this->tipo_pago = $metodo_pago->cuenta;

      $this->monto_ap = $pagos->monto;

      $this->fecha_ap = Carbon::parse($pagos->created_at)->format('d-m-Y');

      $this->recargo_total = $this->monto_ap * $this->recargo;

      $this->total_pago = $this->recargo_total + $this->monto_ap;
  
       // 11-1-2024
       if($this->datos_cliente != null){
       $pago_sucursal = pagos_facturas::where('pago_sucursal_id',$pagos->id)->where('eliminado',0)->first();
       
       $sucursal_id_compra = sucursales::find($this->datos_cliente->sucursal_id)->sucursal_id;
      // aca pasamos la sucursal y el id de pago
       $this->SetSucursalPagos($sucursal_id_compra,$pago_sucursal->id);
       }
       //

    }


    public function MetodoPago($value)
    {

  $metodo_pago = metodo_pago::join('bancos','bancos.id','metodo_pagos.cuenta')->select('metodo_pagos.*','bancos.nombre as nombre_banco')->find($this->metodo_pago_agregar_pago);

  $this->recargo = $metodo_pago->recargo/100;

  $this->recargo_total = $this->monto_ap * $this->recargo;

  $this->total_pago = $this->recargo_total + $this->monto_ap;

    }


    
    public function ActualizarPago($id_pago) {

    if($this->metodo_pago_agregar_pago == 1 && $this->tipo_pago != 1) {
        $this->emit("msg-error","El metodo de pago no puede ser efectivo para un banco/plataforma");
        return;
    }
    
      $pagos = pagos_facturas::find($id_pago);

      $ventaId = $pagos->id_factura;

      $pagos->update([
        'monto' => $this->monto_ap,
        'caja' => $this->caja,
        'recargo' => $this->recargo_total,
        'created_at' => $this->fecha_ap,
        'metodo_pago' => $this->metodo_pago_agregar_pago,
        'banco_id' => $this->tipo_pago,
      ]);

      $ventas_vieja = Sale::find($ventaId);

      $rec = $this->CalcularRecargo($ventaId);
    
      // dd($rec);
      
      $ventas_vieja->update([
        'recargo' => $rec,
        'metodo_pago' => $this->metodo_pago_agregar_pago
      ]);
      
      
      $this->ActualizarTotalesVenta($ventaId);
      
      $this->ActualizarEstadoDeuda($ventaId);
      
      // si es una venta a sucursal, tenemos que actualizar el stock de la compra en la sucursal 
    
      if($this->datos_cliente->sucursal_id != 0) {
      
       
      $this->ActualizarPagoCompra($pagos->id);    
      
      }  
    
      //

      $this->emit('agregar-pago-hide', 'hide');

      $this->emit('pago-actualizado', 'El pago fue actualizado.');

      $this->RenderFactura($ventaId);

      $this->ResetPago();



      $this->estado = "display: block;";

    }

    public function MontoPagoEditarPago($value)
    {
    $metodo_pago = metodo_pago::find($this->metodo_pago_agregar_pago);

    $this->MontoPagoEditarPago = $value;

    $this->recargo = $metodo_pago->recargo/100;

    $this->recargo_total = $this->MontoPagoEditarPago * $this->recargo;

    $this->total_pago = $this->recargo_total + $this->MontoPagoEditarPago;

    }



        public function updatePricePedido($id_pedido_prod, $cant = 1)
        {

          $this->items_viejo = SaleDetail::find($id_pedido_prod);
        
          $venta = Sale::find($this->items_viejo->sale_id);
                   
          if($venta->relacion_precio_iva == 1) {
          $cant = $cant;    
          }
          
          if($venta->relacion_precio_iva == 2) {
           $cant = $cant/(1+$venta->alicuota_iva);   
          }
          
          $product = Product::find($this->items_viejo->product_id);

          $this->qty_item_viejo = $this->items_viejo->quantity;

          $this->price_item_viejo = $this->items_viejo->price;

          // Descuento //
          $this->descuento_viejo = ($this->items_viejo->descuento/$this->items_viejo->quantity)/$this->items_viejo->price;
          $this->descuento_item_viejo = floatval($this->items_viejo->descuento);
          $this->descuento_item_nuevo = $this->items_viejo->quantity*$this->descuento_viejo*$cant;


          $this->diferencia_descuentos = $this->descuento_item_nuevo - $this->descuento_item_viejo;

          $this->items_viejo->update([
            'price' => $cant,
            'descuento' => $this->descuento_item_nuevo
            ]);
         

            $this->items_nuevo = SaleDetail::find($id_pedido_prod);

            $this->ActualizarTotalesVenta($this->items_nuevo->sale_id);
            
            $this->ActualizarEstadoDeuda($this->items_nuevo->sale_id);
            
                    
            // Si es una venta a una sucursal
            if($venta->canal_venta == "Venta a sucursales") {
              
            $compra = compras_proveedores::where('sale_casa_central',$venta->id)->first();
              
            $producto_comprado = detalle_compra_proveedores::where('compra_id',$compra->id)
            ->where('producto_id',$this->items_viejo->product_id)
            ->where('referencia_variacion',$this->items_viejo->referencia_variacion)
            ->first();
              
            $this->updatePriceCompra($producto_comprado->id, $cant);
            }

            $this->RenderFactura($this->items_nuevo->sale_id);

            $this->emit('pago-agregado', 'El precio fue modificado.');
        }

        public function updatePriceCompra($id_pedido_prod, $cant = 1) {
        
              $this->items_viejo = detalle_compra_proveedores::find($id_pedido_prod);
        
              $this->precio_viejo = $this->items_viejo->precio;
              
              $this->iva_total_nuevo = ($cant * (1+$this->items_viejo->alicuota_iva) ) * $this->items_viejo->cantidad;
        
              $this->items_viejo->update([
                'precio' => $cant,
                'iva' => $this->iva_total_nuevo
                ]);
        
            //  Actualizar el total de la compra
            
                $this->ActualizarTotalCompra($this->items_viejo->compra_id);
        
            //  Actualizar el estado de la deuda
            
                $this->ActualizarEstadoDeudaCompra($this->items_viejo->compra_id);
        
         $this->emit('pago-agregado', 'El precio fue modificado.');
        }

        public function UpdateTipoComprobante($value,$nro_venta,$origen) {
            $s = Sale::find($nro_venta);
            $s->tipo_comprobante = $value;
            $s->save();
            
            if($origen != 0) {
            $this->RenderFactura($this->NroVenta);
            $this->emit("msg","Tipo de comprobante modificado");
            }
            
        }
        
       public function UpdateRelacionPrecioIva($value,$nro_venta,$origen){
    
       
       $sale_detail = SaleDetail::where('sale_id',$nro_venta)->get();   
        
        //dd($sale_detail);
        
        foreach($sale_detail as $sd) {
        //dd($sd->id);
        if($sd->iva == null){
            $alicuota = 0;
        } else {
          $alicuota =   $sd->iva;
        }
        //dd($value);
          if($value == 0){
          $precio = $sd->precio_original;
          $iva_total = 0;
          $sd->update([
              'price' => $precio,
              'iva' => 0,
              'iva_total' => $iva_total,
              'relacio_precio_iva' => $value
            ]); 
            
        //dd($sd);
          }
          
          if($value == 1){
          $precio = $sd->precio_original;     
          $iva_total = $sd->precio_original*$alicuota; 
          $sd->update([
              'price' => $precio,
              'iva_total' => $iva_total,
              'relacion_precio_iva' => $value
              ]); 
          }
          
          if($value == 2){
              
          $precio = $sd->precio_original/(1+$alicuota); 
          $iva_total = $precio * $alicuota;
          $sd->update([
              'price' => $precio,
              'iva_total' => $iva_total,
              'relacion_precio_iva' => $value
              ]); 
          }
          
    
          
        }
        
        $sale = Sale::find($nro_venta);
        $sale->relacion_precio_iva = $value;    
        if($value == 0) {
        $sale->alicuota_iva = $value;    
        }
        $sale->save();
            
            
        $this->ActualizarTotalesVenta($nro_venta);
            
        $this->ActualizarEstadoDeuda($nro_venta);

        if($origen != 0) {
        $this->RenderFactura($nro_venta);
        }
        
        $this->emit('pago-agregado', 'La relacion precio iva fue modificada.');
         
        }
        
        public function UpdateIvaGral($value,$nro_venta,$origen) {
        
        //dd($value,$nro_venta,$origen);
        
        $this->NroVenta = $nro_venta;
        
        $sale_detail = SaleDetail::where('sale_id',$nro_venta)->get();   
        
        foreach($sale_detail as $sd) {
        //dd($sd->relacion_precio_iva);
        if($sd->relacion_precio_iva == 1){
        $this->UpdateIva($sd->id,$value);   
        }
        if($sd->relacion_precio_iva == 2){
        $this->UpdateIvaPrecio2($sd->id,$value);   
        }
        }
        
        $sale = Sale::find($nro_venta);
        if($value == 0) {
        $sale->relacion_precio_iva = $value;    
        }
        $sale->alicuota_iva = $value;
        $sale->save();
        
        if($origen != 0) {
        $this->RenderFactura($nro_venta);
        }
        
        $this->emit('pago-agregado', 'El IVA fue modificado.');
        }
        
        public function UpdateIva($id_pedido_prod, $cant = 1)
        {

          $this->items_viejo = SaleDetail::find($id_pedido_prod);

          $this->iva_viejo = $this->items_viejo->iva;

          $this->items_viejo->update([
            'iva' => $cant
            ]);

          $this->items_nuevo = SaleDetail::find($id_pedido_prod);

          $this->iva_nuevo = $this->items_nuevo->iva;

          $this->diferencia_iva = ($this->iva_viejo - $this->items_nuevo->iva) * $this->items_nuevo->price * $this->items_nuevo->quantity;


          $this->venta = Sale::find($this->items_nuevo->sale_id);

          $this->total_iva_nuevo = floatval($this->venta->iva) - floatval($this->diferencia_iva);

          $this->total_nuevo = floatval($this->venta->total) - floatval($this->diferencia_iva);


          $this->venta->update([
            'iva' => $this->total_iva_nuevo,
            'total' => $this->total_nuevo
            ]);

            $this->ActualizarTotalesVenta($this->NroVenta);
            
            $this->ActualizarEstadoDeuda($this->venta->id);

            $this->RenderFactura($this->items_nuevo->sale_id);

        }

        public function UpdateIvaPrecio2($id_pedido_prod, $cant = 1)
        {

          $items_viejo = SaleDetail::find($id_pedido_prod);
        
          // precio 
          $precio_nuevo = $items_viejo->precio_original/(1+$cant);
          $iva_nuevo = $precio_nuevo * $cant;
          
          $this->iva_viejo = $items_viejo->iva;

          $items_viejo->update([
            'price' => $precio_nuevo,
            'iva' => $cant,
            'iva_total' => $iva_nuevo,
            ]);
            
            $s = Sale::find($this->NroVenta);
            $s->alicuota_iva = $cant;
            $s->save();
            
            $this->ActualizarTotalesVenta($this->NroVenta);
            
            $this->ActualizarEstadoDeuda($this->NroVenta);

            $this->RenderFactura($this->NroVenta);

        }



    public function ActualizarEstadoModal($saleId)
    {

      $this->id_pedido = $saleId;

        $this->emit('show-modal-actualizar-estado','details loaded');

    }



    
    
    public function updatedQueryCliente()
    {


      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;

      if($this->sucursal_id != null) {
        $this->sucursal_id = $this->sucursal_id;
      } else {
        $this->sucursal_id = $comercio_id;
      }


        $this->clientes_s = 	ClientesMostrador::where('comercio_id', 'like', $this->sucursal_id)
        ->where( function($query) {
              $query->where('nombre', 'like', '%' . $this->query_cliente . '%');
          })
            ->limit(5)
            ->get()
            ->toArray();

            $this->RenderFactura($this->NroVenta);



    }
    
   
       public function resetCliente()
   {
     $this->clientes_s = [];
      $this->query_clientes = '';
       $this->RenderFactura($this->NroVenta);
   }


        
       
       
       public function selectCliente($item) {
           
           //dd($item);
           
           $this->id_cliente_elegido = $item;
           
           $venta = Sale::find($this->NroVenta);
           $venta->cliente_id = $item;
           $venta->save();
           
           $this->RenderFactura($this->NroVenta);

           $this->emit("msg","Cliente modificado");
           
           
       }



public function UpdateCliente() {
    
    $venta = Sale::find($this->NroVenta);
    $venta->cliente_id = $this->id_cliente_elegido;
    $venta->save();
    
    $this->resetCliente();
    
    $this->emit('editar-cliente-hide');
    $this->emit('pago-agregado', 'El cliente fue modificado.');
}
   public function BuscarCodeVariacion($barcode)
   {

   $this->product = explode('|-|',$barcode);

   $barcode = 	$this->product[0];
   $variacion = 	$this->product[1];

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

     $record = Product::where('barcode',$barcode)->where('comercio_id', $this->casa_central_id)->where('eliminado',0)->first();

     $product_stock = productos_stock_sucursales::join('products','products.id','productos_stock_sucursales.product_id')
     ->select('productos_stock_sucursales.*')
     ->where('productos_stock_sucursales.product_id',$record->id)
     ->where('productos_stock_sucursales.referencia_variacion',$variacion)
     ->where('productos_stock_sucursales.comercio_id', $this->casa_central_id)
     ->where('products.eliminado',0)
     ->first();

    // Si es compra en casa central 
    $venta = Sale::find($this->NroVenta);
          
    if($venta->canal_venta == "Venta a sucursales") {
      $product_price = $product->precio_interno;  
      } else {
      $product_price = productos_lista_precios::join('products','products.id','productos_lista_precios.product_id')
     ->select('productos_lista_precios.*')
     ->where('productos_lista_precios.product_id',$record->id)
     ->where('productos_lista_precios.referencia_variacion',$variacion)
     ->where('productos_lista_precios.lista_id', 0)
     ->where('products.eliminado',0)
     ->first()->precio_lista;
         }


    if($product_stock->stock < 1 && $record->stock_descubierto == "si" ) {

    $this->emit('no-stock','Stock insuficiente');
    $this->RenderFactura($this->NroVenta);

   } else {

            $venta = Sale::find($this->NroVenta);

           $tot = $venta->total + ($product_price * (1+$this->iva_agregar));

           $venta->update([
             'total' => $tot,
             'subtotal' => $tot,
             'items' => $venta->items + 1
           ]);


             $productos_variaciones_datos = productos_variaciones::join('variaciones','variaciones.id','productos_variaciones.variacion_id')
             ->select('variaciones.nombre')
             ->where('productos_variaciones.referencia_id',$variacion)
             ->get();

             $pvd = [];

             foreach ($productos_variaciones_datos as $pv) {

                   array_push($pvd, $pv->nombre);

                     }

             $var = implode(" ",$pvd);

             $this->name = $record->name." - ".$var;

         SaleDetail::create([
           'price' => $product_price,
           'quantity' => 1,
           'product_name' => $this->name,
           'product_barcode' => $record->barcode,
           'product_id' => $record->id,
           'referencia_variacion' => $variacion,
           'metodo_pago'  => $venta->metodo_pago,
           'seccionalmacen_id' => $record->seccionalmacen_id,
           'comercio_id' => $record->comercio_id,
           'sale_id' => $this->NroVenta,
           'iva' => $this->iva_agregar,
           'canal_venta' => $venta->canal_venta,
           'cliente_id' => $venta->cliente_id
         ]);



         //update stock
         $product_stock->stock = $product_stock->stock - 1;
         $product_stock->save();

         $usuario_id = Auth::user()->id;

         if(Auth::user()->comercio_id != 1)
         $comercio_id = Auth::user()->comercio_id;
         else
         $comercio_id = Auth::user()->id;

         if($this->sucursal_id != null) {
           $this->sucursal_id = $this->sucursal_id;
         } else {
           $this->sucursal_id = $comercio_id;
         }


         $historico_stock = historico_stock::create([
           'tipo_movimiento' => 2,
           'sale_id' => $venta->id,
           'producto_id' => $record->id,
           'cantidad_movimiento' => -1,
           'stock' => $product_stock->stock,
           'usuario_id' => $usuario_id,
           'comercio_id'  => $this->sucursal_id
         ]);


         $this->emit('variacion-elegir-hide', '');
         $this->resetProduct();
         $this->ActualizarEstadoDeuda($this->NroVenta);
         $this->RenderFactura($this->NroVenta);

   }

   }

    public function RedireccionarFactura($ventaId)
    {

      return \Redirect::to("factura/$ventaId");

    }



//////////////////////////////////////////////////////////////////////


////////////// FACTURA ///////////////




public function RenderFactura($ventaId)
   {

     $v = Sale::find($ventaId);
     $this->Nro_Venta = $v->nro_venta;
     
     $this->NroVenta = $ventaId;
     $this->id_pedido = $ventaId;

     if(Auth::user()->comercio_id != 1)
     $comercio_id = Auth::user()->comercio_id;
     else
     $comercio_id = Auth::user()->id;

     if($this->sucursal_id != null) {
       $this->sucursal_id = $this->sucursal_id;
     } else {
       $this->sucursal_id = $comercio_id;
     }

    $this->nota_credito = nota_credito::where('venta_id',$ventaId)->first();
    
    $this->ecommerce_envio_form = ecommerce_envio::leftjoin('provincias','provincias.id','ecommerce_envios.provincia')
    ->select('ecommerce_envios.*','provincias.provincia  as nombre_provincia')
    ->where('sale_id',$ventaId)
    ->first();

     $this->data_total = Sale::select('sales.total')
     ->where('sales.id', $ventaId)
     ->get();

     $this->tot = $this->data_total->sum('total');

     $this->ventaId = $ventaId;
     
     $this->clientes = 	ClientesMostrador::where('comercio_id', 'like', Auth::user()->casa_central_user_id)->where('eliminado',0)->get();


//////////// HOJA DE RUTA ////////////////////////////

$this->hojar = hoja_ruta::join('sales','sales.hoja_ruta','hoja_rutas.id')->select('hoja_rutas.*')->where('sales.id', $ventaId)->first();



  $this->listado_hojas_ruta = hoja_ruta::where('hoja_rutas.comercio_id', $this->sucursal_id)
  ->where('hoja_rutas.fecha', '>=', Carbon::today())
  ->orderBy('hoja_rutas.nro_hoja','DESC')
  ->limit(20)
  ->get();


/////////////// DETALLE DE VENTA /////////////////////7
       $this->detalle_venta = SaleDetail::join('products as p','p.id','sale_details.product_id')
       ->join('sales','sales.id','sale_details.sale_id')
       ->select('sale_details.id','sale_details.descuento','sale_details.recargo','sale_details.id','sale_details.price','sale_details.quantity','sale_details.iva','sale_details.product_name','sale_details.product_barcode','p.stock','p.stock_descubierto','sales.status')
       ->where('sale_details.sale_id', $ventaId)
       ->where('sale_details.eliminado',0)
       ->get();


       $this->total_total = Sale::join('metodo_pagos as m','m.id','sales.metodo_pago')
       ->select('sales.relacion_precio_iva','sales.cliente_id','sales.recargo','sales.status','sales.alicuota_iva','sales.tipo_comprobante','sales.created_at','sales.descuento','sales.subtotal','sales.total','sales.created_at as fecha','sales.observaciones','sales.nota_interna', 'm.nombre as metodo_pago','sales.cae','sales.vto_cae','sales.nro_factura','sales.iva')
       ->where('sales.id', $ventaId)
       ->get();
       
       
        
        foreach($this->total_total as $t) {
            $this->cliente_id = $t->cliente_id;
            $this->alicuota_iva = $t->alicuota_iva;
            $this->status = $t->status;
            $this->nota_interna = $t->nota_interna;
            $this->observaciones = $t->observaciones;
            $this->tipo_factura = $t->tipo_comprobante;
            $this->relacion_precio_iva = $t->relacion_precio_iva;
        }
       
       // 11-1-2024
       $this->datos_cliente = ClientesMostrador::find($this->cliente_id);
       
       if($this->datos_cliente != null){
       if($this->datos_cliente->sucursal_id != 0){
       $sucursal_id_compra = sucursales::find($this->datos_cliente->sucursal_id)->sucursal_id;
      
      // aca pasamos la sucursal y el id de pago
       $this->SetSucursalPagos($sucursal_id_compra,0);
       }
       }
       //
       
       
       // dd($ventaId,$this->alicuota_iva);
        
       $this->usuario = User::select('users.image','users.name')
       ->where('users.id', $this->sucursal_id)
       ->get();


       $this->detalle_cliente = Sale::join('clientes_mostradors as c','c.id','sales.cliente_id')
       ->select('c.id','c.nombre','c.telefono','c.email','c.dni','c.direccion','c.barrio','c.localidad','c.provincia','sales.observaciones','sales.metodo_pago','sales.fecha_entrega','sales.cash','sales.status')
       ->where('sales.id', $ventaId)
       ->get();


  //////////////// PAGOS //////////////
        $this->pagos2 = pagos_facturas::join('metodo_pagos as mp','mp.id','pagos_facturas.metodo_pago')
        ->join('cajas','cajas.id','pagos_facturas.caja')
        ->select('mp.nombre as metodo_pago','pagos_facturas.id','cajas.nro_caja','pagos_facturas.recargo','pagos_facturas.monto','pagos_facturas.created_at as fecha_pago')
        ->where('pagos_facturas.id_factura', $ventaId)
        ->where('pagos_facturas.eliminado',0)
        ->get();


        $this->suma_monto = $this->pagos2->sum('monto');
        $this->rec = $this->pagos2->sum('recargo');
        $this->rec = $this->rec ?? 0; 
        
        $this->desc = $this->pagos2->sum('descuento');


        $this->estado = "display: none;";
        $this->estado2 = "display: none;";

        $this->total_total2 = Sale::join('metodo_pagos as m','m.id','sales.metodo_pago')
        ->select('sales.recargo','sales.tipo_comprobante','sales.created_at','sales.descuento','sales.total','sales.created_at as fecha','sales.observaciones','sales.nota_interna', 'm.nombre as metodo_pago','sales.cae','sales.vto_cae','sales.nro_factura')
        ->where('sales.id', $ventaId)
        ->first();

        if($this->total_total2->cae != null) {
        $this->CodigoBarrasAfip($ventaId);
         $this->CodigoQRAfip($ventaId);
        }

        if($this->ecommerce_envio_form != null) {
           $this->tipo_entrega = $this->ecommerce_envio_form->metodo_entrega;
           $this->nombre_destinatario = $this->ecommerce_envio_form->nombre_destinatario;
           $this->direccion = $this->ecommerce_envio_form->direccion;
           $this->ciudad = $this->ecommerce_envio_form->ciudad;
           $this->nombre_provincia = $this->ecommerce_envio_form->nombre_provincia;
           $this->telefono = $this->ecommerce_envio_form->telefono;
        }

        $this->emit('modal-show','Show modal');



              //
   }
        
        public function CodigoQRAfip($ventaId) {
        
         $ventaIdFactura =  $ventaId;
        
            if($this->detalle_facturacion2 != null) {
        
        
              if(Auth::user()->comercio_id != 1)
              $comercio_id = Auth::user()->comercio_id;
              else
              $comercio_id = Auth::user()->id;
        
              if($this->sucursal_id != null) {
                $this->sucursal_id = $this->sucursal_id;
              } else {
                $this->sucursal_id = $comercio_id;
              }
        
        
        	$this->datos_facturacion = datos_facturacion::where('comercio_id', $this->sucursal_id)->first();
        
        
        	if($this->datos_facturacion->cuit != null || $this->datos_facturacion->cuit != '') {
        
        
        	$cuit =$this->datos_facturacion->cuit;
        
        	$afip = new Afip(array('CUIT' =>$cuit , 'production' => true));
        
        	/**
        	* Numero del punto de venta
        	**/
        	$punto_de_venta = $this->datos_facturacion->pto_venta;
        
        	$this->factura = Sale::join('clientes_mostradors','clientes_mostradors.id','sales.cliente_id')->select('clientes_mostradors.id as cliente_id','clientes_mostradors.dni','sales.nro_factura','sales.tipo_comprobante')->find($ventaIdFactura);
        
        
        	if($this->factura->tipo_comprobante == 'C' || $this->factura->tipo_comprobante == 'CF') {
        
        	  $tipo_de_comprobante = 11;
        
        
        	}
        
        	if($this->factura->tipo_comprobante == 'B') {
        	  $tipo_de_comprobante = 6;
        	}
        
        
        
        	if($this->factura->tipo_comprobante == 'A') {
        	$tipo_de_comprobante = 1;
        	}
        
        
        	$porciones = explode("-", $this->factura->nro_factura);
        	$tipo_factura = $porciones[0]; // porción1
        	$pto_venta = $porciones[1]; // porción2
        	$nro_factura = $porciones[2]; // porción2
        	$n = $porciones[2]; // porción2
        
        	                                    /**
        	* Numero de factura
        	**/
        	$numero_de_factura = $nro_factura;
        
        	                    /**
        	                     * Numero del punto de venta
        	                     **/
        	                    $punto_de_venta = $punto_de_venta;
        
        	                    /**
        	                     * Tipo de comprobante
        	                     **/
        	                    $tipo_de_comprobante = $tipo_de_comprobante; // 6 = Factura B
        
        	                    /**
        	                     * Informacion de la factura
        	                     **/
        
        	                    $informacion = $afip->ElectronicBilling->GetVoucherInfo($numero_de_factura, $punto_de_venta, $tipo_de_comprobante);
        
        	                    if($informacion === NULL){
        	                        echo 'La factura no existe';
        	                    }
        	                    else{
        	                      /**
        	                       * Mostramos por pantalla la información de la factura
        	                       **
        
        	                       dd($informacion);
        
        	                       */
        
        	                       $cuit = intval($cuit);
                                   $numero_de_factura = intval($numero_de_factura);
                                   $cae = intval($informacion->CodAutorizacion);
                                   $dia = substr($informacion->CbteFch, -2);
                                   $mes = substr($informacion->CbteFch, -4, 2);
                                   $año = substr($informacion->CbteFch, -8, 4);
        
                                   $fecha = $año."-".$mes."-".$dia;
        
        
        	                                               // genero los datos para AFIP
        	                        $url = 'https://www.afip.gob.ar/fe/qr/'; // URL que pide AFIP que se ponga en el QR.
        	                        $datos_cmp_base_64 = json_encode([
        	                            "ver" => 1,                         // Numérico 1 digito -  OBLIGATORIO – versión del formato de los datos del comprobante	1
        	                            "fecha" => $fecha,            // full-date (RFC3339) - OBLIGATORIO – Fecha de emisión del comprobante
        	                            "cuit" => $cuit,        // Numérico 11 dígitos -  OBLIGATORIO – Cuit del Emisor del comprobante
        	                            "ptoVta" => $informacion->PtoVta,               // Numérico hasta 5 digitos - OBLIGATORIO – Punto de venta utilizado para emitir el comprobante
        	                            "tipoCmp" => $informacion->CbteTipo,               // Numérico hasta 3 dígitos - OBLIGATORIO – tipo de comprobante (según Tablas del sistema. Ver abajo )
        	                            "nroCmp" => $numero_de_factura,               // Numérico hasta 8 dígitos - OBLIGATORIO – Número del comprobante
        	                            "importe" => $informacion->ImpTotal,         // Decimal hasta 13 enteros y 2 decimales - OBLIGATORIO – Importe Total del comprobante (en la moneda en la que fue emitido)
        	                            "moneda" => "PES",                  // 3 caracteres - OBLIGATORIO – Moneda del comprobante (según Tablas del sistema. Ver Abajo )
        	                            "ctz" => 1,                 // Decimal hasta 13 enteros y 6 decimales - OBLIGATORIO – Cotización en pesos argentinos de la moneda utilizada (1 cuando la moneda sea pesos)
        	                            "tipoCodAut" => "E",                // string - OBLIGATORIO – “A” para comprobante autorizado por CAEA, “E” para comprobante autorizado por CAE
        	                            "codAut" =>  $cae    // Numérico 14 dígitos -  OBLIGATORIO – Código de autorización otorgado por AFIP para el comprobante
        	                        ]);
        
        
        	                        $datos_cmp_base_64 = base64_encode($datos_cmp_base_64);
        	                        $this->codigo_qr = $url.'?p='.$datos_cmp_base_64;
        
                                    $codigoQR = QrCode::size(90)->generate($this->codigo_qr);
        
        	                    }
        
        
        
        	}
        
        
        } else {
            $codigo_qr = 0;
            $codigoQR = 0;
        }
        
        
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
        
          if($this->sucursal_id != null) {
            $this->sucursal_id = $this->sucursal_id;
          } else {
            $this->sucursal_id = $comercio_id;
          }
        
        
          $this->detalle_facturacion2 = datos_facturacion::where('datos_facturacions.comercio_id', $this->sucursal_id)->first();
        
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
        
          if ($this->total_total2->tipo_comprobante == "CF") {
           $tipo_de_comprobante = '099';
          }
        
        
        
          $cuit = $this->detalle_facturacion2->cuit;
        
          /**
           * Punto de venta (4 caracteres, completado con 0's)
           **/
           $porciones = explode("-", $this->total_total2->nro_factura);
           $tipo_factura = $porciones[0]; // porción1
           $pto_venta = $porciones[1]; // porción2
           $nro_factura_ = $porciones[2]; // porción2
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



// SECCION PAGOS

   public function ResetPago() {
     $this->metodo_pago_agregar_pago = 1;
     $this->monto_ap = 0;
     $this->formato_modal = 0;
     $this->recargo = 0;
     $this->tipo_pago = 1;
     $this->recargo_total = 0;
     $this->total_pago = 0;
     $this->recargo_mp = 0;
     $this->metodo_pago_ap = 1;
     $this->fecha_ap = Carbon::now()->format('d-m-Y');
   }


   function CerrarAgregarPago($ventaId) {

     $this->emit('agregar-pago-hide','details loaded');

     $this->ResetPago();

     $this->RenderFactura($ventaId);


   }

   public function CreatePago2($ventaId)
   {
       
    if($this->metodo_pago_agregar_pago == 1 && $this->tipo_pago != 1) {
    $this->emit("msg-error","La forma de pago no puede ser efectivo para un banco/plataforma");
    return;
    }

     $this->cliente_query = Sale::find($ventaId);

     if(Auth::user()->comercio_id != 1)
     $comercio_id = Auth::user()->comercio_id;
     else
     $comercio_id = Auth::user()->id;

     if($this->sucursal_id != null) {
       $this->sucursal_id = $this->sucursal_id;
     } else {
       $this->sucursal_id = $comercio_id;
     }


  $pago_factura =   pagos_facturas::create([
       'monto' => $this->monto_ap,
       'caja' => $this->caja,
       'metodo_pago' => $this->metodo_pago_agregar_pago,
       'banco_id' => $this->tipo_pago,
       'recargo' => $this->recargo_total,
       'created_at' => $this->fecha_ap,
       'comercio_id' => $this->sucursal_id,
       'id_factura' => $ventaId,
       'cliente_id' => $this->cliente_query->cliente_id,
       'tipo_pago' => 1,
       'eliminado' => 0
     ]);

     $ventas_vieja = Sale::find($ventaId);

     $rec = $this->CalcularRecargo($ventaId);
    //dd($this->metodo_pago_agregar_pago);

     $ventas_vieja->update([
       'recargo' => $rec,
       'metodo_pago' => $this->metodo_pago_agregar_pago
     ]);
     
      $this->ActualizarTotalesVenta($ventaId);
      
      $deuda = $this->ActualizarEstadoDeuda($ventaId);
    
    // si es una venta a sucursal, tenemos que actualizar el stock de la compra en la sucursal 
    
    if($ventas_vieja->canal_venta == "Venta a sucursales") {
    $compra = compras_proveedores::where('sale_casa_central',$ventas_vieja->id)->first();
    $this->CreatePagoCompra($ventaId,$compra->id,$pago_factura);    

    }  
    
    //
    
     $this->monto_ap = '';
     $this->metodo_pago_ap = 'Elegir';
     $this->caja = cajas::where('estado',0)->where('eliminado',0)->where('user_id',Auth::user()->id)->max('id');

      $this->emit('pago-agregado', 'El pago fue guardado.');

      $this->emit('agregar-pago-hide', 'hide');

      $this->ResetPago();

      $this->RenderFactura($ventaId);

   }

   public function DeletePago($id)
   {
          $pago_viejo = pagos_facturas::find($id);
          $pago_viejo_id = $pago_viejo->id;
          
          $ventaId = $pago_viejo->id_factura;

          $ventas_vieja = Sale::find($ventaId);
        
          $rec = $this->CalcularRecargo($ventaId);
          
          $ventas_vieja->update([
            'recargo' => $this->recargo_nvo_venta
          ]);

          $pago_viejo->delete();
          
          // si es una venta a sucursal, tenemos que actualizar el stock de la compra en la sucursal 
                
          if($ventas_vieja->canal_venta == "Venta a sucursales") {
          $compra = compras_proveedores::where('sale_casa_central',$ventas_vieja->id)->first();
          $id_sucursal_id = pagos_facturas::where('pago_sucursal_id',$pago_viejo_id)->first()->id;
          $this->DeletePagoCompra($id_sucursal_id);    
          }  
                
          //
            
          $this->emit('pago-eliminado', 'El pago fue eliminado.');
     
          $this->ActualizarTotalesVenta($ventaId);
      
          $this->ActualizarEstadoDeuda($ventaId);

          $this->RenderFactura($ventaId);

          $this->estado = "display: block;";

   }

   public function TipoPago($value)
   {

   if($value == '2') {
   $this->emit('pago-dividido','Sales');
   }

   if($value == '1' || $value == '2') {

   $this->metodo_pago = $value;

   if($value == 1) {
     $this->metodo_pago_agregar_pago = 1;
     $this->recargo = 0;
     $this->recargo_total = 0;
     $this->total_pago = $this->monto_ap;
   }


   } else {
   	$this->metodo_pago = 'Elegir';
   }

   }
   public function CambioCaja() {


  $this->tipo_click = 1;

  if(Auth::user()->comercio_id != 1)
  $comercio_id = Auth::user()->comercio_id;
  else
  $comercio_id = Auth::user()->id;

  if($this->sucursal_id != null) {
    $this->sucursal_id = $this->sucursal_id;
  } else {
    $this->sucursal_id = $comercio_id;
  }



  $this->fecha_pedido_desde = $this->fecha_ap.' 00:00:00';

  $this->fecha_pedido_hasta = $this->fecha_ap.' 23:59:50';

  $this->emit('modal-estado','details loaded');

  $this->lista_cajas_dia = cajas::where('comercio_id', $this->sucursal_id)->where('eliminado',0)->whereBetween('fecha_inicio',[$this->fecha_pedido_desde, $this->fecha_pedido_hasta])->get();


}

    public function ElegirCaja($caja_id)
    {
    
    $this->caja = $caja_id;
    
    
    $this->emit('modal-estado-hide','close');
    
    }


    public function CancelarPagos($ventaId)
    {
    
    $this->cancelar_pago = pagos_facturas::where('pagos_facturas.id_factura', $ventaId)->get();
    
    foreach ($this->cancelar_pago as $pago) {
    
    $pago_individual = pagos_facturas::find($pago->id);
    $pago_individual->delete();
    
    }
    
    $this->emit('pago-eliminado', 'Los pagos asociados al pedido fueron eliminados.');
    
    }

public function ModalAbrirCaja() {

$this->emit('agregar-pago-hide','details loaded');

$this->emit('abrir-caja','details loaded');


}


  public function AbrirCajaGuardar() {

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    if($this->sucursal_id != null) {
      $this->sucursal_id = $this->sucursal_id;
    } else {
      $this->sucursal_id = $comercio_id;
    }

    $ultimo = cajas::where('cajas.comercio_id', 'like', $this->sucursal_id)->where('eliminado',0)->select('cajas.nro_caja')->latest('nro_caja')->first();

    if($ultimo != null)
    $nro = $ultimo->nro_caja + 1;
    else
    $nro = 1;



    $cajas = cajas::create([
      'user_id' => Auth::user()->id,
      'comercio_id' => $this->sucursal_id,
      'nro_caja' => $nro,
      'monto_inicial' => $this->monto_inicial,
      'estado' => '0',
      'fecha_inicio' => Carbon::now()

    ]);

    $this->caja = $cajas->id;

    $this->caja_seleccionada = $cajas->id;

    $this->emit('abrir-caja-hide','Show modal');

    $this->emit('agregar-pago','Show modal');

    $this->emit('cierre','Caja cerrada correctamente.');

  }


// SECCION HOJA DE RUTA

   public function AsignarHojaRuta($HojaRutaElegida, $ventaId)
   {

       $Hruta = Sale::find($ventaId);

       $Hruta->update([
         'hoja_ruta' => $HojaRutaElegida
       ]);

       $this->RenderFactura($ventaId);

       $this->emit('hr-asignada', 'El pedido fue agregado a la Hoja de Ruta.');


     }

   public function SinAsignarHojaRuta($ventaId)
   {

         $Hruta = Sale::find($ventaId);

         $Hruta->update([
           'hoja_ruta' => null
         ]);

         $this->RenderFactura($ventaId);


       }


   public function GuardarHojaDeRuta($ventaId)
   {

         if(Auth::user()->comercio_id != 1)
         $comercio_id = Auth::user()->comercio_id;
         else
         $comercio_id = Auth::user()->id;

         if($this->sucursal_id != null) {
           $this->sucursal_id = $this->sucursal_id;
         } else {
           $this->sucursal_id = $comercio_id;
         }


         $ultimo = hoja_ruta::where('hoja_rutas.comercio_id', 'like', $this->sucursal_id)->select('hoja_rutas.nro_hoja','hoja_rutas.id')->latest('nro_hoja')->first();

         if($ultimo == null) {

           $product = hoja_ruta::create([
             'nro_hoja' => 1,
             'fecha' => Carbon::parse($this->fecha_hr)->format('Y-m-d'),
             'nombre' => $this->nombre_hr,
             'tipo' => $this->tipo,
             'observaciones' => $this->observaciones_hr,
             'turno' => $this->turno,
             'comercio_id' => $this->sucursal_id
           ]);



           if(Auth::user()->comercio_id != 1)
           $comercio_id = Auth::user()->comercio_id;
           else
           $comercio_id = Auth::user()->id;

           if($this->sucursal_id != null) {
             $this->sucursal_id = $this->sucursal_id;
           } else {
             $this->sucursal_id = $comercio_id;
           }




           $Hruta = Sale::find($ventaId);

           $Hruta->update([
             'hoja_ruta' => $product->id
           ]);

           $this->turno = 'Elegir';
            $this->selected_id = '';
            $this->fecha = Carbon::now()->format('d-m-Y');

            $this->RenderFactura($ventaId);


            $this->emit('hr-added', 'Hoja de ruta registrada y agregado el pedido.');

            $this->emit('modal-hr-hide', '');




         } else {
           $hoja = $ultimo->nro_hoja + 1;
           $hoja_ulti = $ultimo->id + 1;

           $product = hoja_ruta::create([
             'nro_hoja' => $hoja,
             'fecha' => Carbon::parse($this->fecha_hr)->format('Y-m-d'),
             'nombre' => $this->nombre_hr,
             'tipo' => $this->tipo,
             'observaciones' => $this->observaciones_hr,
             'turno' => $this->turno,
             'comercio_id' => $this->sucursal_id
           ]);



           if(Auth::user()->comercio_id != 1)
           $comercio_id = Auth::user()->comercio_id;
           else
           $comercio_id = Auth::user()->id;

           if($this->sucursal_id != null) {
             $this->sucursal_id = $this->sucursal_id;
           } else {
             $this->sucursal_id = $comercio_id;
           }




           $Hruta = Sale::find($ventaId);

           $Hruta->update([
             'hoja_ruta' => $product->id
           ]);

           $this->turno = 'Elegir';
            $this->selected_id = '';
            $this->fecha = Carbon::now()->format('d-m-Y');

            $this->RenderFactura($ventaId);


            $this->emit('hr-added', 'Hoja de ruta registrada y agregado el pedido.');

            $this->emit('modal-hr-hide', '');





         }







       }


   public function getDetails3($saleId)
   {
             $this->id_pedido = $saleId;

               $this->emit('show-modal3','details loaded');

               $this->RenderFactura($saleId);
           }

    public function AbrirHRNueva($ventaId) {

    $this->RenderFactura($ventaId);

      $this->CerrarFactura();


        $this->emit('abrir-hr-nueva', 'Hr');

}



// SECCION ELIMINAR Y RESTAURAR VENTAS

// Restaurar Venta 

public function RestaurarVenta($id) {

    
    // elimina la venta
    $venta = Sale::find($id);
    $venta->eliminado = 0;
    $venta->save();
    
    // devuelve el stock de todos los productos vendidos
    
    $items = SaleDetail::where('sale_details.sale_id',$this->id_pedido)->get();

    foreach ($items as  $item) {
                
            $product = Product::find($item->product_id);
              //update stock
             
            $product_stock = productos_stock_sucursales::where('productos_stock_sucursales.product_id',$item->product_id)
            ->where('productos_stock_sucursales.referencia_variacion',$item->referencia_variacion)
            ->first();
            $product_stock->stock = $product_stock->stock - $item->quantity;
            $product_stock->save();

               $usuario_id = Auth::user()->id;

                if(Auth::user()->comercio_id != 1)
                $comercio_id = Auth::user()->comercio_id;
                else
                $comercio_id = Auth::user()->id;
            
            // creamos los movimientos de stock
            
              $historico_stock = historico_stock::create([
               'tipo_movimiento' => 13,
               'sale_id' => $this->id_pedido,
               'producto_id' => $product_stock->product_id,
               'referencia_variacion' => $product_stock->referencia_variacion,
               'cantidad_movimiento' => -$item->quantity,
               'stock' => $product_stock->stock,
               'usuario_id' => $usuario_id,
               'comercio_id'  => $this->sucursal_id
               ]);

                // Buscamos los pagos asociados a la venta y los eliminamos
                
                $pagos_venta = pagos_facturas::where('id_factura',$this->id_pedido)->get();
                
                foreach($pagos_venta as $pf) {
                
                $pago = pagos_facturas::find($pf->id);
                $pago->eliminado = 0;
                $pago->save();
                
                }


              ////////// WooCommerce ////////////

              if(Auth::user()->comercio_id != 1)
              $comercio_id = Auth::user()->comercio_id;
              else
              $comercio_id = Auth::user()->id;

              $wc = wocommerce::where('comercio_id', $comercio_id)->first();

              if($wc != null){

              $woocommerce = new Client(
                $wc->url,
                $wc->ck,
                $wc->cs,

                  [
                      'version' => 'wc/v3',
                  ]
              );

              $product = Product::find($item->product_id);

              if($product->wc_canal == 1) {

                $data = [
                    "stock_quantity" => $product_stock->stock,
                ];

                $this->wocommerce_product_id = 'products/'.$product->wc_product_id;

                $woocommerce->put($this->wocommerce_product_id , $data);

              }

              }


              ///////////////////////////////////////////////////


            }

    
}
// Eliminar una venta 

public function EliminarVenta($id) {
    
    // elimina la venta
    $venta = Sale::find($id);
    $venta->eliminado = 1;
    $venta->save();
    
    // devuelve el stock de todos los productos vendidos
    
    $items = SaleDetail::where('sale_details.sale_id',$this->id_pedido)->get();

    foreach ($items as  $item) {
                
            $product = Product::find($item->product_id);
              //update stock
             
            $product_stock = productos_stock_sucursales::where('productos_stock_sucursales.product_id',$item->product_id)
            ->where('productos_stock_sucursales.referencia_variacion',$item->referencia_variacion)
            ->first();
            $product_stock->stock = $product_stock->stock + $item->quantity;
            $product_stock->save();

               $usuario_id = Auth::user()->id;

                if(Auth::user()->comercio_id != 1)
                $comercio_id = Auth::user()->comercio_id;
                else
                $comercio_id = Auth::user()->id;
            
            // creamos los movimientos de stock
            
              $historico_stock = historico_stock::create([
               'tipo_movimiento' => 13,
               'sale_id' => $this->id_pedido,
               'producto_id' => $product_stock->product_id,
               'referencia_variacion' => $product_stock->referencia_variacion,
               'cantidad_movimiento' => $item->quantity,
               'stock' => $product_stock->stock,
               'usuario_id' => $usuario_id,
               'comercio_id'  => $this->sucursal_id
               ]);

                // Buscamos los pagos asociados a la venta y los eliminamos
                
                $pagos_venta = pagos_facturas::where('id_factura',$this->id_pedido)->get();
                
                foreach($pagos_venta as $pf) {
                
                $pago = pagos_facturas::find($pf->id);
                $pago->delete();
                
                }


              ////////// WooCommerce ////////////

              if(Auth::user()->comercio_id != 1)
              $comercio_id = Auth::user()->comercio_id;
              else
              $comercio_id = Auth::user()->id;

              $wc = wocommerce::where('comercio_id', $comercio_id)->first();

              if($wc != null){

              $woocommerce = new Client(
                $wc->url,
                $wc->ck,
                $wc->cs,

                  [
                      'version' => 'wc/v3',
                  ]
              );

              $product = Product::find($item->product_id);

              if($product->wc_canal == 1) {

                $data = [
                    "stock_quantity" => $product_stock->stock,
                ];

                $this->wocommerce_product_id = 'products/'.$product->wc_product_id;

                $woocommerce->put($this->wocommerce_product_id , $data);

              }

              }


              ///////////////////////////////////////////////////


            }

}

// Cambio de estado de los pedidos

    public function Update2($estado_id, $origen)
    {
        
        $this->SetCasaCentral();
        // Aca tenemos que modificar los stocks en casa central y sucursales


      $estado = Sale::select('sales.status','sales.id','users.email','sales.canal_venta','sales.wc_order_id','sales.cliente_id')
      ->join('clientes_mostradors','clientes_mostradors.id','sales.cliente_id')
      ->leftjoin('users','users.cliente_id','clientes_mostradors.id')
      ->where('sales.id', $this->id_pedido)->first();

      $estado_original = $estado->status;
        
     // dd($estado->cliente_id);

      $estado->update([
        'status' => $estado_id
      ]);

    /*  if($estado->wc_order_id != null) {

           ////////// WooCommerce ////////////

              if(Auth::user()->comercio_id != 1)
              $comercio_id = Auth::user()->comercio_id;
              else
              $comercio_id = Auth::user()->id;


              $wc = wocommerce::where('comercio_id', $comercio_id)->first();

              if($wc != null){

              $woocommerce = new Client(
                $wc->url,
                $wc->ck,
                $wc->cs,

                  [
                      'version' => 'wc/v3',
                  ]
              );


                  if($estado->status == 2) {
                      $this->status = "processing" ;

                    }
                    if($estado->status ==  3) {
                      $this->status = "completed";

                    }
                    if($estado->status == 4) {
                      $this->status = "cancelled" ;

                    }
                    if($estado->status == 1) {
                      $this->status = "on-hold";


                    }

                    $data = [
                    'status' => $this->status
                    ];

                    $woocommerce->put('orders/'.$estado->wc_order_id, $data);


      }


      } */


      // si el estado original es cancelado 
        
      if($estado_original == "Cancelado") {

        if($estado_id <> 4) {

          $items = SaleDetail::where('sale_details.sale_id',$this->id_pedido)->get();

            foreach ($items as  $item) {
                
            $product = Product::find($item->product_id);
              //update stock
             
            $product_stock = productos_stock_sucursales::where('productos_stock_sucursales.product_id',$item->product_id)
            ->where('productos_stock_sucursales.referencia_variacion',$item->referencia_variacion)
            ->where('productos_stock_sucursales.sucursal_id',$this->sucursal_id)
            ->where('productos_stock_sucursales.comercio_id',$this->casa_central_id)
            ->first();
            $product_stock->stock = $product_stock->stock - $item->quantity;
            $product_stock->save();

               $usuario_id = Auth::user()->id;

                if(Auth::user()->comercio_id != 1)
                $comercio_id = Auth::user()->comercio_id;
                else
                $comercio_id = Auth::user()->id;

              $historico_stock = historico_stock::create([
               'tipo_movimiento' => 1,
               'sale_id' => $this->id_pedido,
               'producto_id' => $product_stock->product_id,
               'referencia_variacion' => $product_stock->referencia_variacion,
               'cantidad_movimiento' => -$item->quantity,
               'stock' => $product_stock->stock,
               'usuario_id' => $usuario_id,
               'comercio_id'  => $this->sucursal_id
               ]);
               
               
            // si es una venta a sucursal, tenemos que actualizar el stock de la compra en la sucursal 
            if($estado->canal_venta == "Venta a sucursales") {
                
                
            }




              ////////// WooCommerce ////////////

              if(Auth::user()->comercio_id != 1)
              $comercio_id = Auth::user()->comercio_id;
              else
              $comercio_id = Auth::user()->id;

              $wc = wocommerce::where('comercio_id', $comercio_id)->first();

              if($wc != null){

              $woocommerce = new Client(
                $wc->url,
                $wc->ck,
                $wc->cs,

                  [
                      'version' => 'wc/v3',
                  ]
              );

              $product = Product::find($item->product_id);

              if($product->wc_canal == 1) {

                $data = [
                    "stock_quantity" => $product_stock->stock,
                ];

                $this->wocommerce_product_id = 'products/'.$product->wc_product_id;

                $woocommerce->put($this->wocommerce_product_id , $data);

              }

              }


              ///////////////////////////////////////////////////


            }
            

        }

      } else {
        
        // si el estado original no es cancelado 
        
        if($estado_id == 4) {

          $items = SaleDetail::where('sale_details.sale_id',$this->id_pedido)->get();

            foreach ($items as  $item) {
                
            $product = Product::find($item->product_id);
              //update stock
             
            $product_stock = productos_stock_sucursales::where('productos_stock_sucursales.product_id',$item->product_id)
            ->where('productos_stock_sucursales.referencia_variacion',$item->referencia_variacion)
            ->first();
            $product_stock->stock = $product_stock->stock + $item->quantity;
            $product_stock->save();

               $usuario_id = Auth::user()->id;

                if(Auth::user()->comercio_id != 1)
                $comercio_id = Auth::user()->comercio_id;
                else
                $comercio_id = Auth::user()->id;
            
            // creamos los movimientos de stock
            
              $historico_stock = historico_stock::create([
               'tipo_movimiento' => 13,
               'sale_id' => $this->id_pedido,
               'producto_id' => $product_stock->product_id,
               'referencia_variacion' => $product_stock->referencia_variacion,
               'cantidad_movimiento' => $item->quantity,
               'stock' => $product_stock->stock,
               'usuario_id' => $usuario_id,
               'comercio_id'  => $this->sucursal_id
               ]);

                // Buscamos los pagos asociados a la venta y los eliminamos
                
                $pagos_venta = pagos_facturas::where('id_factura',$this->id_pedido)->get();
                
                foreach($pagos_venta as $pf) {
                
                $pago = pagos_facturas::find($pf->id);
                $pago->delete();
                
                }


              ////////// WooCommerce ////////////

              if(Auth::user()->comercio_id != 1)
              $comercio_id = Auth::user()->comercio_id;
              else
              $comercio_id = Auth::user()->id;

              $wc = wocommerce::where('comercio_id', $comercio_id)->first();

              if($wc != null){

              $woocommerce = new Client(
                $wc->url,
                $wc->ck,
                $wc->cs,

                  [
                      'version' => 'wc/v3',
                  ]
              );

              $product = Product::find($item->product_id);

              if($product->wc_canal == 1) {

                $data = [
                    "stock_quantity" => $product_stock->stock,
                ];

                $this->wocommerce_product_id = 'products/'.$product->wc_product_id;

                $woocommerce->put($this->wocommerce_product_id , $data);

              }

              }


              ///////////////////////////////////////////////////


            }

        }


      /////////////// SI EL ESTADO NUEVO ES DISTINTO A CANCELADO //////////////////

      if($estado_id != 4)
      {
        $items = SaleDetail::where('sale_details.sale_id',$this->id_pedido)->get();

          foreach ($items as  $item) {

          }


      }

      }

    
      if($estado->canal_venta == "Ecommerce") {



		return \Redirect::to('estado-email/pdf' . '/' . $estado->id  . '/' . $estado->email . '/' . $estado->status);

      }

    if($origen == 1) {
      $this->emit('hide-modal-actualizar-estado','');
    } 
    if($origen == 2) {
    
      $this->RenderFactura($this->id_pedido);

      $this->emit('hide-modal3','details loaded');

    }

    //3-7
    
    $this->ActualizarEstadoDeuda($this->id_pedido);
    
    }
    
    
    // Cambio de estado de los pedidos

    public function Update($estado_id, $origen)
    {   
        $this->SetCasaCentral();
        
        // Aca tenemos que modificar los stocks en casa central y sucursales

      $estado = Sale::select('sales.status','sales.id','users.email','sales.canal_venta','sales.wc_order_id','sales.cliente_id')
      ->join('clientes_mostradors','clientes_mostradors.id','sales.cliente_id')
      ->leftjoin('users','users.cliente_id','clientes_mostradors.id')
      ->where('sales.id', $this->id_pedido)->first();

      $estado_original = $estado->status;
     
      $estado->update([
        'status' => $estado_id
      ]);


     $items = SaleDetail::where('sale_details.sale_id',$this->id_pedido)->get();

     foreach ($items as  $item) {
                
            $product = Product::find($item->product_id);
              //update stock

            // Si el estado nuevo es cancelado 
            
            //dd($estado_original,$estado_id);

            $this->SwitchUpdateEstado($estado_original,$estado_id,$item,$this->sucursal_id);
            
            // SI EL USUARIO ES CASA CENTRAL, Y LA VENTA ES A UNA SUCURSAL
            
            if($this->sucursal_id == 0) {
            
            if($estado->canal_venta == "Venta a sucursales") {
            
            $this->UpdateEstadoSucursal($this->id_pedido,$estado,$estado_id,$estado_original,$item);
            
            }

            }
            
            //if($this->sucursal_id == 0) {
            //$this->SwitchUpdateEstadoCasaCentral($estado_original,$estado_id,$item);     
            //}

          
               

              ///////////////////////////////////////////////////
              //dd($item);
              
         
     }
 
      if($estado->canal_venta == "Ecommerce") {

		return \Redirect::to('estado-email/pdf' . '/' . $estado->id  . '/' . $estado->email . '/' . $estado->status);

      }
      
      $this->wooCommerceUpdateStockGlobal($estado->id,1); // Aca tenemos que modificar los stock de wocommerce cuando se modifican los estados
      
      //dd($origen);
       

      if($origen == 2) {
      $this->emit('hide-modal-actualizar-estado','details loaded');
      
      $this->ActualizarTotalesVenta($this->id_pedido);
        
      $this->ActualizarEstadoDeuda($this->id_pedido);
        
      } else {
      
      $this->ActualizarTotalesVenta($this->id_pedido);
        
      $this->ActualizarEstadoDeuda($this->id_pedido);

      
      $this->RenderFactura($this->id_pedido);

     
      }

        
    $this->emit('pago-agregado', 'El estado fue guardado.');
    }
    
    public function UpdateEstadoSucursal($id_pedido,$estado,$estado_id,$estado_original,$item) {
    
            $cp = compras_proveedores::where('sale_casa_central',$id_pedido)->where('eliminado',0)->first();
         
            $cp->update([
            'status' => $estado_id
            ]);
            
            //
            $sucursal = User::where('cliente_id',$estado->cliente_id)->first()->id;
            
            // Si el estado era pendiente y pasa a entregado, en la sucursal se actualiza 
            // Si el estado nuevo es entregado 
            if($estado_original != "Entregado" && $estado_id == 3) {
            $return = $this->UpdateEstadoSumarStock($item,$sucursal,$estado_id);   // Sumar stock en la sucursal   
            $returnReal = $this->UpdateEstadoSumarStockReal($item,$sucursal,$estado_id);   // Sumar stock en la sucursal   
            
            $this->HistoricoStock($item->product_id,$item->referencia_variacion,$item->quantity,$returnReal,$sucursal,$estado_id,2);
            }
            
            // Si el estado anterior era entregado y el nuevo es otro que no sea entregado 
            if($estado_original == "Entregado" && $estado_id != 3) {
            $return = $this->UpdateEstadoRestarStock($item,$sucursal,$estado_id);   // Sumar stock en la sucursal    
            $returnReal = $this->UpdateEstadoRestarStockReal($item,$sucursal,$estado_id);   // Sumar stock en la sucursal  
            
            $this->HistoricoStock($item->product_id,$item->referencia_variacion,-$item->quantity,$returnReal,$sucursal,$estado_id,2);
            }    
    }
    
    public function SwitchUpdateEstado($estado_original,$estado_id,$item,$sucursal_id) {
        
            // Si el ESTADO ORIGINAL es Pendiente/En proceso  
            
            if(($estado_original == "Pendiente") ||  ($estado_original == "En proceso" ) ) {
                
                // El estado nuevo es cancelado -- OK
                if($estado_id == 4) {
                    $return = $this->UpdateEstadoSumarStock($item,$sucursal_id,$estado_id);    // Actualiza stock de casa central -- Sumandole las unidades
                }   
                    
                // El estado nuevo es entregado  -- OK    
               if($estado_id == 3) {
                    // El stock disponible queda igual
                    $returnReal = $this->UpdateEstadoRestarStockReal($item,$sucursal_id,$estado_id);    // Actualiza stock de casa central -- Restandole las unidades al stock real     
     
                    // sumamos el historico de stock
                    $this->HistoricoStock($item->product_id,$item->referencia_id,-$item->quantity,$returnReal,$item->comercio_id,$estado_id,1);
              
               }   
                
                
                
            }
            
            // Si el ESTADO ORIGINAL es ENTREGADO
            
            if(($estado_original == "Entregado")) {
                
                // El estado nuevo es cancelado -- OK
                if($estado_id == 4) {
                $return = $this->UpdateEstadoSumarStock($item,$sucursal_id,$estado_id);    // Actualiza stock disponible de casa central -- Sumandole las unidades
                $returnReal = $this->UpdateEstadoSumarStockReal($item,$sucursal_id,$estado_id);    // Actualiza stock real de casa central -- Sumandole las unidades
                
                // sumamos el historico de stock
                $this->HistoricoStock($item->product_id,$item->referencia_id,$item->quantity,$returnReal,$item->comercio_id,$estado_id,1); 
                }   
                
                // El estado nuevo es pendiente/en proceso -- OK
                
                if($estado_id != 4 && $estado_id != 3) {
                $returnReal = $this->UpdateEstadoSumarStockReal($item,$sucursal_id,$estado_id);    // Actualiza stock real de casa central -- Sumandole las unidades
                
                // sumamos el historico de stock
                $this->HistoricoStock($item->product_id,$item->referencia_id,$item->quantity,$returnReal,$item->comercio_id,$estado_id,1);     
                }   
                
            }
            
             // Si el ESTADO ORIGINAL es Cancelado 
            
            if(($estado_original == "Cancelado")) {
                
            // El estado nuevo es entregado -- OK
                if($estado_id == 3) {
                $return = $this->UpdateEstadoRestarStock($item,$sucursal_id,$estado_id);    // Actualiza stock disponible de casa central -- restandole las unidades
                $returnReal = $this->UpdateEstadoRestarStockReal($item,$sucursal_id,$estado_id);    // Actualiza stock real de casa central -- restandole las unidades
                
                // sumamos el historico de stock
                $this->HistoricoStock($item->product_id,$item->referencia_id,-$item->quantity,$returnReal,$item->comercio_id,$estado_id,1); 
                }      
                
            // El estado nuevo es pendiente/en proceso -- OK
                
                if($estado_id != 4 && $estado_id != 3) {
                // El stock real queda igual
                 $return = $this->UpdateEstadoRestarStock($item,$sucursal_id,$estado_id);    // Actualiza stock disponible de casa central -- restandole las unidades
                //dd($return);
                 
                // dd($return,$returnReal);
               }   
                
            }
          
    }
    
    public function GetStockUpdateEstado($item, $sucursal, $estado_id) {
      return  productos_stock_sucursales::where('productos_stock_sucursales.product_id',$item->product_id)
            ->where('productos_stock_sucursales.referencia_variacion',$item->referencia_variacion)
            ->where('productos_stock_sucursales.sucursal_id',$sucursal)
            ->where('productos_stock_sucursales.comercio_id',$this->casa_central_id)
            ->first();  
    }


    public function UpdateEstadoRestarStock($item, $sucursal, $estado_id) {
            
            $product_stock = $this->GetStockUpdateEstado($item, $sucursal, $estado_id);
            
            $stock_disponible = $product_stock->stock - $item->quantity;
            
            $product_stock->update([
                'stock' => $stock_disponible,
                'stock_real' => $product_stock->stock_real
                ]);        
         
    }
    
    
    
    public function UpdateEstadoRestarStockReal($item, $sucursal, $estado_id) {
        
            $product_stock = $this->GetStockUpdateEstado($item, $sucursal, $estado_id);
            
            $stock_real = $product_stock->stock_real - $item->quantity;
            
            $product_stock->update([
                'stock' => $product_stock->stock,
                'stock_real' => $stock_real
            ]);
            
            return $stock_real;

    }
  
    // ACTUALIZAR EL STOCK DISPONIBLE
      
    public function UpdateEstadoSumarStock($item, $sucursal, $estado_id) { 
        
            $product_stock = $this->GetStockUpdateEstado($item, $sucursal, $estado_id);
            
            $stock = $product_stock->stock + $item->quantity;
            
            $product_stock->update([
                'stock' => $stock,
                'stock_real' => $product_stock->stock_real
                ]);    

    }

    
    // ACTUALIZAR EL STOCK REAL
    
    public function UpdateEstadoSumarStockReal($item, $sucursal, $estado_id) {

            $product_stock = $this->GetStockUpdateEstado($item, $sucursal, $estado_id);
            
            $stock_real = $product_stock->stock_real + $item->quantity;
            
            $product_stock->update([
                'stock'=> $product_stock->stock,
                'stock_real' => $stock_real,
                ]);    
                
              //  dd($product_stock);
              
              return $stock_real;
               
    }    
        
    
// SECCION INTERACCIONES CON AFIP 

    public function CerrarFactura() {
              $this->emit('cerrar-factura','details loaded');
            }


    public function VerAfip() {


                $afip = new Afip(array('CUIT'=> 20127072583, 'production' => true)); //Reemplazar el CUIT

                //Esta línea es solo para probar si funciona pero no es obligatorio
                $test = $afip->ElectronicBilling->GetDocumentTypes();

                dd($test);

                    }
                    
    // Facturacion de AFIP de las ventas

	public function FacturarAfip($ventaIdFactura)
	{
	    $this->EmitirFacturaTrait($ventaIdFactura);
	}
	
	

    public function ObtenerInfoFactura($VentaId) {
    
    if(Auth::user()->comercio_id != 1)
    
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;
    $this->datos_facturacion = datos_facturacion::where('comercio_id', $comercio_id)->first();
    
    $venta = Sale::find($VentaId);
    
    $array = explode("-",$venta->nro_factura);
    
    $tipo_factura = $array[0];
    
    /* Vemos que tipo de factura es */
    if($tipo_factura == "FB") {
     $tipo_comprobante = 6;   
    }
    if($tipo_factura == "B") {
     $tipo_comprobante = 6;   
    }
    if($tipo_factura == "FA") {
     $tipo_comprobante = 1;   
    }
    if($tipo_factura == "A") {
     $tipo_comprobante = 1;   
    }
    
    $nro_factura = $array[2];
    
    
    if($this->datos_facturacion->cuit != null || $this->datos_facturacion->cuit != '') {
    
    
    $afip = new Afip(array('CUIT' => $this->datos_facturacion->cuit, 'production' => true));
    
    
    /**
     * Numero de factura
     **/
    $numero_de_factura = $nro_factura;
    
    /**
     * Numero del punto de venta
     **/
    $punto_de_venta = $this->datos_facturacion->pto_venta;
    
    /**
     * Tipo de comprobante
     **/
    $tipo_de_comprobante = $tipo_comprobante; // 6 = Factura B , 1 = Factura A
    
    /**
     * Informacion de la factura
     **/
    $informacion = $afip->ElectronicBilling->GetVoucherInfo($numero_de_factura, $punto_de_venta, $tipo_de_comprobante); 
    
    if($informacion === NULL){
        dd('La factura no existe');
    }
    else{
    	/**
    	 * Mostramos por pantalla la información de la factura
    	 **/
        dd($informacion->ImpNeto, $informacion->ImpIVA);
    
    }
    
    }
    }
    
        
    public function AnularFactura($ventaId) {
        
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;
    $this->datos_facturacion = datos_facturacion::where('comercio_id', $comercio_id)->first();
    
    
    $this->factura = Sale::join('clientes_mostradors','clientes_mostradors.id','sales.cliente_id')->select('clientes_mostradors.id as cliente_id','clientes_mostradors.dni','sales.*')->find($ventaId);
    
    $venta = Sale::find($ventaId);
    
    $array = explode("-",$venta->nro_factura);
    
    $tipo_factura = $array[0];
    
    /* Vemos que tipo de factura es */
    if($tipo_factura == "FB") {
     $tipo_comprobante_factura = 6;
     $tipo_comprobante_nota_credito = 8;   
    }
    if($tipo_factura == "B") {
    $tipo_comprobante_factura = 6;
    $tipo_comprobante_nota_credito = 8;   
    }
    if($tipo_factura == "FA") {
    $tipo_comprobante_factura = 1;
    $tipo_comprobante_nota_credito = 3;   
    }
    if($tipo_factura == "A") {
    $tipo_comprobante_factura = 1;
     $tipo_comprobante_nota_credito = 3;   
    }
    if($tipo_factura == "FC") {
    $tipo_comprobante_factura = 11;
    $tipo_comprobante_nota_credito = 13;   
    }
    if($tipo_factura == "C") {
    $tipo_comprobante_factura = 11;
    $tipo_comprobante_nota_credito = 13;   
    }
    
    
    /* Obtenemos el punto de venta usado en esa factura */
    $pto_venta = $array[1];
    /* Obtenemos el numero de factura */
    $nro_factura = $array[2];
    
    
    if($this->datos_facturacion->cuit != null || $this->datos_facturacion->cuit != '') {
    
    
    $afip = new Afip(array('CUIT' => $this->datos_facturacion->cuit, 'production' => true));
    
    /**
     * Numero del punto de venta
     **/
    $punto_de_venta = $pto_venta;
    
    /**
     * Tipo de Nota de Crédito
     **/
    $tipo_de_nota = $tipo_comprobante_nota_credito; // 8 = Nota de Crédito B
    
    /**
     * Número de la ultima Nota de Crédito B
     **/
    $last_voucher = $afip->ElectronicBilling->GetLastVoucher($punto_de_venta, $tipo_de_nota);
    
    /**
     * Numero del punto de venta de la Factura 
     * asociada a la Nota de Crédito
     **/
    $punto_factura_asociada = $pto_venta;
    
    /**
     * Tipo de Factura asociada a la Nota de Crédito
     **/
    $tipo_factura_asociada = $tipo_comprobante_factura; // 6 = Factura B
    
    /**
     * Numero de Factura asociada a la Nota de Crédito
     **/
    $numero_factura_asociada = $nro_factura;
    
    /**
     * Concepto de la Nota de Crédito
     *
     * Opciones:
     *
     * 1 = Productos 
     * 2 = Servicios 
     * 3 = Productos y Servicios
     **/
    $concepto = 1;
    
    /**
     * Tipo de documento del comprador
     *
     * Opciones:
     *
     * 80 = CUIT 
     * 86 = CUIL 
     * 96 = DNI
     * 99 = Consumidor Final 
     **/
     
     
    /**
     * Numero de documento del comprador (0 para consumidor final)
     **/
     
    if ($this->factura->cliente_id == 1) {
    
     $tipo_de_documento = 99;
    
    $numero_de_documento = 0;
    
    } else {
    
    $tipo_de_documento = 80;
    
    $numero_de_documento = $this->factura->dni;
    
    }
    
    /**
     * Numero de Nota de Crédito
     **/
    $numero_de_nota = $last_voucher+1;
    
    /**
     * Fecha de la Nota de Crédito en formato aaaa-mm-dd (hasta 10 dias antes y 10 dias despues)
     **/
    $fecha = date('Y-m-d');
    
    /**
     * Informacion de la factura
     **/
    $informacion = $afip->ElectronicBilling->GetVoucherInfo($nro_factura, $pto_venta, $tipo_comprobante_factura); 
    
    if($informacion === NULL){
        dd('La factura no existe');
    }
    else{
        
    $importe_gravado = $informacion->ImpNeto;
    $importe_iva = $informacion->ImpIVA;
    
    /**
     * Importe exento al IVA
     **/
    $importe_exento_iva = 0;
    
    /**
     * Importe de IVA
     **/
    
    $fecha_servicio_desde = null;
    $fecha_servicio_hasta = null;
    $fecha_vencimiento_pago = null;
    
    
    
    $data = array(
    	'CantReg' 	=> 1, // Cantidad de Notas de Crédito a registrar
    	'PtoVta' 	=> $punto_de_venta,
    	'CbteTipo' 	=> $tipo_de_nota, 
    	'Concepto' 	=> $concepto,
    	'DocTipo' 	=> $tipo_de_documento,
    	'DocNro' 	=> $numero_de_documento,
    	'CbteDesde' => $numero_de_nota,
    	'CbteHasta' => $numero_de_nota,
    	'CbteFch' 	=> intval(str_replace('-', '', $fecha)),
    	'FchServDesde'  => $fecha_servicio_desde,
    	'FchServHasta'  => $fecha_servicio_hasta,
    	'FchVtoPago'    => $fecha_vencimiento_pago,
    	'ImpTotal' 	=> $importe_gravado + $importe_iva + $importe_exento_iva,
    	'ImpTotConc'=> 0, // Importe neto no gravado
    	'ImpNeto' 	=> $importe_gravado,
    	'ImpOpEx' 	=> $importe_exento_iva,
    	'ImpIVA' 	=> $importe_iva,
    	'ImpTrib' 	=> 0, //Importe total de tributos
    	'MonId' 	=> 'PES', //Tipo de moneda usada en la Nota de Crédito ('PES' = pesos argentinos) 
    	'MonCotiz' 	=> 1, // Cotización de la moneda usada (1 para pesos argentinos)  
    	'CbtesAsoc' => array( //Factura asociada
    		array(
    			'Tipo' 		=> $tipo_factura_asociada,
    			'PtoVta' 	=> $punto_factura_asociada,
    			'Nro' 		=> $numero_factura_asociada,
    		)
    	),
    	'Iva' 		=> array(// Alícuotas asociadas a la Nota de Crédito
    		array(
    			'Id' 		=> 5, // Id del tipo de IVA (5 = 21%)
    			'BaseImp' 	=> $importe_gravado,
    			'Importe' 	=> $importe_iva 
    		)
    	), 
    );
    
    /** 
     * Creamos la Nota de Crédito 
     **/
    $res = $afip->ElectronicBilling->CreateVoucher($data);
    
    /**
     * Mostramos por pantalla los datos de la nueva Nota de Crédito 
     **/
    
    
       nota_credito::create([
          'cae' => $res['CAE'], //CAE asignado a la Factura
          'vto_cae' => $res['CAEFchVto'], //Fecha de vencimiento del CAE
          'nro_nota_credito' => "NC-".$tipo_factura."-".$numero_de_nota,
          'nro_factura' => $venta->nro_factura,
          'venta_id' => $ventaId,
          'comercio_id' => $venta->comercio_id 
          ]);
    
     $this->emit('pago-actualizado', 'NOTA DE CREDITO GENERADA CORRECTAMENTE');
    
    }
    }
    }


// SECCION ACCIONES DE LA VENTA (MAILS, IMPRIMIR, ETC)

public function CerrarModalEstado()
{

  $this->emit('modal-estado-hide','close');

    $this->tipo_click = 0;
 
}



public function AccionEnLote($ids, $id_accion)
{

$this->ventas_checked = Sale::select('sales.id','sales.nro_factura')->whereIn('sales.id',$ids)->orderBy('sales.id','asc')->get();

foreach ($this->ventas_checked as $vc) {

    if($vc->nro_factura == null) {

    $this->FacturarAfip($vc->id);

    }

    }
    
$this->id_check = [];


}


public function MailModal($ventaId) {

    $this->ventaId = $ventaId;
    $venta = Sale::find($ventaId);
    
    $cliente = ClientesMostrador::find($venta->cliente_id);
    
    $this->mail_ingresado = $cliente->email;
    $this->emit('mail-modal', '');

}



public function MailModalVerVenta($ventaId) {

    $this->ventaId = $ventaId;
    $venta = Sale::find($ventaId);
    
    $cliente = ClientesMostrador::find($venta->cliente_id);
    
    $this->mail_ingresado = $cliente->email;
    $this->emit('mail-modal', '');

    $this->RenderFactura($ventaId);

}


public function EnviarMail() {


      return redirect('report-email/pdf' . '/' . $this->ventaId  . '/' . $this->mail_ingresado);

}





public function ElegirSucursal($sucursal_id) {

  	$this->sucursal_id = $sucursal_id;

  }

public function AbrirImprimir($ventaId) {

    $this->RenderFactura($ventaId);

      $this->CerrarFactura();


        $this->emit('abrir-imprimir', 'imprimir');

}


public function ExportarReporte($url) {

    return redirect('report/excel/'.$this->sucursal_id.'/'. $url .'/'. Carbon::now()->format('d_m_Y_H_i_s'));

}


// SECCION EDITAR VENTA

       
public function EditarPedido($style) {

    if ($style === "none") {

      $this->style = "block";

      $this->style2 = "none";

      $this->RenderFactura($this->NroVenta);

    } else {
      $this->style = "none";

      $this->style2 = "block";

      $this->RenderFactura($this->NroVenta);

    }



}

public function AbrirModalEditarCliente() {

    $this->emit('editar-cliente','Show modal');
    
    $this->RenderFactura($this->NroVenta);
}




 //-------- FUNCION QUE MUESTRA LOS PRODUCTOS PARA AGREGAR UNO NUEVO A LA VENTA --------------------------//
 
     public function updatedQueryProduct()
    {

    $this->TipoUsuario();
    
    $this->products_s = 	Product::where('comercio_id', 'like', $this->casa_central_id)
    ->where('eliminado',0)
    ->where( function($query) {
              $query->where('name', 'like', '%' . $this->query_product . '%')->orWhere('barcode', 'like', $this->query_product . '%');
    })
            ->limit(5)
            ->get()
            ->toArray();

    $this->RenderFactura($this->NroVenta);

    }
    
    
       public function selectProduct($item)
       {
           
         // FALTA ACTUALIZAR         
         
         // Aca si es una venta a una sucursal tiene que tomar los precios internos y actualizar el stock en la sucursal si es entregado


         $this->TipoUsuario();

         $producto_venta = SaleDetail::where('sale_details.product_id', $item)->where('sale_details.sale_id', $this->NroVenta)->where('sale_details.eliminado', 0)->first();

         $product = Product::find($item);


         ////////// SI ES VARIACION //////////////////////


         if($product->producto_tipo == "v") {

         $this->productos_variaciones_datos =  productos_variaciones_datos::where('product_id',$product->id)->where('comercio_id', $this->casa_central_id)->get();

         $this->atributos = productos_variaciones::join('variaciones','variaciones.id','productos_variaciones.variacion_id')
         ->select('variaciones.nombre','variaciones.id as atributo_id', 'productos_variaciones.referencia_id')
         ->where('productos_variaciones.producto_id', $product->id)
         ->get();

         $this->product_id = $product->id;
         $this->barcode = $product->barcode;

         $this->variaciones = variaciones::where('variaciones.comercio_id', $this->casa_central_id)->get();



         $this->RenderFactura($this->NroVenta);
         $this->emit('variacion-elegir', $product->id);

         $this->resetProduct();


         return $this->barcode;
         }

         ////////////////////////////////////////////////////

        
         $product_stock = productos_stock_sucursales::join('products','products.id','productos_stock_sucursales.product_id')
         ->select('productos_stock_sucursales.*')
         ->where('products.id', $item)
         ->where('referencia_variacion',0)
         ->where('productos_stock_sucursales.sucursal_id', $this->sucursal_id)
         ->where('products.eliminado', 0)
         ->first();
         
         
         $venta = Sale::find($this->NroVenta);
         
         // Aca si es producto de venta interno tiene que traer el precio interno 
         
        if($venta->canal_venta == "Venta a sucursales") {
           $product_price = $product->precio_interno;  
         } else {
         $product_price = productos_lista_precios::join('products','products.id','productos_lista_precios.product_id')
         ->select('productos_lista_precios.*')
         ->where('products.id', $item)
         ->where('products.eliminado', 0)
         ->where('referencia_variacion',0)
         ->where('productos_lista_precios.lista_id',0)
         ->first()->precio_lista;
         }
         
         
         
         
         // Datos del producto nuevo a ingresar 
         
         
        $datos_facturacion = datos_facturacion::where('datos_facturacions.comercio_id', $venta->comercio_id)->first();

         // Si trabaja con precio + IVA
        if($venta != null) {
         if($venta->relacion_precio_iva == 1 || $venta->relacion_precio_iva == 0 ) {
                
         $precio = $product_price;
          
         }
       
         // Si el IVA esta dentro del precio
            
         if($venta->relacion_precio_iva == 2) {
 
         $precio = $product_price/(1+$venta->alicuota_iva);
           
         }
         
         $iva_defecto = $venta->alicuota_iva;
         
        } else {
        $precio = $product_price;   
        $iva_defecto = 0;
        }
        
        // Comprueba si hay stock o no del producto
        
        if($product_stock->stock < 1 && $product->stock_descubierto == "si" ) {

           $this->emit('no-stock','Stock insuficiente');
           $this->RenderFactura($this->NroVenta);

         } else {
         
         // comprueba si el producto esta en la venta o no...
         
         if($producto_venta == [] || $producto_venta == null || empty($producto_venta))
         {
             
        // no esta en la venta, lo ingresa....

         SaleDetail::create([
           'price' => $precio,
           'quantity' => 1,
           'product_name' => $product->name,
           'product_barcode' => $product->barcode,
           'product_id' => $product->id,
           'metodo_pago'  => $venta->metodo_pago,
           'seccionalmacen_id' => $product->seccionalmacen_id,
           'comercio_id' => $this->sucursal_id,
           'sale_id' => $this->NroVenta,
           'iva' => $iva_defecto,
           'canal_venta' => $venta->canal_venta,
           'descuento' => 0,
           'recargo' => 0,
           'cliente_id' => $venta->cliente_id
         ]);
         
         if($venta->canal_venta == "Venta a sucursales") {         
         
         $compras_proveedores = compras_proveedores::where('sale_casa_central',$this->NroVenta)->first();
         
         
         // actualizacion del detalle de compra --
         
          detalle_compra_proveedores::create([
            'producto_id' => $product->id, 
            'referencia_variacion' => 0,
            'precio' => $precio,
            'nombre' => $product->name,
            'barcode' => $product->barcode,
            'cantidad' => 1,
            'iva' => $iva_defecto*$precio,
            'alicuota_iva' => $iva_defecto,
            'compra_id' => $compras_proveedores->id,
            'comercio_id' => $this->sucursal_id
          ]);
          
         }

         //update stock
         $product_stock->stock = $product_stock->stock - 1;
         $product_stock->save();
         
         $usuario_id = Auth::user()->id;

         if(Auth::user()->comercio_id != 1)
         $comercio_id = Auth::user()->comercio_id;
         else
         $comercio_id = Auth::user()->id;

         if($this->sucursal_id != null) {
           $this->sucursal_id = $this->sucursal_id;
         } else {
           $this->sucursal_id = $comercio_id;
         }


         $historico_stock = historico_stock::create([
           'tipo_movimiento' => 2,
           'sale_id' => $venta->id,
           'producto_id' => $product->id,
           'cantidad_movimiento' => -1,
           'stock' => $product_stock->stock,
           'usuario_id' => $usuario_id,
           'comercio_id'  => $this->sucursal_id
         ]);
        
         $this->ActualizarTotalesVenta($this->NroVenta);
         $this->ActualizarEstadoDeuda($this->NroVenta);

         } else {

        // Si el producto existe en la venta

         $producto_venta->update([
           'quantity' => $producto_venta->quantity + 1
         ]);


         //update stock
         $product_stock->stock = $product_stock->stock - 1;
         $product_stock->save();

         $usuario_id = Auth::user()->id;

         if(Auth::user()->comercio_id != 1)
         $comercio_id = Auth::user()->comercio_id;
         else
         $comercio_id = Auth::user()->id;

         if($this->sucursal_id != null) {
           $this->sucursal_id = $this->sucursal_id;
         } else {
           $this->sucursal_id = $comercio_id;
         }


         $historico_stock = historico_stock::create([
           'tipo_movimiento' => 1,
           'cantidad_movimiento' => -1,
           'sale_id' => $venta->id,
           'producto_id' => $product->id,
           'stock' => $product_stock->stock,
           'usuario_id' => $usuario_id,
           'comercio_id'  => $this->sucursal_id
         ]);
            
        $this->ActualizarTotalesVenta($this->NroVenta);
        $this->ActualizarEstadoDeuda($this->NroVenta);
       }
       
       $this->emit('pago-agregado', 'El producto fue agregado.');
             
         }


           $this->resetProduct();

             $this->RenderFactura($this->NroVenta);
       }
       
       
    public function resetProduct()
   {
     $this->products_s = [];
      $this->query_product = '';
       $this->RenderFactura($this->NroVenta);
   }
   
   public function EliminarProductoPedido($id_pedido_prod)
   {
          
        //  dd($id_pedido_prod);
          $this->items = SaleDetail::find($id_pedido_prod);

          $this->details = SaleDetail::where('sale_id',$this->NroVenta)->where('eliminado',0)->get();

          $this->items->update([
            'eliminado' => 1
            ]);
            
          $cantidad = 0;
          
          $this->TipoUsuario();
          
          // Si es compra en casa central 
          $venta = Sale::find($this->NroVenta);
          
          if($venta->canal_venta == "Venta a sucursales") {
          
          $compra = compras_proveedores::where('sale_casa_central',$venta->id)
          ->first();    
          
          $detalle_compra = detalle_compra_proveedores::where('compra_id',$compra->id)
          ->where('producto_id',$this->items->product_id)
          ->where('referencia_variacion',$this->items->referencia_variacion)
          ->where('eliminado',0)
          ->first();
    
          $detalle_compra->update([
            'eliminado' => 1
            ]);
            
            
            
          }
         
        
          $this->QueryStock($id_pedido_prod, $cantidad);
        
          $this->ActualizarTotalesVenta($this->NroVenta);
          
          $this->ActualizarEstadoDeuda($this->items->sale_id);

          $this->RenderFactura($this->items->sale_id);
          
          $this->emit('pago-agregado', 'El producto fue eliminado.');
        
       // Actualizar en wocommerce
    $result = $this->UpdateProductStockIndividualWocommerce($this->items->product_id,$this->items->referencia_variacion,$this->items->comercio_id);
         
   }

   public function updateQtyPedido($id_pedido_prod, $cant = 1)
   {

        $this->TipoUsuario();
        
        $item = SaleDetail::find($id_pedido_prod);
        $venta = Sale::find($item->sale_id);
        
        if($venta->status != "Cancelado") {
        $this->QueryStock($id_pedido_prod, $cant);
        }

        $this->ActualizarTotalesVenta($item->sale_id);
        
        $this->ActualizarEstadoDeuda($item->sale_id);

        $this->RenderFactura($item->sale_id);

        $this->emit('pago-agregado', 'La cantidad fue modificada.');
    }
   
    //--------- FUNCION QUE ACTUALIZA EL STOCK Y EL DETALLE DE VENTAS --------- //
    
    public function QueryStock($id_pedido_prod, $cantidad) {

    // guardamos como estaba el registro antes de actualizar //
    
    $this->items_viejo = SaleDetail::find($id_pedido_prod);
    
    $this->NroVenta = $this->items_viejo->sale_id;
    
    $venta = Sale::find($this->NroVenta);

    
	$this->prod = productos_stock_sucursales::where('sucursal_id',$this->sucursal_id)
	->where('product_id',$this->items_viejo->product_id)
	->where('referencia_variacion', $this->items_viejo->referencia_variacion)
	->first();
	
	$product = Product::find($this->items_viejo->product_id);

    // variables a usar
    
	$cantidad_anterior = $this->items_viejo->quantity;
	
	// Stock Disponible
	
	//Stock anterior
	$stock_anterior = $this->prod->stock;
	$stock_real_anterior = $this->prod->stock_real;
	
	// Cantidades de movimiento
	$cantidad_nueva = $cantidad;
	$cantidad_movimiento = $cantidad_anterior - $cantidad_nueva;
	
	// Stocks nuevos
	$stock = $stock_anterior + $cantidad_movimiento;
	$stock_real = $stock_real_anterior + $cantidad_movimiento;
	
 //	dd('cantidad anterior: '.$cantidad_anterior , 'stock anterior: '.$stock_anterior , 'cantidad nueva: '.$cantidad_nueva , 'cantidad movimiento '.$cantidad_movimiento, 'stock nuevo: '.$stock );
	//
	
	//dd($cantidad_anterior, $cantidad_nueva, $stock_anterior);
	
	$diferencia_stock = $cantidad_nueva - $cantidad_anterior;
    
	if($cantidad_anterior < $cantidad_nueva) {
	
    //dd($diferencia_stock,$stock_anterior);
	
	if( ($stock_anterior < $diferencia_stock) && $product->stock_descubierto == "si" ) {
 
    $stock_disponible_pedido = $cantidad_anterior + $stock_anterior;
       
    $this->emit('no-stock','Stock insuficiente.');
    $this->emit('volver-stock', $this->items_viejo->id.'-'.$stock_disponible_pedido);
    $this->RenderFactura($this->NroVenta);
       
    $this->items_viejo->update([
	'quantity' => $stock_disponible_pedido
	]);

    return;

    }
    
	}

	
	// ACA HAY QUE VER COMO ACTUALIZA EL STOCK SEGUN COMO ESTE EL ESTADO DEL PEDIDO 
	
	// Si esta entregado actualiza los dos stocks
	
	if($venta->status == "Entregado") {
	
	$this->prod->update([
	'stock' => $stock,
	'stock_real' => $stock_real
	]);
	
	    
	}
	
	// Si no esta entregado actualiza solo el stock disponible 
	if($venta->status != "Entregado" && $venta->status != "Cancelado") {
	
	$this->prod->update([
	'stock' => $stock
	]);
	
	    
	}
	
	// Actualiza el historico de movimientos

    if($this->sucursal_id == 0) { $this->sucursal_id = $this->casa_central_id;} else {$this->sucursal_id = $this->sucursal_id; }

	$historico_stock = historico_stock::create([
	'tipo_movimiento' => 3,
	'producto_id' => $this->items_viejo->product_id,
	'cantidad_movimiento' => $cantidad_movimiento,
	'referencia_variacion' => $this->items_viejo->referencia_variacion,
	'stock' => $stock,
	'usuario_id' => $this->sucursal_id,
	'comercio_id'  => $this->sucursal_id
	]);
	
	// Calcula nuevamente los descuentos y Actualiza las cantidades en el detalle de ventas
	

	$descuento_unitario = $this->items_viejo->descuento/$this->items_viejo->quantity;
	$descuento_nuevo = $descuento_unitario*$cantidad_nueva;
	
	$this->items_viejo->update([
	'quantity' => $cantidad_nueva,
	'descuento' => $descuento_nuevo
	]);


    // Si es una venta a una sucursal
    if($venta->canal_venta == "Venta a sucursales") {


      $compra = compras_proveedores::where('sale_casa_central',$venta->id)->first();
      
      $producto_comprado = detalle_compra_proveedores::where('compra_id',$compra->id)
      ->where('producto_id',$this->items_viejo->product_id)
      ->where('referencia_variacion',$this->items_viejo->referencia_variacion)
      ->first();
      
    $producto_comprado->update([
	'cantidad' => $cantidad_nueva
	]);
	
	$movimiento = $diferencia_stock;
    // dd($producto_comprado);
    
    // dd($diferencia_stock);
    
    
    // Aca hay que cambiar cambiar el stock de la sucursal si es que esta como entregado
    if($venta->status == "Entregado") {
    
    $this->UpdateQtySucursal($this->items_viejo->product_id,$this->items_viejo->referencia_variacion,$compra->comercio_id,$this->casa_central_id,$movimiento);

    }
    
    }
    
    // Actualizar en wocommerce
    $result = $this->UpdateProductStockIndividualWocommerce($this->items_viejo->product_id,$this->items_viejo->referencia_variacion,$this->items_viejo->comercio_id);
  
    //dd($result);
        
    }
    
    // FUNCION QUE ACTUALIZA EL STOCK EN UNA SUCURSAL
    
    public function UpdateQtySucursal($product_id,$referencia_variacion,$sucursal,$casa_central,$movimiento) {
     
     // Aca cuando un pedido esta entregado y se cambia la cantidad de productos por menos productos, se debe devolver la diferencia a la casa central VER.
     $product_stock = productos_stock_sucursales::where('productos_stock_sucursales.product_id',$product_id)
     ->where('productos_stock_sucursales.referencia_variacion',$referencia_variacion)
     ->where('productos_stock_sucursales.sucursal_id',$sucursal)
     ->where('productos_stock_sucursales.comercio_id',$casa_central)
     ->first();
    
     $stock_sucursal = $product_stock->stock;
     $product_stock->stock = $stock_sucursal + $movimiento;
     $product_stock->save();    
    
        
    }
    
    //--------- FUNCION QUE ACTUALIZA LA VENTA --------- //
    
    public function ActualizarTotalesVenta($venta) {
    
    $this->details = SaleDetail::where('sale_id',$venta)->where('eliminado',0)->get();
          //
          $suma = $this->details->sum(function($item){
              return $item->price * $item->quantity;
          });

          $iva = $this->details->sum(function($item){
              return $item->iva * $item->quantity *$item->price;
          });

        $descuento = $this->details->sum(function($item){
              return $item->descuento * (1 + $item->iva);
          });
          
         $recargo = $this->CalcularRecargo($venta);
         
         //$recargo = $this->details->sum(function($item){
         //     return $item->recargo * (1 + $item->iva);
         // });

          $this->descuento = $descuento;
          $this->recargo = $recargo;
          $this->subtotal_venta_nuevo = $suma;
          $this->iva_venta_nuevo = $iva;
          
          $suma_con_iva = $suma + $iva;
          
          $this->total_venta_nuevo = $suma + $iva + $recargo - $descuento;
            
          $this->items_venta_nuevo = $this->details->sum('quantity');
            
            
      //  dd($suma, $this->iva_venta_nuevo , $this->descuento);
        
          $this->venta = Sale::find($venta);

          $this->venta->update([
            'subtotal' => $this->subtotal_venta_nuevo,
            'total' => $this->total_venta_nuevo,
            'iva' =>  $this->iva_venta_nuevo,
            'descuento' => $this->descuento,
            'recargo' => $this->recargo
            ]);
            
        // Si es una venta a una sucursal
        if($this->venta->canal_venta == "Venta a sucursales") {
          
          $compra = compras_proveedores::where('sale_casa_central',$this->venta->id)->first();
          
          $this->ActualizarTotalCompra($compra->id);
          $this->ActualizarEstadoDeudaCompra($compra->id);
          
        }
        
        
    }
    
    


//////////// ESTA FUNCION ACTUALIZA EL MONTO TOTAL DE LA COMPRA LUEGO DE EDITARLA /////////

public function ActualizarTotalCompra($compra_id) {

      $compra = compras_proveedores::find($compra_id);
      $venta = Sale::find($compra->sale_casa_central);
      
      $subtotal = $venta->subtotal;
      $total = $venta->total;
      $iva = $venta->iva;
      $items = $venta->items;
      $recargo = $venta->recargo;
      
      $compra = compras_proveedores::find($compra_id);
      
      $compra->update([
        'subtotal' => $subtotal,
        'total' => $total,
        'items' => $items,
        'recargos' => $recargo,
        'iva' => $iva
        ]);

     // dd($compra);

}




    //--------- FUNCION QUE DETERMINA EL TIPO DE USUARIO --------- //
    
    public function TipoUsuario() {
        
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;
    
    $this->comercio_id = $comercio_id;

    $this->tipo_usuario = User::find(Auth::user()->id);

    if($this->tipo_usuario->sucursal != 1) {
    $this->casa_central_id = $comercio_id;
    $this->sucursal_id = 0;
    } else {
	$this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
	$this->casa_central_id = $this->casa_central->casa_central_id;
	$this->sucursal_id = $comercio_id;
	}


    }
        
    
    public function UpdateDescuentoRecargo($id_pedido_prod, $cant)
        {
            
          if($cant < 0) {
            $this->emit("msg-error","Los descuentos deben ser numeros positivos");
            return;
          }
          //dd($cant);
        
          $cant = str_replace(",",".",$cant);
            
          // dd($cant);
            
          $this->items_viejo = SaleDetail::find($id_pedido_prod);
          $items_viejo = $this->items_viejo;
          
          $this->descuento_viejo = $this->items_viejo->descuento;
          $this->recargo_viejo = $this->items_viejo->recargo;
            
          $cant = $cant/100; // expresamos el valor de descuento en porcentaje
          
          
          $cant = $cant * $this->items_viejo->price * $this->items_viejo->quantity;
    
          $items_viejo->descuento = $cant;
          $items_viejo->save();
          
          
          $items_nuevo = SaleDetail::find($id_pedido_prod);
        
        // dd($items_nuevo);
        
        $this->venta = Sale::find($this->items_viejo->sale_id);
        
        $this->ActualizarTotalesVenta($this->venta->id);


        $this->ActualizarEstadoDeuda($this->venta->id);


        $this->RenderFactura($this->venta->id);


        }
        
        
        
    
public function ActualizarEstadoDeudaCompra($ventaId)
{
  /////////////////   ACTUALIZAR ESTADO DE PAGO   /////////////////////
        $compra = compras_proveedores::select('compras_proveedores.total')
       ->where('compras_proveedores.id', $ventaId)
       ->first();

       $pagos = pagos_facturas::where('pagos_facturas.id_compra', $ventaId)
       ->where('pagos_facturas.eliminado',0)
       ->get();


       $suma_pagos = $pagos->sum('monto_compra');

       $suma_compra = $compra->total;

       //dd($suma_compra,$suma_pagos);
       
       $deuda = $suma_compra - $suma_pagos;

     $this->deuda_vieja = compras_proveedores::find($ventaId);

       $this->deuda_vieja->update([
         'deuda' => $deuda
         ]);


       ///////////////////////////////////////////////////////////////////
}




       public function ActualizarEstadoDeuda($ventaId)
       {
         /////////////////   ACTUALIZAR ESTADO DE PAGO   /////////////////////
    
    
              $this->data_cash = Sale::select('sales.cash','sales.created_at as fecha_factura')
              ->where('sales.id', $ventaId)
              ->get();
    
              $this->data_total = Sale::select('sales.total','sales.status','sales.recargo','sales.descuento')
              ->where('sales.id', $ventaId)
              ->first();
    
              $this->pagos2 = pagos_facturas::join('metodo_pagos as mp','mp.id','pagos_facturas.metodo_pago')
              ->select('mp.nombre as metodo_pago','pagos_facturas.id','pagos_facturas.recargo','pagos_facturas.monto','pagos_facturas.created_at as fecha_pago')
              ->where('pagos_facturas.id_factura', $ventaId)
              ->where('pagos_facturas.eliminado',0)
              ->get();
    
              // dd($this->data_total);
              
              //dd($this->pagos2);
              
              // Pagos
              $this->suma_monto = $this->pagos2->sum('monto');
              
              // Recargos
              $this->rec = $this->pagos2->sum('recargo');
              
              // total de la factura
              $this->tot = $this->data_total->total;
              
             // dd($this->tot,$this->suma_monto,$this->rec);
             
              if($this->data_total->status != "Cancelado") {
              $deuda = $this->tot - $this->suma_monto - $this->rec;    
              } else {
                  $deuda = 0;
              }
              
                
    
             $this->deuda_vieja = Sale::find($ventaId);
    
            //dd($this->deuda_vieja);
            
              $this->deuda_vieja->update([
                'deuda' => $deuda
                ]);
    
    
              ///////////////////////////////////////////////////////////////////
     }
    
    // Filtra por eliminado o activos 
    
	public function Filtro($estado)
	{
	   $this->estado_filtro = $estado;
	   
	}
	
// 19-7-2023
  public function CalcularRecargo($id_venta){  
       
         //////////////// PAGOS //////////////
        $pagos = pagos_facturas::where('pagos_facturas.id_factura', $id_venta)
        ->where('pagos_facturas.eliminado',0)
        ->get();
        
    //   $this->a_recargar = 1 + $recargo;
       
        $sum_recargo = $pagos->sum(function($item){
            return $item->recargo;
        });
        
   //     dd($sum_recargo);
        
        return $sum_recargo;
  }

public function DiscriminarIva($venta_id) {
    
    $attributes = SaleDetail::where('sale_id',$venta_id)->get();
    
    $collection = collect($attributes);
    
    $filteredCollection = $collection->filter(function ($item) {
        return isset($item['iva']) && $item['iva'] == 0.105;
    });
    
    // Convertir la colección filtrada de nuevo a un array
    $filteredArray = $filteredCollection->all();
       
    $totalSum = $filteredCollection->reduce(function ($carry, $item) {
        return $carry + floatval($item['price'] * $item['quantity'] );
    }, 0);
    
   dd($totalSum);
   
   // Array del IVA 
   
    $ivaArray = array(
    'Iva' => array(
           array(
            'Id' => 5,
            'BaseImp' => 100,
            'Importe' => 21
        ),
        array(
            'Id' => 4,
            'BaseImp' => 100,
            'Importe' => 10.5
        )
    )
    );


}

public function SetCasaCentral() {
    
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;


    $this->tipo_usuario = User::find(Auth::user()->id);

    if($this->tipo_usuario->sucursal != 1) {
    $this->casa_central_id = $comercio_id;
    $this->sucursal_id = 0;
    } else {
    $this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
    $this->casa_central_id = $this->casa_central->casa_central_id;
    $this->sucursal_id = $comercio_id;
    }

}
    
  // 1-12-2023
  
  public function CreatePagoCompra($ventaId,$compraId,$pago_casa_central)
  {
      
    // Aca hay que crear un id del pago de una compra por parte de la sucursal
    
    $banco = metodo_pago::find($this->metodo_pago_agregar_pago)->cuenta;
    
    $compras_proveedores = compras_proveedores::find($compraId);
    
    $monto_total = $pago_casa_central->monto + $pago_casa_central->recargo;

    $pago_factura =   pagos_facturas::create([
      'monto_compra' => $monto_total,
      'caja' => null,
      'banco_id' => $this->tipo_pago_sucursal,
      'created_at' => $this->fecha_ap,
      'proveedor_id' => 2,
      'comercio_id' => $compras_proveedores->comercio_id,
      'id_compra' => $compraId,
      'eliminado' => 0,
      'pago_sucursal_id' => $pago_casa_central->id
    ]);
    
    $this->ActualizarTotalCompra($compraId);
    $this->ActualizarEstadoDeudaCompra($compraId);
  
    $this->tipo_pago_sucursal = 1;
        
    $this->ResetPago();
    
    return $pago_factura->id;

  }
  
  // 1-12-2023

public function ActualizarPagoCompra($id_pago_casa_central) {

  $id = $id_pago_casa_central;
  
  $pagos_sucursal = pagos_facturas::where('pago_sucursal_id',$id)->where('eliminado',0)->first();
  
  $monto_total = $this->monto_ap + $this->recargo;
  $banco_id = intval($this->tipo_pago_sucursal);
  
  $pagos_sucursal->update([
    'banco_id' => $banco_id,
    'monto_compra' => $monto_total,
    'created_at' => $this->fecha_ap,
    'metodo_pago' => $this->metodo_pago_agregar_pago,
  ]);
  
  $compraId = $pagos_sucursal->id_compra;
            
  $this->ActualizarTotalCompra($compraId);
  $this->ActualizarEstadoDeudaCompra($compraId);
  
  $this->tipo_pago_sucursal = 1;
}


public function DeletePagoCompra($id)
{
    
    $pago_viejo = pagos_facturas::find($id);

    $pago_viejo->eliminado = 1;
    $pago_viejo->save();

    $compraId = $pago_viejo->id_compra;
    
    $this->ActualizarTotalCompra($compraId);
    $this->ActualizarEstadoDeudaCompra($compraId);

}

public function CerrarModal(){
     $this->NroVenta = 0;
     $this->id_pedido = 0;
}

public function UpdateNotaInterna($value)
{
$sale = Sale::find($this->NroVenta);
$sale->nota_interna = $value;
$sale->save();
}

public function UpdateObservaciones($value)
{
$sale = Sale::find($this->NroVenta);
$sale->observaciones = $value;
$sale->save();    
}
    
public function CerrarModalMail() {
    $this->emit('cerrar-modal-mail','');
}


public function UpdateTipoEntrega($value) {
   
   if($this->ecommerce_envio_form != null) {
        
        $this->ecommerce_envio_form->update([
            'metodo_entrega' => $value
            ]);  

        }
        $this->RenderFactura($this->NroVenta);
        

}

public function UpdateNombreDestinatario($value){
    
    //dd($value);
    
     if($this->ecommerce_envio_form != null) {
        $this->ecommerce_envio_form->update([
            'nombre_destinatario' => $value
            ]);  

        }  
        $this->RenderFactura($this->NroVenta);
}

public function UpdateDireccion($value){
       if($this->ecommerce_envio_form != null) {
        $this->ecommerce_envio_form->update([
            'direccion' => $value
            ]);  

        }    
        $this->RenderFactura($this->NroVenta);
}

public function UpdateCiudad($value){
       if($this->ecommerce_envio_form != null) {
        $this->ecommerce_envio_form->update([
            'ciudad' => $value
            ]);  

        }  
        $this->RenderFactura($this->NroVenta);
}

public function UpdateProvincia($value){
               if($this->ecommerce_envio_form != null) {
        $this->ecommerce_envio_form->update([
            'nombre_provincia' => $value
            ]);  

        }
        $this->RenderFactura($this->NroVenta);
}

public function UpdateTelefono($value){
       if($this->ecommerce_envio_form != null) {
        $this->ecommerce_envio_form->update([
            'telefono' => $value
            ]);  

        }  
        $this->RenderFactura($this->NroVenta);
    
}


  
  public function HistoricoStock($product_id,$referencia_variacion,$cantidad_movimiento,$stock_real,$comercio_id,$estado_id,$usuario){
        // El 1 y 2 son pendiente y en proceso
        // El 3 es entregado
        // El 4 es cancelado
        
        // si el usuario es 1 es casa central si es 2 es sucursal
        if($usuario == 1) {
        
        if($estado_id == 3) {
        $tipo_movimiento =  15;   
        }
        
        if($estado_id != 3 && $estado_id != 4) {
        $tipo_movimiento =  16;   
        }
        
        if($estado_id == 4) {
        $tipo_movimiento =  13;   
        }
        
        }
        
        if($usuario == 2) {
        
        if($estado_id == 3) {
        $tipo_movimiento =  17;   
        }
        
        if($estado_id != 3 && $estado_id != 4) {
        $tipo_movimiento =  19;   
        }
        
        if($estado_id == 4) {
        $tipo_movimiento =  18;   
        }
        
        }
        
        $historico_stock = historico_stock::create([
        'producto_id' => $product_id,
        'referencia_variacion' => $referencia_variacion,
        'tipo_movimiento' => $tipo_movimiento,
        'cantidad_movimiento' => $cantidad_movimiento,
        'stock' => $stock_real,
        'usuario_id' => $comercio_id,
        'comercio_id'  => $comercio_id
        ]);
        
        return $historico_stock;
        
  }
  
  public function OrdenarColumna($columna)
{
    
    
    if ($this->columnaOrden == $columna) {
        // Cambiar la dirección de orden si la columna es la misma
        $this->columnaOrden = $columna;
        $this->direccionOrden = $this->direccionOrden == 'asc' ? 'desc' : 'asc';
    } else {
        // Si es una columna diferente, cambiar a la nueva columna y establecer la dirección predeterminada
        $this->columnaOrden = $columna;
        $this->direccionOrden = 'asc';
        
       // dd($this->columnaOrden,$this->direccionOrden);
    }
    
    $this->render();
}

  public function Filtros($mostrar)
{   
    $this->MostrarOcultar = $this->MostrarOcultar == 'block' ? 'none' : 'block';

}

public function ElegirCondicionesIVA($sale) {
    
    $this->relacion_precio_iva_form = $this->datos_facturacion_elegidos->relacion_precio_iva;
    $this->alicuota_iva_form = $this->datos_facturacion_elegidos->iva_defecto;
    
    $this->venta_facturar = $sale;
    $this->venta_form = Sale::find($sale);
    
    if($this->datos_facturacion_elegidos->condicion_iva == "IVA Responsable inscripto" && $this->venta_form->cliente_id == 1) {
    $this->tipo_comprobante_form = "B";  
    } else {
    $this->tipo_comprobante_form = "Elegir";      
    }
    
    $this->emit("elegir-condicion-iva","");
}

public function RecalcularIVA() {

if($this->tipo_comprobante_form == "Elegir") {
    $this->emit("msg-error","Debe elegir el tipo de comprobante para un cliente no consumidor final");
    return;
}


$this->UpdateTipoComprobante($this->tipo_comprobante_form,$this->venta_facturar,0);  

$this->UpdateRelacionPrecioIva($this->relacion_precio_iva_form,$this->venta_facturar,0);
$this->UpdateIvaGral($this->alicuota_iva_form,$this->venta_facturar,0);

$this->NroVenta = $this->venta_facturar;

 $this->emit("elegir-condicion-iva-hide","");
 
$this->FacturarAfip($this->venta_facturar);

//$this->venta_facturar = null;

}


    public function toggleColumnVisibility($columnName)
    {
        $isVisible = !($this->columns[$columnName] ?? false);
        ColumnConfiguration::updateOrCreate(
            ['user_id' => Auth::id(),'table_name' => 'reports', 'column_name' => $columnName],
            ['is_visible' => $isVisible]
        );

        $this->columns[$columnName] = $isVisible;
    }
    
//10-12

public function ModalAgregarCliente(){
    $this->lista_precios = lista_precios::where('comercio_id',$this->casa_central_id)->get();
    $this->emit('modal-agregar-cliente','');
}

public function StoreCliente(){
    $this->sucursal_agregar_cliente = $this->comercio_id;
    $cliente = $this->StoreClienteTrait();
    $this->selectCliente($cliente->id);
    //dd($cliente);
    $this->emit("modal-agregar-cliente-hide","Cliente agregado");
    $this->render();
    $this->RenderFactura($this->NroVenta);
}
    
    
// 9-1-2024

public function SetNroCompra($comercio_id){
      
      $compra = compras_proveedores::where('comercio_id',$comercio_id)->orderBy('id','desc')->first();
      
      if($compra != null) {
      if($compra->nro_compra != null) {
      $nro_compra = $compra->nro_compra + 1;    
      } else {
      $nro_compra = 1;    
      }
          
      } else {$nro_compra = 1;}
      
      return $nro_compra;
      
    }
    
public function SetSucursalPagos($sucursal_id,$pago_id){
    
      // Setea los tipos de pago     
       $this->tipos_pago_sucursal = bancos::join('bancos_muestra_sucursales','bancos_muestra_sucursales.banco_id','bancos.id')
      ->select('bancos.*')
      ->where('bancos_muestra_sucursales.sucursal_id', $sucursal_id)
       ->where('bancos_muestra_sucursales.muestra', 1)
      ->orderBy('bancos.nombre','asc')->get();
      
      //  si $pago_id != 0 tiene que traer el metodo de pago de la sucursal
      
      if($pago_id != 0){
      $tipo_pago_sucursal = pagos_facturas::find($pago_id)->banco_id;
      $this->tipo_pago_sucursal = $tipo_pago_sucursal;
      }
      
      
    }
    

}


