<?php
namespace App\Traits;


use Darryldecode\Cart\Facades\CartFacade as Cart;
//use App\Services\CartProduccion;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Livewire\Session;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Component;
use App\Models\proveedores;
use App\Models\produccion_detalles_insumos;
use App\Models\productos_stock_sucursales;
use App\Models\receta;
use App\Models\insumo;
use App\Models\insumos_stock_sucursales;
use App\Models\User;
use App\Models\historico_stock_insumo;
use App\Models\produccion;
use App\Models\produccion_detalle;
use App\Models\Category;
use App\Models\metodo_pago;
use App\Models\cajas;
use App\Models\historico_stock;
use App\Models\pagos_facturas;
use App\Models\unidad_medida;
use App\Models\tipo_unidad_medida;
use App\Models\unidad_medida_relacion;
use App\Models\Product;
use App\Models\bancos;
use App\Models\compras_proveedores;
use App\Models\productos_variaciones_datos;
use App\Models\productos_variaciones;
use App\Models\variaciones;
use Notification;
use App\Notifications\NotificarCambios;
use App\Models\detalle_compra_proveedores;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


trait ProduccionTrait {


  /* REVISAR ESTO ACA TENEMOS QUE GUARDAR produccion_detalles_insumos con el listado del detalle de los insumos incurridos en la produccion 
  y tenemos que guardar historico_stock_insumo para cualquier cambio que tengan esos stock de insumos
  */ 
  
  
  public function GetRelacionUnidadesMedida($unidad_medida_receta,$unidad_medida_insumo){
    $relacion_receta = unidad_medida_relacion::where('unidad_medida',$unidad_medida_receta)->first();
    $relacion_insumo = unidad_medida_relacion::where('unidad_medida', $unidad_medida_insumo)->first();
    $relacion_medidas = $relacion_receta->relacion/$relacion_insumo->relacion;
    return  $relacion_medidas;
  }

public function GetConsumoInsumoEnProduccionDetalle($produccion_detalle_id, $receta) {
    $insumos = Product::find($receta->insumo_id);
    
    $produccion_detalle = produccion_detalle::find($produccion_detalle_id);

    // Verificar que $produccion_detalle sea un objeto y tenga la propiedad 'cantidad'
    if (!is_object($produccion_detalle) || !isset($produccion_detalle->cantidad)) {
        dd('produccion_detalle is not an object or does not have cantidad', $produccion_detalle, gettype($produccion_detalle));
    }

    // Obtener la relación entre unidades de medida
    $relacion_medidas = $this->GetRelacionUnidadesMedida($receta->unidad_medida, $insumos->unidad_medida);

    $produccion_detalle_cantidad = intval($produccion_detalle->cantidad);

    $cantidad_del_contenido = $insumos->cantidad;
    // Calculo de cantidad de insumo consumido -- con unidad de medida de la receta
    $cantidad_insumo_unidad = $receta->cantidad / $receta->rinde;
    $cantidad_consumida = $cantidad_insumo_unidad * $produccion_detalle_cantidad;
    $unidad_medida_consumida = $receta->unidad_medida;

    // Calculo de cantidad de insumo del paquete consumido -- con unidad de medida del stock   
    $cantidad_consumida_envase = ($cantidad_insumo_unidad * $produccion_detalle_cantidad * $relacion_medidas) / $cantidad_del_contenido;
    $unidad_medida_envase = $insumos->unidad_medida;

    $costo_envase = $insumos->cost;
    
    $costo_total = $costo_envase * $cantidad_consumida_envase;
    
    $costo_unitario_consumido = $costo_total / $cantidad_consumida;
    $costo_unitario_consumido_envase = $costo_envase / $insumos->cantidad;

    $array = [
        'produccion_detalles_id' => $produccion_detalle->id,
        'insumo_id' => $insumos->id,
        'insumo_codigo' => $insumos->barcode,
        'insumo_nombre' => $insumos->name,
        'tipo_unidad_medida' => $insumos->tipo_unidad_medida,
        // Receta
        'cantidad_consumida' => $cantidad_consumida,
        'unidad_medida_consumida' => $unidad_medida_consumida,
        // Insumo
        'cantidad_consumida_envase' => $cantidad_consumida_envase,
        'unidad_medida_envase' => $unidad_medida_envase,
        // Costos unitario de lo consumido
        'costo_unitario_consumido' => $costo_unitario_consumido,
        'costo_unitario_consumido_envase' => $costo_unitario_consumido_envase,
        // Costos unitario de lo consumido
        'costo_total' => $costo_total
    ];

    return $array;
}


  public function SetProduccionDetalleInsumosDB($product_id,$referencia_variacion,$cantidad,$comercio_id,$produccion_detalle) {

    $recetas = receta::where('product_id', $product_id)->where('referencia_variacion', $referencia_variacion)->where('eliminado',0)->get();
    
    foreach ($recetas as $receta) {

      $data = $this->GetConsumoInsumoEnProduccionDetalle($produccion_detalle,$receta);

      produccion_detalles_insumos::create($data);
      
    }
  
      
  }
  
  /*
  public function SetInsumosStock($product_id,$referencia_variacion,$cantidad,$comercio_id,$produccion_detalle_id){
            
            $produccion_detalle = produccion_detalle::find($produccion_detalle_id);      
            $recetas = receta::where('product_id', $product_id)->where('referencia_variacion', $referencia_variacion)->where('eliminado',0)->get();

            foreach ($recetas as $r) {
            
            $data = $this->GetConsumoInsumoEnProduccionDetalle($produccion_detalle->id,$r);
            $casa_central_id = Auth::user()->casa_central_user_id;
            
            $insumos_stock = $this->GetStockInsumoEnSucursalById($r->insumo_id,$comercio_id,$casa_central_id); 
            $stock_nuevo_insumos = $insumos_stock->stock - $data['cantidad_consumida_envase'];
            $insumos_stock->stock = $stock_nuevo_insumos;
            $insumos_stock->save();
              
              
            $historico_stock = historico_stock_insumo::create([
                'tipo_movimiento' => 11,
                'insumo_id' => $r->insumo_id,
                'produccion_detalle_id' => $produccion_detalle->id,
                'cantidad_receta' => $r->cantidad,
                'unidad_medida_receta' => $r->unidad_medida,
                'cantidad_movimiento' => -$cantidad_insumos,
                'cantidad_contenido' => $insumos->cantidad,
                'unidad_medida_insumo' => $insumos->unidad_medida,
                'relacion_unidad_medida' => $relacion,
                'stock' => $stock_nuevo_insumos,
                'comercio_id'  => $comercio_id,
                'usuario_id'  => Auth::user()->id
              ]);
            
            

            }
  }
  */
  public function SetProductStock($product_id,$referencia_variacion,$cantidad){

    //update stock
    $product_stock = productos_stock_sucursales::where('product_id', $product_id)
    ->where('productos_stock_sucursales.referencia_variacion', $referencia_variacion)
    ->first();
            
    $product_stock->stock = $product_stock->stock + $cantidad;
    $product_stock->save();
    
    //Log::info($product_stock);
    
    return $product_stock;
            
  }
  
  public function SetHistoricoStockProduct($product_id,$referencia_variacion,$cantidad,$comercio_id,$product_stock){
    
     $historico_stock = historico_stock::create([
      'tipo_movimiento' => 11,
      'producto_id' => $product_id,
      'referencia_variacion' => $referencia_variacion,
      'cantidad_movimiento' => $cantidad,
      'stock' => $product_stock->stock,
      'comercio_id'  => $comercio_id,
      'usuario_id'  => Auth::user()->id
    ]);
            
  }
  
  public function GetDetalleItems($origen,$item,$estado,$comercio_id,$sale,$produccion_id){
      if($origen == 1){
        
        $detalle = [
            'producto_id' => $item['product_id'],
            'costo' => $item['cost'],
            'nombre' => $item['name'],
            'barcode' => $item['barcode'],
            'referencia_variacion' => $item['referencia_variacion'],
            'cantidad' => $item['qty'],
            'estado' => $estado,
            'produccion_id' => $sale->id,
            'comercio_id' => $comercio_id,
            'tipo_producto' => 2
          ];          
      }

      if($origen == 2){
       
        $detalle = [
          'costo' => $item->attributes->cost,
          'nombre' => $item->name,
          'barcode' => $item->attributes->barcode,
          'referencia_variacion' => $item->attributes->referencia_variacion,
          'producto_id' => $item->attributes->product_id,
          'cantidad' => $item->quantity,
          'estado' => $estado,
          'produccion_id' => $produccion_id,
          'comercio_id' => $comercio_id,
          'tipo_producto' => 3
        ];          
      }   
      
      if($origen == 3){
       
        $detalle = [
          'costo' => $item->cost,
          'nombre' => $item->product_name,
          'barcode' => $item->product_barcode,
          'referencia_variacion' => $item->referencia_variacion,
          'producto_id' => $item->product_id,
          'cantidad' => $item->quantity,
          'estado' => $estado,
          'produccion_id' => $produccion_id,
          'comercio_id' => $comercio_id,
          'tipo_producto' => 3
        ];          
      }   

    return $detalle;
  }
  
    public function SetProduccionDetalleProductosDB($origen, $item, $sale, $comercio_id, $estado,$sale_details_id,$produccion_id) {
    //dd($origen, $item, $estado, $comercio_id, $sale);

      
    $detalle = $this->GetDetalleItems($origen, $item, $estado, $comercio_id, $sale,$produccion_id);
    //dd($detalle);
    $array_detalle = [
        'producto_id' => $detalle['producto_id'],
        'costo' => $detalle['costo'],
        'nombre' => $detalle['nombre'],
        'barcode' => $detalle['barcode'],
        'referencia_variacion' => $detalle['referencia_variacion'],
        'cantidad' => $detalle['cantidad'],
        'estado' => $detalle['estado'],
        'produccion_id' => $detalle['produccion_id'],
        'comercio_id' => $detalle['comercio_id'],
        'sale_details_id' => $sale_details_id,
        'tipo_producto' => $detalle['tipo_producto']
    ];       
          
    $produccion_detalle = produccion_detalle::create($array_detalle);
    return $produccion_detalle->id;  
    }
  
  public function SetProduccionDB($cart,$total,$cantidad_items,$observaciones,$comercio_id,$estado,$fecha_produccion){
      
      if($estado == "Pendiente"){$estado = 1;}
      if($estado == "En proceso"){$estado = 2;}
      if($estado == "Entregado"){$estado = 3;}
      
      $sale = produccion::create([
          'total' => $total,
          'items' => $cantidad_items,
          'observaciones' => $observaciones,
          'estado' => $estado,
          'inicio_produccion' => $fecha_produccion,
          'comercio_id' => $comercio_id,
          'user_id' => Auth::user()->id
        ]);

        return $sale;

  }
  
  public function GetDatosProductoYCantidades($origen,$item){
      if($origen == 1){
        $product_id = $item['product_id'];
        $referencia_variacion = $item['referencia_variacion'];
        $cantidad = $item['qty'];
      }
      if($origen == 2){
        $referencia_variacion = $item->attributes->referencia_variacion;
        $product_id = $item->attributes->product_id;
        $cantidad = $item->quantity;
      }
      if($origen == 3){
        $referencia_variacion = $item->referencia_variacion;
        $product_id = $item->product_id;
        $cantidad = $item->quantity;
      }
      $datos = ['product_id' => $product_id, 'referencia_variacion' => $referencia_variacion, 'cantidad' => $cantidad];
      return $datos;
  }
  
  // guardar venta
  public function SaveProduccionTrait($origen,$cart,$total,$cantidad_items,$observaciones,$estado,$fecha_produccion,$sale_details_id)
  {

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    if($fecha_produccion != '') {
    $fecha_produccion = $fecha_produccion;    
    } else {
    $fecha_produccion = Carbon::now();
    }
    
    DB::beginTransaction();

    if($origen == 2){
        $cart = Cart::getContent(); // Obtener todos los items del carrito
    }    
    
    try {

      $sale = $this->SetProduccionDB($cart,$total,$cantidad_items,$observaciones,$comercio_id,$estado,$fecha_produccion);
      
      if($sale)
      {
        
        $casa_central_id = Auth::user()->casa_central_user_id;
        
        foreach ($cart as $item) {
        $produccion_detalle_id = $this->SetProduccionDetalleProductosDB($origen, $item, $sale, $comercio_id, $estado,$sale_details_id,$sale->id);
        
        $datos_producto = $this->GetDatosProductoYCantidades($origen, $item);
        $product_id = $datos_producto['product_id'];
        $referencia_variacion = $datos_producto['referencia_variacion'];
        $cantidad = $datos_producto['cantidad'];
        
        // Producto terminado
        $this->SetProduccionDetalleInsumosDB($product_id, $referencia_variacion, $cantidad, $comercio_id, $produccion_detalle_id);
    
        if ($estado == 3) {
            // Aca
            
            $product_stock = $this->SetProductStock($product_id, $referencia_variacion, $cantidad);
            $historico_stock = $this->SetHistoricoStockProduct($product_id, $referencia_variacion, $cantidad, $comercio_id, $product_stock);
            $this->SetStockInsumosRecetaByProductId($product_id,$referencia_variacion,$comercio_id,$casa_central_id,$produccion_detalle_id,"restar");
            
            //$this->SetInsumosStock($product_id, $referencia_variacion, $cantidad, $comercio_id, $produccion_detalle_id);
        }
        
        // Producto en proceso
        if ($estado == 2) {
            $this->SetStockInsumosRecetaByProductId($product_id,$referencia_variacion,$comercio_id,$casa_central_id,$produccion_detalle_id,"restar");
        }
        
        // Producto pendiente
        if ($estado == 1) {
        //    $this->SetStockInsumosRecetaByProductId($product_id,$referencia_variacion,$comercio_id,$casa_central_id,$produccion_detalle_id,"restar");
        }
    }


      }


      DB::commit();

      return auth::user();



    } catch (Exception $e) {
      DB::rollback();
      $this->emit('sale-error', $e->getMessage());
    }

  } 
  
  public function SaveProduccionInmediataTraitDB($observaciones,$estado,$fecha_produccion)
  {

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    if($fecha_produccion != '') {
    $fecha_produccion = $fecha_produccion;    
    } else {
    $fecha_produccion = Carbon::now();
    }

    $cart = Cart::getContent(); // Obtener todos los items del carrito
    
    $total = 0;
    $cantidad_items = 0;
    
    foreach($cart as $item){
    $product = Product::find($item->attributes->product_id);
    if($product->tipo_producto == 3){
    $total += $item->attributes->cost * $item->quantity; // Suponiendo que getPriceSum() devuelve el precio total del item
    $cantidad_items += $item->quantity; // Suponiendo que 'quantity' es la cantidad de este item en el carrito
    }
    }
    
    if(0 < $cantidad_items){
    $produccion = $this->SetProduccionDB($cart,$total,$cantidad_items,$observaciones,$comercio_id,$estado,$fecha_produccion);
    return $produccion->id;        
    } else {
    return 0;
    }

  }
  
  
  // ORIGEN 2 SIGNIFICA QUE VA DESDE LA VENTA EN PRODUCCION INMEDIATA 
  
  public function SaveProduccionInmediataDetalleTraitDB($origen,$item,$estado,$sale_details_id,$produccion_id)
  {
      
    if($estado == "Pendiente"){$estado = 1;}
    if($estado == "En proceso"){$estado = 2;}
    if($estado == "Entregado"){$estado = 3;}
    if($estado == "Pendiente de Retiro en Sucursal"){$estado = 7;}
    
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;
    $casa_central_id = Auth::user()->casa_central_user_id;
    
    $produccion_detalle_id = $this->SetProduccionDetalleProductosDB($origen, $item, $produccion_id, $comercio_id, $estado,$sale_details_id,$produccion_id);
    
    $datos_producto = $this->GetDatosProductoYCantidades($origen, $item);
    $product_id = $datos_producto['product_id'];
    $referencia_variacion = $datos_producto['referencia_variacion'];
    $cantidad = $datos_producto['cantidad'];
        
    // Producto terminado
    $this->SetProduccionDetalleInsumosDB($product_id, $referencia_variacion, $cantidad, $comercio_id, $produccion_detalle_id);
    
    if ($estado == 3) {
        // Aca
        $product_stock = $this->SetProductStock($product_id, $referencia_variacion, $cantidad);
        $historico_stock = $this->SetHistoricoStockProduct($product_id, $referencia_variacion, $cantidad, $comercio_id, $product_stock);
        $this->SetStockInsumosRecetaByProductId($product_id,$referencia_variacion,$comercio_id,$casa_central_id,$produccion_detalle_id,"restar");
    }
    
    if ($estado == 2) {
        // Aca
        $this->SetStockInsumosRecetaByProductId($product_id,$referencia_variacion,$comercio_id,$casa_central_id,$produccion_detalle_id,"restar");
    }
        
  }
 
  
  // Obtener stocks
  
public function SetSucursalOCero($casa_central_id,$sucursal_id){
    
    if($casa_central_id == $sucursal_id){
        return 0;
    } else {
        return $sucursal_id;
    }
    
}

public function GetStockInsumoEnSucursal($barcode,$sucursal_stock,$casa_central_id){

    $sucursal_stock = $this->SetSucursalOCero($casa_central_id,$sucursal_stock);
    
    $stock =  productos_stock_sucursales::join('products','products.id','productos_stock_sucursales.product_id')
      ->select('productos_stock_sucursales.id','productos_stock_sucursales.stock_real as stock')
      ->where('products.barcode',$barcode)
      ->where('products.eliminado', 0)
      ->where('productos_stock_sucursales.sucursal_id', $sucursal_stock)
      ->where('productos_stock_sucursales.comercio_id', $casa_central_id)
      ->first();  
      
      return $stock;
  }
  
    public function GetStockInsumoEnSucursalById($id,$sucursal_id,$casa_central_id){

    $sucursal_stock = $this->SetSucursalOCero($casa_central_id,$sucursal_id);
    
    $stock = productos_stock_sucursales::join('products','products.id','productos_stock_sucursales.product_id')
      ->select('productos_stock_sucursales.id','productos_stock_sucursales.stock_real','productos_stock_sucursales.stock')
      ->where('products.id',$id)
      ->where('products.eliminado', 0)
      ->where('productos_stock_sucursales.sucursal_id', $sucursal_stock)
      ->where('productos_stock_sucursales.comercio_id', $casa_central_id)
      ->first();  
      
      return $stock;
  }
  
  public function SetMovimientosInsumosStockDB($tipo_movimiento,$insumo,$receta,$produccion_detalle,$cantidad_insumos,$sucursal_id,$comercio_id){
    
    $relacion = 0;
    
    $historico_stock = historico_stock_insumo::create([
        'tipo_movimiento' => $tipo_movimiento,
        'insumo_id' => $insumo->id,
        'produccion_detalle_id' => $produccion_detalle->id,
        'cantidad_receta' => $receta->cantidad,
        'unidad_medida_receta' => $receta->unidad_medida,
        'cantidad_movimiento' => -$cantidad_insumos,
        'cantidad_contenido' => $insumo->cantidad,
        'unidad_medida_insumo' => $insumo->unidad_medida,
        'relacion_unidad_medida' => $relacion,
        'stock' => $stock_nuevo_insumos,
        'comercio_id'  => $comercio_id,
        'usuario_id'  => Auth::user()->id
      ]);
  }
  
  
  public function SetStockInsumosRecetaByProductId($product_id,$referencia_variacion,$sucursal_id,$casa_central_id,$produccion_detalle_id,$accion){
            
            $receta = receta::where('product_id', $product_id )->where('referencia_variacion', $referencia_variacion )->where('eliminado',0)->get();

            $cambios = [];
            
            foreach ($receta as $r) {

            $data = $this->GetConsumoInsumoEnProduccionDetalle($produccion_detalle_id, $r); 
            
            $insumos_stock = $this->GetStockInsumoEnSucursalById($r->insumo_id,$sucursal_id,$casa_central_id); 
            
            if($accion == "sumar"){
            $stock_nuevo_insumos = $insumos_stock->stock + $data['cantidad_consumida_envase'];    
            $stock_real_nuevo_insumos = $insumos_stock->stock_real + $data['cantidad_consumida_envase'];    
            }
            if($accion == "restar"){
            $stock_nuevo_insumos = $insumos_stock->stock - $data['cantidad_consumida_envase'];    
            $stock_real_nuevo_insumos = $insumos_stock->stock_real - $data['cantidad_consumida_envase'];    
            }
            
           // dd($resultado);
            
            $insumos_stock->update([
                'stock' => $stock_nuevo_insumos,
                'stock_real' => $stock_real_nuevo_insumos
                ]);
            
            
           // Log::info($insumos_stock);
            
           // dd($insumos_stock);
            array_push($cambios,$insumos_stock->stock);
            }
            
    }  
  

    // ACTUALIZAR LOS ESTADOS DE LAS PRODUCCIONES ACA 
    
        // Logica ---> pendiente entra y no hace nada , en proceso empieza a descontar insumos y terminado suma el stock del producto nuevo.
    
    //    PENDIENTE
    
    public function UpdateProduccionPendiente($tipo_producto,$estado_id,$product_id,$referencia_variacion,$sucursal_id,$comercio_id,$produccion_detalle,$product_stock,$cantidad,$origen){
          
          $stock_producto = 0;
          $stock_insumos = 0;
    //    Pendiente - En proceso   
          if($estado_id == "2") {
          $stock_insumos =  $this->SetStockInsumosRecetaByProductId($product_id,$referencia_variacion,$sucursal_id,$comercio_id,$produccion_detalle->id,"restar");
          }
          
    //    Pendiente - Cancelado (con desperdicios) -- NADA
    //    Pendiente - Cancelado (sin desperdicios) -- NADA 

    //    Pendiente - Terminado
          if($estado_id == "3") {
            if($tipo_producto != 3){
             $stock_producto = $this->SetearStockProductoProduccion($product_stock,$cantidad,"sumar");       
            }
          $stock_insumos =  $this->SetStockInsumosRecetaByProductId($product_id,$referencia_variacion,$sucursal_id,$comercio_id,$produccion_detalle->id,"restar");
          }     
          
          return ['stock_producto' => $stock_producto , 'stock_insumos' => $stock_insumos];
    }
    //    EN PROCESO
    public function UpdateProduccionEnProceso($tipo_producto,$estado_id,$product_id,$referencia_variacion,$sucursal_id,$comercio_id,$produccion_detalle,$product_stock,$cantidad,$origen){
          
          $stock_producto = 0;
          $stock_insumos = 0;
    //    En proceso a pendiente    
          if($estado_id == 2) {
          $stock_insumos =  $this->SetStockInsumosRecetaByProductId($product_id,$referencia_variacion,$sucursal_id,$comercio_id,$produccion_detalle->id,"sumar");
          }
    //    En proceso - Cancelado (con desperdicios) -- NADA
    //    En proceso - Cancelado (sin desperdicios)
          if($estado_id == "5" || $estado_id == "4" ) {
          $stock_insumos =  $this->SetStockInsumosRecetaByProductId($product_id,$referencia_variacion,$sucursal_id,$comercio_id,$produccion_detalle->id,"sumar");
          }
    //    En proceso - Terminado
          if($estado_id == "3") {
             if($tipo_producto != 3){
          $stock_producto = $this->SetearStockProductoProduccion($product_stock,$cantidad,"sumar");
             }
          }     
          
          return ['stock_producto' => $stock_producto , 'stock_insumos' => $stock_insumos];
    }
    //    Terminado
    public function UpdateProduccionTerminada($tipo_producto,$estado_id,$product_id,$referencia_variacion,$sucursal_id,$comercio_id,$produccion_detalle,$product_stock,$cantidad,$origen){
 
          $stock_producto = 0;
          $stock_insumos = 0;
    //    Terminado - Pendiente
          if($estado_id == "1") {
              if($tipo_producto != 3){
          $stock_producto = $this->SetearStockProductoProduccion($product_stock,$cantidad,"restar");
              }
          $stock_insumos = $this->SetStockInsumosRecetaByProductId($product_id,$referencia_variacion,$sucursal_id,$comercio_id,$produccion_detalle->id,"sumar");
          }
    //    Terminado - En proceso
          if($estado_id == "2") {
              if($tipo_producto != 3){
          $stock_producto = $this->SetearStockProductoProduccion($product_stock,$cantidad,"restar");
              }
          }
    //    Terminado - Cancelado (sin desperdicios)
          if($estado_id == "5" || $estado_id == "4" ) {
              if($tipo_producto != 3){
          $stock_producto = $this->SetearStockProductoProduccion($product_stock,$cantidad,"restar");    
              }
          $stock_insumos = $this->SetStockInsumosRecetaByProductId($product_id,$referencia_variacion,$sucursal_id,$comercio_id,$produccion_detalle->id,"sumar");
          } 
          //    Terminado - Cancelado (con desperdicios) 
          if($estado_id == "6") {
              if($tipo_producto != 3){
          $stock_producto = $this->SetearStockProductoProduccion($product_stock,$cantidad,"restar");
              }
          }

          return ['stock_producto' => $stock_producto , 'stock_insumos' => $stock_insumos];
          
    }

    //    Cancelado (sin desperdicios)

    public function UpdateProduccionCanceladaSinDesperdicios($tipo_producto,$estado_id,$product_id,$referencia_variacion,$sucursal_id,$comercio_id,$produccion_detalle,$product_stock,$cantidad,$origen){
          
          $stock_producto = 0;
          $stock_insumos = 0;
    //    Cancelado (sin desperdicios) - Cancelado (con desperdicios)
          if($estado_id == "6") {
          $stock_insumos = $this->SetStockInsumosRecetaByProductId($product_id,$referencia_variacion,$sucursal_id,$comercio_id,$produccion_detalle->id,"restar");
          }  
    //    Cancelado (sin desperdicios) -  Pediente ---> NADA
    
    //    Cancelado (sin desperdicios) - En proceso
          if($estado_id == "2") {
          $stock_insumos = $this->SetStockInsumosRecetaByProductId($product_id,$referencia_variacion,$sucursal_id,$comercio_id,$produccion_detalle->id,"restar");
          }  
    //    Cancelado (sin desperdicios) - Terminado
          if($estado_id == "3") {
              if($tipo_producto != 3){
          $stock_producto = $this->SetearStockProductoProduccion($product_stock,$cantidad,"sumar");
              }
          $stock_insumos = $this->SetStockInsumosRecetaByProductId($product_id,$referencia_variacion,$sucursal_id,$comercio_id,$produccion_detalle->id,"restar");
          } 
          
          return ['stock_producto' => $stock_producto , 'stock_insumos' => $stock_insumos];
          
    }


    //    Cancelado (con desperdicios)
    
    public function UpdateProduccionCanceladaConDesperdicios($tipo_producto,$estado_id,$product_id,$referencia_variacion,$sucursal_id,$comercio_id,$produccion_detalle,$product_stock,$cantidad,$origen){
          
          $stock_producto = 0;
          $stock_insumos = 0;
    //    Cancelado (con desperdicios) - Cancelado (sin desperdicios)
          if($estado_id == "5" || $estado_id == "4" ) {
          $stock_insumos = $this->SetStockInsumosRecetaByProductId($product_id,$referencia_variacion,$sucursal_id,$comercio_id,$produccion_detalle->id,"sumar");
          }     
    //    Cancelado (con desperdicios) - Pendiente
          if($estado_id == "1") {
          $stock_insumos = $this->SetStockInsumosRecetaByProductId($product_id,$referencia_variacion,$sucursal_id,$comercio_id,$produccion_detalle->id,"sumar");
          }     
    //    Cancelado (con desperdicios) - En proceso --> NADA
    
    //    Cancelado (con desperdicios) - Terminado
          if($estado_id == "3") {
              if($tipo_producto != 3){
          $stock_producto = $this->SetearStockProductoProduccion($product_stock,$cantidad,"sumar");
              }
          }    
          
          return ['stock_producto' => $stock_producto , 'stock_insumos' => $stock_insumos];
          
        }
    
  
    
    public function UpdateEstadoProduccionTrait($estado_id,$origen,$produccion_detalle_id)
    {

      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;
      
      $sucursal_id = $comercio_id;
      $casa_central_id = Auth::user()->casa_central_user_id;
      
      $produccion_detalle = produccion_detalle::find($produccion_detalle_id);
      
      $product_stock = productos_stock_sucursales::where('product_id',$produccion_detalle->producto_id)->where('referencia_variacion',$produccion_detalle->referencia_variacion)->where('eliminado',0)->first();
      
    // ------------------- SI ESTABA PENDIENTE  -------------------------------//
 
      if($produccion_detalle->estado == "1" ){
      $cambios =  $this->UpdateProduccionPendiente($produccion_detalle->tipo_producto,$estado_id,$produccion_detalle->producto_id,$produccion_detalle->referencia_variacion,$sucursal_id,$casa_central_id,$produccion_detalle,$product_stock,$produccion_detalle->cantidad,$origen);
      }
    // ------------------- SI ESTABA EN PROCESO -------------------------------//
 
      if($produccion_detalle->estado == "2"){
      $cambios =  $this->UpdateProduccionEnProceso($produccion_detalle->tipo_producto,$estado_id,$produccion_detalle->producto_id,$produccion_detalle->referencia_variacion,$sucursal_id,$casa_central_id,$produccion_detalle,$product_stock,$produccion_detalle->cantidad,$origen);
      }

      
    // ------------------- SI ESTABA TERMINADA -------------------------------//

      if($produccion_detalle->estado == "3"){
      $cambios =  $this->UpdateProduccionTerminada($produccion_detalle->tipo_producto,$estado_id,$produccion_detalle->producto_id,$produccion_detalle->referencia_variacion,$sucursal_id,$casa_central_id,$produccion_detalle,$product_stock,$produccion_detalle->cantidad,$origen);       
      }

    // el estado 4 no existe porque es el cancelado de productos de compra-venta
    
    // ------------------- SI ESTABA TERMINADA -------------------------------//

      if($produccion_detalle->estado == "5" || $produccion_detalle->estado == "4"){
      $cambios =  $this->UpdateProduccionCanceladaSinDesperdicios($produccion_detalle->tipo_producto,$estado_id,$produccion_detalle->producto_id,$produccion_detalle->referencia_variacion,$sucursal_id,$casa_central_id,$produccion_detalle,$product_stock,$produccion_detalle->cantidad,$origen);       
      }

    // ------------------- SI ESTABA TERMINADA -------------------------------//

      if($produccion_detalle->estado == "6"){
      $cambios =  $this->UpdateProduccionCanceladaConDesperdicios($produccion_detalle->tipo_producto,$estado_id,$produccion_detalle->producto_id,$produccion_detalle->referencia_variacion,$sucursal_id,$casa_central_id,$produccion_detalle,$product_stock,$produccion_detalle->cantidad,$origen);       
      }

    // ------------------------------------------------------------------------//
      $this->cambios_stock_producto = $cambios['stock_producto'];
      $this->cambios_stock_insumos = $cambios['stock_insumos'];
      

      
      $produccion_detalle->update([
        'estado' => $estado_id
      ]);

      $produccion = produccion::find($produccion_detalle->produccion_id);

      $produccion->update([
        'estado' => $estado_id
      ]);

      $this->emit('hide-modal','details loaded');


    }
     
    // ACTUALIZAR CANTIDADES DE PRODUCCION DESDE ACA 

    public function updateQtyProduccion($produccion_detalle_id,$cant,$origen)
    { 
        
      if($cant == 0 && $origen != 2){
          $this->emit("msg-error","La cantidad no puede ser 0, en todo caso cambia el estado a cancelado");
          return;
      }
      
      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;
      
      $sucursal_id = $comercio_id;
      $casa_central_id = Auth::user()->casa_central_user_id;
      
      $produccion_detalle = produccion_detalle::find($produccion_detalle_id);

      // Obtenemos las cantidades
      $diferencia = $cant - $produccion_detalle->cantidad;
      $product_id = $produccion_detalle->producto_id;
      $referencia_variacion = $produccion_detalle->referencia_variacion;
      
      $product_stock = productos_stock_sucursales::where('product_id',$produccion_detalle->producto_id)->where('referencia_variacion',$produccion_detalle->referencia_variacion)->where('eliminado',0)->first();
      
    
    
      // Si origen es 1 viene de produccion, si es 2 viene del reporte por productos
      
    // ------------------- SI ESTABA PENDIENTE  -------------------------------// NO REALIZA ACCION
    
    // ------------------- SI ESTABA EN PROCESO -------------------------------//
 
      if($produccion_detalle->estado == "2"){
          $stock_insumos = $this->SetStockInsumosRecetaCambioQtyByProductId($product_id,$referencia_variacion,$sucursal_id,$casa_central_id,$produccion_detalle->id,"sumar",$diferencia);
      }

      
    // ------------------- SI ESTABA TERMINADA -------------------------------//

      if($produccion_detalle->estado == "3"){
          if($origen == 1){
          $stock_producto = $this->SetearStockProductoProduccion($product_stock,$diferencia,"sumar"); // aca va sumar porque en la diferencia ya va positivo o negativo
          }
          $stock_insumos = $this->SetStockInsumosRecetaCambioQtyByProductId($product_id,$referencia_variacion,$sucursal_id,$casa_central_id,$produccion_detalle->id,"sumar",$diferencia);
      }

    // el estado 4 no existe porque es el cancelado de productos de compra-venta
    
    // ------------------- SI ESTABA CANCELADA SIN DESPERDICIOS -------------------------------//

      if($produccion_detalle->estado == "5"){
          if($origen == 1){
          $stock_producto = $this->SetearStockProductoProduccion($product_stock,$diferencia,"sumar"); // aca va sumar porque en la diferencia ya va positivo o negativo
          }
          $stock_insumos = $this->SetStockInsumosRecetaCambioQtyByProductId($product_id,$referencia_variacion,$sucursal_id,$casa_central_id,$produccion_detalle->id,"sumar",$diferencia);
      }

    // ------------------- SI ESTABA CANCELADA CON DESPERDICION -------------------------------//

      if($produccion_detalle->estado == "6"){
          if($origen == 1){
          $stock_producto = $this->SetearStockProductoProduccion($product_stock,$diferencia,"sumar"); // aca va sumar porque en la diferencia ya va positivo o negativo
          }
          $stock_insumos = $this->SetStockInsumosRecetaCambioQtyByProductId($product_id,$referencia_variacion,$sucursal_id,$casa_central_id,$produccion_detalle->id,"sumar",$diferencia);
      }

    // ------------------------------------------------------------------------//


      
      $produccion_detalle->update([
        'cantidad' => $cant
      ]);
        /*
      $produccion = produccion::find($produccion_detalle->produccion_id);

      $produccion->update([
        'estado' => $estado_id
      ]);
        */
        
      $this->emit('hide-modal','details loaded');


    }
  
  public function GetConsumoInsumoEnProduccionDetalleQty($product_id,$referencia_variacion,$sucursal_id,$casa_central_id,$produccion_detalle_id,$accion,$diferencia){
      
  }
  
  public function SetStockInsumosRecetaCambioQtyByProductId($product_id,$referencia_variacion,$sucursal_id,$casa_central_id,$produccion_detalle_id,$accion,$diferencia){
           
            $cambios = [];
            $produccion_detalles = produccion_detalle::find($produccion_detalle_id);
            $produccion_detalles_insumos = produccion_detalles_insumos::where('produccion_detalles_id',$produccion_detalle_id)->get();
            
            $cantidad_producto_final = $produccion_detalles->cantidad;
            
            foreach($produccion_detalles_insumos as $pdi){

            // DIFERENCIAS 
            
            // cantidades 
            $cantidad_consumida_diferencia = $pdi->cantidad_consumida/$cantidad_producto_final * $diferencia;
            $cantidad_consumida_envase_diferencia = $pdi->cantidad_consumida_envase/$cantidad_producto_final * $diferencia;
            
            // Costos
            $costo_total_diferencia = $pdi->costo_total/$cantidad_producto_final * $diferencia;
            $costo_unitario_consumido_diferencia = $pdi->costo_unitario_consumido/$cantidad_producto_final * $diferencia;
            $costo_unitario_consumido_envase_diferencia = $pdi->costo_unitario_consumido_envase/$cantidad_producto_final * $diferencia;
            
            // VALORES NUEVOS 
              
            // cantidades 
            $cantidad_consumida_nueva = $pdi->cantidad_consumida + $cantidad_consumida_diferencia;
            $cantidad_consumida_envase_nueva = $pdi->cantidad_consumida_envase + $cantidad_consumida_envase_diferencia;
            
            // Costos
            $costo_total_nueva = $pdi->costo_total + $costo_total_diferencia;
            $costo_unitario_consumido_nueva = $pdi->costo_unitario_consumido + $costo_unitario_consumido_diferencia;
            $costo_unitario_consumido_envase_nueva = $pdi->costo_unitario_consumido_envase + $costo_unitario_consumido_envase_diferencia;
  
            //
            // dd($pdi->insumo_id,$sucursal_id,$casa_central_id);
            $insumos_stock = $this->GetStockInsumoEnSucursalById($pdi->insumo_id,$sucursal_id,$casa_central_id); 

            $resultado = $insumos_stock->stock - $cantidad_consumida_envase_diferencia;    
            $insumos_stock->stock = $resultado;
            $insumos_stock->save();
            
            $produccion_detalles_insumos_nuevo = produccion_detalles_insumos::find($pdi->id);
            $produccion_detalles_insumos_nuevo->update([
            'cantidad_consumida' => $cantidad_consumida_nueva,
            'cantidad_consumida_envase' => $cantidad_consumida_envase_nueva,
            'costo_unitario_consumido' => $costo_unitario_consumido_nueva,
            'costo_unitario_consumido_envase' => $costo_unitario_consumido_envase_nueva,
            'costo_total' => $costo_total_nueva,
            ]);
            
            //dd($produccion_detalles_insumos_nuevo);
            }
            
            array_push($cambios,$insumos_stock->stock);
            }


    public function SetearStockProductoProduccion($product_stock,$cantidad,$accion){
        
        $stock =  $product_stock->stock;
        $stock_real = $product_stock->stock_real;
        
        if($accion == "sumar"){
        $stock_nuevo = $stock + $cantidad;
        $stock_real_nuevo = $stock_real + $cantidad;            
        }
        
                
        if($accion == "restar"){
        $stock_nuevo = $stock - $cantidad;
        $stock_real_nuevo = $stock_real - $cantidad;            
        }
        
        
        $product_stock->update([
            'stock' => $stock_nuevo,
            'stock_real' => $stock_real_nuevo
            ]);
        
         $historico_stock = historico_stock::create([
          'tipo_movimiento' => 11,
          'producto_id' => $product_stock->product_id,
          'referencia_variacion' => $product_stock->referencia_variacion,
          'cantidad_movimiento' => $cantidad,
          'stock' => $product_stock->stock_real,
          'comercio_id'  => $product_stock->comercio_id,
          'usuario_id'  => Auth::user()->id
        ]);
                
        return   $product_stock->stock_real;      
      }   
}