<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Trait
use App\Traits\FacturacionNuevoAfip;
use App\Traits\ProductsTrait;
use App\Traits\CartTrait;
use App\Traits\WocommerceTrait;
use App\Traits\ClientesTrait;
use App\Traits\ProduccionTrait;
//
use App\Models\bancos;
use App\Models\metodo_pago;
use App\Models\datos_facturacion;
use App\Models\ClientesMostrador;
use App\Models\Sale;
use App\Models\Product;
use App\Models\User;
use App\Models\lista_precios;
use App\Models\productos_stock_sucursales;
use App\Models\productos_lista_precios;





class APIController extends Controller
{
    use FacturacionNuevoAfip;
    use ProductsTrait;
    use CartTrait;
    use WocommerceTrait;
    use ClientesTrait;
    use ProduccionTrait;
    
  //GET BANCOS
  public function getBancos($comercio_id){
    $result = bancos::join('bancos_muestra_sucursales','bancos_muestra_sucursales.banco_id','bancos.id')
    ->where('bancos_muestra_sucursales.muestra', 1)
    ->where('bancos_muestra_sucursales.sucursal_id', $comercio_id)
    ->select('bancos.id','bancos.cbu','bancos.cuit','bancos.tipo','bancos.nombre','bancos.muestra_sucursales','bancos.comercio_id')
    ->groupBy('bancos.id','bancos.cbu','bancos.cuit','bancos.tipo','bancos.nombre','bancos.muestra_sucursales','bancos.comercio_id')
    ->orderBy('bancos.nombre','asc')->get();
    
    return response()->json($result);
  }

  //GET METODOS DE PAGO
  public function getMetodoPago($comercio_id){
    $result =  metodo_pago::join('bancos','bancos.id','metodo_pagos.cuenta')
    ->join('metodo_pagos_muestra_sucursales','metodo_pagos_muestra_sucursales.metodo_id','metodo_pagos.id')
    ->where('metodo_pagos_muestra_sucursales.muestra', 1)
    ->where('metodo_pagos_muestra_sucursales.sucursal_id', $comercio_id)
    ->where('metodo_pagos.eliminado',0)
    ->select('metodo_pagos.*','bancos.nombre as nombre_banco')
    ->orderBy('metodo_pagos.nombre','asc')->get();
    
    return response()->json($result);
  }
  //GET PUNTOS DE VENTA
  public function getPuntosVentaMount($comercio_id){
    $result = datos_facturacion::where('comercio_id',$comercio_id)->where('eliminado',0)->orderBy('predeterminado','desc')->get();    
    return response()->json($result);
  }
  
  //GET LISTA PRECIOS
  public function getListaPrecios($casa_central_id){
    $result = $this->lista_precios = lista_precios::where('comercio_id',$casa_central_id)->get();
    return response()->json($result);
  }  
  
  // GET LISTADO DE CLIENTES 
  
  public function getProducts($casa_central_id){
    $result = ClientesMostrador::join('users','users.id','clientes_mostradors.creador_id')
    ->select('clientes_mostradors.*','users.name as nombre_sucursal',ClientesMostrador::raw('DATEDIFF(NOW(), clientes_mostradors.last_sale) as dias_desde_creacion'))
    ->where('clientes_mostradors.comercio_id',$casa_central_id)
    ->get();
    
    return response()->json($result);
  } 
  
  // GET LISTADO DE CLIENTES 
  
  public function getClientes($casa_central_id){
    $result = ClientesMostrador::join('users','users.id','clientes_mostradors.creador_id')
    ->select('clientes_mostradors.*','users.name as nombre_sucursal',ClientesMostrador::raw('DATEDIFF(NOW(), clientes_mostradors.last_sale) as dias_desde_creacion'))
    ->where('clientes_mostradors.comercio_id',$casa_central_id)
    ->get();
    
    return response()->json($result);
  }         
  
  // GET CUENTA CORRIENTE CLIENTE 
  public function GetCtaClienteClienteById($cliente_id){
   
   $deuda_inicial = ClientesMostrador::join('saldos_iniciales','saldos_iniciales.referencia_id','clientes_mostradors.id')
   ->select(Sale::raw('SUM(IFNULL(saldos_iniciales.monto,0)) AS saldo'))
   ->where('clientes_mostradors.id',$cliente_id)
   ->where('saldos_iniciales.eliminado',0)
   ->first()->saldo; 
   
      
   $deuda = ClientesMostrador::join('sales','sales.cliente_id','clientes_mostradors.id')
   ->select(Sale::raw('SUM(IFNULL(sales.deuda,0)) AS saldo'))
   ->where('clientes_mostradors.id',$cliente_id)
   ->where('sales.eliminado',0)
   ->first()->saldo; 
   
   if($deuda == null){$deuda = 0;}
   if($deuda_inicial == null){$deuda_inicial = 0;}
   
    return $deuda + $deuda_inicial;
    }
  // GET LISTADO DE STOCK DE LOS PRODUCTOS DE TODAS LAS SUCURSALES

   public function getStocks($comercio_id)
   {
      $result = productos_stock_sucursales::join('products','products.id','productos_stock_sucursales.product_id')
      ->where('productos_stock_sucursales.comercio_id', $comercio_id)
      ->where('products.eliminado', 0)
      ->select('productos_stock_sucursales.id','productos_stock_sucursales.product_id','productos_stock_sucursales.referencia_variacion','productos_stock_sucursales.sucursal_id','productos_stock_sucursales.comercio_id','productos_stock_sucursales.stock','productos_stock_sucursales.stock_real')
      ->get();
      
      return response()->json($result);
   }
   
   
  // GET PRODUCTOS DE LOS PRODUCTOS EN CADA LISTA

   public function getProductos($comercio_id)
   {
      $result = Product::join('categories as c','c.id','products.category_id')
      ->join('proveedores as pr','pr.id','products.proveedor_id')
      ->leftjoin('imagenes as i','i.url','products.image')
      ->where('products.comercio_id', $comercio_id) 
      ->where('products.eliminado', 0)
      ->select('products.*','c.name as nombre_categoria','pr.nombre as nombre_proveedor','i.url as url_imagen','i.base64 as base64_imagen')
      ->get();
    
    return response()->json($result);
   }
   
      
  // GET IVA DE LOS PRODUCTOS


   

  // GET PRECIOS DE LOS PRODUCTOS EN CADA LISTA

   public function getPrecios($comercio_id)
   {
      $result = productos_lista_precios::join('products','products.id','productos_lista_precios.product_id')
      ->where('productos_lista_precios.comercio_id', $comercio_id)
      ->where('products.eliminado', 0)
      ->select('productos_lista_precios.id','productos_lista_precios.product_id','productos_lista_precios.referencia_variacion','productos_lista_precios.lista_id','productos_lista_precios.precio_lista')
      ->get();
    
    return response()->json($result);
   }
   
   
  // GET STOCK DE UN PRODUCTO EN PARTICULAR 

   public function getProductStockProduct($product_id, $variacion, $sucursal_id,$casa_central_id)
   {
      $sucursal_id = $this->SetValorOrCero($casa_central_id,$sucursal_id);
      
      $result = productos_stock_sucursales::join('products','products.id','productos_stock_sucursales.product_id')
      ->where('productos_stock_sucursales.product_id', $product_id)
      ->where('productos_stock_sucursales.sucursal_id', $sucursal_id)
      ->where('productos_stock_sucursales.referencia_variacion', $variacion)
      ->where('products.eliminado', 0)
      ->select('productos_stock_sucursales.id','productos_stock_sucursales.stock','productos_stock_sucursales.product_id','productos_stock_sucursales.referencia_variacion','productos_stock_sucursales.sucursal_id','productos_stock_sucursales.stock_real')
      ->first();
      
      return response()->json($result);
   }
   
  // GET PRECIO DE UN PRODUCTO EN PARTICULAR  

   public function getProductPrecioProduct($product_id, $variacion, $lista_id)
   {
      $result = productos_lista_precios::join('products','products.id','productos_lista_precios.product_id')
      ->where('products.id', $product_id)
      ->where('productos_lista_precios.lista_id', $lista_id)
      ->where('productos_lista_precios.referencia_variacion',  $variacion)
      ->where('products.eliminado', 0)
      ->select('productos_lista_precios.product_id','productos_lista_precios.referencia_variacion','productos_lista_precios.lista_id','productos_lista_precios.precio_lista')
      ->first();
    
    return response()->json($result);
   }
   
   // Funcion auxiliar para poner en 0 si es casa central y el sucursal_id si es sucursal
   public function SetValorOrCero($casa_central_id,$sucursal_id){
    if($casa_central_id == $sucursal_id){
        return 0;
    } else {
        return $sucursal_id;
    }
    }



}
