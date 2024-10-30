<?php

namespace App\Http\Livewire;


// Trait
use App\Traits\FacturacionNuevoAfip;
use App\Traits\WocommerceTrait;
use App\Traits\ClientesTrait;
use App\Traits\DeduccionesTrait; // 30-6-2024

use Livewire\Component;


use Livewire\WithFileUploads;
use App\Models\productos_ivas;
use App\Models\User;
use App\Models\saldos_iniciales;
use App\Models\configuracion_ctas_ctes;
use App\Models\provincias;
use App\Models\paises;
use App\Models\promos;
use App\Models\promos_productos;
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
use App\Models\facturacion;
use App\Models\ecommerce_envio;
use Automattic\WooCommerce\Client;
use Illuminate\Support\Facades\Auth;
use App\Models\SaleDetail;
use Carbon\Carbon;


class CtaCteClientesMovimientosController extends Component
{

    use DeduccionesTrait; // 30-6-2024
    use WithFileUploads;
    use WithPagination;
    use FacturacionNuevoAfip;
    use WocommerceTrait;
    use ClientesTrait;

    //9-8-2024
    public $venta_total,$pagos_total,$monto_saldo_inicial,$forma_calcular_totales;
    
    public $descuento_promo_form,$filtro_operacion,$cantidad_promo_form,$cantidad_promo_max_form,$Id_cart;
    
    public $componentName, $datos_cliente,$nombre_cliente_elegido,$relacion_precio_iva,$alicuota_iva, $cliente_id,$ecommerce_envio_form, $id_cliente_elegido, $caja, $lista_cajas_dia,$monto_inicial, $query_cliente, $estado_facturacion,  $sucursal_stock, $detalle_facturacion, $iva_agregar, $data,  $tipo_pago, $tipos_pago, $details, $sumDetails, $countDetails, $sum, $totales_ver, $cantidad_tickets, $ticket_promedio, $sucursal_id,
    $reportType, $userId, $dateFrom, $dateTo,$facturas, $rec,$tipo_pago_sucursal, $saleId,$tipo_factura, $codigo_barra_afip, $suma_deuda, $ultimas_cajas, $mail_ingresado, $comercio_id,$status, $codigo_qr, $search, $codigoQR, $clienteId, $selected_id, $suma_totales, $suma_cantidades, $id_pedido, $product_agregar, $estado_estado, $NroVenta, $hr_elegida, $desc, $ventaId, $suma_monto, $suma_cash, $tot, $hojar, $hoja_ruta, $monto, $estado, $estado2, $nombre_hr, $tipo, $fecha_hr, $turno, $observaciones_hr, $dateHojaRuta, $estado_pago, $metodo_pago_sale_detail, $recargo_mp, $nombre_mp, $recargo, $id_pago, $monto_ap, $fecha_ap, $metodo_pago_ap, $fecha_editar,$nro_hoja_elegido, $product_wc, $formato_modal, $total_pago, $recargo_total, $recargo_nvo_venta, $style, $style2, $estado_original, $id_checked, $accion_lote;
    
    public $Nro_Venta;
    public $metodo_pago_agregar_pago = [];
    public $id_check = [];
    
    public $subtotal_con_iva, $descuento_promo_con_iva;


    public $mostrarInputFile = false;

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
    public $tipos_pago_sucursal = [];
    public $clientesSelectedId;
    public $UsuarioSelectedName;
    public $EstadoSelectedName;
    public $MetodoPagoSelectedName;
    public $MetodoPagoSeleccionado;

    public $Usuario_SelectedValues;
    public $Estado_SelectedValues;
    public $ultima_factura;
    
    public $nro_comprobante, $comprobante;

    public $usuarioSeleccionado;
    public $ClienteSeleccionado;
    public $EstadoSeleccionado;
    public $clientesSelectedName = [];

    public $locationUsers = [];
    public $usuario_seleccionado = [];
    public $estado_seleccionado = [];
    public $metodo_pago_seleccionado = [];
    
    public $formato_ver;
    
    public $tipo_entrega,$nombre_destinatario,$direccion,$ciudad,$nombre_provincia,$telefono;

    // 2-5-2024
    public $listado_cuits = [];
    public $listado_ventas_id = [];
    
    public $selectedSucursales;
    
    
    public $proveedor_elegido,$id_venta,$datos_cta_cte,$modo_ver_saldo,$selected_pago_saldo_id,$caja_ver,$comprobante_ver,$venta_id;
    public $sucursales_agregan_pago_form;
    
    public $valor;
    public $sucursales_agregan_pago;
    public $configuracion_valor;
    public $configuracion_sucursales_agregan_pago;
    
    
    public function paginationView()
    {
      return 'vendor.livewire.bootstrap';
    }
    
      public function GetConfiguracionCtaCte(){
        if(Auth::user()->comercio_id != 1)
        $comercio_id = Auth::user()->comercio_id;
        else
        $comercio_id = Auth::user()->id;
        
        $configuracion_ctas_ctes = configuracion_ctas_ctes::where('comercio_id',$comercio_id)->first();
        
        if($configuracion_ctas_ctes == null){
        $this->valor = "por_sucursal";
        $this->sucursales_agregan_pago = 0;   
        $this->configuracion_valor = "por_sucursal";
        $this->configuracion_sucursales_agregan_pago = 0;   
        } else {
        $this->valor = $configuracion_ctas_ctes->valor;
        $this->sucursales_agregan_pago = $configuracion_ctas_ctes->sucursales_agregan_pago;  
        $this->configuracion_valor = $configuracion_ctas_ctes->valor;
        $this->configuracion_sucursales_agregan_pago = $configuracion_ctas_ctes->sucursales_agregan_pago;  
        }
    
      }
    protected $listeners = [
        'cambiar-iva' => 'EventoCambiarIva',
        'ver-opciones-pantalla' => 'VerOpcionesPantalla',
        'RestaurarVenta' => 'RestaurarVenta', 
        'AnularFactura' => 'AnularFactura',
        'EliminarVenta' => 'EliminarVenta',
        'CancelarVenta' => 'Update',
        'accion-lote' => 'AccionEnLote',
        'cancelar-pagos' => 'CancelarPagos',
        'deletePago' => 'DeletePago',
        'deleteRow' => 'EliminarProductoPedido', 
        'FacturarVenta' => 'FacturarAfip' ,
        'QuitarPromo',
        'locationUsersSelected',
        'UsuarioSelected',
        'EstadoSelected',
        'Usuario_Selected',
        'MetodoPagoSelected',
        'deletePagoSaldo' => 'DeletePagoSaldo',
        'FechaElegida' => 'FechaElegida'
        ];

    public function FechaElegida($startDate, $endDate)
    {
      // Manejar las fechas seleccionadas aquí
      $this->dateFrom  = $startDate;
      $this->dateTo = $endDate;;
    }

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




    public function resetUI()
    {
      $this->id_pago = '';
      $this->monto = '';


      $this->monto_ap = '';
      $this->fecha_ap = Carbon::now()->format('d-m-Y');
      $this->metodo_pago_ap = '';
    }

    public function resetUIPago(){
      $this->monto_real = 0;
      $this->formato_modal = 0;
      $this->recargo = 0;
      $this->tipo_pago = 1;
      $this->monto_ap = 0;
      $this->fecha_ap = Carbon::now()->format('d-m-Y');
      $this->recargo_total = 0;
      $this->total_pago = 0;

    }


    public function toggleInputFile()
    {
        $this->mostrarInputFile = !$this->mostrarInputFile;
    }


    protected function redirectLogin()
    {
        return \Redirect::to("login");
    }
    

  public function mount($id)
  {
    $this->forma_calcular_totales = "Total adeudado"; // 9-8-2024
    $this->filtro_operacion = 0;
    $this->caja = cajas::select('*')->where('estado',0)->where('user_id',Auth::user()->id)->max('id');
    $fecha_editar = Carbon::now()->format('d-m-Y');
    $this->fecha_ap = Carbon::now();
    $this->tipos_pago = [];
    $this->detalle_compra = [];
    $this->pagos2 = [];
    $this->detalle_proveedor = [];
    $this->dci = [];
    $this->pagar_deuda = [];
    $this->total = [];
    $this->details =[];
    $this->pageTitle = 'Listado';
    $this->cantidad = 1;
    $this->monto_ap = 0;
    $this->tipo_pago = 1;
    $this->metodo_pago_agregar_pago = 1;
    $this->componentName = 'Productos';
    $this->metodo_pago_elegido = 'Elegir';
    $this->categoryid = 'Elegir';
    $this->dateFrom = Carbon::parse('2000-01-01 00:00:00')->format('d-m-Y');
    $this->dateTo = Carbon::now()->format('d-m-Y');
    
    $this->metodo_pago_agregar = [];

    $this->cliente_id = $id;
    
    $this->datos_cliente = ClientesMostrador::find($this->cliente_id);
    
        
    $sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')
    ->select('users.name','sucursales.sucursal_id')
    ->where('sucursales.casa_central_id', auth()->user()->casa_central_user_id)
    ->where('eliminado',0)
    ->get();
    
    $this->GetConfiguracionCtaCte();

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;
        
    if($this->valor == "por_sucursal"){
    $this->selectedSucursales = [$comercio_id]; // Inicializar con el ID del usuario actual
    }

    if($this->valor == "compartido"){
    $this->selectedSucursales = [auth()->user()->casa_central_user_id]; 
    
    foreach ($sucursales as $sucursal) {
        $this->selectedSucursales[] = $sucursal->sucursal_id; // Agregar cada sucursal_id al array
    }
    }
    
    //dd($this->selectedSucursales);
    
  }

public function render()
{
    if(Auth::user()->comercio_id != 1)
        $comercio_id = Auth::user()->comercio_id;
    else
        $comercio_id = Auth::user()->id;
        
    $casa_central_id = Auth::user()->casa_central_user_id;

    $this->comercio_id = $comercio_id;
    
    /*
    $this->tipos_pago = bancos::where('bancos.comercio_id', $casa_central_id)
        ->orderBy('bancos.nombre', 'asc')->get();
    */
    $this->tipos_pago = $this->getBancos($comercio_id);
    
    if ($this->dateFrom !== '' || $this->dateTo !== '') {
        $from = Carbon::parse($this->dateFrom)->format('Y-m-d') . ' 00:00:00';
        $to = Carbon::parse($this->dateTo)->format('Y-m-d') . ' 23:59:59';
    }

    if ($this->estado_pago !== '') {
        if ($this->estado_pago == 'Pendiente') {
            $this->estado_pago_buscar = ' compras_proveedores.deuda > 0 ';
        }

        if ($this->estado_pago == 'Pago') {
            $this->estado_pago_buscar = ' compras_proveedores.deuda = 0';
        }
    }

    $this->from = $from;
    $this->to = $to;
    $userCentralId = auth()->user()->casa_central_user_id;
    $sucursales_agregan_pago = $this->sucursales_agregan_pago;
    
    // Consultas ajustadas para incluir el nombre de la sucursal
    $pagos = pagos_facturas::join('bancos', 'bancos.id', 'pagos_facturas.banco_id')
        ->leftJoin('sales', 'sales.id', 'pagos_facturas.id_factura')
        ->leftJoin('sucursales', 'pagos_facturas.comercio_id', 'sucursales.sucursal_id')
        ->leftjoin('users','users.id','sucursales.sucursal_id')
        ->whereBetween('pagos_facturas.created_at', [$from, $to])
        ->whereIn('pagos_facturas.comercio_id', $this->selectedSucursales)
        ->where('pagos_facturas.tipo_pago', 1)
        ->where('pagos_facturas.eliminado', 0)
        ->where('pagos_facturas.cliente_id', $this->cliente_id)
        ->select(pagos_facturas::raw("IFNULL(sucursales.sucursal_id, " . $userCentralId . ") as sucursal_id"),'pagos_facturas.comercio_id','bancos.nombre as nombre_banco', 'bancos.id as id_banco', 'pagos_facturas.url_comprobante as url_pago', Sale::raw('0 as id_saldo'), Sale::raw('0 as monto_saldo'), 'sales.nro_venta', 'pagos_facturas.id as id_pago', pagos_facturas::raw('0 as id_venta'), 'pagos_facturas.created_at', pagos_facturas::raw('0 as monto'), pagos_facturas::raw(' (pagos_facturas.monto + pagos_facturas.recargo + pagos_facturas.iva_pago + pagos_facturas.iva_recargo)  as monto_pago'), 'users.name as nombre_sucursal');

    $ventas = Sale::leftJoin('sucursales', 'sales.comercio_id', 'sucursales.sucursal_id')
        ->leftjoin('users','users.id','sucursales.sucursal_id')
        ->whereIn('sales.comercio_id', $this->selectedSucursales)
        ->whereBetween('sales.created_at', [$from, $to])
        ->where('sales.cliente_id', $this->cliente_id)
        ->where('sales.eliminado', 0)
        ->where('sales.status', '<>', 'Cancelado')
        ->select(pagos_facturas::raw("IFNULL(sucursales.sucursal_id, " . $userCentralId . ") as sucursal_id"),'sales.comercio_id',Sale::raw('"-" as nombre_banco'), Sale::raw('0 as id_banco'), Sale::raw('0 as url_pago'), Sale::raw('0 as id_saldo'), Sale::raw('0 as monto_saldo'), 'sales.nro_venta', Sale::raw('0 as id_pago'), 'sales.id as id_venta', 'sales.created_at', 'sales.total as monto', compras_proveedores::raw('0 as monto_pago'), 'users.name as nombre_sucursal');

    $saldos_iniciales = saldos_iniciales::join('bancos', 'bancos.id', 'saldos_iniciales.metodo_pago')
        ->leftJoin('sucursales', 'saldos_iniciales.comercio_id', 'sucursales.sucursal_id')
        ->leftjoin('users','users.id','sucursales.sucursal_id');
        
        $saldos_iniciales = $saldos_iniciales->whereIn('saldos_iniciales.sucursal_id', $this->selectedSucursales)
        
        ->whereBetween('saldos_iniciales.created_at', [$from, $to])
        ->where('saldos_iniciales.referencia_id', $this->cliente_id)
        ->where('saldos_iniciales.tipo', 'cliente')
        ->where('saldos_iniciales.eliminado', 0)
        ->select(pagos_facturas::raw("IFNULL(sucursales.sucursal_id, " . $userCentralId . ") as sucursal_id"),'saldos_iniciales.comercio_id','bancos.nombre as nombre_banco', 'bancos.id as id_banco', saldos_iniciales::raw('0 as url_pago'), 'saldos_iniciales.id as id_saldo', 'saldos_iniciales.monto as monto_saldo', saldos_iniciales::raw('0 as nro_venta'), saldos_iniciales::raw('0 as id_pago'), saldos_iniciales::raw('0 as id_venta'), 'saldos_iniciales.created_at', Sale::raw('0 as monto'), Sale::raw('0 as monto_pago'), 'users.name as nombre_sucursal');

    // Unión de las subconsultas
    $union = $pagos->union($ventas)->union($saldos_iniciales);
    
    // Obtener el resultado ordenado
    $compras_clientes = $union->orderBy('created_at', 'desc')->get();

    
    // 9-8-2024
    $this->GetTotales($userCentralId,$from,$to);
    
    // Filtrar los resultados después de obtenerlos
    if ($this->filtro_operacion == "1") {
        $compras_clientes = $compras_clientes->filter(function ($item) {
            return $item->id_venta > 0;
        });
    } elseif ($this->filtro_operacion == "2") {
        $compras_clientes = $compras_clientes->filter(function ($item) {
            return $item->id_pago > 0;
        });
    }

    $compras_clientes_totales = Sale::select(Sale::raw('SUM(sales.subtotal + IFNULL(sales.recargo,0) - IFNULL(sales.descuento ,0) + IFNULL(sales.iva ,0) ) as total'), Sale::raw('COUNT(sales.id) as count_proveedores'), Sale::raw('SUM(sales.deuda) as deuda'))
        ->join('clientes_mostradors', 'clientes_mostradors.id', 'sales.cliente_id')
        ->whereIn('sales.comercio_id', $this->selectedSucursales)
        ->where('sales.status', '<>', 'Cancelado')
        ->where('sales.cliente_id', $this->cliente_id)
        ->whereBetween('sales.created_at', [$from, $to]);

    if ($this->proveedor_elegido) {
        $compras_clientes_totales = $compras_clientes_totales->where('proveedores.id', $this->proveedor_elegido);
    }

    if ($this->search) {
        $compras_clientes_totales = $compras_clientes_totales->where('compras_proveedores.id', 'like', '%' . $this->search . '%');
    }

    $compras_clientes_totales = $compras_clientes_totales->first();

    $this->suma_totales = $compras_clientes_totales->total;
    $this->suma_proveedores = $compras_clientes_totales->count_proveedores;
    $this->suma_deuda = $compras_clientes_totales->deuda;

    $metodo_pagos = bancos::where('bancos.comercio_id', 'like', $comercio_id)->get();

    $this->caja_seleccionada = cajas::find($this->caja);

    $this->ultimas_cajas = cajas::where('comercio_id', $comercio_id)->orderby('created_at', 'desc')->limit(5)->get();

    $this->metodo_pago_agregar = metodo_pago::join('bancos', 'bancos.id', 'metodo_pagos.cuenta')
        ->join('metodo_pagos_muestra_sucursales', 'metodo_pagos_muestra_sucursales.metodo_id', 'metodo_pagos.id')
        ->select('metodo_pagos.*', 'bancos.nombre as nombre_banco')
        ->where('metodo_pagos_muestra_sucursales.sucursal_id', 'like', $comercio_id)
        ->where('metodo_pagos_muestra_sucursales.muestra', 1)
        ->where('metodo_pagos.cuenta', 'like', $this->tipo_pago)
        ->get();

    $this->datos_cta_cte = $compras_clientes;

    return view('livewire.ctacte-clientes-movimientos.component', [
        'datos_cta_cte' => $this->datos_cta_cte,
        'detalle_compra' => $this->detalle_compra,
        'metodo_pago' => $metodo_pagos,
    ])
        ->extends('layouts.theme-pos.app')
        ->section('content');
}




        public function updatePricePedido($id_pedido_prod, $cant = 1)
        {
          // ver este
          $item_viejo = SaleDetail::find($id_pedido_prod);
          $venta = Sale::find($item_viejo->sale_id);
          $precio_original = $cant;
          
          // aca tenemos que tomar el iva del producto modificado
          if($venta->relacion_precio_iva == 1) {
          $cant = $cant;    
          }
          
          if($venta->relacion_precio_iva == 2) {
           $cant = $cant/(1+$item_viejo->iva);   
          }
          
          $array_precio = [
            'price' => $cant,
            'precio_original' => $precio_original
            ];
            
          //dd($array_precio);
          
          $item_viejo->update($array_precio);
         
          $alicuota_descuento_gral = $this->GetAlicuotaDescuentoGeneralProducto($item_viejo->sale_id);
          $descuento_nuevo = $this->SetDescuentoGeneralProducto($item_viejo->id,$item_viejo->quantity,$alicuota_descuento_gral);
 
          $item_viejo->update([
            'descuento' => $descuento_nuevo
          ]);
        
          $this->ActualizarTotalesVenta($item_viejo->sale_id);
            
          $this->ActualizarEstadoDeuda($item_viejo->sale_id);
                    
          // Si es una venta a una sucursal
          if($venta->canal_venta == "Venta a sucursales") {
              
            $compra = compras_proveedores::where('sale_casa_central',$venta->id)->first();
              
            $producto_comprado = detalle_compra_proveedores::where('compra_id',$compra->id)
            ->where('producto_id',$item_viejo->product_id)
            ->where('referencia_variacion',$item_viejo->referencia_variacion)
            ->first();
              
            $this->updatePriceCompra($producto_comprado->id, $cant);
            }

          $this->RenderFactura($item_viejo->sale_id);

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
            
            //dd($origen);
            $s = Sale::find($nro_venta);
            $s->update([
                'tipo_comprobante' => $value
                ]);

            $this->emit('pago-agregado', 'El tipo de comprobante fue guardado.');
            
           $this->RenderFactura($nro_venta);
        }
       
       public function SwitchUpdateRelacionPrecioIva($relacion_precio_iva_nueva,$nro_venta,$origen){
       $sale = Sale::find($nro_venta);
       
       // aca tenemos que conseguir la alicuota del iva
       $iva = $this->GetAlicuotaIVA($nro_venta);
       
       $relacion_precio_iva_viejo = $sale->relacion_precio_iva;
       if(($relacion_precio_iva_viejo == 1 && $relacion_precio_iva_nueva == 2) || ($relacion_precio_iva_viejo == 2 && $relacion_precio_iva_nueva == 1)){
       $this->UpdateRelacionPrecioIva(0,$nro_venta,$origen);
       $this->UpdateRelacionPrecioIva($relacion_precio_iva_nueva,$nro_venta,$origen); 
       $this->emit("confirmar-cambiar-iva",['iva' => $iva, 'nro_venta' => $nro_venta,'origen' => $origen]);
       } else {
       $this->UpdateRelacionPrecioIva($relacion_precio_iva_nueva,$nro_venta,$origen);       
       }
       }
       
       public function UpdateRelacionPrecioIva($relacion_precio_iva,$nro_venta,$origen){

       $sale_detail = SaleDetail::where('sale_id',$nro_venta)->get();   
        
        foreach($sale_detail as $sd) {
        
        $relacion_original = $sd->relacion_precio_iva;
        
        // obtenemos la alicuota
        if($sd->iva == null){ $alicuota = 0; } else { $alicuota =   $sd->iva; }
        $this->ActualizarDetalleVentaRelacionPrecioIVA($relacion_precio_iva,$relacion_original,$sd,$alicuota);    
        }
        
        $sale = Sale::find($nro_venta);
        $iva_viejo = $sale->alicuota_iva;
        
        $sale->update([
            'relacion_precio_iva' => $relacion_precio_iva
            ]);
        
        $this->ActualizarPagosRelacionPrecioIVA($nro_venta,$relacion_original);
        
        $this->ActualizarTotalesVenta($nro_venta);
            
        $this->ActualizarEstadoDeuda($nro_venta);

       if($relacion_precio_iva == 0){
       $this->UpdateIvaGral(0,$nro_venta,$origen);    
       }
       
        if($origen != 0) {
        $this->RenderFactura($nro_venta);
        }
        
        $this->emit('pago-agregado', 'La relacion precio iva fue modificada.');
         
        }
        
        public function ActualizarPagosRelacionPrecioIVA($nro_venta,$relacion_original){
            $pagos_facturas = pagos_facturas::where('id_factura',$nro_venta)->get();
            
            if($relacion_original == 2){
            foreach($pagos_facturas as $pf){
                $pf->monto = $pf->monto + $pf->iva_pago;
                $pf->recargo = $pf->recargo + $pf->iva_recargo;
                $pf->iva_pago = 0;
                $pf->iva_recargo = 0;
                $pf->save();
            }
            } else {
             foreach($pagos_facturas as $pf){
                $pf->iva_pago = 0;
                $pf->iva_recargo = 0;
                $pf->save();
            }   
            }
            
            
        }
        
        
        public function EventoCambiarIva($data){
        $this->UpdateIvaGral($data['iva'],$data['nro_venta'],$data['origen']);    
        }
        
       public function ActualizarDetalleVentaRelacionPrecioIVA($relacion_precio_iva,$relacion_original,$sd,$alicuota){

        // Si la relacion es PRECIO + IVA

          if($relacion_precio_iva == 0 || $relacion_precio_iva == 1){

          $precio = $sd->precio_original;
          $iva_total = $sd->precio_original*$alicuota; 
          if($relacion_original == 2){$recargo = $sd->recargo * (1 + $alicuota);} else {$recargo = $sd->recargo;}
          if($relacion_original == 2){$descuento = $sd->descuento * (1 + $alicuota);} else { $descuento = $sd->descuento; }
          if($relacion_original == 2){$descuento_promo = $sd->descuento_promo * (1 + $alicuota);} else { $descuento_promo = $sd->descuento_promo; }
          
          $this->alicuota_iva = 0;
          }

        // Si la relacion es IVA INCLUIDO EN EL PRECIO
        
          if($relacion_precio_iva == 2){
              
          $precio = $sd->precio_original/(1+$alicuota); 
          $iva_total = $sd->precio_original - $precio;
          $descuento = $sd->descuento/(1 + $alicuota);
          $recargo = $sd->recargo / (1 + $alicuota);
          $descuento_promo = $sd->descuento_promo/(1 + $alicuota);
          }
          
          
          $array = [
              'price' => $precio,
              'iva' => $alicuota,
              'iva_total' => $iva_total,
              'relacion_precio_iva' => $relacion_precio_iva,
              'recargo' => $recargo,
              'descuento' => $descuento,
              'descuento_promo' => $descuento_promo
            ];
          
          //($array);  
          $sd->update($array);   
                
        }
        
        

    public function UpdateIvaGral_DetalleProducto($nro_venta,$value){
                
        $sale_detail = SaleDetail::where('sale_id',$nro_venta)->get();   
        
        // testeado ok
        // aca funciona mal se pasa de iva incluido en el precio a precio + iva
        foreach($sale_detail as $sd) {
     
        if($sd->relacion_precio_iva == 1 || $sd->relacion_precio_iva == 0){
        $this->UpdateIva($sd->id,$value);   
        }
        
        // aca funciona mal el cuando se cambia el iva para las precio + iva
        if($sd->relacion_precio_iva == 2){
        $this->UpdateIva2($sd->id,$value);   
        }
        }    
        }

        public function UpdateIvaGral($value,$nro_venta,$origen) {
        
        //dd($value,$nro_venta,$origen);
        
        $this->NroVenta = $nro_venta;
        $sale = Sale::find($nro_venta);
        $iva_viejo = $sale->alicuota_iva;
        $iva_nuevo = $value;
        
        // Aca modificamos todo lo que es detalle del producto
        $this->UpdateIvaGral_DetalleProducto($nro_venta,$value);
        
        //Aca actualizamos el recargo de los pagos en los casos que el IVA este incluido en el precio del producto
        if($sale->relacion_precio_iva == 2){
        $this->RecalcularRecargoPago($iva_viejo,$iva_nuevo,$nro_venta);    
        }
         
        // Aca actualizamos datos generales de la venta
        $this->ActualizarTotalesVenta($this->NroVenta);
            
        $this->ActualizarEstadoDeuda($this->NroVenta);

        
        if($value == 0) {
        $sale->relacion_precio_iva = $value;    
        }
        $sale->alicuota_iva = $value;
        $sale->save();
        
        $this->RenderFactura($nro_venta);

        $this->emit('pago-agregado', 'El IVA fue modificado.');
        }

        public function UpdateIva2($id_pedido_prod, $iva = 1)
        {
          $items_viejo = SaleDetail::find($id_pedido_prod);
          $venta_viejo = Sale::find($items_viejo->sale_id);
          $venta_id = $venta_viejo->id;
          
          $iva_viejo = $items_viejo->iva;
          
          // precio 
          $precio_original = $items_viejo->precio_original;
          $descuento_promo_original = $items_viejo->descuento_promo * (1 +$iva_viejo);
          $descuento_original = $items_viejo->descuento * (1 +$iva_viejo);
          $recargo_original = $items_viejo->recargo * (1 +$iva_viejo);
          
          $precio_nuevo = $items_viejo->precio_original/(1+$iva);
          $descuento_promo_nuevo = $descuento_promo_original /(1+$iva);
          $descuento_nuevo = $descuento_original /(1+$iva);
          $recargo_nuevo = $recargo_original/(1+$iva);
         
         // dd($precio_nuevo,$descuento_promo_nuevo,$descuento_nuevo,$recargo_nuevo);
         
          $subtotal_original = $precio_original - $descuento_original - $descuento_original + $recargo_original; 
          $subtotal_nuevo = $subtotal_original / (1 + $iva);
          $iva_total_nuevo = $subtotal_original - $subtotal_nuevo;
            
          $array = [
            'price' => $precio_nuevo,
            'iva' => $iva,
            'iva_total' => $iva_total_nuevo,
            'descuento' => $descuento_nuevo,
            'descuento_promo' => $descuento_promo_nuevo,
            'recargo' => $recargo_nuevo
            ];
          
          //dd($items_viejo->id,$array);
          
          $items_viejo->update($array);

          $this->ActualizarTotalesVenta($venta_id);
          $this->ActualizarEstadoDeuda($venta_id);
          
          $this->RenderFactura($venta_id);
          

        }
  
        public function UpdateIvaProducto($id_pedido_prod,$value){
        
        $sd = SaleDetail::find($id_pedido_prod);
        
        if($sd->relacion_precio_iva == 1 || $sd->relacion_precio_iva == 0){
        $this->UpdateIva($id_pedido_prod,$value);   
        }
        
        // aca funciona mal el cuando se cambia el iva para las precio + iva
        if($sd->relacion_precio_iva == 2){
        $this->UpdateIva2($id_pedido_prod,$value);   
        }
        
        }
        
        
        public function UpdateIva($id_pedido_prod, $iva = 1)
        {

          $items_viejo = SaleDetail::find($id_pedido_prod);

          $iva_viejo = $items_viejo->iva;

          $items_viejo->update([
            'iva' => $iva
            ]);
            
            $this->alicuota_iva = $iva;
            
            
          $this->ActualizarTotalesVenta($items_viejo->sale_id);
          $this->ActualizarEstadoDeuda($items_viejo->sale_id);
          
          $this->RenderFactura($items_viejo->sale_id);
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

           $this->emit("pago-agregado","Cliente modificado");
           
           
       }



public function UpdateCliente() {
    
    $venta = Sale::find($this->NroVenta);
    $venta->cliente_id = $this->id_cliente_elegido;
    $venta->save();
    
    $this->resetCliente();
    $this->RenderFactura($this->NroVenta);
           
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
        
        $detalle_venta = SaleDetail::where('product_id',$record->id)->where('referencia_variacion',$variacion)->where('sale_id',$this->NroVenta)->where('eliminado',0)->first();
        
        if($detalle_venta != null){
        $detalle_venta->update([
            'quantity' => $detalle_venta->quantity + 1
            ]);    
        } else {
        $detalle_venta = SaleDetail::create([
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
           'producto_id' => $record->id,
           'cantidad_movimiento' => -1,
           'stock' => $product_stock->stock,
           'usuario_id' => $usuario_id,
           'comercio_id'  => $this->sucursal_id
         ]);

        // AKA
        
        $sd = SaleDetail::find($detalle_venta->id);
        
        // Buscamos la promo
        $promo = $this->GetDescuentoPromo($sd->product_id,$sd->referencia_variacion);
        //   $this->SetDescuentoPromo($promo,$product_id,$referencia_variacion,$detalle_venta,$cantidad_nueva);
        $this->SetDescuentoPromo($promo,$sd->product_id,$sd->referencia_variacion,$sd,$sd->quantity);
        $this->ActualizarTotalesVenta($this->NroVenta);
        $this->ActualizarEstadoDeuda($this->NroVenta);
        
        // promo tipo 2
        if($promo != null){
        if($promo->tipo_promo == 2){
        $this->SetPromoTipo2($promo);    
        }
        }
    
 
        
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
    
     $this->id_venta = $ventaId;
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
     
     $this->relacion_precio_iva  = $this->GetRelacionPrecioIVA($ventaId);
     //dd($this->relacion_precio_iva);
     
     $this->data_total = Sale::select('sales.total')
     ->where('sales.id', $ventaId)
     ->get();

     $this->tot = $this->data_total->sum('total');

     $this->ventaId = $ventaId;
     
     $this->clientes = 	ClientesMostrador::where('comercio_id', 'like', Auth::user()->casa_central_user_id)->where('eliminado',0)->get();


    /////////////// DETALLE DE VENTA /////////////////////7
       $this->detalle_venta = SaleDetail::join('products as p','p.id','sale_details.product_id')
       ->join('sales','sales.id','sale_details.sale_id')
       ->select('sale_details.id_promo','sale_details.nombre_promo','sale_details.cantidad_promo','sale_details.descuento_promo','sale_details.precio_original','sale_details.estado','sale_details.id','sale_details.descuento','sale_details.recargo','sale_details.id','sale_details.price','sale_details.quantity','sale_details.iva','sale_details.product_name','sale_details.product_barcode','p.stock','p.stock_descubierto','sales.status')
       ->where('sale_details.sale_id', $ventaId)
       ->where('sale_details.eliminado',0)
       ->get();


       $this->total_total = Sale::join('metodo_pagos as m','m.id','sales.metodo_pago')
       ->select('sales.relacion_precio_iva','sales.cliente_id','sales.descuento_promo','sales.recargo','sales.status','sales.alicuota_iva','sales.tipo_comprobante','sales.created_at','sales.descuento','sales.subtotal','sales.total','sales.created_at as fecha','sales.observaciones','sales.nota_interna', 'm.nombre as metodo_pago','sales.cae','sales.vto_cae','sales.nro_factura','sales.iva')
       ->where('sales.id', $ventaId)
       ->get();
       
       //$this->facturas = facturacion::where('sale_id',$ventaId)->get();
       //$this->ultima_factura = facturacion::where('sale_id',$ventaId)->orderBy('id','desc')->first();

       
       // 2-5-2024
       $this->facturas = facturacion::leftjoin('datos_facturacions','datos_facturacions.id','facturacions.datos_facturacion_id')->select('facturacions.*','datos_facturacions.razon_social')->where('sale_id',$ventaId)->get();
       $this->ultima_factura = facturacion::where('sale_id',$ventaId)->orderBy('id','desc')->first();
       
       //

        $this->subtotal_con_iva = $this->sumarSubtotalConIva($this->detalle_venta);
        $this->descuento_promo_con_iva = $this->sumarDescuentoPromoConIva($this->detalle_venta);
          
        foreach($this->total_total as $t) {
            $this->cliente_id = $t->cliente_id;
            $alicuota_iva = $t->total/($t->subtotal + $t->recargo - $t->descuento - $t->descuento_promo);
            $this->alicuota_iva = $alicuota_iva - 1;
            $this->status = $t->status;
            $this->nota_interna = $t->nota_interna;
            $this->observaciones = $t->observaciones;
            $this->tipo_factura = $t->tipo_comprobante;
            $this->iva_total = $t->iva;
            $this->relacion_precio_iva = $t->relacion_precio_iva;
        }
       
       
       //dd($this->alicuota_iva);

       
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
        ->select('pagos_facturas.url_comprobante','pagos_facturas.nro_comprobante','mp.nombre as metodo_pago','pagos_facturas.id','pagos_facturas.iva_recargo','pagos_facturas.iva_pago','cajas.nro_caja','pagos_facturas.recargo','pagos_facturas.monto','pagos_facturas.created_at as fecha_pago')
        ->where('pagos_facturas.id_factura', $ventaId)
        ->where('pagos_facturas.eliminado',0)
        ->get();

    //    dd($this->pagos2);
        
        $this->suma_monto = $this->pagos2->sum('monto');
        $this->rec = $this->pagos2->sum('recargo');
        $this->rec = $this->rec ?? 0; 
        
        $this->sum_iva_pago = $this->pagos2->sum('iva_pago');
        $this->sum_iva_pago = $this->sum_iva_pago ?? 0; 
        
        $this->sum_iva_recargo = $this->pagos2->sum('iva_recargo');
        $this->sum_iva_recargo = $this->sum_iva_recargo ?? 0; 
        
        $this->total_recargo = $this->rec + $this->sum_iva_recargo;
        //dd($total_recargo);
        
        $this->desc = $this->pagos2->sum('descuento');


        $this->estado = "display: none;";
        $this->estado2 = "display: none;";

        $this->total_total2 = Sale::join('metodo_pagos as m','m.id','sales.metodo_pago')
        ->select('sales.recargo','sales.tipo_comprobante','sales.created_at','sales.descuento','sales.total','sales.created_at as fecha','sales.observaciones','sales.nota_interna', 'm.nombre as metodo_pago','sales.cae','sales.vto_cae','sales.nro_factura')
        ->where('sales.id', $ventaId)
        ->first();

        if($this->ecommerce_envio_form != null) {
           $this->tipo_entrega = $this->ecommerce_envio_form->metodo_entrega;
           $this->nombre_destinatario = $this->ecommerce_envio_form->nombre_destinatario;
           $this->direccion = $this->ecommerce_envio_form->direccion;
           $this->ciudad = $this->ecommerce_envio_form->ciudad;
           $this->nombre_provincia = $this->ecommerce_envio_form->nombre_provincia;
           $this->telefono = $this->ecommerce_envio_form->telefono;
        }

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
     $this->nro_comprobante = null;
     $this->comprobante = null;
   }


   function CerrarAgregarPago($ventaId) {

     $this->emit('agregar-pago-hide','details loaded');

     $this->ResetPago();

     $this->RenderFactura($ventaId);

   }


 // NUEVO
    public function MontoPagoReal($vale) {
     $alicuota_iva = $this->GetAlicuotaIVA($this->id_pedido);   
     $this->monto_ap = $this->monto_real * (1 + $alicuota_iva); 
     $this->MontoPagoEditarPago($this->monto_ap);
    }


 public function setMontoReal($monto,$alicuota_iva,$relacion_precio_iva){
 $valor = $monto / (1 + $alicuota_iva);
 $value = floatval($valor);
 return $value;     
 }    
 
 
 public function setRecargoPagos($monto,$recargo){
    return $monto * $recargo;
 }
 
 public function setRecargoTotal($recargo_total,$iva_recargo,$relacion_precio_iva){
  if($relacion_precio_iva == 2) {      
  $recargo = $recargo_total + $iva_recargo;    
  }
  if($relacion_precio_iva == 1 || $relacion_precio_iva == 0) {      
  $recargo = $recargo_total;    
  }  
  return $recargo;    
 }

  public function setMontoPagos($monto_original,$monto_real,$recargo,$iva_pago,$iva_recargo,$relacion_precio_iva){
  //dd($relacion_precio_iva);
  // si el pago es total
  if($relacion_precio_iva == 2) {      
  $monto_seted = $monto_real;    
  }
  if($relacion_precio_iva == 1 || $relacion_precio_iva == 0) {      
  $monto_seted = $monto_original;    
  }  
  return $monto_seted;
  }

  
  public function sumarIVAPagoNuevo($efectivo,$subtotal,$descuento,$descuento_promo,$recargo_total,$alicuota_iva,$pago_parcial) {
  if($pago_parcial == 0){
  $this->sum_iva_pago = ($subtotal - $descuento - $descuento_promo) * $alicuota_iva;
  } 
  if($pago_parcial == 1){
  $this->sum_iva_pago = $efectivo * $alicuota_iva;    
  }
  return $this->sum_iva_pago;    
  }

  
  public function sumarIVAPago($monto_real,$alicuota_iva) {
  return $monto_real * $alicuota_iva;    
  }
  
  public function sumarIVARecargo($recargo_total,$alicuota_iva) {
  return $recargo_total * $alicuota_iva;
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
     
     $this->MetodoPago(1);
   }


   } else {
    $this->metodo_pago_agregar_pago = 'Elegir';
   	$this->metodo_pago = 'Elegir';
   }
    $alicuota_iva = $this->GetAlicuotaIVA($this->id_pedido);   
    $this->monto_real = $this->setMontoReal($this->monto_ap,$alicuota_iva,$this->relacion_precio_iva);
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
            $product_stock->stock = $product_stock->stock + $item->quantity;
            $product_stock->save();

               $usuario_id = Auth::user()->id;

                if(Auth::user()->comercio_id != 1)
                $comercio_id = Auth::user()->comercio_id;
                else
                $comercio_id = Auth::user()->id;
            
            // creamos los movimientos de stock
            
              $historico_stock = historico_stock::create([
               'tipo_movimiento' => 22,
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
               'tipo_movimiento' => 21,
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

    $pagos = pagos_facturas::where('id_factura',$id)->get();
    foreach($pagos as $p){
        $p->eliminado = 1;
        $p->save();
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
    public function UpdateEstadoVerVenta($estado, $origen) {
    if($estado == "Pendiente"){$estado_id = 1;}
    if($estado == "En proceso"){$estado_id = 2;}
    if($estado == "Entregado"){$estado_id = 3;}
    if($estado == "Cancelado"){$estado_id = 4;}
    if($estado == "Entrega Parcial"){$estado_id = 5;}
    $this->Update($estado_id, $origen);
    
    }
    
    public function Update($estado_id, $origen)
    {   
        
        //dd($estado_id);
        $this->SetCasaCentral();
        
        // Aca tenemos que modificar los stocks en casa central y sucursales

      $estado = Sale::select('sales.status','sales.id','users.email','sales.canal_venta','sales.wc_order_id','sales.cliente_id')
      ->join('clientes_mostradors','clientes_mostradors.id','sales.cliente_id')
      ->leftjoin('users','users.cliente_id','clientes_mostradors.id')
      ->where('sales.id', $this->id_pedido)->first();

      $estado_original = $estado->status;
      
      if($estado_original == "Entrega Parcial") {
      if($estado_id == 1 || $estado_id == 2) {
      $this->emit("msg-error","No puede pasar a pendiente el pedido, ya que tiene entregados algunos productos");
      return;    
      }}
            
            
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
    
    public function SetEstadoItemSucursal($item,$accion) {
      if($accion == 1){$estado = 1;} else {$estado = 0;}
      $dcp = detalle_compra_proveedores::where('sale_detail_casa_central',$item->id)->first();
      $dcp->estado = $estado;
      $dcp->save();

      return $dcp->compra_id; 
    }    
    
    public function EntregaParcial($producto_id,$accion)
    {   
      // $accion 1 es entregado, 0 es pendiente de entrega
      
      $this->SetCasaCentral();
        
      // Aca tenemos que modificar los stocks en casa central y sucursales

      $venta = Sale::find($this->id_pedido);

      $item = SaleDetail::find($producto_id);
      $product_stock = $this->GetStockUpdateEstado($item, $this->sucursal_id, 0);
      $cliente_sucursal = ClientesMostrador::find($venta->cliente_id);
      
      
      // Si se pasa a entregado 
      if($accion == 1){
      if($item->estado != 1) {
          
      if($product_stock->stock_real < 1){
          $this->emit("msg-error","No hay stock disponible. Disponible: ".$product_stock->stock_real);
          return;
      }
      if($product_stock->stock_real < $item->quantity){
          $this->emit("msg-error","No hay stock disponible. Disponible: ".$product_stock->stock_real);
          return;
      }
      
      
      $returnReal = $this->UpdateEstadoRestarStockReal($item, $this->sucursal_id, 0);

      $this->HistoricoStock($item->product_id,$item->referencia_variacion,-$item->quantity,$returnReal,$this->comercio_id,3,1);     
      
      // valores a pasar $id_venta, $venta, $estado_nuevo, $estado_original, $sale_details
      $estado_nuevo = 3;
      $estado_original = "Pendiente";
      
      if($cliente_sucursal->sucursal_id != null) {
      $this->UpdateEstadoSucursal($this->id_pedido,$venta,$estado_nuevo,$estado_original,$item); //aca actualizar el stock de la sucursal
      $compra_id =  $this->SetEstadoItemSucursal($item,$accion);      
      }
      
      $item->estado = 1;
      $item->save();
      }
      }
      
      // Si se pasa a pendiente 
      if($accion == 2){
      if($item->estado == 1) {
      $returnReal = $this->UpdateEstadoSumarStockReal($item, $this->sucursal_id, 0);
      
      $this->HistoricoStock($item->product_id,$item->referencia_variacion,$item->quantity,$returnReal,$this->comercio_id,1,1);      
      
      $estado_nuevo = 1;
      $estado_original = "Entregado";
      
      if($cliente_sucursal->sucursal_id != null) {
      $this->UpdateEstadoSucursal($this->id_pedido,$venta,$estado_nuevo,$estado_original,$item); //aca actualizar el stock de la sucursal
      $compra_id = $this->SetEstadoItemSucursal($item,$accion);
      }
      
      $item->estado = 0;
      $item->save();
     
      }
      }

      $estado = $this->SetEstadoComposicionVenta($venta);

      if($cliente_sucursal->sucursal_id != null) {
      $this->SetEstadoComposicionCompra($compra_id,$estado);          
      }
      
      $this->RenderFactura($this->id_pedido);
   
      $this->emit('pago-agregado', 'El estado fue guardado.');
    }
    
    public function SetEstadoComposicionVenta($venta){
     
     $composicion_venta = SaleDetail::where('sale_id',$venta->id)->where('eliminado',0)->get();
      
      $entregado = true; // Supongamos que todos los elementos están inicialmente marcados como "entregado"

      //dd($composicion_venta);
      foreach ($composicion_venta as $detalleVenta) {
          if ($detalleVenta->estado == 0) {
          // Si al menos un elemento tiene estado igual a 0, actualiza la variable $entrega_parcial
          $entregado = false;
          break; // Puedes detener el bucle tan pronto como encuentres un elemento con estado diferente de 0
        }
      }
        
      if ($entregado == true) {
        $estado = "Entregado";  
      } else {
        $estado = "Entrega Parcial";  
      }

      // Verificar si todos los elementos tienen estado igual a 0
        $todosConEstadoCero = $composicion_venta->every(function ($detalleVenta) {
            return $detalleVenta->estado == 0;
        });
        
        if ($todosConEstadoCero) {
         $estado = "Pendiente";
        }
            
      $venta->update([
        'status' => $estado
      ]);
      
      return $estado;
    
    }
    
    public function SetEstadoComposicionCompra($compra_id,$estado){
      
    if($estado == "Pendiente"){$estado_id = 1;}
    if($estado == "En proceso"){$estado_id = 2;}
    if($estado == "Entregado"){$estado_id = 3;}
    if($estado == "Cancelado"){$estado_id = 4;}
    if($estado == "Entrega Parcial"){$estado_id = 5;}
 
      
      $compra = compras_proveedores::find($compra_id);
      
      $compra->update([
        'status' => $estado_id
      ]);
    
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
    
    public function UpdateEstadoConEstadoParcial($estado_id,$item,$sucursal_id){
                
                // El estado nuevo es cancelado -- OK
                if($estado_id == 4) {
                    $return = $this->UpdateEstadoSumarStock($item,$sucursal_id,$estado_id);    // Actualiza stock de casa central -- Sumandole las unidades
                    $this->SetEstadoItem($item,0);
                }   
                    
                // El estado nuevo es entregado  -- OK    
               if($estado_id == 3) {
                    // El stock disponible queda igual -- para todos aquellos que no han sido entregados
                    if($item->estado == 0){
                    $returnReal = $this->UpdateEstadoRestarStockReal($item,$sucursal_id,$estado_id);    // Actualiza stock de casa central -- Restandole las unidades al stock real     
                    $this->HistoricoStock($item->product_id,$item->referencia_id,-$item->quantity,$returnReal,$item->comercio_id,$estado_id,1);
                    $this->SetEstadoItem($item,1);
                    }
               }   
                
                        
    }    
    public function SwitchUpdateEstado($estado_original,$estado_id,$item,$sucursal_id) {


            // ver tratamiento de ($estado_original == "Entrega Parcial" )
            if(($estado_original == "Entrega Parcial" )) {
            $this->UpdateEstadoConEstadoParcial($estado_id,$item,$sucursal_id);
            }                    
            // Si el ESTADO ORIGINAL es Pendiente/En proceso  

            // ver tratamiento de ($estado_original == "Entrega Parcial" )
            if(($estado_original == "Pendiente") ||  ($estado_original == "En proceso" ) ) {
                
                // El estado nuevo es cancelado -- OK
                if($estado_id == 4) {
                    $return = $this->UpdateEstadoSumarStock($item,$sucursal_id,$estado_id);    // Actualiza stock de casa central -- Sumandole las unidades
                    $this->SetEstadoItem($item,0);
                }   
                    
                // El estado nuevo es entregado  -- OK    
               if($estado_id == 3) {
                //dd($estado_original,$estado_id,$sucursal_id);
                    // El stock disponible queda igual
                    $returnReal = $this->UpdateEstadoRestarStockReal($item,$sucursal_id,$estado_id);    // Actualiza stock de casa central -- Restandole las unidades al stock real     
                    
                    
                    // sumamos el historico de stock
                    $this->HistoricoStock($item->product_id,$item->referencia_id,-$item->quantity,$returnReal,$item->comercio_id,$estado_id,1);
                    $this->SetEstadoItem($item,1);
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
                
                $this->SetEstadoItem($item,0);
                }   
                
                // El estado nuevo es pendiente/en proceso -- OK
                
                if($estado_id != 4 && $estado_id != 3) {
                $returnReal = $this->UpdateEstadoSumarStockReal($item,$sucursal_id,$estado_id);    // Actualiza stock real de casa central -- Sumandole las unidades
                
                // sumamos el historico de stock
                $this->HistoricoStock($item->product_id,$item->referencia_id,$item->quantity,$returnReal,$item->comercio_id,$estado_id,1);     
                $this->SetEstadoItem($item,0);
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
                $this->SetEstadoItem($item,1);
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
            //dd($product_stock);
            
            $product_stock->update([
                'stock' => $product_stock->stock,
                'stock_real' => $stock_real
            ]);
            
            //dd($product_stock);
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

	public function FacturarAfipOld($ventaIdFactura)
	{
	    //dd($ventaIdFactura);
	    $this->EmitirFacturaTrait($ventaIdFactura);
	    
	    if(0 < $this->NroVenta){
	    $this->RenderFactura($this->NroVenta);
	    }
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
    
    
    public function AnularFacturaOld($ventaId) {
        
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
        
    public function AnularFactura($factura_id) {
        $this->AnularFacturaTrait($factura_id);
        $this->RenderFactura($this->NroVenta);
    }



// SECCION ACCIONES DE LA VENTA (MAILS, IMPRIMIR, ETC)

public function CerrarModalEstado()
{

  $this->emit('modal-estado-hide','close');

    $this->tipo_click = 0;
 
}



public function AccionEnLote($ids, $id_accion)
{

//EliminarVenta($id)
//dd($id_accion);
$this->ventas_checked = Sale::select('sales.id','sales.nro_factura')->whereIn('sales.id',$ids)->orderBy('sales.id','asc')->get();

// 2-5-2024
if($id_accion == 1){
    $this->ElegirCUITFacturar($ids);
}

if($id_accion == 2){
foreach ($this->ventas_checked as $vc) {
$this->EliminarVenta($vc->id);    
}
$this->id_check = [];
}

}

public function AccionEnLoteOld($ids, $id_accion)
{

//EliminarVenta($id)
//dd($id_accion);
$this->ventas_checked = Sale::select('sales.id','sales.nro_factura')->whereIn('sales.id',$ids)->orderBy('sales.id','asc')->get();

foreach ($this->ventas_checked as $vc) {

if($id_accion == 2){
$this->EliminarVenta($vc->id);    
}

if($vc->nro_factura == null && $id_accion == 1) {
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



public function MailModalVerVenta($origen,$Id) {
    
    $this->origen_mail_modal = $origen;
    
    if($origen == "venta") {
    $this->ventaId = $Id;
    $venta = Sale::find($Id);
    
    $cliente = ClientesMostrador::find($venta->cliente_id);
    
    $this->mail_ingresado = $cliente->email;
    $this->emit('mail-modal', '');

    $this->RenderFactura($Id);
    }
    
    if($origen == "factura") {

    $this->factura_id = $Id;
    $factura = facturacion::find($Id);
    
    $cliente = ClientesMostrador::find($factura->cliente_id);
    
    $this->mail_ingresado = $cliente->email;
    $this->emit('mail-modal', '');

    $this->RenderFactura($factura->sale_id);
    }

}


public function EnviarMail() {

     if($this->origen_mail_modal == "venta"){
      return redirect('report-email/pdf' . '/' . $this->ventaId  . '/' . $this->mail_ingresado);     
     }

    if($this->origen_mail_modal == "factura"){
     return redirect('enviar-factura/pdf' . '/' . $this->factura_id  . '/' . $this->mail_ingresado);        
    }

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

         $detalle_venta = SaleDetail::where('sale_details.product_id', $item)->where('sale_details.sale_id', $this->NroVenta)->where('sale_details.eliminado', 0)->first();

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

         $ip = productos_ivas::where('product_id',$item)->first();
         $iva_producto = $ip->iva ?? 0;
        
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
 
         $precio = $product_price/(1+$iva_producto);
           
         }
         
         $iva_defecto = $iva_producto;
         
        } else {
        $precio = $product_price;   
        $iva_defecto = 0;
        }
        
        //dd($precio,$iva_defecto);
        
        // Comprueba si hay stock o no del producto
        
        if($product_stock->stock < 1 && $product->stock_descubierto == "si" ) {

           $this->emit('no-stock','Stock insuficiente');
           $this->RenderFactura($this->NroVenta);

         } else {
         
         // comprueba si el producto esta en la venta o no...
         
        $alicuota_descuento_gral = $this->GetAlicuotaDescuentoGeneralProducto($this->NroVenta);
        
         if($detalle_venta == [] || $detalle_venta == null || empty($detalle_venta))
         {
            
        // no esta en la venta, lo ingresa.... 
        $descuento = $precio * $alicuota_descuento_gral;
        
        $detalle_venta = SaleDetail::create([
           'price' => $precio,
           'precio_original' => $product_price,
           'quantity' => 1,
           'product_name' => $product->name,
           'product_barcode' => $product->barcode,
           'product_id' => $product->id,
           'metodo_pago'  => $venta->metodo_pago,
           'seccionalmacen_id' => $product->seccionalmacen_id,
           'comercio_id' => $venta->comercio_id,
           'sale_id' => $this->NroVenta,
           'iva' => $iva_defecto,
           'iva_total' => $precio*$iva_defecto,
           'canal_venta' => $venta->canal_venta,
           'descuento' => $descuento,
           'recargo' => 0,
           'cliente_id' => $venta->cliente_id,
           'relacion_precio_iva' => $venta->relacion_precio_iva 
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
        

         } else {
        
        // Si el producto existe en la venta
        $cantidad_nueva = $detalle_venta->quantity + 1;
        
        // aca updateqty con la cantidad nueva   
        $this->updateQtyPedido($detalle_venta->id, $cantidad_nueva);
        }

        $this->resetProduct();
        
        $sd = SaleDetail::find($detalle_venta->id);
        
        // Buscamos la promo
        $promo = $this->GetDescuentoPromo($sd->product_id,$sd->referencia_variacion);
        //   $this->SetDescuentoPromo($promo,$product_id,$referencia_variacion,$detalle_venta,$cantidad_nueva);
        $this->SetDescuentoPromo($promo,$sd->product_id,$sd->referencia_variacion,$sd,$sd->quantity);

        // promo tipo 2
        if($promo != null){
        if($promo->tipo_promo == 2){
        $this->SetPromoTipo2($promo);    
        }
        }
        
        $alicuota_descuento_gral = $this->GetAlicuotaDescuentoGeneralProducto($sd->sale_id);
        $descuento_nuevo = $this->SetDescuentoGeneralProducto($sd->id,$sd->quantity,$alicuota_descuento_gral);
 
        $sd->update([
          'descuento' => $descuento_nuevo
        ]);
          
        $this->ActualizarTotalesVenta($this->NroVenta);
        $this->ActualizarEstadoDeuda($this->NroVenta);
        
        $this->emit('pago-agregado', 'El producto fue agregado.');
         }        
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
    
    $detalle_venta = SaleDetail::find($id_pedido_prod);
    
    $product_id = $detalle_venta->product_id;
    $referencia_variacion = $detalle_venta->referencia_variacion;
    $this->NroVenta = $detalle_venta->sale_id;

    // Aca setea el descuento general    
    $alicuota_descuento_gral = $this->GetAlicuotaDescuentoGeneralProducto($detalle_venta->sale_id);
    //dd($alicuota_descuento_gral);
    $product_stock = $this->GetProductStock($product_id,$referencia_variacion,$this->sucursal_id);
    
    // variables a usar
    
	$cantidad_anterior = $detalle_venta->quantity;
	
	// Stock Disponible
	
	//Stock anterior
	$stock_anterior = $product_stock->stock;
	$stock_real_anterior = $product_stock->stock_real;
	
	// Cantidades de movimiento
	$cantidad_nueva = $cantidad;
	$cantidad_movimiento = $cantidad_anterior - $cantidad_nueva;
	
	// Stocks nuevos
	$stock = $stock_anterior + $cantidad_movimiento;
	$stock_real = $stock_real_anterior + $cantidad_movimiento;

	$diferencia_stock = $cantidad_nueva - $cantidad_anterior;

	$product = Product::find($product_id);
    
	if($cantidad_anterior < $cantidad_nueva) {
	
	if( ($stock_anterior < $diferencia_stock) && $product->stock_descubierto == "si" ) {
 
    $stock_disponible_pedido = $cantidad_anterior + $stock_anterior;
       
    $this->emit('no-stock','Stock insuficiente.');
    $this->emit('msg-error','Stock insuficiente. Disponible: '.$stock_disponible_pedido);
    $this->emit('volver-stock', $detalle_venta->id.'-'.$stock_disponible_pedido);
    $this->RenderFactura($this->NroVenta);
    
       
    $detalle_venta->update([
	'quantity' => $stock_disponible_pedido
	]);

    $return = false;
    return;
    } 
    
	}    

	// ACA HAY QUE VER COMO ACTUALIZA EL STOCK SEGUN COMO ESTE EL ESTADO DEL PEDIDO 
    
    $venta = Sale::find($this->NroVenta);
	
	// Si esta entregado actualiza los dos stocks
	$this->SetProductStock($detalle_venta,$product_stock,$stock,$stock_real);

	// Actualiza el historico de movimientos

    if($this->sucursal_id == 0) { $this->sucursal_id = $this->casa_central_id;} else {$this->sucursal_id = $this->sucursal_id; }

    $historico_stock =  $this->SetHistoricoStock(3,$this->NroVenta,$product_id,$referencia_variacion,$cantidad_movimiento,$stock,$this->sucursal_id,$this->sucursal_id);

	// Calcula nuevamente los descuentos y Actualiza las cantidades en el detalle de ventas
    
    // Buscamos la promo
    $promo = $this->GetDescuentoPromo($product_id,$referencia_variacion);
    // VEMOS SI CORRESPONDE PROMO TIPO 1 Y ACTUALIZA
    $this->SetDescuentoPromo($promo,$product_id,$referencia_variacion,$detalle_venta,$cantidad_nueva);

    // Aca setea el descuento general    
    $descuento_nuevo = $this->SetDescuentoGeneralProducto($detalle_venta->id,$cantidad_nueva,$alicuota_descuento_gral);

	
	$detalle_venta->update([
	'quantity' => $cantidad_nueva,
	'descuento' => $descuento_nuevo,
	]);


    // Si es una venta a una sucursal
    if($venta->canal_venta == "Venta a sucursales") {

    $compra = compras_proveedores::where('sale_casa_central',$venta->id)->first();
      
    $producto_comprado = detalle_compra_proveedores::where('compra_id',$compra->id)
    ->where('producto_id',$product_id)
    ->where('referencia_variacion',$referencia_variacion)
    ->first();
      
    $producto_comprado->update([
	'cantidad' => $cantidad_nueva
	]);
	
	$movimiento = $diferencia_stock;
    
    // Aca hay que cambiar cambiar el stock de la sucursal si es que esta como entregado
    if($detalle_venta->estado == 1) {
    
    $this->UpdateQtySucursal($product_id,$referencia_variacion,$compra->comercio_id,$this->casa_central_id,$movimiento);

    }
    
    }
    

    if($promo != null){
    if($promo->tipo_promo == 2){
    $this->SetPromoTipo2($promo);    
    }
    }
    
    // Actualizar en wocommerce
    $result = $this->UpdateProductStockIndividualWocommerce($product_id,$referencia_variacion,$detalle_venta->comercio_id);
  
    //dd($result);
        
    }
    
    public function GetAlicuotaDescuentoGeneralProducto($sale_id){
    $sale = Sale::find($sale_id);
    $alicuota_descuento = $sale->alicuota_descuento;
    return $alicuota_descuento;
    }
    
    /*    
    public function GetAlicuotaDescuentoGeneralProducto($detalle_venta){
    $total_sin_promo = ($detalle_venta->price * $detalle_venta->quantity) - ($detalle_venta->descuento_promo * $detalle_venta->cantidad_promo);
    $alicuota_descuento = $detalle_venta->descuento / $total_sin_promo;
    //dd($alicuota_descuento);
    return $alicuota_descuento;
    }
    */
    
    public function SetDescuentoGeneralProducto($detalle_venta_id,$cantidad_nueva,$alicuota_descuento_gral){
    $sale_detail = SaleDetail::find($detalle_venta_id);
    $subtotal = $sale_detail->price * $cantidad_nueva;
    $descuento_promo = $sale_detail->descuento_promo * $sale_detail->cantidad_promo;
    $total_sin_promo = $subtotal - $descuento_promo;
    $descuento_nuevo = $total_sin_promo * $alicuota_descuento_gral;
	
	return $descuento_nuevo;
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
              ->select('mp.nombre as metodo_pago','pagos_facturas.id','pagos_facturas.iva_recargo','pagos_facturas.iva_pago','pagos_facturas.recargo','pagos_facturas.monto','pagos_facturas.created_at as fecha_pago')
              ->where('pagos_facturas.id_factura', $ventaId)
              ->where('pagos_facturas.eliminado',0)
              ->get();
    
              // dd($this->data_total);
              
              //dd($this->pagos2);
              
              // Pagos
              $this->suma_monto = $this->pagos2->sum('monto');
              
              // Recargos
              $this->rec = $this->pagos2->sum('recargo');
              
              // iva pago
              $iva_pago = $this->pagos2->sum('iva_pago');
                            
              // IVA Recargo
              $iva_recargo = $this->pagos2->sum('iva_recargo');
              
              // total de la factura
              $this->tot = $this->data_total->total;
              
             // dd($this->tot,$this->suma_monto,$this->rec);
             
              if($this->data_total->status != "Cancelado") {
              $deuda = $this->tot - $this->suma_monto - $this->rec - $iva_pago - $iva_recargo;    
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

    if (!Auth::check()) {
        // Redirigir al inicio de sesión y retornar una vista vacía
        $this->redirectLogin();
        return view('auth.login');
    }
        
            
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
    
    $monto_total = $pago_casa_central->monto + $pago_casa_central->recargo + $pago_casa_central->iva_recargo + $pago_casa_central->iva_pago;
    
    $pago_factura =   pagos_facturas::create([
      'monto_compra' => $monto_total,
      'caja' => null,
      'banco_id' => $this->tipo_pago_sucursal ?? 1,
      'nro_comprobante' => $this->nro_comprobante,
      'created_at' => $this->fecha_ap,
      'proveedor_id' => 2,
      'comercio_id' => $compras_proveedores->comercio_id,
      'id_compra' => $compraId,
      'eliminado' => 0,
      'pago_sucursal_id' => $pago_casa_central->id
    ]);

    
    if($this->comprobante)
	{
		$customFileName = uniqid() . '_.' . $this->comprobante->extension();
		$this->comprobante->storeAs('public/comprobantes', $customFileName);
		$pago_factura->url_comprobante = $customFileName;
		$pago_factura->save();
	}
		
		    
    $this->ActualizarTotalCompra($compraId);
    $this->ActualizarEstadoDeudaCompra($compraId);
  
    $this->tipo_pago_sucursal = 1;
        
    $this->ResetPago();
    
    return $pago_factura->id;

  }
  
  // 1-12-2023 --> ver aca

public function ActualizarPagoCompra($id_pago_casa_central) {

  $pago_casa_central = pagos_facturas::find($id_pago_casa_central);
  
  $id = $id_pago_casa_central;
  
  $pagos_sucursal = pagos_facturas::where('pago_sucursal_id',$id)->where('eliminado',0)->first();
  
  $monto_total = $pago_casa_central->monto + $pago_casa_central->recargo + $pago_casa_central->iva_recargo + $pago_casa_central->iva_pago;

  $banco_id = intval($this->tipo_pago_sucursal);
  
  $pagos_sucursal->update([
    'banco_id' => $banco_id,
    'monto_compra' => $monto_total,
    'created_at' => $this->fecha_ap,
    'nro_comprobante' => $this->nro_comprobante,
    'metodo_pago' => $this->metodo_pago_agregar_pago,
  ]);
  
  $compraId = $pagos_sucursal->id_compra;


    if($this->comprobante != $pago_casa_central->url_comprobante)
	{
		$customFileName = uniqid() . '_.' . $this->comprobante->extension();
		$this->comprobante->storeAs('public/comprobantes', $customFileName);
		$pago_casa_central->url_comprobante = $customFileName;
		$pago_casa_central->save();
	}
	
	            
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
     $this->id_venta = 0;
}

public function UpdateNotaInterna($value)
{
$sale = Sale::find($this->NroVenta);
$sale->nota_interna = $value;
$sale->save();

$this->RenderFactura($this->id_pedido);
$this->emit("msg","Nota interna guardada");
}

public function UpdateObservaciones($value)
{
$sale = Sale::find($this->NroVenta);
$sale->observaciones = $value;
$sale->save();    

$this->RenderFactura($this->id_pedido);
$this->emit("msg","Observacion guardada");
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
        
        $array = [
        'producto_id' => $product_id,
        'referencia_variacion' => $referencia_variacion,
        'tipo_movimiento' => $tipo_movimiento,
        'cantidad_movimiento' => $cantidad_movimiento,
        'stock' => $stock_real,
        'usuario_id' => $comercio_id,
        'comercio_id'  => $comercio_id
        ];
        
        //dd($array);
        $historico_stock = historico_stock::create($array);
        
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

    public function loadColumns()
    {
        $columns = ColumnConfiguration::where(['user_id' => Auth::id(), 'table_name' => 'reports'])
            ->pluck('is_visible', 'column_name')
            ->toArray();

        // Todas las columnas disponibles
        $allColumns = [
            'nro_venta' => true,
            'created_at' => true,
            'nombre_cliente' => true,
            'subtotal' => true,
            'iva' => true,
            'total' => true,
            'nombre_banco' => true,
            'nro_factura' => true,
            'deuda' => true,
            'status' => true,
            'nota_interna' => true,
            'entrega_parcial' => true,
            'observaciones' => true,
            'descuento_promo' => true,
            'descuento' => true,
            'recargo' => true,
            'dias_desde_creacion' => true
        ];

        // Fusionar columnas personalizadas con todas las columnas disponibles
        $this->columns = array_merge($allColumns, $columns);
    }

    public function toggleColumnVisibility($columnName)
    {
        //dd($this->columns[$columnName]);
        $isVisible = ($this->columns[$columnName] ?? false);
        ColumnConfiguration::updateOrCreate(
            ['user_id' => Auth::id(), 'table_name' => 'reports', 'column_name' => $columnName],
            ['is_visible' => $isVisible]
        );

        $this->columns[$columnName] = $isVisible;
    }
    
//10-12

public function ModalAgregarCliente(){
    
    $this->paises = paises::all();
    $this->provincias = provincias::all();
    $this->lista_precios = lista_precios::where('comercio_id',$this->casa_central_id)->get();
    $this->emit('modal-agregar-cliente','');
    $this->RenderFactura($this->NroVenta);
}

public function resetUICliente(){
    $this->RenderFactura($this->NroVenta);
    $this->emit('modal-agregar-cliente-hide','');
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
    
    public function SetEstadoItem($item,$estado){
    $item->estado = $estado;
    $item->save();
    }

    public function VerOpcionesPantalla($value) {
    
    if($value == 1) {$this->ver_opciones_pantalla = 0;}
    if($value == 0) {$this->ver_opciones_pantalla = 1;}
    }

    
    //--------- FUNCION QUE ACTUALIZA LA VENTA --------- //
    
    public function ActualizarTotalesVentaOld($venta) {
    
    $sale = Sale::find($venta);
    $alicuota_recargo = $sale->recargo/($sale->subtotal-$sale->descuento-$sale->descuento_promo);
    
    $this->details = SaleDetail::where('sale_id',$venta)->where('eliminado',0)->get();
    
           //
           $subtotal = $this->details->sum(function($item){
              return $item->price * $item->quantity;
           });

          $descuento = $this->details->sum(function($item){
              return $item->descuento;
          });
          
          $descuento_promo = $this->details->sum(function($item){
              return $item->descuento_promo * $item->cantidad_promo;
          });
          
          $recargo = $this->CalcularRecargo($venta);

          $this->descuento_promo = $descuento_promo;
          $this->descuento = $descuento;
          $this->recargo = $recargo;
          $this->subtotal_venta_nuevo = $subtotal;

          $alicuota_recargo = $recargo / ($subtotal - $descuento_promo - $descuento);

          //dd($alicuota_recargo);
          //$iva = $this->CalcularIVA($subtotal,$descuento,$descuento_promo,$recargo,$this->alicuota_iva);
          
          $s = $subtotal - $descuento_promo - $descuento + $recargo;
        //  dd($s);
          
          $iva = $this->sumarIVATOTAL($this->details, $alicuota_recargo);
          
          $this->iva_venta_nuevo = $iva;
          
          $suma_con_iva = $subtotal + $iva;
          
          $this->total_venta_nuevo = $subtotal + $iva + $recargo - $descuento - $descuento_promo;
          
          $alicuota_iva = $this->total_venta_nuevo/($subtotal + $recargo - $descuento - $descuento_promo);
          $alicuota_iva = $alicuota_iva - 1;
          
          
          $this->items_venta_nuevo = $this->details->sum('quantity');
            
          $this->venta = Sale::find($venta);
          
          
          $array_sale = [
            'subtotal' => $this->subtotal_venta_nuevo,
            'total' => $this->total_venta_nuevo,
            'iva' =>  $this->iva_venta_nuevo,
            'descuento' => $this->descuento,
            'descuento_promo' => $this->descuento_promo,
            'recargo' => $this->recargo,
            'alicuota_iva' => $alicuota_iva
            ];        
          
          //dd($array_sale);
          
          $this->venta->update($array_sale);
            
        // Si es una venta a una sucursal
        if($this->venta->canal_venta == "Venta a sucursales") {
          
          $compra = compras_proveedores::where('sale_casa_central',$this->venta->id)->first();
          
          $this->ActualizarTotalCompra($compra->id);
          $this->ActualizarEstadoDeudaCompra($compra->id);
          
        }
        
        
    }
    

    public function sumarIVATOTALOld($carro, $alicuota_recargo){  
        
        $sum_iva = $carro->sum(function($item) use ($alicuota_recargo) {
              
            // Diferencia precio
            $diferencia_precio = ($item->precio_original - $item->price) * $item->quantity;
    
            // Alicuota IVA
            $iva = $item->iva;
    
            // Subtotal 
            $subtotal_sin_iva = $item->price * $item->quantity;
    
            // Descuento promo
            $descuento_promo_sin_iva = $item->descuento_promo * $item->cantidad_promo;
            $diferencia_descuento_promo = $descuento_promo_sin_iva * $iva;
     
            $subtotal_sin_promo = $subtotal_sin_iva - $descuento_promo_sin_iva;
            
            // Descuento gral
            $descuento_gral = $subtotal_sin_promo * ($item->descuento/100);
            $diferencia_descuento_gral = $descuento_gral * $iva;
     
            $subtotal_sin_promo_sin_desc_gral = $subtotal_sin_promo - $descuento_gral;
            
            // Recargo gral
            $recargo_gral = $subtotal_sin_promo_sin_desc_gral * $alicuota_recargo;
            $diferencia_recargo_gral = $recargo_gral * $iva;
    
            
            if($item->relacion_precio_iva == 2) { 
                $iva_total = $diferencia_precio - $diferencia_descuento_gral + $diferencia_recargo_gral - $diferencia_descuento_promo; 
            }
            
            if($item->relacion_precio_iva == 1) { 
                $iva_total = ($subtotal_sin_iva - $descuento_promo_sin_iva - $descuento_gral + $recargo_gral) * $iva; 
            }
    
            if($item->relacion_precio_iva == 0) { 
                $iva_total = 0; 
            }
         
            return $iva_total;
        });
            
        return $sum_iva ?? 0;
    }         

    public function ActualizarTotalesVenta($venta) {
        
        $sale = Sale::find($venta);
        
        $relacion_precio_iva = $sale->relacion_precio_iva;
        
        $this->details = SaleDetail::where('sale_id',$venta)->where('eliminado',0)->get();
        
               //
               $subtotal = $this->details->sum(function($item){
                  return $item->price * $item->quantity;
               });
    
              $descuento = $this->details->sum(function($item){
                  return $item->descuento;
              });
              
              $descuento_promo = $this->details->sum(function($item){
                  return $item->descuento_promo * $item->cantidad_promo;
              });
    
              $this->descuento_promo = $descuento_promo;
              $this->descuento = $descuento;
              $this->subtotal_venta_nuevo = $subtotal;
    
              $iva = $this->sumarIVASUBTOTAL($this->details);

              $subtotal_sin_recargo = $subtotal - $descuento - $descuento_promo;
              $alicuota_iva = $iva/$subtotal_sin_recargo;
            //  dd($alicuota_iva);
              
              $this->SetearRecargoIvaNuevo($venta,$alicuota_iva,$relacion_precio_iva);
              
              $recargo = $this->CalcularRecargo($venta);
              $iva_recargo = $this->CalcularIVARecargo($venta);
              
              $this->recargo = $recargo;
              $alicuota_recargo = $recargo / ($subtotal - $descuento_promo - $descuento);
              
              $iva = $iva + $iva_recargo;
              $this->iva_venta_nuevo = $iva; 
              $this->total_venta_nuevo = $subtotal + $iva + $recargo - $descuento - $descuento_promo;
    
              $this->items_venta_nuevo = $this->details->sum('quantity');
                
              $this->venta = Sale::find($venta);
              
              $array_sale = [
                'subtotal' => $this->subtotal_venta_nuevo,
                'total' => $this->total_venta_nuevo,
                'iva' =>  $this->iva_venta_nuevo,
                'descuento' => $this->descuento,
                'descuento_promo' => $this->descuento_promo,
                'recargo' => $this->recargo,
                'alicuota_iva' => $alicuota_iva
                ];        
              
              //dd($array_sale);
              
              $this->venta->update($array_sale);
                
            // Si es una venta a una sucursal
            if($this->venta->canal_venta == "Venta a sucursales") {
              
              $compra = compras_proveedores::where('sale_casa_central',$this->venta->id)->first();
              
              $this->ActualizarTotalCompra($compra->id);
              $this->ActualizarEstadoDeudaCompra($compra->id);
              
            }
            
            
        }

    public function sumarIVASUBTOTAL($carro){  
        
        $sum_iva = $carro->sum(function($item) {
              
            // Diferencia precio
            $diferencia_precio = ($item->precio_original - $item->price) * $item->quantity;
    
            // Alicuota IVA
            $iva = $item->iva;
    
            // Subtotal 
            $subtotal_sin_iva = $item->price * $item->quantity;
    
            // Descuento promo
            $descuento_promo_sin_iva = $item->descuento_promo * $item->cantidad_promo;
            $diferencia_descuento_promo = $descuento_promo_sin_iva * $iva;
     
            $subtotal_sin_promo = $subtotal_sin_iva - $descuento_promo_sin_iva;
            
            // Descuento gral
            $descuento_gral = $item->descuento;
            $diferencia_descuento_gral = $descuento_gral * $iva;
     
            $subtotal_sin_promo_sin_desc_gral = $subtotal_sin_promo - $descuento_gral;
            
            if($item->relacion_precio_iva == 2) { 
                $iva_total = $diferencia_precio - $diferencia_descuento_gral - $diferencia_descuento_promo;  
            }
            
            if($item->relacion_precio_iva == 1) { 
                $iva_total = ($subtotal_sin_iva - $descuento_promo_sin_iva - $descuento_gral) * $iva; 
            }
            
            if($item->relacion_precio_iva == 0) { 
                $iva_total = 0; 
            }
            
            return $iva_total;
        });
            
        return $sum_iva ?? 0;
    }
    public function CalcularIVA($subtotal,$descuento,$descuento_promo,$recargo,$alicuota_iva){
        return ($subtotal - $descuento - $descuento_promo + $recargo) * $alicuota_iva;
    }


  // 19-7-2023
  
  public function RecalcularRecargoPago($iva_viejo,$iva_nuevo,$venta_id){ 
      $pagos = pagos_facturas::where("id_factura",$venta_id)->get();
      foreach($pagos as $p){
          $recargo_viejo = $p->recargo;
          $iva_recargo_viejo = $p->iva_recargo;
          
          $recargo_original = $p->recargo + $p->iva_recargo;
          
          $recargo_nuevo = $recargo_original / (1 + $iva_nuevo);
          $iva_recargo_nuevo = $recargo_original - $recargo_nuevo;
          
          $p->recargo = $recargo_nuevo;
          $p->iva_recargo = $iva_recargo_nuevo;
          $p->save();
      }
  }
  
  public function CalcularRecargo($id_venta){  
       
        $venta = Sale::find($id_venta);
        
        $recargo_original = $venta->recargo;
        
        //////////////// PAGOS //////////////
        $pagos = pagos_facturas::where('pagos_facturas.id_factura', $id_venta)
        ->where('pagos_facturas.eliminado',0)
        ->get();       

           $sum_recargo = $pagos->sum(function($item){
            return $item->recargo;
        });  
   
        //dd($sum_recargo);
        $this->alicuota_iva_recargo = $venta->alicuota_iva;

        return $sum_recargo;
  }
  
  public function CalcularIVARecargo($id_venta){  
        
        //////////////// PAGOS //////////////
        $pagos = pagos_facturas::where('pagos_facturas.id_factura', $id_venta)
        ->where('pagos_facturas.eliminado',0)
        ->get();       

           $sum_iva_recargo = $pagos->sum(function($item){
            return $item->iva_recargo;
        });  
   

        return $sum_iva_recargo;
  }

public function SetearRecargoIvaNuevo($id_venta,$alicuota_iva,$relacion_precio_iva){

        //////////////// PAGOS //////////////
        $pagos = pagos_facturas::where('pagos_facturas.id_factura', $id_venta)
        ->where('pagos_facturas.eliminado',0)
        ->get();       

        foreach($pagos as $p){
            
            if($relacion_precio_iva == 1){
              $iva_pago_nuevo = $p->monto * $alicuota_iva;
              $iva_recargo_nuevo = $p->recargo * $alicuota_iva;
              
              $array = [
                'iva_pago' => $iva_pago_nuevo,
                'iva_recargo' => $iva_recargo_nuevo
                ];
            }
             
            if($relacion_precio_iva == 2){
            $total_pago = $p->monto + $p->iva_pago; 
            $total_recargo = $p->recargo + $p->iva_recargo; 
            
            $monto_nuevo = $total_pago / (1 + $alicuota_iva);
            $iva_pago_nuevo = $total_pago - $monto_nuevo;
            
            $recargo_nuevo = $total_recargo / (1 + $alicuota_iva);
            $iva_recargo_nuevo = $total_recargo - $recargo_nuevo;

            $array = [
                'monto' => $monto_nuevo,
                'iva_pago' => $iva_pago_nuevo,
                'recargo' => $recargo_nuevo,
                'iva_recargo' => $iva_recargo_nuevo
                ];
            }

                        
            if($relacion_precio_iva == 0){
              $array = [];
            }

                        
            $pagos_facturas = pagos_facturas::find($p->id);
            $pagos_facturas->update($array);
            
        }

}



public function GetRelacionPrecioIVA($ventaId){
     return Sale::find($ventaId)->relacion_precio_iva;
}


public function SetHistoricoStock($tipo_movimiento,$sale_id,$product_id,$referencia_variacion,$cantidad_movimiento,$stock,$usuario_id,$comercio_id){
        return historico_stock::create([
           'tipo_movimiento' => $tipo_movimiento,
           'sale_id' => $sale_id,
           'producto_id' => $product_id,
           'referencia_variacion' => $referencia_variacion,
           'cantidad_movimiento' => $cantidad_movimiento,
           'stock' => $stock,
           'usuario_id' => $usuario_id,
           'comercio_id'  => $comercio_id
         ]);	    
	}
	
	public function GetProductStock($product_id,$referencia_variacion,$sucursal_id){
	return productos_stock_sucursales::where('sucursal_id',$sucursal_id)
	->where('product_id',$product_id)
	->where('referencia_variacion', $referencia_variacion)
	->first();
	}
	
	public function SetProductStock($detalle_venta,$product_stock,$stock,$stock_real){
	
	if($detalle_venta->estado == 1) {
	
	$product_stock->update([
	'stock' => $stock,
	'stock_real' => $stock_real
	]);

	}
	
	// Si no esta entregado actualiza solo el stock disponible 
	if($detalle_venta->estado != 1) {
	
	$product_stock->update([
	'stock' => $stock
	]);
	}  
	
	}
	
	public function ValidateCantidadStock($cantidad_anterior,$cantidad_nueva,$stock_anterior,$diferencia_stock,$product,$detalle_venta){
	
	$return = true;
	if($cantidad_anterior < $cantidad_nueva) {
	
	if( ($stock_anterior < $diferencia_stock) && $product->stock_descubierto == "si" ) {
 
    $stock_disponible_pedido = $cantidad_anterior + $stock_anterior;
       
    $this->emit('no-stock','Stock insuficiente.');
    $this->emit('volver-stock', $detalle_venta->id.'-'.$stock_disponible_pedido);
    $this->RenderFactura($this->NroVenta);
       
    $this->items_viejo->update([
	'quantity' => $stock_disponible_pedido
	]);

    $return = false;

    } 
    
	}    
	}
	
	
public function GetDescuentoPromo($product_id, $referencia_variacion){
    $return = promos_productos::join('promos','promos.id','promos_productos.promo_id')->where("promos_productos.product_id", $product_id)->where("promos_productos.referencia_variacion", $referencia_variacion)->where("promos.activo", 1)->where("promos_productos.activo", 1)->select("promos_productos.*", "promos.vigencia_desde", "promos.vigencia_hasta","promos.precio_promo" ,"promos.limitar_vigencia","promos.tipo_promo")->first();
    $sale = Sale::find($this->NroVenta);
    
    
    // Verificar la vigencia_promo y las fechas
    if ($return && $return->limitar_vigencia == 1) {
        $fechaActual = $sale->created_at; // Obtener la de creacion de la venta
        $vigenciaDesde = \Carbon\Carbon::parse($return->vigencia_desde);
        $vigenciaHasta = \Carbon\Carbon::parse($return->vigencia_hasta);

        // Verificar si la fecha actual está entre vigencia_desde y vigencia_hasta
        if ($fechaActual->between($vigenciaDesde, $vigenciaHasta)) {
            // La fecha actual está dentro del rango de vigencia
            return $return;
        }
    } elseif ($return && $return->limitar_vigencia == 0) {
        // Vigencia_promo es igual a 0, no se aplica la verificación de fechas
        return $return;
    } else {
        // La vigencia_promo no es igual a 1 o no se encontró la promoción
        return null;
    }
}		

// CalcularDescuentoPromos($detalle_venta,$cantidad_nueva,$promo)
public function CalcularDescuentoPromos($detalle_venta,$cantidad,$promo) {
    
    $nombrePromo = null;
    $cantidadPromo = 0;  
    $descuentoPromo = 0;
    
    if($promo != null){
    
    $nombrePromo = $promo->nombre_promo;
    $cantidadPromo = $detalle_venta->cantidad_promo ?? 0;
    $descuentoPromo = $detalle_venta->descuento_promo ?? 0;
    
    $calculo = $cantidad / $promo->cantidad;    
    $cantidadPromo = floor($calculo);
    
    if (0 < $cantidadPromo) {
        // La tercera unidad se añadió, acumular descuento del % de esa unidad.
        $descuentoPorcentaje = $promo->porcentaje_descuento;
        $descuento = ($detalle_venta->price * $descuentoPorcentaje) / 100;
        
        // Acumular el descuento
        $descuentoPromo = $descuento;
        $cantidadPromo = $cantidadPromo; 
        
        
    }
    }
    
    //dd($nombrePromo);
    
    return [$nombrePromo,$cantidadPromo,$descuentoPromo];
}
	
public function SetDescuentoPromo($promo,$product_id,$referencia_variacion,$detalle_venta,$cantidad_nueva){
    
   // dd($promo);
    
    if($promo != null) {

    if($promo->tipo_promo == 1){            
    $resultado_promo = $this->CalcularDescuentoPromos($detalle_venta,$cantidad_nueva,$promo);

    $nombre_promo = $resultado_promo[0];
    $cantidad_promo = $resultado_promo[1];
    $descuento_promo = $resultado_promo[2];
	
	//dd($nombre_promo,$cantidad_promo,$descuento_promo);
	
	$detalle_venta->update([
	'id_promo' => $promo->promo_id,
	'nombre_promo' => $nombre_promo,
	'cantidad_promo' => $cantidad_promo,
	'descuento_promo' => $descuento_promo
	]);
        
    }
    }
}


	public function SetPromoTipo2($promo){
	
	$promo_id = $promo->promo_id;
	$idsCarro = null;
    $cantidadMinima  = null;
    $sumaSubtotal = null;
    $products_id = null;
    $totalPromo = $promo->precio_promo;
    
    $productos_promocion_original = promos_productos::where('promo_id',$promo->promo_id)->get();
    $carro = SaleDetail::where("sale_id",$this->id_pedido)->where('eliminado',0)->get();
    
    $idsCarro = $this->verificarExistenciaEnCarroPromo2($productos_promocion_original, $carro);
    
    if($idsCarro != null) {
    
    $respuesta_formulas_promos = $this->ObtenerFormulasPromo2($idsCarro,$carro,$promo);
    
    $cantidadMinima = min(array_column($respuesta_formulas_promos, 'formula_promo'));

    $this->ActualizarProductosPromo2($respuesta_formulas_promos,$promo,$cantidadMinima,$carro);

    $this->ActualizarDescuentosGeneralPromo2($respuesta_formulas_promos);
    
    }
	}

    public function ActualizarDescuentosGeneralPromo2($respuesta_formulas_promos){
    foreach($respuesta_formulas_promos as $product_promo){
    $datos = explode("-",$product_promo['id']);
    $sale_details_id = $datos[2];
    // Aca setea el descuento general    
    $detalle_venta = SaleDetail::find($sale_details_id);
    // Aca setea el descuento general    
    $alicuota_descuento_gral = $this->GetAlicuotaDescuentoGeneralProducto($detalle_venta->sale_id);
    $descuento_nuevo = $this->SetDescuentoGeneralProducto($detalle_venta->id,$detalle_venta->quantity,$alicuota_descuento_gral);
    
    $detalle_venta->update([
        'descuento' => $descuento_nuevo
        ]);
    }
    
    }
    
	public function ActualizarProductosPromo2($respuesta_formulas_promos,$promo,$cantidadPromo,$carro){
    
    //dd($respuesta_formulas_promos);
    
    foreach($respuesta_formulas_promos as $product_promo){

       $datos = explode("-",$product_promo['id']);
       $sale_details_id = $datos[2];
       
       $exist = SaleDetail::find($sale_details_id);

       if($exist->relacion_precio_iva == 2){$descuento_promo = $product_promo['descuento']/(1 + $exist->iva);} else{$descuento_promo = $product_promo['descuento'];}
    
       $array = [
           'id_promo' => $promo->promo_id,
           'nombre_promo' => $promo->nombre_promo,
           'descuento_promo' => $descuento_promo,
           'cantidad_promo' => $cantidadPromo,
          ];

    //   dd($sale_details_id,$exist->relacion_precio_iva,$array);
       
       $exist->update($array);
      
	 }
	 
	}	
	public function ObtenerFormulasPromo2($ids_carro,$carro,$promo){
    
    $formula_cantidades = [];
    $totalPromo = $promo->precio_promo;
    //dd($totalPromo); // viene null
    foreach($ids_carro as $ic){
    
    $datos = explode("-",$ic);
    $product_id = $datos[0];
    $referencia_variacion = $datos[1];
    $sale_details_id = $datos[2];
    
    $productos_promocion = promos_productos::where("product_id",$product_id)->where("referencia_variacion",$referencia_variacion)->where("activo",1)->first();
    $item = SaleDetail::find($sale_details_id);
    $cantidad_carrito = $item->quantity;
    $cantidad_promocion = $productos_promocion->cantidad;
    $division = $cantidad_carrito/$cantidad_promocion;
    $division = floor($division);
    $precio = $item->precio_original;
    $subtotal_producto = $cantidad_promocion * $precio;
    array_push($formula_cantidades,['id'=> $ic,'formula_promo' => $division, 'subtotal_producto' => $subtotal_producto]);
    }
    
    $sumaSubtotal = array_sum(array_column($formula_cantidades, 'subtotal_producto'));
    $descuento = 1 - ($totalPromo/$sumaSubtotal);
    
   // dd($totalPromo,$sumaSubtotal);
    
    // tenemos el % de descuento
    foreach ($formula_cantidades as &$elemento) {
        $elemento["descuento"] = $elemento["subtotal_producto"] * $descuento;
    }
    
    //dd($formula_cantidades);
    
    return $formula_cantidades;
	}
	
    public function verificarExistenciaEnCarroPromo2($productos_promocion, $carro)
    {
        // Obtener los product_id y referencia_variacion de $productos_promocion
        $idsPromocion = $productos_promocion->pluck('product_id');
        // Verificar si todos los elementos de $productos_promocion están en $carro
        $todosPresentes = $idsPromocion->every(function ($product_id) use ($carro) {
            return $carro->contains(function ($item) use ($product_id) {
                return $item->product_id == $product_id; 
            });
        });
    
        // Si todos los elementos están presentes, devolver los ids de $carro
        if ($todosPresentes) {
            
        // Obtener los ids y referencias de $carro
        $carroInfo = $carro->filter(function ($item) use ($idsPromocion) {
            return $idsPromocion->contains($item->product_id);
        })->pluck('id')->toArray();

        $idsCarro = [];
        // Construir el array de la forma $product_id . "-" . $referencia_variacion
        foreach ($carroInfo as $CI) {
            $sd = SaleDetail::find($CI);
            $idsCarro[] = $sd->product_id . "-" . $sd->referencia_variacion."-".$sd->id;
        }
        
        return $idsCarro;
        }
    
        return null;
    }


    
    public function ModalAgregarPromo($id){
    		
    		$this->Id_cart = $id;
    		$item = SaleDetail::find($id);
  
            $this->RenderFactura($this->NroVenta);
            
     		if(1 < $item->id_promo && 0 < $item->cantidad_promo) {
    		    $this->emit("msg-error","No puede agregar descuentos a productos con promociones vigentes");
    		    $this->RenderFactura($this->NroVenta);
    		    return;
    		}
    		
    		$descuento_promo = $item->descuento_promo/($item->price)*100;
    		$this->descuento_promo_form = number_format($descuento_promo,0);
    		$this->cantidad_promo_form = $item->cantidad_promo ?? 1;
    		$this->cantidad_promo_max_form = $item->quantity;
    
    		$this->emit('show-modal-descuentos','details loaded');
    		
    		
    		
    }	


	
	public function guardarPromoIndividual($productId)
	{
	    $item = SaleDetail::find($productId);
        
        //dd($item);
        //dd($item->quantity,$this->cantidad_promo_form);
        
        if($item->quantity < $this->cantidad_promo_form){
            $this->emit("msg-error","La cantidad de unidades a las que se le aplica descuento no pueden ser mayor a la cantidad de unidades en la venta");
            $this->RenderFactura($this->NroVenta);
            return;
        }

        $alicuota_descuento = $this->descuento_promo_form/100;
        $descuento = $item->price * $alicuota_descuento;
        $cantidad_promo = $this->cantidad_promo_form;

        $item->update([
            'id_promo' => 1,
            'nombre_promo' => "Descuento manual ".$this->descuento_promo_form." %",
            'descuento_promo' => $descuento,
            'cantidad_promo' => $cantidad_promo,            
            ]);
            
        $alicuota_descuento_gral = $this->GetAlicuotaDescuentoGeneralProducto($item->sale_id);
        $descuento_nuevo = $this->SetDescuentoGeneralProducto($item->id,$item->quantity,$alicuota_descuento_gral);
    
        $item->update([
        'descuento' => $descuento_nuevo
        ]);

        
        $this->ActualizarTotalesVenta($item->sale_id);
        $this->ActualizarEstadoDeuda($item->sale_id);
        
        $this->RenderFactura($this->NroVenta);
        
        
        $this->emit('hide-modal-descuentos', '');
        $this->emit('pago-agregado', 'El descuento fue modificado.');


	}	
	
	public function QuitarPromo($promo_id,$sale_details_id){

        $promo = promos::find($promo_id);
        
        //dd($promo_id,$promo);
        
        // SI ES UNA PROMO COMBINADA 
        
        if($promo->tipo_promo == 2){
        
	    $sd = SaleDetail::where('sale_id',$this->NroVenta)->where('id_promo',$promo_id)->get();
	    
	    foreach($sd as $sale_detail){
        
        $detalle = SaleDetail::find($sale_detail->id);
	    $detalle->update([
	    "id_promo" => null,
        "nombre_promo" => null,
        "descuento_promo" => 0,
        "cantidad_promo" => 0
	    ]);
	    
        // Aca setea el descuento general    
        $alicuota_descuento_gral = $this->GetAlicuotaDescuentoGeneralProducto($detalle->sale_id);
        $descuento_nuevo = $this->SetDescuentoGeneralProducto($detalle->id,$detalle->quantity,$alicuota_descuento_gral);
        
        $detalle->update([
            'descuento' => $descuento_nuevo
            ]);
        
	    }  
	    
	    
        } else {
        
        // SI ES UNA PROMO DE UN SOLO PRODUCTO
        
        $sd = SaleDetail::find($sale_details_id);
        $sd->update([
	    "id_promo" => null,
        "nombre_promo" => null,
        "descuento_promo" => 0,
        "cantidad_promo" => 0
        ]);
        
        	    
        // Aca setea el descuento general    
        $alicuota_descuento_gral = $this->GetAlicuotaDescuentoGeneralProducto($sd->sale_id);
        $descuento_nuevo = $this->SetDescuentoGeneralProducto($sd->id,$sd->quantity,$alicuota_descuento_gral);
        
        $sd->update([
            'descuento' => $descuento_nuevo
            ]);
        
        }
        
	    
	    $this->ActualizarTotalesVenta($this->NroVenta);
        $this->ActualizarEstadoDeuda($this->NroVenta);
        
        $this->RenderFactura($this->NroVenta);
        
        $this->emit('pago-agregado', 'El descuento/promocion fue eliminado.');
	    
	}
	
	
	public function GetAlicuotaIVA($ventaId){
	    $t = Sale::find($ventaId);
	    $alicuota_iva = $t->total/($t->subtotal + $t->recargo - $t->descuento - $t->descuento_promo);
        $this->alicuota_iva = $alicuota_iva - 1;
        return $this->alicuota_iva;
	}
	
	
	  
   public function sumarSubtotalConIva($carro){  

        $sum_sobtotal_con_iva = $carro->sum(function($item){
            return ($item->price * $item->quantity * (1 + $item->iva)  );
        });
        
        return $sum_sobtotal_con_iva;
  }
  
   public function sumarDescuentoPromoConIva($carro){  

        $sum_descuento_promo = $carro->sum(function($item){
            return ($item->descuento_promo * $item->cantidad_promo * (1 + $item->iva)  );
        });
        
        return $sum_descuento_promo;
  }
  
    
// 2-5-2024
public function ElegirCUITFacturar($venta_id){
    
    $datos = datos_facturacion::where('comercio_id',$this->comercio_id)->where('eliminado',0)->get();
    $this->listado_cuits = $datos;
    $this->emit('listado-cuit-show','');
    
    if (!is_array($venta_id)) {
    $this->RenderFactura($venta_id);
    } else {
    $this->listado_ventas_id = $venta_id;    
    }
}

public function ElegirCuitYFacturar($ventaIdFactura,$datos_id){

if($ventaIdFactura != 0){
    $this->FacturarAfip($ventaIdFactura,$datos_id);    
} else {
    foreach($this->listado_ventas_id as $id){
     $vc = Sale::select('sales.id','sales.nro_factura')->where('sales.id',$id)->orderBy('sales.id','asc')->first();
     if($vc->nro_factura == null) { $this->FacturarAfip($vc->id,$datos_id);}   
    }
}
    
}	


    // MODIFICADO - Facturacion de AFIP de las ventas

	public function FacturarAfip($ventaIdFactura, $datos_id)
	{
	    //dd($ventaIdFactura,$datos_id);
	    
	    $this->EmitirFacturaTrait($ventaIdFactura,$datos_id);
	    
	    if (0 < $this->NroVenta) {
	    $this->RenderFactura($this->NroVenta);
	    }
	    
	    $this->emit('listado-cuit-hide','');
	}
	
    public function convertirFormatoMoneda($valor) {
        // Eliminar los puntos
        $valor = str_replace('.', '', $valor);
        // Reemplazar la coma con punto
        $valor = str_replace(',', '.', $valor);
        return $valor;
    }	
	
	// PAGOS
	    
    public function MontoPagoEditarPago($value)
    {
        
    //$value = $this->convertirFormatoMoneda($value);
    // modificar aca
    $metodo_pago = metodo_pago::find($this->metodo_pago_agregar_pago);
    
    $this->MontoPagoEditarPago = $value;

    $this->recargo = $metodo_pago->recargo/100;

    $this->recargo_total = $this->MontoPagoEditarPago * $this->recargo;

    $this->total_pago = $this->recargo_total + $this->MontoPagoEditarPago;
   
    $alicuota_iva = $this->GetAlicuotaIVA($this->id_pedido);   
    $this->monto_real = $this->setMontoReal($this->monto_ap,$alicuota_iva,$this->relacion_precio_iva);

    $this->RecalcularDeduccionesMonto($this->id_pago,$this->total_pago); // 30-6-2024
    }



    function AgregarPago($venta_id,$origen) {
    $this->ResetPago();
    
    $this->formato_ver = 1;
    $this->deducciones = [];
    
    $this->origen = $origen;
    $this->relacion_precio_iva  = $this->GetRelacionPrecioIVA($venta_id);
    
    $this->emit('agregar-pago','details loaded');

    $this->caja_pago = cajas::select('*')->where('id', $this->caja)->get();
    
    $this->id_pedido = $venta_id;
    $this->id_pago = $venta_id;
    
    if($origen == 2){
    $this->CerrarVerPagos();    
    }
        
//    $this->RenderFactura($venta_id);

    }

 
    function EditPago($id_pago,$origen) {
   
    $this->deducciones = [];
    $this->formato_ver = 1;
    
      $this->origen = $origen;
      if($origen == 2){
          $this->CerrarVerPagos();
      }
        
      $this->emit('agregar-pago','details loaded');

      $this->formato_modal = 1;

      $this->id_pago = $id_pago;

      $pagos = pagos_facturas::find($id_pago);
      $this->id_pedido = $pagos->id_factura;
      
      $this->caja = $pagos->caja;

      $this->nro_comprobante = $pagos->nro_comprobante;
      $this->comprobante = $pagos->url_comprobante;
      
      $this->metodo_pago_agregar_pago = $pagos->metodo_pago;

      $metodo_pago = metodo_pago::find($this->metodo_pago_agregar_pago);

      $this->recargo = $metodo_pago->recargo/100;

      $this->tipo_pago = $metodo_pago->cuenta;

      $this->monto_ap = $pagos->monto + $pagos->iva_pago;

      $this->fecha_ap = Carbon::parse($pagos->created_at)->format('d-m-Y');

      $this->recargo_total = $this->monto_ap * $this->recargo;

      $this->total_pago = $this->recargo_total + $this->monto_ap;
  
       // 11-1-2024
       
       if($this->datos_cliente != null){
       if($this->datos_cliente->sucursal_id != 0){
       $pago_sucursal = pagos_facturas::where('pago_sucursal_id',$pagos->id)->where('eliminado',0)->first();
       
       $sucursal_id_compra = sucursales::find($this->datos_cliente->sucursal_id)->sucursal_id;
      // aca pasamos la sucursal y el id de pago
       $this->SetSucursalPagos($sucursal_id_compra,$pago_sucursal->id);
       }
       }
       //
    $alicuota_iva = $this->GetAlicuotaIVA($pagos->id_factura);   
    $this->monto_real = $this->setMontoReal($this->monto_ap,$alicuota_iva,$this->relacion_precio_iva);
    
     $this->VerDeducciones($pagos); // 30-6-2024
     
   // $this->RenderFactura($pagos->id_factura);
    }


    public function MetodoPago($value)
    {

      $metodo_pago = metodo_pago::join('bancos','bancos.id','metodo_pagos.cuenta')->select('metodo_pagos.*','bancos.nombre as nombre_banco')->find($this->metodo_pago_agregar_pago);
    
      $this->recargo = $metodo_pago->recargo/100;
      
      $this->recargo_total = $this->monto_ap * $this->recargo;
     
      $this->total_pago = $this->recargo_total + $this->monto_ap;
      
      $alicuota_iva = $this->GetAlicuotaIVA($this->id_pedido);   
      $this->monto_real = $this->setMontoReal($this->monto_ap,$alicuota_iva,$this->relacion_precio_iva);
    
      $this->RecalcularDeduccionesMetodoPago($this->id_pago,$metodo_pago->id,$this->total_pago);
      }
      
      
      
   public function CreatePago2($ventaId)
   {
    
    if($this->metodo_pago_agregar_pago == "Elegir" && $this->tipo_pago != 1) {
        $this->emit("msg-error","Tenes que elegir un metodo de pago.");
        return;
    }
    
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
     
    
    $alicuota_iva = $this->GetAlicuotaIVA($this->id_pedido);    
    // seteamos los montos
    $monto_original = $this->monto_ap;
    $monto_real = $this->setMontoReal($this->monto_ap,$alicuota_iva,$this->relacion_precio_iva);

    $recargo = $this->setRecargoPagos($monto_real,$this->recargo);
    $iva_pago = $this->sumarIVAPago($monto_real,$alicuota_iva);
    
    $iva_recargo = $this->sumarIVARecargo($recargo,$alicuota_iva,$this->relacion_precio_iva);
    $monto = $this->setMontoPagos($monto_original,$monto_real,$this->recargo_total,$iva_pago,$iva_recargo,$this->relacion_precio_iva);

 // falta cuando tiene IVA calcular el IVA del recargo
  $array = [
       'monto' => $monto_real,
       'recargo' => $recargo,
       'iva_pago' => $iva_pago,
       'iva_recargo' => $iva_recargo,
       'caja' => $this->caja,
       'metodo_pago' => $this->metodo_pago_agregar_pago,
       'banco_id' => $this->tipo_pago,
       'nro_comprobante' => $this->nro_comprobante,
       'created_at' => $this->fecha_ap,
       'comercio_id' => $this->sucursal_id,
       'id_factura' => $ventaId,
       'cliente_id' => $this->cliente_query->cliente_id,
       'tipo_pago' => 1,
       'eliminado' => 0
     ];
  
  //dd($array);
  
  $pago_factura =   pagos_facturas::create($array);

    
    if($this->comprobante)
	{
		$customFileName = uniqid() . '_.' . $this->comprobante->extension();
		$this->comprobante->storeAs('public/comprobantes', $customFileName);
		$pago_factura->url_comprobante = $customFileName;
		$pago_factura->save();
	}
		
		
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

      $this->guardarDeducciones($pago_factura); // 30-6-2024
      
      $this->emit('pago-agregado', 'El pago fue guardado.');

      $this->emit('agregar-pago-hide', 'hide');

      if($this->origen == 2){
          $this->VerPagos($ventaId);
      }
      $this->ResetPago();

   //   $this->RenderFactura($ventaId);

   }

   public function DeletePago($datos)
   {
          $id = $datos[0];
          $origen = $datos[1];
          
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

       //   $this->RenderFactura($ventaId);
          
          if($origen == 2){
              $this->VerPagos($ventaId);
          }
          
          $this->estado = "display: block;";

   }

    public function ActualizarPago($id_pago) {
    
    if($this->metodo_pago_agregar_pago == "Elegir" && $this->tipo_pago != 1) {
        $this->emit("msg-error","Tenes que elegir un metodo de pago.");
        return;
    }
    
    if($this->metodo_pago_agregar_pago == 1 && $this->tipo_pago != 1) {
        $this->emit("msg-error","El metodo de pago no puede ser efectivo para un banco/plataforma");
        return;
    }
    
      $pagos = pagos_facturas::find($id_pago);

      $ventaId = $pagos->id_factura;


    $alicuota_iva = $this->GetAlicuotaIVA($this->id_pedido);    
    // seteamos los montos
    $monto_original = $this->monto_ap;
    $monto_real = $this->setMontoReal($this->monto_ap,$alicuota_iva,$this->relacion_precio_iva);

    
    //dd($monto_original,$monto_real,);
    $recargo = $this->setRecargoPagos($monto_real,$this->recargo);
    $iva_pago = $this->sumarIVAPago($monto_real,$alicuota_iva);
    
    $iva_recargo = $this->sumarIVARecargo($recargo,$alicuota_iva,$this->relacion_precio_iva);
    $monto = $this->setMontoPagos($monto_original,$monto_real,$this->recargo_total,$iva_pago,$iva_recargo,$this->relacion_precio_iva);
    
  $array = [
       'monto' => $monto_real,
       'iva_pago' => $iva_pago,
       'iva_recargo' => $iva_recargo,
       'recargo' => $recargo,
       'caja' => $this->caja,
       'nro_comprobante' => $this->nro_comprobante,
       'created_at' => $this->fecha_ap,
       'metodo_pago' => $this->metodo_pago_agregar_pago,
       'banco_id' => $this->tipo_pago,

       ];
  
 // dd($array);
  
     $pagos->update($array);

    
    if($this->comprobante != $pagos->url_comprobante)
	{
		$customFileName = uniqid() . '_.' . $this->comprobante->extension();
		$this->comprobante->storeAs('public/comprobantes', $customFileName);
		$pagos->url_comprobante = $customFileName;
		$pagos->save();
	}
	
	
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

  //    $this->RenderFactura($ventaId);

      if($this->origen == 2){
          $this->VerPagos($ventaId);
      }
    
      $this->guardarDeducciones($pagos); // 30-6-2024

      $this->ResetPago();

      $this->estado = "display: block;";
    }
    
    
public function VerPagos($venta_id){
    
    $this->id_pedido = $venta_id;
    $venta = Sale::find($venta_id);
    
   //////////////// PAGOS //////////////
    $this->pagos2 = pagos_facturas::join('bancos as mp','mp.id','pagos_facturas.banco_id')
    ->leftjoin('cajas','cajas.id','pagos_facturas.caja')
    ->select('mp.nombre as metodo_pago','cajas.nro_caja',
    'pagos_facturas.url_comprobante','pagos_facturas.nro_comprobante','pagos_facturas.id',
    'pagos_facturas.comercio_id','pagos_facturas.monto','pagos_facturas.recargo','pagos_facturas.iva_recargo','pagos_facturas.iva_pago','pagos_facturas.monto','pagos_facturas.actualizacion','pagos_facturas.actualizacion','pagos_facturas.created_at as fecha_pago')
    ->where('pagos_facturas.id_factura', $venta_id)
    ->where('pagos_facturas.eliminado',0)
    ->get();
    
    //dd($this->sucursales_agregan_pago,$venta->comercio_id,$this->comercio_id);

//$this->sucursales_agregan_pago_form = ($venta->comercio_id != $this->comercio_id) ? $this->sucursales_agregan_pago : 1;


// Si la venta es distinta al comercio
if ($venta->comercio_id != $this->comercio_id) {
    // Si las sucursales pueden cambiar el resto
    $this->sucursales_agregan_pago_form = $this->sucursales_agregan_pago;
} else {
    $this->sucursales_agregan_pago_form = 1;
}
    
    
    
 //   $this->suma_monto = $this->pagos2->sum('monto') + $this->pagos2->sum('recargo') + $this->pagos2->sum('monto') + $this->pagos2->sum('monto');

    $this->emit("show-ver-pagos","");
    
  //  $this->VerDeducciones($pagos); // 30-6-2024
     
    $this->render();
}    


 
// MANIPULACION DE SALDOS INICIALES 


    public function RenderSaldo($id){
    $pago = saldos_iniciales::join('bancos','bancos.id','saldos_iniciales.metodo_pago')->select('saldos_iniciales.*','bancos.nombre as nombre_banco')->find($id);    
    $this->monto_saldo = abs($pago->monto);
    $this->metodo_pago_saldo = $pago->metodo_pago;
    $this->fecha_saldo = Carbon::parse($pago->created_at)->format("Y-m-d");
    $this->emit("ver-pago-saldo-inicial","");
    $this->modo_ver_saldo = 1;
    }
    
    public function AgregarPagoSaldo(){
    $this->selected_pago_saldo_id = 0;
    $this->monto_saldo = 0;
    $this->metodo_pago_saldo = 1;
    $this->fecha_saldo = Carbon::now()->format("Y-m-d");
    $this->emit("ver-pago-saldo-inicial","");
    $this->modo_ver_saldo = 0;    
    }
    
    public function EditPagoSaldo($id_pago){
    $this->selected_pago_saldo_id = $id_pago;
    $this->RenderSaldo($id_pago);
    $this->modo_ver_saldo = 0;
    }
 
    
    public function CerrarAgregarPagoSaldo(){
    $this->emit("ver-pago-saldo-inicial-hide","");    
    }

    public function ActualizarPagoSaldo($id_pago){
    $pago = saldos_iniciales::find($id_pago);  
    
    if($pago->concepto == "Pago"){$monto = -1*$this->monto_saldo;} else {$monto = $this->monto_saldo;}
    
    $pago->update([
        'monto' => $monto,
        'metodo_pago' => $this->metodo_pago_saldo
        ]);
        
    $si = saldos_iniciales::where("referencia_id",$pago->referencia_id)->where("tipo","cliente")->where("eliminado",0)->get();
	
	$sum_si = $si->sum('monto');

	$this->emit("ver-pago-saldo-inicial-hide","");
	
    }
    
    public function DeletePagoSaldo($id_pago){
    //dd($id_pago);
    
    $pago = saldos_iniciales::find($id_pago);  
    
    $pago->update([
        'eliminado' => 1
        ]);
    
    //dd($pago);
    
    $si = saldos_iniciales::where("referencia_id",$pago->referencia_id)->where("tipo","cliente")->where("eliminado",0)->get();
	
	$sum_si = $si->sum('monto');
	
   }
 
    public function CreatePagoSaldo(){

    $monto = -1*$this->monto_saldo;
    
    $array = [
        'monto' => $monto,
        'metodo_pago' => $this->metodo_pago_saldo,
        'tipo' => 'cliente',
        'concepto'  => 'Pago',
        'referencia_id' => $this->datos_cliente->id,
        'comercio_id' => $this->comercio_id,
        'sucursal_id' => $this->comercio_id,
        ];
    
    $pago = saldos_iniciales::create($array);
        
    $si = saldos_iniciales::where("referencia_id",$pago->referencia_id)->where("tipo","cliente")->where("eliminado",0)->get();
	
	$sum_si = $si->sum('monto');

	$this->emit("ver-pago-saldo-inicial-hide","");
        
    }
    
    
    
    public function RenderPago($id){
    
    $this->formato_ver = 1;
    $pago = pagos_facturas::join('bancos','bancos.id','pagos_facturas.banco_id')->select('pagos_facturas.*','bancos.nombre as nombre_banco')->find($id);    
    
    $this->monto_ver = $pago->monto + $pago->recargo + $pago->iva_recargo + $pago->iva_pago;
    $this->nombre_banco_ver = $pago->nombre_banco;
    $this->nro_comprobante_ver = $pago->nro_comprobante;
    $this->comprobante_ver = $pago->url_comprobante;
    $caja_ver = cajas::find($pago->caja);
    if($caja_ver != null){
    $this->caja_ver = $caja_ver->nro_caja;
    } else {
    $this->caja_ver = 0;    
    }
    $this->emit("ver-pago","");
    }

    
public function CerrarVerPagos(){
 $this->emit("hide-ver-pagos","");
 }
 
     
    public function CerrarVerPago(){
       $this->emit('ver-pago-hide','');
    }
    
    
    public function ExportarExcel($cliente_id){
        
        return redirect('movimientos-cta-cte-clientes/excel/'. $cliente_id .'/' . $this->from .'/'. $this->to . '/' . Carbon::now() ); 
        
    }
    
    public function ExportarExcelPorProducto($cliente_id){
        
        return redirect('movimientos-cta-cte-clientes-producto/excel/'. $cliente_id .'/' . $this->from .'/'. $this->to . '/' . Carbon::now() ); 
        
    }
    
     public function ExportarPDF($cliente_id){
        
        return redirect('movimientos-cta-cte-clientes/pdf/'. $cliente_id .'/' . $this->from .'/'. $this->to . '/' . Carbon::now() ); 
        
    }



//9-8-2024

public function FormaCalcularTotales($value){
    $this->forma_calcular_totales = $value;
}

public function GetTotales($userCentralId,$from, $to){

    // Consultas ajustadas para incluir el nombre de la sucursal
    $pagos = pagos_facturas::join('bancos', 'bancos.id', 'pagos_facturas.banco_id')
        ->leftJoin('sales', 'sales.id', 'pagos_facturas.id_factura')
        ->leftJoin('sucursales', 'pagos_facturas.comercio_id', 'sucursales.sucursal_id')
        ->leftjoin('users','users.id','sucursales.sucursal_id')
        ->whereIn('pagos_facturas.comercio_id', $this->selectedSucursales)
        ->where('pagos_facturas.tipo_pago', 1)
        ->where('pagos_facturas.eliminado', 0)
        ->where('pagos_facturas.cliente_id', $this->cliente_id);
        
        if($this->forma_calcular_totales == "Deuda en Fecha filtrada"){
        $pagos = $pagos->whereBetween('pagos_facturas.created_at', [$from, $to]);    
        }
        
        $pagos = $pagos->select(pagos_facturas::raw("IFNULL(sucursales.sucursal_id, " . $userCentralId . ") as sucursal_id"),'pagos_facturas.comercio_id','bancos.nombre as nombre_banco', 'bancos.id as id_banco', 'pagos_facturas.url_comprobante as url_pago', Sale::raw('0 as id_saldo'), Sale::raw('0 as monto_saldo'), 'sales.nro_venta', 'pagos_facturas.id as id_pago', pagos_facturas::raw('0 as id_venta'), 'pagos_facturas.created_at', pagos_facturas::raw('0 as monto'), pagos_facturas::raw(' (pagos_facturas.monto + pagos_facturas.recargo + pagos_facturas.iva_pago + pagos_facturas.iva_recargo)  as monto_pago'), 'users.name as nombre_sucursal');

    $ventas = Sale::leftJoin('sucursales', 'sales.comercio_id', 'sucursales.sucursal_id')
        ->leftjoin('users','users.id','sucursales.sucursal_id')
        ->whereIn('sales.comercio_id', $this->selectedSucursales)
        ->where('sales.cliente_id', $this->cliente_id)
        ->where('sales.eliminado', 0)
        ->where('sales.status', '<>', 'Cancelado');
        
        if($this->forma_calcular_totales == "Deuda en Fecha filtrada"){
        $ventas = $ventas->whereBetween('sales.created_at', [$from, $to]);    
        }
        
        $ventas = $ventas->select(pagos_facturas::raw("IFNULL(sucursales.sucursal_id, " . $userCentralId . ") as sucursal_id"),'sales.comercio_id',Sale::raw('"-" as nombre_banco'), Sale::raw('0 as id_banco'), Sale::raw('0 as url_pago'), Sale::raw('0 as id_saldo'), Sale::raw('0 as monto_saldo'), 'sales.nro_venta', Sale::raw('0 as id_pago'), 'sales.id as id_venta', 'sales.created_at', 'sales.total as monto', compras_proveedores::raw('0 as monto_pago'), 'users.name as nombre_sucursal');

    $saldos_iniciales = saldos_iniciales::join('bancos', 'bancos.id', 'saldos_iniciales.metodo_pago')
        ->leftJoin('sucursales', 'saldos_iniciales.comercio_id', 'sucursales.sucursal_id')
        ->leftjoin('users','users.id','sucursales.sucursal_id');
        
        $saldos_iniciales = $saldos_iniciales->whereIn('saldos_iniciales.sucursal_id', $this->selectedSucursales)
        
        ->where('saldos_iniciales.referencia_id', $this->cliente_id)
        ->where('saldos_iniciales.tipo', 'cliente')
        ->where('saldos_iniciales.eliminado', 0);
        
        if($this->forma_calcular_totales == "Deuda en Fecha filtrada"){
        $saldos_iniciales = $saldos_iniciales->whereBetween('saldos_iniciales.created_at', [$from, $to]);    
        }
        
        $saldos_iniciales = $saldos_iniciales->select(pagos_facturas::raw("IFNULL(sucursales.sucursal_id, " . $userCentralId . ") as sucursal_id"),'saldos_iniciales.comercio_id','bancos.nombre as nombre_banco', 'bancos.id as id_banco', saldos_iniciales::raw('0 as url_pago'), 'saldos_iniciales.id as id_saldo', 'saldos_iniciales.monto as monto_saldo', saldos_iniciales::raw('0 as nro_venta'), saldos_iniciales::raw('0 as id_pago'), saldos_iniciales::raw('0 as id_venta'), 'saldos_iniciales.created_at', Sale::raw('0 as monto'), Sale::raw('0 as monto_pago'), 'users.name as nombre_sucursal');

    // Unión de las subconsultas
    $union = $pagos->union($ventas)->union($saldos_iniciales);
    
    $compras_clientes = $union->orderBy('created_at', 'desc')->get();
    
// Sumar los montos
$totalMonto = $compras_clientes->sum('monto');
$totalMontoPago = $compras_clientes->sum('monto_pago');
$totalMontoSaldo = $compras_clientes->sum('monto_saldo');

$this->venta_total = $totalMonto;
$this->pagos_total = $totalMontoPago;
$this->monto_saldo_inicial = $totalMontoSaldo;

} 

   public function getBancos($comercio_id){
    return bancos::join('bancos_muestra_sucursales','bancos_muestra_sucursales.banco_id','bancos.id')
    ->where('bancos_muestra_sucursales.muestra', 1)
    ->where('bancos_muestra_sucursales.sucursal_id', $comercio_id)
    ->select('bancos.*')
    ->orderBy('bancos.nombre','asc')
    ->get();
    
   
  }
    
 
}






