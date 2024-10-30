<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Product;
use App\Models\gastos;
use App\Models\pagos_facturas;
use App\Models\productos_stock_sucursales;
use App\Models\ClientesMostrador;
use App\Models\proveedores;
use App\Models\beneficios;
use App\Models\productos_variaciones_datos;
use App\Models\compras_proveedores;
use App\Models\Sale;
use App\Models\sucursales;
use Illuminate\Support\Facades\Auth;
use App\Models\SaleDetail;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    
  use WithPagination;
  
  private $pagination = 15;
  
  public $search_tabla_stock, $search_tabla_stock_minimo;
  
  public $componentName, $data, $orden_rentabilidad_producto, $nombre_sucursal,$sucursal_elegida, $details, $sumDetails, $countDetails,
  $reportType, $userId, $ventas_por_dia, $dateFrom, $dateTo, $filtro_ingresos_egresos, $saleId, $sucursal_name, $comercio_id, $selected_id, $detalle_ingresos, $Id, $productos, $categorias, $metodos_pago, $ventas_total, $ventas_cliente, $distances, $seriesData, $mensualizado, $data_total, $data_deuda, $data_mes, $total_mes, $mes,$sucursales_id, $gastos_total_mes, $gastos_mes, $total_gastos, $categorias_total, $categorias_nombre, $total_canal, $canal, $sucursal_id;

  protected $tabla_stock, $tabla_stock_minimo;
  public $costos_ventas_totales;
  public $total_ingresos;
  public $mensual,$rentabilidad_marginal_venta,$rentabilidad_porcentaje, $porcentaje_rentabilidad_producto_rentabilidad;
  public $ver;
  public $unidades_stock, $costo_unidades_stock, $valor_unidades_stock;

  public $total_ingresos_grafico, $total_gastos_grafico;
  
  public $sucursales_elegidas;
  
  public $selectedSucursales = [];
  public $selectedSucursalesStock = [];
  public $selectedSucursalesCheckbox = [];
   
  // Rentabilidad por producto
  public $switch_ventas_unidades,$switch_margen_rentabilidad;
  public $costo_producto_rentabilidad,$venta_producto_rentabilidad,$rentabilidad_producto_rentabilidad,$cantidad_producto_rentabilidad,$nombre_producto,$criterio_rentabilidad_producto;

  // Rentabilidad por categoria 
  public $criterio_rentabilidad_categoria,$orden_rentabilidad_categoria,$switch_ventas_unidades_categoria,$switch_margen_rentabilidad_categoria;
  public $categorias_mas_rentables,$cantidad_categoria_rentabilidad,$venta_categoria_rentabilidad,$rentabilidad_categoria_rentabilidad,$porcentaje_rentabilidad_categoria_rentabilidad,$nombre_categoria;

  // Descuentos
  public $data_meses,$data_total_ventas,$data_total_ventas_descuento,$data_descuento_promo,$data_descuento;
  
  // Ventas por vendedor
  public $ventas_vendedor,$data_ventas_vendedor,$promedio_ventas_vendedores,$data_vendedor;
  
  // Ventas por canal
  public $ventas_canal,$data_canal,$data_total_canal,$ventas_totales_canal;
  
  
  public function FiltroTipoGrafico($value){
      $this->ver = $value;
  }
  
	public function paginationView()
	{
		return 'vendor.livewire.bootstrap';
	}
	
  public function mount(Request $request)
  {
      // Inicializamos $selectedSucursales con el ID del usuario logueado
    
      $ver = $request->input('v');
      $sucursales = $request->input('suc');
      $dateFrom = $request->input('dateFrom');
      $dateTo = $request->input('dateTo');

      $sucursales_id = explode(",",$sucursales);
      
      $this->selectedSucursales = $sucursales ? $sucursales_id :  [auth()->user()->id];
      $this->sucursales_elegidas = $sucursales ?? auth()->user()->id;
      
      
      if($ver == 1){$ver = 'ventas';}
      if($ver == 2){$ver = 'ingresos-gastos';}
      if($ver == 3){$ver = 'stock';}
      
      if($sucursales != null){
      foreach($sucursales_id as $s){
      $this->selectedSucursalesCheckbox[$s] = true;
      }
      
      // Reemplazar 295 con 0 si está presente en el array
      foreach ($sucursales_id as $key => $value) {
       if ($value == auth()->user()->casa_central_user_id) {
            $sucursales_id[$key] = 0;
        } else {
            $sucursales_id[$key] = $value;
        }
      }
      
      $this->selectedSucursalesStock = $sucursales_id;
      
      } else {
      $this->selectedSucursalesCheckbox[auth()->user()->id] = true;
      if(auth()->user()->id == auth()->user()->casa_central_user_id){$this->selectedSucursalesStock["0"] = true;} else {$this->selectedSucursalesStock[auth()->user()->id] = true;}
      }

      
      $this->ver = $ver ?? "ventas";
      
      $this->estado_filtros = 0;
      $this->filtro_ventas = "Meses";
      $this->filtro_ingresos_egresos = "Meses";
      
      //grafico ingresos por metodo de pago
      $this->columnaOrdenMetodos = "total";
      $this->direccionOrdenMetodos = "desc";
      
      //grafico rentabilidad por producto
      $this->criterio_rentabilidad_producto = 3;
      $this->orden_rentabilidad_producto = "desc";
      $this->switch_ventas_unidades = 1;
      $this->switch_margen_rentabilidad = 1;
      //
      
      //grafico rentabilidad por categoria
      $this->criterio_rentabilidad_categoria = 3;
      $this->orden_rentabilidad_categoria = "desc";
      $this->switch_ventas_unidades_categoria = 1;
      $this->switch_margen_rentabilidad_categoria = 1;
      //
      
      //grafico ventas por producto
      $this->columnaOrdenProductos = "quantity";
      $this->direccionOrdenProductos = "desc";
      //
      
      $this->componentName ='Reportes de Ventas';
      $this->data =[];
      $this->tipo_grafico_venta = 1;
      $this->details =[];
      $this->sumDetails =0;
      $this->countDetails =0;
      $this->reportType =0;
      $this->userId =0;
      $this->proveedor_elegido =0;
      $this->saleId =0;
      $this->usuarioSeleccionado = 0;
      $this->ClienteSeleccionado = 0;
      $this->clienteId =0;
      $this->clientesSelectedName = [];
      
      // Obtener el primer día de este año
      $this->dateFrom = $dateFrom ?? Carbon::now()->firstOfYear();
      // hasta
      $this->dateTo = $dateTo ?? Carbon::now()->format('d-m-Y');
      
      $this->sucursal_elegida = Auth::user()->id;

  }
    protected $listeners = ['FechaElegida' => 'FechaElegida'];
    
    public function FechaElegida($startDate, $endDate)
    {
      // Manejar las fechas seleccionadas aquí
      $this->dateFrom  = $startDate;
      $this->dateTo = $endDate;;
    }

  public function render()
  {
    $this->SetFechas();

    $this->SetComercio();
    
    $this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')->select('users.name','sucursales.sucursal_id')->where('casa_central_id', $this->comercio_id)->where('eliminado',0)->get();
    $this->proveedores = proveedores::where('proveedores.comercio_id', 'like', $this->casa_central_id)->where('eliminado',0)->get();
    
    $this->SetSucursal();
    
    if($this->ver == "ventas"){
    $this->ContadorDeUsuarios();    
    }
    
    $this->CalculadorDeTotales();
    
    $this->RenderizarReportes();

//    $this->SalesByDate();

    // Aquí deberías pasar solo la colección de elementos a tu vista
   
    
    $usuario_id = Auth::user()->id;
    
    return view('livewire.dashboard.component', [
    'tabla_stock_minimo' => $this->tabla_stock_minimo,    
    'tabla_stock' => $this->tabla_stock,
    'sucursales' => $this->sucursales,
     'users' => User::where('comercio_id', 'like', $this->sucursal_id)
     ->orWhere('id', 'like', $this->usuario_id)
     ->orderBy('name','asc')->get(),

 ])->extends('layouts.theme-pos.app')
 ->section('content');
  }

    public function SetComercio() {
    $this->usuario_id = Auth::user()->id;
    $this->casa_central_id = Auth::user()->casa_central_user_id;
    
    if(Auth::user()->comercio_id != 1)
    $this->comercio_id = Auth::user()->comercio_id;
    else
    $this->comercio_id = Auth::user()->id;
    
    }
    
    
  public function SetSucursal() {
    
    if($this->sucursal_elegida == 0) {
    
    $array_sucursales = [];
    
    foreach($this->sucursales as $s) {
    array_push($array_sucursales,$s->sucursal_id);    
    }  
    
//  array_push($array_sucursales,$this->comercio_id);
    $this->sucursales_ids = $array_sucursales;
    $this->nombre_sucursal = "Todas las sucursales";
    } 

    if($this->sucursal_id != null) {
    $this->sucursal_id = $this->sucursal_id;
    } else {
    $this->sucursal_id = $this->comercio_id;
    }
    
    if($this->sucursal_elegida != 0) {
    $u = User::find($this->sucursal_id);
    $this->nombre_sucursal = $u->name;    
    }

    
}   

  public function SetFechas() {
  if($this->dateFrom !== '' || $this->dateTo !== '')
  {
    $this->from = Carbon::parse($this->dateFrom)->format('Y-m-d').' 00:00:00';
    $this->to = Carbon::parse($this->dateTo)->format('Y-m-d').' 23:59:59';

  }
  
}
  public function ContadorDeUsuarios(){
  
  // clientes
  $this->cantidad_clientes = ClientesMostrador::select(ClientesMostrador::raw('COUNT(clientes_mostradors.id) as cantidad_clientes'))
  ->whereIn('comercio_id',$this->selectedSucursales)
  ->where('sucursal_id',0)
  ->where('eliminado',0)
  ->first()->cantidad_clientes;
  
  // proveedores
  $this->cantidad_proveedores = proveedores::select(proveedores::raw('COUNT(proveedores.id) as cantidad_proveedores'))
  ->whereIn('comercio_id',$this->selectedSucursales)
  ->where('eliminado',0)
  ->first()->cantidad_proveedores;
  
  // cantidad de facturas de venta
  $this->cantidad_facturas_ventas = Sale::select(Sale::raw('COUNT(sales.id) as cantidad_facturas_ventas'))
  ->where('status','<>','Cancelado');
    
  $this->cantidad_facturas_ventas = $this->cantidad_facturas_ventas->whereIn('sales.comercio_id', $this->selectedSucursales);
            
  $this->cantidad_facturas_ventas = $this->cantidad_facturas_ventas->where('eliminado',0)
  ->whereBetween('sales.created_at', [$this->from, $this->to])
  ->first()->cantidad_facturas_ventas;
  
  // cantidad de facturas de compra
  $this->cantidad_facturas_compras = compras_proveedores::select(compras_proveedores::raw('COUNT(compras_proveedores.id) as cantidad_facturas_compras'));
  
  $this->cantidad_facturas_compras = $this->cantidad_facturas_compras->whereIn('comercio_id', $this->selectedSucursales);
    
  $this->cantidad_facturas_compras = $this->cantidad_facturas_compras->where('eliminado',0)
  ->whereBetween('compras_proveedores.created_at', [$this->from, $this->to])
  ->first()->cantidad_facturas_compras;
        
  }
  
  public function CalculadorDeTotalesVentas(){

  // ventas totales 
  $this->ventas_totales1 = Sale::select(Sale::raw('SUM(sales.total) as ventas_totales'));
  
  $this->ventas_totales1 = $this->ventas_totales1->whereIn('comercio_id', $this->selectedSucursales);

  $this->ventas_totales1 = $this->ventas_totales1
  ->where('status','<>','Cancelado')
  ->where('eliminado',0)
  ->whereBetween('sales.created_at', [$this->from, $this->to])
  ->first()->ventas_totales;
  
  // Costos totales 
  $costos_ventas_totales = SaleDetail::join('sales','sales.id','sale_details.sale_id')
  ->select(SaleDetail::raw('SUM(sale_details.cost*sale_details.quantity) as costo'));
  
  $costos_ventas_totales = $costos_ventas_totales->whereIn('sales.comercio_id', $this->selectedSucursales);

  $costos_ventas_totales = $costos_ventas_totales
  ->where('sales.status','<>','Cancelado')
  ->where('sales.eliminado',0)
  ->where('sale_details.eliminado',0)
  ->whereBetween('sales.created_at', [$this->from, $this->to])
  ->first()->costo;



   // Ventas vs Costo de las ventas
   $this->costos_ventas_totales = number_format($costos_ventas_totales, 2);
   $this->ventas_totales = number_format($this->ventas_totales1, 2);
   $rentabilidad_marginal_venta = $this->ventas_totales1-$costos_ventas_totales;
   $this->rentabilidad_marginal_venta = number_format($rentabilidad_marginal_venta, 2);
   if($this->ventas_totales1 != 0 && $this->ventas_totales1 != null && $costos_ventas_totales != 0 && $costos_ventas_totales != null) { $rentabilidad_porcentaje = ($rentabilidad_marginal_venta/$costos_ventas_totales); } else {$rentabilidad_porcentaje = 0;}
   $this->rentabilidad_porcentaje = number_format($rentabilidad_porcentaje, 2);
   
   
      
  }
  
  public function CalculadorDeTotalesIngresosEgresos(){
  
  $this->ingresos_totales1 = pagos_facturas::select(pagos_facturas::raw('(  SUM(pagos_facturas.monto) + SUM(pagos_facturas.recargo) + SUM(pagos_facturas.iva_recargo) + SUM(pagos_facturas.iva_pago) - SUM(pagos_facturas.deducciones) ) as ingresos_totales'));
  
 $this->ingresos_totales1 = $this->ingresos_totales1->whereIn('comercio_id', $this->selectedSucursales);

  $this->ingresos_totales1 = $this->ingresos_totales1
  ->where('eliminado',0)
  ->whereBetween('pagos_facturas.created_at', [$this->from, $this->to])
  ->first()->ingresos_totales;
  
  // egresos totales
  $this->egresos_totales1 = pagos_facturas::select(
             pagos_facturas::raw('(SUM(pagos_facturas.monto_gasto) + SUM(pagos_facturas.monto_compra)) AS total')

         );
      $this->egresos_totales1 = $this->egresos_totales1->whereIn('comercio_id', $this->selectedSucursales);

    $this->egresos_totales1 = $this->egresos_totales1->whereBetween('pagos_facturas.created_at', [$this->from, $this->to])
    ->first()->total;  
    
  // ganancias totales
    $this->ganancias_totales1 = $this->ingresos_totales1 - $this->egresos_totales1;

   
   // Ingresos vs Egresos
   $this->ingresos_totales  = number_format($this->ingresos_totales1 , 2);
   $this->egresos_totales = number_format($this->egresos_totales1, 2);
   $this->ganancias_totales = number_format($this->ganancias_totales1, 2);
      
  }
  
  public function CalculadorDeTotalesStock(){
  
  $this->unidades_stock = $this->CantidadUnidadesStock();
  $this->costo_unidades_stock = $this->CostoUnidadesStock();
  $this->valor_unidades_stock = $this->ValorUnidadesStock();

  }
  
  
  public function CalculadorDeTotales(){
   
   
  // ventas totales
  if($this->ver == "ventas"){
  $this->CalculadorDeTotalesVentas();
  }
  
  // ingresos totales
  if($this->ver == "ingresos-gastos"){
  $this->CalculadorDeTotalesIngresosEgresos();
  }
  
  // stock
  
  if($this->ver == "stock"){
  $this->CalculadorDeTotalesStock();
  }
        
  }
  
  public function RenderizarReportes(){

  
  if($this->ver == "ventas"){
   $this->VentasPorDia();
   $this->ProductosMasVendidos();
   $this->ReportePorFecha();
   $this->RentabilidadPorProducto();
   $this->RentabilidadPorCategoria();
   $this->DescuentosPorMes();
   $this->VentasPorVendedor();
   $this->VentasPorCanal();
  }
  
  if($this->ver == "ingresos-gastos"){
   $this->MetodosDePago();      
   $this->IngresosPorMes();
   $this->IngresosEgresos();
  }
   
   if($this->ver == "stock"){
       $this->tabla_stock_minimo = [];
     //$this->StockPorDebajoMinimo();
     $this->TablaStockContabilizado();
   }
  }
  
  
public function IngresosPorMes(){
  
   $this->total_ingresos_egresos = pagos_facturas::select(
        pagos_facturas::raw('(IFNULL(SUM(pagos_facturas.monto),0) - IFNULL(SUM(pagos_facturas.deducciones) , 0) + IFNULL(SUM(pagos_facturas.recargo) , 0) + IFNULL(SUM(pagos_facturas.iva_recargo) , 0) + IFNULL(SUM(pagos_facturas.iva_pago) , 0) ) as ingresos'),
        pagos_facturas::raw('(IFNULL(SUM(pagos_facturas.monto_gasto),0) + IFNULL(SUM(pagos_facturas.monto_compra) , 0) ) as egresos'),
        pagos_facturas::raw("DATE_FORMAT(pagos_facturas.created_at,'%m-%Y') as months")

         );
            $this->total_ingresos_egresos = $this->total_ingresos_egresos->whereIn('pagos_facturas.comercio_id', $this->selectedSucursales);

             $this->total_ingresos_egresos = $this->total_ingresos_egresos->whereBetween('pagos_facturas.created_at', [$this->from, $this->to])
             ->where('pagos_facturas.eliminado',0)
             ->groupBy('months')
             ->orderBy('pagos_facturas.created_at','asc')
             ->get();

    
}  

public function GetVentas(){

    
   if($this->filtro_ventas == "Meses"){
       $s = Sale::raw("DATE_FORMAT(sales.created_at,'%m-%Y') as months");       
    }
           
    if($this->filtro_ventas == "Dias"){
       $s = Sale::raw("DATE_FORMAT(sales.created_at,'%d-%m-%Y') as months");       
    }
    
    $ventas = Sale::select(
             Sale::raw('(SUM(sales.subtotal) - IFNULL(SUM(sales.descuento_promo),0) - SUM(sales.descuento) + SUM(sales.recargo) + SUM(sales.iva) ) / (SUM(sales.descuento) + SUM(sales.descuento_promo)) as porcentaje_total_ventas'),
             Sale::raw('(SUM(sales.subtotal) - IFNULL(SUM(sales.descuento_promo),0) - SUM(sales.descuento) + SUM(sales.recargo) + SUM(sales.iva) ) as total_ventas'),
             
             Sale::raw('SUM(sales.deuda) as total_deuda'),
             Sale::raw('ROUND(IFNULL(SUM(sales.descuento),0),2) as descuento'),
             Sale::raw('ROUND(IFNULL(SUM(sales.descuento_promo),0),2) as descuento_promo'),
             $s
             );
            $ventas = $ventas->whereIn('sales.comercio_id', $this->selectedSucursales);

           $ventas = $ventas->whereBetween('sales.created_at', [$this->from, $this->to]);
           $ventas = $ventas->where('sales.status', '<>' , 'Cancelado')
           ->where('sales.eliminado',0)
           ->groupBy('months')
           ->orderBy('sales.created_at','asc')
           ->get();
           
    return $ventas;       
}
public function ReportePorFecha(){

           
        $this->data = $this->GetVentas();

        $data = $this->data;
        $this->data_total_ventas = $data->sum('total_ventas');
        $this->data_total = $data->pluck('total_ventas');
        $this->data_recargo = $data->pluck('recargo');
        $this->data_deuda = $data->pluck('total_deuda');
        $this->data_mes = $data->pluck('months');
    
        $this->data_total_ventas = number_format($this->data_total_ventas,2);
        // Formatea los meses con Carbon para mostrar "ENE-2023" en lugar de "01-2023"
       
               
        if($this->filtro_ventas == "Dias"){
        $mesesFormateados = $this->data_mes->map(function ($mes) {
            return Carbon::createFromFormat('d-m-Y', $mes)->format('d-m-Y');
        });  
        }
               
        if($this->filtro_ventas == "Meses"){
        $mesesFormateados = $this->data_mes->map(function ($mes) {
            return Carbon::createFromFormat('m-Y', $mes)->format('M-Y');
        });       
        }

        $this->data_mes = $mesesFormateados;

        if($this->dateFrom !== '' || $this->dateTo !== '' )
        {

            $this->emit('mes', [
                
            'data_total_ventas' => $this->data_total_ventas,
            'data_total' => $this->data_total,
            'data_recargo' => $this->data_recargo,
            'data_deuda' => $this->data_deuda,
            'data_mes' => $this->data_mes,

       ]);


        } 
        
   
  }
  
public function VentasPorDia()
{
    // Obtener todas las ventas
    $sales = Sale::where('sales.eliminado',0);

    $sales = $sales->whereIn('sales.comercio_id', $this->selectedSucursales);
    
    $sales = $sales->whereBetween('sales.created_at', [$this->from, $this->to]);
    $sales = $sales->where('sales.status', '<>' , 'Cancelado')
    ->get();
           
    // Inicializar un array para almacenar las ventas por día
    $salesByDay = [
        'Monday' => 0,
        'Tuesday' => 0,
        'Wednesday' => 0,
        'Thursday' => 0,
        'Friday' => 0,
        'Saturday' => 0,
        'Sunday' => 0,
    ];

    // Iterar sobre todas las ventas y sumar las ventas por día
    foreach ($sales as $sale) {
        // Obtener el día de la semana de la venta (1 para lunes, 2 para martes, etc.)
        $dayOfWeek = date('N', strtotime($sale->created_at));

        // Obtener el nombre del día de la semana
        $dayName = date('l', strtotime($sale->created_at));
        
        // Sumar el monto de la venta al día correspondiente
        $salesByDay[$dayName] += $sale->total; // Ajusta esto según el campo que contenga el monto de la venta
    }

    // Devolver el array con las ventas por día
    $this->ventas_por_dia = $salesByDay;
}

public function CantidadUnidadesStock(){

        $cantidad = productos_stock_sucursales::join('products','products.id','productos_stock_sucursales.product_id')
                  ->select(Product::raw("SUM(productos_stock_sucursales.stock_real) as cantidad"))
                  ->whereIn('productos_stock_sucursales.sucursal_id', $this->selectedSucursalesStock)
                  ->where('products.comercio_id', $this->casa_central_id)
                  ->where('products.eliminado', 0)
                  ->where('products.eliminado', 0)
                  ->where('productos_stock_sucursales.eliminado', 0)
                  ->first()->cantidad;

        return  $cantidad ?? 0;
}

public function CostoUnidadesStock(){

        $costo_simple = Product::join('productos_stock_sucursales as PSS','PSS.product_id','products.id')
                  ->select(Product::raw("SUM(products.cost * PSS.stock_real) as costo"))
                  ->whereIn('PSS.sucursal_id', $this->selectedSucursalesStock)
                  ->where('products.comercio_id', $this->casa_central_id)
                  ->where('products.eliminado', 0)
                  ->where('PSS.eliminado', 0)
                  ->first()->costo;

        $costo_variable = productos_variaciones_datos::join('productos_stock_sucursales as PSS','PSS.referencia_variacion','productos_variaciones_datos.referencia_variacion')
                  ->leftjoin('products','products.id','productos_variaciones_datos.product_id')
                  ->select(Product::raw("SUM(productos_variaciones_datos.cost * PSS.stock_real) as costo"))
                  ->whereIn('PSS.sucursal_id', $this->selectedSucursalesStock)
                  ->where('products.comercio_id', $this->casa_central_id)
                  ->where('products.eliminado', 0)
                  ->where('PSS.eliminado', 0)
                  ->first()->costo;

        $costo_total = $costo_simple + $costo_variable;
    
        return  $costo_total ?? 0;
}

public function ValorUnidadesStock(){

        $valor_simple = Product::join('productos_stock_sucursales as PSS','PSS.product_id','products.id')
                  ->join('productos_lista_precios as PLP','PLP.product_id','products.id')
                  ->select(Product::raw("SUM(PLP.precio_lista * PSS.stock_real) as valor"))
                  ->whereIn('PSS.sucursal_id', $this->selectedSucursalesStock)
                  ->where('products.comercio_id', $this->casa_central_id)
                  ->where('PLP.lista_id',0)
                  ->where('products.eliminado', 0)
                  ->where('PSS.eliminado', 0)
                  ->first()->valor;

        $valor_variable = productos_variaciones_datos::join('productos_stock_sucursales as PSS','PSS.referencia_variacion','productos_variaciones_datos.referencia_variacion')
                  ->join('productos_lista_precios as PLP','PLP.referencia_variacion','productos_variaciones_datos.referencia_variacion')
                  ->leftjoin('products','products.id','productos_variaciones_datos.product_id')
                  ->select(Product::raw("SUM(PLP.precio_lista * PSS.stock_real) as valor"))
                  ->whereIn('PSS.sucursal_id', $this->selectedSucursalesStock)
                  ->where('products.comercio_id', $this->casa_central_id)
                  ->where('products.eliminado', 0)
                  ->where('PSS.eliminado', 0)
                  ->first()->valor;

        $valor_total = $valor_simple + $valor_variable;
    
        return  $valor_total ?? 0;
}


public function StockPorDebajoMinimo()
{

    $subquery = "IF (PSS.referencia_variacion > 0, 
        (SELECT SUM(stock_real) AS stock_real FROM productos_stock_sucursales 
         WHERE referencia_variacion = PV.referencia_variacion 
         AND sucursal_id IN (" . implode(',', $this->selectedSucursalesStock) . ") 
         AND eliminado = 0 LIMIT 1),
        (SELECT SUM(stock_real) AS stock_real FROM productos_stock_sucursales 
         WHERE product_id = P.id 
         AND sucursal_id IN (" . implode(',', $this->selectedSucursalesStock) . ") 
         AND productos_stock_sucursales.eliminado = 0 LIMIT 1)
    )";
    
    $query = $subquery.' AS STOCK';
    
    $stock = DB::raw($query);
    $where = DB::raw($subquery);
    
    $select = ['P.barcode','P.name','P.alerts',$stock];
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    $datos = DB::table('productos_lista_precios AS PLP')
              ->select($select)
              ->leftjoin('products as P', 'P.id', 'PLP.product_id')
              ->leftjoin('productos_stock_sucursales as PSS','PSS.product_id','P.id')
              ->leftjoin('productos_variaciones_datos AS PV', 'PV.referencia_variacion', 'PSS.referencia_variacion')
              ->where('P.comercio_id', $this->casa_central_id)
              ->whereIn('PSS.sucursal_id', $this->selectedSucursalesStock)
              ->where('P.eliminado', 0)
              ->where('PLP.eliminado', 0) 
              ->where('PSS.eliminado', 0) 
              ->where( function($query) {
			     $query->where('PV.eliminado',0)
				->orWhereNull('PV.eliminado');
				})
              ->whereRaw("($where) < P.alerts");
              
                      
                if ($this->search_tabla_stock_minimo) {
                    $datos->where(function ($query) {
                        $query->where('P.name', 'like', '%' . $this->search_tabla_stock_minimo . '%')
                              ->orWhere('P.barcode', 'like', '%' . $this->search_tabla_stock_minimo . '%');
                    });
                }
                
              $datos = $datos->distinct()
              ->paginate(20);
       
       $this->tabla_stock_minimo = $datos;  
    
    
    //dd($datos);
    
    //  $this->tabla_stock = $this->tabla_stock->items(); 
}


public function TablaStockContabilizado()
{
    ///////////////////////////   COSTOS    ///////////////////////////////////////////////
    $costo = DB::raw("
        IF (
            PLP.referencia_variacion > 0, 
            (SELECT cost FROM productos_variaciones_datos WHERE referencia_variacion = PV.referencia_variacion AND eliminado = 0 LIMIT 1),
            (SELECT cost FROM products WHERE id = P.id AND eliminado = 0 LIMIT 1)
        ) AS COSTO
    ");

    ///////////////////////////   PRECIOS    ///////////////////////////////////////////////
    $precio = DB::raw("
        IF (
            PLP.referencia_variacion > 0, 
            (SELECT precio_lista FROM productos_lista_precios WHERE referencia_variacion = PV.referencia_variacion AND lista_id = 0 AND eliminado = 0 LIMIT 1),
            (SELECT precio_lista FROM productos_lista_precios WHERE product_id = P.id AND lista_id = 0 AND eliminado = 0 LIMIT 1)
        ) AS PRECIO
    ");

    /////////////////////////// STOCKS //////////////////////////////////////////////
    $stock = DB::raw("
        IF (
            PSS.referencia_variacion > 0, 
            (SELECT SUM(stock_real) FROM productos_stock_sucursales 
             WHERE referencia_variacion = PV.referencia_variacion 
             AND sucursal_id IN (" . implode(',', $this->selectedSucursalesStock) . ") 
             AND eliminado = 0),
            (SELECT SUM(stock_real) FROM productos_stock_sucursales 
             WHERE product_id = P.id 
             AND sucursal_id IN (" . implode(',', $this->selectedSucursalesStock) . ") 
             AND eliminado = 0)
        ) AS STOCK
    ");

    $select = [
        'P.barcode',
        'P.unidad_medida',
        'P.name',
        $costo,
        $precio,
        $stock
    ];

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    $data = DB::table('productos_lista_precios AS PLP')
        ->select($select)
        ->leftJoin('products as P', 'P.id', '=', 'PLP.product_id')
        ->leftJoin('productos_stock_sucursales as PSS', 'PSS.product_id', '=', 'P.id')
        ->leftJoin('productos_variaciones_datos AS PV', 'PV.referencia_variacion', '=', 'PSS.referencia_variacion')
        ->where('P.comercio_id', $this->casa_central_id)
        ->whereIn('PSS.sucursal_id', $this->selectedSucursalesStock)
        ->where('P.eliminado', 0)
        ->where('PLP.eliminado', 0)
        ->where('PSS.eliminado', 0)
        ->where(function ($query) {
            $query->where('PV.eliminado', 0)
                ->orWhereNull('PV.eliminado');
        });

        if ($this->search_tabla_stock) {
            $data->where(function ($query) {
                $query->where('P.name', 'like', '%' . $this->search_tabla_stock . '%')
                      ->orWhere('P.barcode', 'like', '%' . $this->search_tabla_stock . '%');
            });
        }
        
    $this->tabla_stock = $data->distinct()->paginate(20);
}

public function TablaStockContabilizadoOLD(){

    ///////////////////////////   COSTOS    ///////////////////////////////////////////////
    $costo = DB::raw("IF (PLP.referencia_variacion > 0, (SELECT  cost FROM `productos_variaciones_datos` where referencia_variacion = PV.referencia_variacion AND eliminado = 0 LIMIT 1) ,
    (SELECT  cost FROM `products` where id = P.id AND eliminado = 0 LIMIT 1) ) AS COSTO");

    ///////////////////////////   PRECIOS    ///////////////////////////////////////////////
    $precio = DB::raw("IF (PLP.referencia_variacion > 0, (SELECT  precio_lista FROM `productos_lista_precios` where referencia_variacion = PV.referencia_variacion  and lista_id = 0 AND eliminado = 0 LIMIT 1) ,
    (SELECT  precio_lista FROM `productos_lista_precios` where product_id = P.id and lista_id = 0 AND eliminado = 0 LIMIT 1) ) AS PRECIO");

    /////////////////////////// STOCKS //////////////////////////////////////////////
    
    
    $stock = DB::raw("IF (PSS.referencia_variacion > 0, 
        (SELECT SUM(stock_real) AS stock_real FROM productos_stock_sucursales 
         WHERE referencia_variacion = PV.referencia_variacion 
         AND sucursal_id IN (" . implode(',', $this->selectedSucursalesStock) . ") 
         AND eliminado = 0 LIMIT 1),
        (SELECT SUM(stock_real) AS stock_real FROM productos_stock_sucursales 
         WHERE product_id = P.id 
         AND sucursal_id IN (" . implode(',', $this->selectedSucursalesStock) . ") 
         AND productos_stock_sucursales.eliminado = 0 LIMIT 1)
    ) AS STOCK");
  
    $select = ['P.barcode','P.unidad_medida','P.name',$costo,$precio,$stock];
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $data = DB::table('productos_lista_precios AS PLP')
              ->select($select)
              ->leftjoin('products as P', 'P.id', 'PLP.product_id')
              ->leftjoin('productos_stock_sucursales as PSS','PSS.product_id','P.id')
              ->leftjoin('productos_variaciones_datos AS PV', 'PV.referencia_variacion', 'PSS.referencia_variacion')
              ->join('categories as c','c.id','P.category_id')
        	  ->join('seccionalmacens as a','a.id','P.seccionalmacen_id')
        	  ->join('proveedores as pr','pr.id','P.proveedor_id')
        	  ->leftjoin('imagenes as i','i.url','P.image')
              ->where('P.comercio_id', $this->casa_central_id)
              ->whereIn('PSS.sucursal_id', $this->selectedSucursalesStock)
              ->where('P.eliminado', 0)
              ->where('PLP.eliminado', 0) 
              ->where('PSS.eliminado', 0) 
              ->where( function($query) {
			     $query->where('PV.eliminado',0)
				->orWhere('PV.eliminado', null);
				});
				
              $this->tabla_stock = $data
              ->distinct()
              ->get();
              
             //  $this->tabla_stock = $this->tabla_stock->items(); 
            
    }

public function MetodosDePago(){

    $this->metodos_pago = pagos_facturas::join('metodo_pagos as m','m.id','pagos_facturas.metodo_pago')
    ->join('bancos','bancos.id','m.cuenta')
    ->select('m.id as id_metodo_pago','m.nombre', 'bancos.nombre as banco','bancos.cbu',pagos_facturas::raw('(SUM(pagos_facturas.monto) - SUM(pagos_facturas.deducciones)  + SUM(pagos_facturas.recargo) + IFNULL(SUM(pagos_facturas.iva_recargo) , 0) + IFNULL(SUM(pagos_facturas.iva_pago) , 0)) as total'));
    
    $this->metodos_pago = $this->metodos_pago->whereIn('pagos_facturas.comercio_id', $this->selectedSucursales);

    $this->metodos_pago = $this->metodos_pago->where('pagos_facturas.eliminado',0)
    ->whereBetween('pagos_facturas.created_at', [$this->from, $this->to])
    ->groupby('m.id','m.nombre','bancos.nombre','bancos.cbu')
    ->orderBy($this->columnaOrdenMetodos, $this->direccionOrdenMetodos)
    ->get();
    
    //dd($this->metodos_pago);
}  

public function ProductosMasVendidos(){
   
    $this->productos = SaleDetail::join('products as p','p.id','sale_details.product_id')
    ->join('sales','sales.id','sale_details.sale_id')
    ->join('proveedores','proveedores.id','p.proveedor_id')
    ->select('proveedores.nombre as nombre_proveedor','p.id as id_producto','p.barcode','p.name as product',
    SaleDetail::raw('SUM( ((sale_details.price*sale_details.quantity) - IFNULL(sale_details.descuento,0) + IFNULL(sale_details.recargo,0) ) * (1 + IFNULL(sale_details.iva,0)) ) as total'),
    SaleDetail::raw('SUM(sale_details.quantity) as quantity'), SaleDetail::raw('SUM(sale_details.recargo) as recargo'), 
    SaleDetail::raw('SUM(((sale_details.price*sale_details.quantity) - IFNULL(sale_details.descuento,0) + IFNULL(sale_details.recargo,0) )*sale_details.iva) as iva'));
    
    //($d->price*$d->quantity) - $d->descuento + $d->recargo)  * (1+$d->iva)
    
    $this->productos = $this->productos->whereIn('sales.comercio_id', $this->selectedSucursales);

    if($this->proveedor_elegido != 0) {
    $this->productos = $this->productos->where('p.proveedor_id', $this->proveedor_elegido);       
    }
    
    $this->productos = $this->productos->where('sales.status','<>', 'Cancelado')
    ->whereBetween('sale_details.created_at', [$this->from, $this->to])
    ->where('sale_details.eliminado', 0)
    ->where('sales.eliminado',0)
    ->orderBy($this->columnaOrdenProductos, $this->direccionOrdenProductos)
    ->groupby('proveedores.nombre','p.id','p.barcode','p.name')
    ->get();
    
    
}



public function FiltroVentas($value){
    $this->estado_filtros = 1;
    $this->filtro_ventas = $value;
}

public function FiltroIngresosEgresos($value) {
    $this->estado_filtros = 1;
    $this->filtro_ingresos_egresos = $value;
   // dd($this->filtro_ingresos_egresos);
}

public function IngresosEgresos() {
 

        if($this->filtro_ingresos_egresos == "Dias") {
            $filtro = pagos_facturas::raw("DATE_FORMAT(created_at,'%d-%M-%Y') as months");
        }
        
        if($this->filtro_ingresos_egresos == "Meses") {
           $filtro =  pagos_facturas::raw("DATE_FORMAT(created_at,'%M-%Y') as months");
        }
        
          $this->ingresos_egresos = pagos_facturas::select(
           pagos_facturas::raw('(SUM(pagos_facturas.monto) - SUM(pagos_facturas.deducciones) + SUM(pagos_facturas.recargo) + IFNULL(SUM(pagos_facturas.iva_recargo) , 0) + IFNULL(SUM(pagos_facturas.iva_pago) , 0)) as total_ingresos'),
           pagos_facturas::raw('(SUM(pagos_facturas.monto_gasto) + SUM(pagos_facturas.monto_compra) ) as total_egresos'),
           $filtro
       );

        $this->ingresos_egresos = $this->ingresos_egresos->whereIn('pagos_facturas.comercio_id', $this->selectedSucursales);

        $this->ingresos_egresos = $this->ingresos_egresos->whereBetween('pagos_facturas.created_at', [$this->from, $this->to])
           ->where('pagos_facturas.eliminado',0)
           ->groupBy('months')
           ->orderBy('pagos_facturas.created_at','asc')
           ->get();
           
        $mensualizado = $this->ingresos_egresos;
        $this->total_ingresos_grafico = $mensualizado->pluck('total_ingresos');
        $this->total_gastos_grafico = $mensualizado->pluck('total_egresos');
        $this->mes = $mensualizado->pluck('months');


        if($this->dateFrom !== '' || $this->dateTo !== '')
        {
            $this->emit('mes-ingresos', [
            'total_ingresos_grafico' => $this->total_ingresos_grafico,  
            'total_gastos_grafico' => $this->total_gastos_grafico,
            'mes' => $this->mes

       ]);

        } 
   
}

      public function ElegirSwitchVentasUnidades($value){
          $this->switch_ventas_unidades = $value;
      }
      
    public function ElegirSwitchMargenRentabilidad($value){
      $this->switch_margen_rentabilidad = $value;
    }
    
    
  public function RentabilidadPorProducto(){
   
   // Si es 1 filtra por ventas, si es 2 por unidades vendidas
   //$this->switch_ventas_unidades = 2;
   //$this->switch_margen_rentabilidad = 1;
   //
   
   if($this->criterio_rentabilidad_producto == 1){
   $criterio_busqueda = 'ROUND(SUM((sale_details.price*sale_details.quantity) - IFNULL(sale_details.descuento_promo*sale_details.cantidad_promo,0)  - IFNULL(sale_details.descuento,0) + IFNULL(sale_details.recargo,0) - (sale_details.cost*sale_details.quantity)), 2)';
   }

   if($this->criterio_rentabilidad_producto == 2){
   $criterio_busqueda = 'ROUND(IFNULL((SUM((sale_details.price*sale_details.quantity) - IFNULL(sale_details.descuento_promo*sale_details.cantidad_promo,0)  - IFNULL(sale_details.descuento,0) + IFNULL(sale_details.recargo,0) - (sale_details.cost*sale_details.quantity))) / (SUM((sale_details.cost*sale_details.quantity))),0), 2) ';
   }
   
    if($this->criterio_rentabilidad_producto == 3){
    $criterio_busqueda = 'ROUND(SUM((sale_details.price*sale_details.quantity) - IFNULL(sale_details.descuento_promo*sale_details.cantidad_promo,0)  - IFNULL(sale_details.descuento,0) + IFNULL(sale_details.recargo,0)), 2) ';
    }
    
    if($this->criterio_rentabilidad_producto == 4){
    $criterio_busqueda = 'SUM(sale_details.quantity) ';
    }

    
   $productos_mas_rentables = SaleDetail::join('products as p','p.id','sale_details.product_id')
    ->join('sales','sales.id','sale_details.sale_id')
    ->select('p.id as id_producto','p.barcode','p.name',
    
    SaleDetail::raw('CONCAT(p.barcode," - ",p.name) as nombre_producto'),
    SaleDetail::raw('SUM(sale_details.quantity) as cantidad'),
    SaleDetail::raw('ROUND(SUM((sale_details.cost*sale_details.quantity)), 2) as costo'),
    SaleDetail::raw('ROUND(SUM((sale_details.price*sale_details.quantity) - IFNULL(sale_details.descuento_promo*sale_details.cantidad_promo,0)  - IFNULL(sale_details.descuento,0) + IFNULL(sale_details.recargo,0)), 2) as venta'),
    SaleDetail::raw('ROUND(SUM((sale_details.price*sale_details.quantity) - IFNULL(sale_details.descuento_promo*sale_details.cantidad_promo,0)  - IFNULL(sale_details.descuento,0) + IFNULL(sale_details.recargo,0) - (sale_details.cost*sale_details.quantity)), 2) as rentabilidad'),
    SaleDetail::raw('ROUND(IFNULL((SUM((sale_details.price*sale_details.quantity) - IFNULL(sale_details.descuento_promo*sale_details.cantidad_promo,0)  - IFNULL(sale_details.descuento,0) + IFNULL(sale_details.recargo,0) - (sale_details.cost*sale_details.quantity))) / (SUM((sale_details.cost*sale_details.quantity))),0), 2) * 100 as porcentaje_rentabilidad')
    
    )
    
    ->whereIn('sales.comercio_id', $this->selectedSucursales)
    ->where('sales.status','<>', 'Cancelado')
    ->where('sale_details.eliminado', 0)
    ->where('sales.eliminado',0)
    ->whereBetween('sale_details.created_at', [$this->from, $this->to])

    ->orderByRaw($criterio_busqueda . $this->orden_rentabilidad_producto)

    ->groupby('p.id','p.barcode','p.name')
    ->get();
    
    $this->productos_mas_rentables = $productos_mas_rentables;
    
    $productos_mas_rentables = $productos_mas_rentables->take(10);
    
    $this->cantidad_producto_rentabilidad = $productos_mas_rentables->pluck('cantidad');
    $this->venta_producto_rentabilidad = $productos_mas_rentables->pluck('venta');
    $this->rentabilidad_producto_rentabilidad = $productos_mas_rentables->pluck('rentabilidad');
    $this->porcentaje_rentabilidad_producto_rentabilidad = $productos_mas_rentables->pluck('porcentaje_rentabilidad');
    $this->nombre_producto = $productos_mas_rentables->pluck('nombre_producto');


    if($this->dateFrom !== '' || $this->dateTo !== '')
    {
        $this->emit('rentabilidad-producto', [
        'cantidad_producto_rentabilidad' => $this->cantidad_producto_rentabilidad,  
        'venta_producto_rentabilidad' => $this->venta_producto_rentabilidad,
        'rentabilidad_producto_rentabilidad' => $this->rentabilidad_producto_rentabilidad,
        'porcentaje_rentabilidad_producto_rentabilidad' => $this->porcentaje_rentabilidad_producto_rentabilidad,
        'nombre_producto' => $this->nombre_producto,
        'switch_ventas_unidades' => $this->switch_ventas_unidades,
        'switch_margen_rentabilidad' => $this->switch_margen_rentabilidad
       ]);

    } 
      
  }
  

      public function ElegirSwitchVentasUnidadesCategoria($value){
          $this->switch_ventas_unidades_categoria = $value;
      }
      
    public function ElegirSwitchMargenRentabilidadCategoria($value){
      $this->switch_margen_rentabilidad_categoria = $value;
    }
    
  public function RentabilidadPorCategoria(){
   
   
   if($this->criterio_rentabilidad_categoria == 1){
   $criterio_busqueda = 'ROUND(SUM((sale_details.price*sale_details.quantity) - IFNULL(sale_details.descuento_promo*sale_details.cantidad_promo,0)  - IFNULL(sale_details.descuento,0) + IFNULL(sale_details.recargo,0) - (sale_details.cost*sale_details.quantity)), 2)';
   }

   if($this->criterio_rentabilidad_categoria == 2){
   $criterio_busqueda = 'ROUND(IFNULL((SUM((sale_details.price*sale_details.quantity) - IFNULL(sale_details.descuento_promo*sale_details.cantidad_promo,0)  - IFNULL(sale_details.descuento,0) + IFNULL(sale_details.recargo,0) - (sale_details.cost*sale_details.quantity))) / (SUM((sale_details.cost*sale_details.quantity))),0), 2) ';
   }
   
    if($this->criterio_rentabilidad_categoria == 3){
    $criterio_busqueda = 'ROUND(SUM((sale_details.price*sale_details.quantity) - IFNULL(sale_details.descuento_promo*sale_details.cantidad_promo,0)  - IFNULL(sale_details.descuento,0) + IFNULL(sale_details.recargo,0)), 2) ';
    }
    
    if($this->criterio_rentabilidad_categoria == 4){
    $criterio_busqueda = 'SUM(sale_details.quantity) ';
    }

    
   $categorias_mas_rentables = SaleDetail::join('products as p','p.id','sale_details.product_id')
    ->join('sales','sales.id','sale_details.sale_id')
    ->join('categories','categories.id','p.category_id')
    ->select('categories.id as id_categoria','categories.name',
    SaleDetail::raw('SUM(sale_details.quantity) as cantidad'),
    SaleDetail::raw('ROUND(SUM((sale_details.cost*sale_details.quantity)), 2) as costo'),
    SaleDetail::raw('ROUND(SUM((sale_details.price*sale_details.quantity) - IFNULL(sale_details.descuento_promo*sale_details.cantidad_promo,0)  - IFNULL(sale_details.descuento,0) + IFNULL(sale_details.recargo,0)), 2) as venta'),
    SaleDetail::raw('ROUND(SUM((sale_details.price*sale_details.quantity) - IFNULL(sale_details.descuento_promo*sale_details.cantidad_promo,0)  - IFNULL(sale_details.descuento,0) + IFNULL(sale_details.recargo,0) - (sale_details.cost*sale_details.quantity)), 2) as rentabilidad'),
    SaleDetail::raw('ROUND(IFNULL((SUM((sale_details.price*sale_details.quantity) - IFNULL(sale_details.descuento_promo*sale_details.cantidad_promo,0)  - IFNULL(sale_details.descuento,0) + IFNULL(sale_details.recargo,0) - (sale_details.cost*sale_details.quantity))) / (SUM((sale_details.cost*sale_details.quantity))),0), 2) * 100 as porcentaje_rentabilidad')
    
    )
    
    ->whereIn('sales.comercio_id', $this->selectedSucursales)
    ->where('sales.status','<>', 'Cancelado')
    ->where('sale_details.eliminado', 0)
    ->where('sales.eliminado',0)
    ->whereBetween('sale_details.created_at', [$this->from, $this->to])
    ->orderByRaw($criterio_busqueda . $this->orden_rentabilidad_categoria)
    ->groupby('categories.id','categories.name')
    ->get();
    
    
    $this->categorias_mas_rentables = $categorias_mas_rentables;
    $categorias_mas_rentables = $categorias_mas_rentables->take(10);
    
    
    $this->cantidad_categoria_rentabilidad = $categorias_mas_rentables->pluck('cantidad');
    $this->venta_categoria_rentabilidad = $categorias_mas_rentables->pluck('venta');
    $this->rentabilidad_categoria_rentabilidad = $categorias_mas_rentables->pluck('rentabilidad');
    $this->porcentaje_rentabilidad_categoria_rentabilidad = $categorias_mas_rentables->pluck('porcentaje_rentabilidad');
    $this->nombre_categoria = $categorias_mas_rentables->pluck('name');


    if($this->dateFrom !== '' || $this->dateTo !== '')
    {
        $this->emit('rentabilidad-categoria', [
        'cantidad_categoria_rentabilidad' => $this->cantidad_categoria_rentabilidad,  
        'venta_categoria_rentabilidad' => $this->venta_categoria_rentabilidad,
        'rentabilidad_categoria_rentabilidad' => $this->rentabilidad_categoria_rentabilidad,
        'porcentaje_rentabilidad_categoria_rentabilidad' => $this->porcentaje_rentabilidad_categoria_rentabilidad,
        'nombre_categoria' => $this->nombre_categoria,
        'switch_ventas_unidades_categoria' => $this->switch_ventas_unidades_categoria,
        'switch_margen_rentabilidad_categoria' => $this->switch_margen_rentabilidad_categoria
       ]);

    } 
      
  }


public function VentasPorCanal(){
       
       $data = Sale::select(
           'sales.canal_venta',
           Sale::raw('SUM(sales.total) as total_canal')
           )
       ->where('sales.status','<>', 'Cancelado')
       ->where('sales.eliminado',0)
       ->whereIn('sales.comercio_id', $this->selectedSucursales)
       ->whereBetween('sales.created_at', [$this->from, $this->to])
       ->groupby('sales.canal_venta')
       ->get();
       
        $this->ventas_canal = $data;
        $this->data_canal = $data->pluck('canal_venta');
        $this->data_total_canal = $data->pluck('total_canal');
        $this->ventas_totales_canal = $data->sum('total_canal');
        

        if($this->dateFrom !== '' || $this->dateTo !== '' )
        {
            $this->emit('canal', [
            'data_canal' => $this->data_canal,
            'data_total_canal' => $this->data_total_canal
       ]);
     
        }
    
}
public function VentasPorVendedor(){

    $ventas = Sale::select(
             'users.id',
             'users.name',
             Sale::raw('(SUM(sales.subtotal) - IFNULL(SUM(sales.descuento_promo),0) - SUM(sales.descuento) + SUM(sales.recargo) + SUM(sales.iva) ) / (SUM(sales.descuento) + SUM(sales.descuento_promo)) as porcentaje_total_ventas'),
             Sale::raw('(SUM(sales.subtotal) - IFNULL(SUM(sales.descuento_promo),0) - SUM(sales.descuento) + SUM(sales.recargo) + SUM(sales.iva) ) as total_ventas')
             );
            $ventas = $ventas->whereIn('sales.comercio_id', $this->selectedSucursales);
            $ventas = $ventas->whereBetween('sales.created_at', [$this->from, $this->to]);
            $ventas = $ventas->where('sales.status', '<>' , 'Cancelado')
           ->join('users','users.id','sales.user_id')
           ->where('sales.eliminado',0)
           ->where('sales.status','<>','Cancelado')
           ->groupBy('users.id','users.name')
           ->orderBy('users.name','asc')
           ->get();

    $this->ventas_vendedor = $ventas;
    $this->data_ventas_vendedor = $ventas->pluck('total_ventas');
    
    $total_ventas = $ventas->sum('total_ventas');
    $cantidad_vendedores = $ventas->count('name');
    if(0 < $cantidad_vendedores){
    $promedio_ventas = $total_ventas/$cantidad_vendedores;    
    } else {
    $promedio_ventas = 0;    
    }
    $this->promedio_ventas_vendedores = $promedio_ventas;
    
    $this->data_vendedor = $ventas->pluck('name');
    
    
    //dd($this->ventas_vendedor);
    
    if($this->dateFrom !== '' || $this->dateTo !== '' )
        {
            $this->emit('ventas-vendedor', [
            'promedio_ventas_vendedores' => $this->promedio_ventas_vendedores,
            'data_ventas_vendedor' => $this->data_ventas_vendedor,
            'data_vendedor' => $this->data_vendedor
            ]);
        }                

    
}

public function VentasPorCliente(){
    
         $this->ventas_cliente = Sale::join('clientes_mostradors as c','c.id','sales.cliente_id')
         ->select('c.id as id_cliente','c.nombre', 
         Sale::raw('SUM(sales.subtotal - IFNULL(sales.descuento,0) - IFNULL(sales.descuento_promo,0)  + IFNULL(sales.recargo,0) + IFNULL(sales.iva,0) ) as total'),
         Sale::raw('SUM(IFNULL(sales.descuento,0)) as descuento'),
         Sale::raw('SUM(IFNULL(sales.descuento_promo,0)) as descuento_promo'),
         Sale::raw('SUM(sales.items) as quantity')
         )
         ->whereIn('sales.comercio_id', $this->selectedSucursales)
         ->where('sales.status','<>', 'Cancelado')
         ->whereBetween('sales.created_at', [$this->from, $this->to])
         ->groupby('c.id','c.nombre')
         ->orderby('total','desc')
         ->get();
         
} 


public function DescuentosPorMes(){

        $data_descuento = $this->GetVentas();
        
        $data = $data_descuento;
        $this->data_total_ventas_descuento = $data->pluck('total_ventas');
        $this->data_descuento_promo = $data->pluck('descuento_promo');
        $this->data_descuento = $data->pluck('descuento');
        $this->data_mes = $data->pluck('months');
        
        
        $this->data_total_ventas_descuento = $this->data_total_ventas_descuento;
        // Formatea los meses con Carbon para mostrar "ENE-2023" en lugar de "01-2023"
               
        if($this->filtro_ventas == "Dias"){
        $mesesFormateados = $this->data_mes->map(function ($mes) {
            return Carbon::createFromFormat('d-m-Y', $mes)->format('d-m-Y');
        });  
        }
               
        if($this->filtro_ventas == "Meses"){
        $mesesFormateados = $this->data_mes->map(function ($mes) {
            return Carbon::createFromFormat('m-Y', $mes)->format('M-Y');
        });       
        }

        $this->data_mes = $mesesFormateados;


        if($this->dateFrom !== '' || $this->dateTo !== '' )
        {
            $this->emit('descuento', [
            'data_total_ventas_descuento' => $this->data_total_ventas_descuento,
            'data_decuento' => $this->data_descuento,
            'data_descuento_promo' => $this->data_descuento_promo,
            'data_mes' => $this->data_mes

       ]);
       
        }


} 


  public function SalesByDate()
  {
         $this->ventas_total = Sale::select('sales.comercio_id', Sale::raw('SUM(IFNULL(sales.total,0)) as total'),Sale::raw('SUM(sales.deuda) as deuda'),Sale::raw('COUNT(sales.id) as cantidad'),Sale::raw('SUM(sales.items) as items'))
         ->where('sales.comercio_id', 'like', $this->sucursal_id)
         ->whereBetween('sales.created_at', [$this->from, $this->to])
         ->where('sales.status','<>', 'Cancelado')
         ->where('sales.eliminado',0)
         ->groupby('sales.comercio_id')
         ->first();
         
         // Ventas total 
         if($this->ventas_total != null) {
         $this->ventas_total_total =     $this->ventas_total->total;
         $this->ventas_total_cantidad =     $this->ventas_total->cantidad;
         $this->ventas_total_deuda =     $this->ventas_total->deuda;
         } else {
         $this->ventas_total_total  = null;  
         $this->ventas_total_cantidad  = null;  
         $this->ventas_total_deuda  = null;  
         }
         
         
         

       // dd($this->ventas_total);



         $this->total_ingresos = pagos_facturas::select(
             pagos_facturas::raw('(IFNULL(SUM(pagos_facturas.monto),0) + IFNULL(SUM(pagos_facturas.recargo) , 0) - IFNULL( SUM(pagos_facturas.monto_gasto) , 0) - IFNULL( SUM(pagos_facturas.monto_compra), 0) ) as total')

         )
             ->where('pagos_facturas.comercio_id','like',$this->sucursal_id)
             ->where('pagos_facturas.tipo_pago',1)
             ->whereBetween('pagos_facturas.created_at', [$this->from, $this->to])
             ->get();


             $this->detalle_ingresos = pagos_facturas::select(
             pagos_facturas::raw('SUM(pagos_facturas.monto) as total, SUM(pagos_facturas.recargo) as recargos,  SUM(pagos_facturas.monto_gasto) as gastos, SUM(pagos_facturas.monto_compra) as compras')

         )
             ->where('pagos_facturas.comercio_id','like',$this->sucursal_id)
             ->where('pagos_facturas.tipo_pago',1)
             ->whereBetween('pagos_facturas.created_at', [$this->from, $this->to])
             ->get();

            $this->total_cuentas_corrientes_venta = Sale::select(Sale::raw('SUM(sales.deuda) as deuda'))
            ->where('sales.comercio_id', 'like', $this->sucursal_id)
            ->where('sales.status','<>', 'Cancelado')
            ->first();
            
            $this->total_cuentas_corrientes_proveedores = compras_proveedores::select(compras_proveedores::raw('SUM(compras_proveedores.deuda) as deuda'))
            ->where('compras_proveedores.comercio_id', 'like', $this->sucursal_id)
            ->first();

           $this->total_gastos = pagos_facturas::select(
             pagos_facturas::raw('(SUM(pagos_facturas.monto_gasto) + SUM(pagos_facturas.monto_compra)) AS total')

         )
             ->where('pagos_facturas.comercio_id','like',$this->sucursal_id)
             ->whereBetween('pagos_facturas.created_at', [$this->from, $this->to])
             ->get();


           $this->ventas_canal = Sale::select('sales.canal_venta',Sale::raw('SUM(sales.subtotal - IFNULL(sales.descuento,0) + IFNULL(sales.recargo,0) + IFNULL(sales.iva,0) ) as total_canal'))
           ->where('sales.comercio_id', 'like', $this->sucursal_id)
           ->whereBetween('sales.created_at', [$this->from, $this->to])
           ->groupby('sales.canal_venta')
           ->get();

           $this->categorias = SaleDetail::join('products as p','p.id','sale_details.product_id')
           ->join('categories as c','c.id','p.category_id')
           ->select('c.name as categoria', SaleDetail::raw('FORMAT(SUM(sale_details.price*sale_details.quantity),2) as total'),SaleDetail::raw('SUM(sale_details.quantity) as cantidad'))
           ->where('sale_details.comercio_id','like',$this->sucursal_id)
           ->where('sale_details.eliminado', 0)
           ->whereBetween('sale_details.created_at', [$this->from, $this->to])
           ->groupby('c.name')
           ->orderBy('cantidad','desc')
           ->limit(10)
           ->get();



        $this->total_canal = $this->ventas_canal->pluck('total_canal');
        $this->canal = $this->ventas_canal->pluck('canal_venta');


        $categorias = $this->categorias;
        $this->categorias_nombre = $categorias->pluck('categoria');
        $this->categorias_total = $this->categorias->pluck('cantidad');


}

public function AplicarElegirSucursal(){
    // Obtener las claves donde el valor es true
    $clavesTrue = array_keys($this->selectedSucursalesCheckbox, true);
    
    foreach($this->selectedSucursalesCheckbox as $key => $value){
      $this->selectedSucursalesCheckbox[$key] = $value;  
    }
      
    // Reindexar el array
    $this->selectedSucursales = array_values($clavesTrue);
    $selectedSucursalesStock = array_values($clavesTrue);
    
    // Reemplazar 295 con 0 si está presente en el array
    foreach ($selectedSucursalesStock as $key => $value) {
        if ($value == $this->casa_central_id) {
            $selectedSucursalesStock[$key] = 0;
        }
    }
    
    $this->selectedSucursalesStock = $selectedSucursalesStock;
    $this->sucursales_elegidas = implode(",",$this->selectedSucursales);
}

public function ElegirSucursalOld($sucursal_id) {
  
  if($sucursal_id == 0){$this->sucursal_elegida = 0;} else {$this->sucursal_elegida = $sucursal_id;}
  $this->sucursal_name = User::find($sucursal_id);
  $this->sucursal_id = $sucursal_id;

}

  
  public function OrdenarColumnaMetodos($columna)
{
    
    
    if ($this->columnaOrdenMetodos == $columna) {
        // Cambiar la dirección de orden si la columna es la misma
        $this->columnaOrdenMetodos = $columna;
        $this->direccionOrdenMetodos = $this->direccionOrdenMetodos == 'asc' ? 'desc' : 'asc';
    } else {
        // Si es una columna diferente, cambiar a la nueva columna y establecer la dirección predeterminada
        $this->columnaOrdenMetodos = $columna;
        $this->direccionOrdenMetodos = 'asc';
        
       // dd($this->columnaOrden,$this->direccionOrden);
    }
}

  public function OrdenarColumnaProductos($columna)
{
    
    
    if ($this->columnaOrdenProductos == $columna) {
        // Cambiar la dirección de orden si la columna es la misma
        $this->columnaOrdenProductos = $columna;
        $this->direccionOrdenProductos = $this->direccionOrdenProductos == 'asc' ? 'desc' : 'asc';
    } else {
        // Si es una columna diferente, cambiar a la nueva columna y establecer la dirección predeterminada
        $this->columnaOrdenProductos = $columna;
        $this->direccionOrdenProductos = 'asc';
        
       // dd($this->columnaOrden,$this->direccionOrden);
    }
}

  
public function PromedioVentaProductos($products)
{

    $resultado = [];

    foreach ($products as $product) {
       
        $logs = SaleDetail::where('product_id', $product->id_producto)->where('referencia_variacion',$product->referencia_variacion)->orderBy('created_at')->get();

        $stock = productos_stock_sucursales::where('product_id',$product->id_producto)->where('referencia_variacion',$product->referencia_variacion);
    
        if($this->sucursal_elegida == 0) {
        $stock = $stock->whereIn('productos_stock_sucursales.sucursal_id', $this->sucursales_ids);
        } else {
        $stock = $stock->where('productos_stock_sucursales.sucursal_id', $this->sucursal_id);       
        }
        
        $stock = $stock->first();
       
        
        $timeDifferences = [];

        for ($i = 1; $i < count($logs); $i++) {
            $timeDifferences[] = $logs[$i]->created_at->diffInMinutes($logs[$i - 1]->created_at);
        }

        $averageMinutes = count($timeDifferences) > 0 ? array_sum($timeDifferences) / count($timeDifferences) : 0;

        // Convertir minutos a horas y minutos
        $horas_promedio_entre_compras = floor($averageMinutes / 60);
        $minutos_promedio_entre_compras = $averageMinutes % 60;

        $stock_disponible = $stock->stock_real;
        // Cálculo del tiempo total hasta agotar las 100 unidades
        $totalTimeInMinutes = $averageMinutes * $stock_disponible;

        // Convertir el tiempo total a días con decimales
        $dias_totales_antes_de_sin_stock = $totalTimeInMinutes / (60 * 24);

        $array = [
            'name' => $product->product,
            'barcode' => $product->barcode,
            'product_id' => $product->id_product,
            'referencia_variacion' => $product->referencia_variacion,
            'horas_promedio' => $horas_promedio_entre_compras,
            'minutos_promedio' => $minutos_promedio_entre_compras,
            'stock' => $stock_disponible,
            'dias_totales_antes_de_sin_stock' => $dias_totales_antes_de_sin_stock,
        ];

        array_push($resultado, $array);
    }


    $this->resultado = $resultado;
}


public function PromedioVentaProducto($productId){
    
  //  dd($productId);
  //  $productId = 1; // ID del producto que te interesa

    $logs = SaleDetail::where('product_id', $productId)->orderBy('created_at')->get();
    
    $timeDifferences = [];
    
    for ($i = 1; $i < count($logs); $i++) {
        $timeDifferences[] = $logs[$i]->created_at->diffInMinutes($logs[$i - 1]->created_at);
    }
    
    $averageMinutes = count($timeDifferences) > 0 ? array_sum($timeDifferences) / count($timeDifferences) : 0;
    
    // Convertir minutos a horas y minutos
    $horas_promedio_entre_compras = floor($averageMinutes / 60);
    $minutos_promedio_entre_compras = $averageMinutes % 60;
    
    $stock_disponible = 100;
    // Cálculo del tiempo total hasta agotar las 100 unidades
    $totalTimeInMinutes = $averageMinutes * $stock_disponible;
    
    // Convertir el tiempo total a horas y minutos
    $horas_totales_antes_de_sin_stock = floor($totalTimeInMinutes / 60);
    $minutos_totales_antes_de_sin_stock = $totalTimeInMinutes % 60;
    
    // $totalHours y $totalRemainingMinutes contienen el tiempo total hasta agotar las 100 unidades

   // dd($horas_totales_antes_de_sin_stock,$minutos_totales_antes_de_sin_stock);   
    }    

    
    
}
