<?php
namespace App\Traits;


// servicios de las variaciones de product trait

use App\Services\CartVariaciones;
//
use Darryldecode\Cart\Facades\CartFacade as Cart;
use App\Models\Product;
use App\Models\productos_ivas;
use App\Models\promos_productos;
use App\Models\datos_facturacion;
use Carbon\Carbon;
use App\Models\lista_precios;
use App\Models\sucursales;
use App\Models\SaleDetail;
use App\Models\Sale;
use App\Models\pagos_facturas;
use App\Models\productos_variaciones;
use App\Models\productos_variaciones_datos;
use App\Models\asistente_produccions;
use App\Models\User;
use App\Models\atributos;
use App\Models\variaciones;
use App\Models\productos_stock_sucursales;
use App\Models\ClientesMostrador;
use App\Models\metodo_pago;
use App\Models\productos_lista_precios;
use Illuminate\Support\Facades\Auth;

use App\Models\bancos;
use App\Models\wocommerce;
use App\Models\cajas;
use Illuminate\Support\Facades\DB;
use App\Models\provincias;

use App\Models\ecommerce_envio;

// 9-1-2024
use App\Models\compras_proveedores;
//

use Automattic\WooCommerce\Client;

trait CartNuevoTrait {

public $iva_elegido_subtotal;
public $sum_iva_subtotal;
public $iva_pago_dividido_total;
public $a_cobrar_total;
public $iva_total;
public $iva_elegido;
public $metodo_pago;
public $metodopago;
public $sum_iva;
public $sum_descuento;
public $iva_defecto;
public $a_recargar;
public $productos_stock_sucursales;
public $nombre_lista_precios;
public $noEncontrados;
public $dataProductsSimple;
public $iva_recargo;
public $metodo_pago_div1,$metodo_pago_div2;


public $metodos_pago_dividido = [];
public $efectivo_dividido = [];
public $a_cobrar = [];
public $recargo_div = [];
public $recargo_total_div = [];
public $recargo_total;
public $sum_descuento_promo_con_iva;
public $sum_subtotal_con_iva;


public $iva_total_dividido = [];
public $iva_pago_dividido = [];
public $iva_recargo_dividido = [];



public function mount()
{

$this->recargo = 0;

}




///////////////////////////////////////

//version en la que estoy trabajando
//////////////////////////////////////

   public function ScanearCode($barcode, $cant = 1, $scanearVariacion = false)
    {               
        
        // 17-1-2024
        $this->idVenta = $this->SetNroCarro();
        
        $variacion = 0;
        
        if(strpos($barcode, '/') !== false) {
        $barcode =  $this->setBarcodeVariacion($barcode);         
          //return dd($barcode);
          if($barcode !== null){
            $this->product = explode('|-|',$barcode);
            $product_id = 	$this->product[0];  
            $variacion = 	$this->product[1]; 
            $scanearVariacion = true;
          }else{
            return $this->emit('scan-notfound', $barcode);
          }
         
      }
        
      /////////////////////////////

        if($scanearVariacion === true ) {
          //return dd($barcode);  
          $this->product = explode('|-|',$barcode);
          $product_id = 	$this->product[0];
          $variacion = 	$this->product[1];
          
          
        }

      //Reemplazar y elimianr liego
      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;

      //return dd($this->comercio_id);

      //Testeado funcion ok
      $this->tipo_usuario = User::find($this->comercio_id); 
      $this->casa_central_id = $this->setCasaCentralId($this->tipo_usuario, $this->comercio_id);   
      
        //Testeado para productos simples OK ///////////////////
        
        if($scanearVariacion === false){
          $product = $this->getProduct($barcode, $this->casa_central_id);      
        }else{
          $product = $this->getProductVariacion($product_id, $this->casa_central_id);      
        }

        //Emit MSJ: quiere registrar el producto
        //Testeado OK
        if($product == null || empty($product))
        {
          $this->emit('scan-notfound', $barcode);
        }  else {
         
        //** EL PRODUCTO EXISTE *///

        //---- Variaciones ----///
        
        if($product->producto_tipo == "v" &&  $scanearVariacion === false) {

          $this->productos_variaciones_datos = $this->getProductVariacionDatos($product->id, $this->casa_central_id);        
          $this->atributos = $this->getAtributos($product->id, $this->comercio_id);        
          $this->product_id = $product->id;        
          $this->variaciones =  $this->getVariaciones($this->comercio_id);         
          $this->emit('variacion-elegir', $product->id);    //Abre moda elegir y vuelve a entrar en esta funcion
          return $this->barcode;
        }

        //----   Stock    ----//
        $vc = explode('-',$variacion);
        $variacion_cart = $vc[0];
        
        $exist = Cart::get($product->id."-".$variacion_cart."-".$this->comercio_id); //Viene nulo al agregar producto
        //dd($exist);
        
        $stock_comprometido = $this->GetStockComprometido($product->id,$variacion,$this->comercio_id);
        $stock_disponible = $this->GetStockDisponible($stock_comprometido,$product->id, $this->casa_central_id,$variacion,$this->comercio_id);
        $stock_disponible_carro = $this->GetStockDisponibleCarro($stock_disponible,$exist);
        
        if($stock_disponible_carro < 1 && $product->stock_descubierto == 'si' )
          {
                  $this->emit('no-stock','Stock insuficiente, disponibles:  '.$stock_disponible);
                  $this->emit('volver-stock', $product->id);
                  return;
          }
    
        $stock_disponible_nuevo = $stock_disponible_carro - $cant;
        $sucursal_id = $this->SetSucursalOrCero($this->casa_central_id,$this->comercio_id);
        $stock_d = $this->SetStockDisponibleCarro($product->id, $this->casa_central_id,$sucursal_id, $variacion, $stock_disponible_nuevo);
        $this->set_almacen_id = $this->SetAlmacenId($product->id, $this->casa_central_id,$sucursal_id, $variacion);

        $this->product_stock = $stock_d;          
     
        $this->df = $this->getDatosFacturacion($this->comercio_id); //ok
        
        //---- Precio ----/// 
                   
        // Aca le pasamos la lista que esta seleccionada para buscar el precio del producto 
                
        $this->query_id = session('IdCliente');
        
        $this->lista_precios_elegida = session('ListaPrecios');

        $lista_precios_defecto = $this->tipo_usuario->lista_defecto;
        
        $this->precio_original =  $this->setListaPrecio($product->id, $this->lista_precios_elegida, $variacion);
        
        $this->SetearIVAProducto($this->precio_original,$exist,$this->df,$product,$variacion);
            
        $this->descuento_gral = $this->setDescuentoGeneral(session('DescuentoGral')); 
           
        $id = $this->setProductId($variacion, $product, $this->comercio_id);
                
//        $tipo_unidad_medida = $product->unidad_medida;
//        $cantidad_unidad_medida = $product->cantidad_unidad_medida;
                  
        if($scanearVariacion === true) {       
         $product->name = $product->name." - " .  $this->setProductosVariacionesName($variacion);
         $pvd_datos = $this->setProductosVariacionesDatos($variacion, $product);
         $product->cost = $pvd_datos->cost;
//         $tipo_unidad_medida = $pvd_datos->unidad_medida;
//         $cantidad_unidad_medida = $pvd_datos->cantidad_unidad_medida;
        }
        
             
        $promo = $this->GetDescuentoPromo($product->id,$variacion);
        //dd($promo);
        
        //dd($this->lista_precios_elegida);
        if($this->lista_precios_elegida == 0){ 
        if($promo != null) {if($promo->tipo_promo == 1){

        $cant = ($exist->quantity ?? 0) + 1;
        $resultado_promo = $this->CalcularDescuentoPromos($exist,$cant,$this->precio,$this->iva,$this->relacion_precio_iva,$promo,0);
        }}
        }
              
        $id_promo = $resultado_promo[0] ?? $exist->attributes['id_promo'] ?? null;
        $nombre_promo = $resultado_promo[1] ?? $exist->attributes['nombre_promo'] ?? null;
        $cantidad_promo = $resultado_promo[2] ?? $exist->attributes['cantidad_promo'] ?? null;
        $descuento_promo = $resultado_promo[3] ?? $exist->attributes['descuento_promo'] ?? null;

              Cart::add(array(
                  'id' => $id,
                  'name' => $product->name,
                  'price' => $this->precio,
                  'quantity' => 1,
                  'attributes' => array(
                  'product_id' => $product->id,
                  'image' => $product->image,
                  'cost' => $product->cost,
                  'alto' => '1',
                  'ancho' => '1',
                  'iva' => $this->iva,
                  'relacion_precio_iva' => $this->relacion_precio_iva,
                  'seccionalmacen_id' => $this->set_almacen_id,
                  'referencia_variacion' => $variacion,
                  'descuento' => $this->descuento_gral,
                  'id_promo' => $id_promo,
                  'nombre_promo' => $nombre_promo,
                  'descuento_promo' => $descuento_promo,
                  'cantidad_promo' => $cantidad_promo,
//                  'tipo_unidad_medida' => $tipo_unidad_medida,
//                  'cantidad_unidad_medida' => $cantidad_unidad_medida,
                  'comercio_id' => $comercio_id,
                  'sucursal_id' => $comercio_id,
                  'barcode' => $product->barcode,
                  'stock' => $this->product_stock,
                  'stock_descubierto' => $product->stock_descubierto,
                  'added_at' => Carbon::now(),
                  'comentario' => '',
                  'precio_original' => $this->precio_original
                  )));
                
                if($this->lista_precios_elegida == 0){
                if($promo != null) { if($promo->tipo_promo == 2){
                    $this->SetPromoTipo2($promo);    
                }}}
                
                $this->CalcularTotales();
                
                                
                $this->emit('scan-ok','Producto agregado');              
            
            
        }

}

public function cambioACobrar($value){
    dd($value);
}
public function UpdatePrecio($id, $product_id, $variacion, $sucursal, $price = 1)
{

    if($price == "") {
        $price = 1;
    } else {
        $price = $price;
    }

        $exist = Cart::get($id);
        $product = Product::find($product_id);
        
$this->UpdatePrecioIndividual($exist,$product,$variacion,$price);


}

public function SetDescuentoGeneralProducto($precio,$cantidad,$descuento_promo,$cantidad_promo,$alicuota_descuento_gral){
    $subtotal = $precio * $cantidad;
    $descuento_promo = $descuento_promo * $cantidad_promo;
    $total_sin_promo = $subtotal - $descuento_promo;
    $descuento_nuevo = $total_sin_promo * $alicuota_descuento_gral;
	
	return $descuento_nuevo;
}
    
public function UpdatePrecioIndividual($exist,$product,$variacion,$precio_original){
    
    $this->precio_original = $precio_original;
    
    $this->removeItemsUpdate($exist->id);
    $img = (count($exist->attributes) > 0 ? $exist->attributes['image'] : Product::find($product->id)->imagen);

    $producto_iva = productos_ivas::where('product_id',$product->id)->where('sucursal_id',$this->comercio_id)->first();
    $iva = $producto_iva->iva ?? 0;     
    $precio =  $this->precio_original;
    $descuento_promo = 0;
    $descuento = 0;

    if($this->df != null){
    $relacion_precio_iva = $this->df->relacion_precio_iva;    
    } else {
    $relacion_precio_iva = 0;    
    }
    
    $this->setRelacionPrecioIva($relacion_precio_iva, $precio, $iva,$descuento_promo,$descuento);
    
    $id_promo = $exist->attributes['id_promo'];
    // Si la lista de precios es 0 entonces corresponden las promociones y descuentos individuales

    if($id_promo == 1){
    // Si la id_promo es 1, entonces no busca la promo sino que cambia los precios
    
    if($this->lista_precios_elegida != 1){
    $alicuota_descuento_promo = $exist->attributes['descuento_promo'] / $exist->price; 
    $descuento_promo_nuevo = $alicuota_descuento_promo * $this->precio;
    $id_promo = $exist->attributes['id_promo'];
    $nombre_promo = $exist->attributes['nombre_promo'];
    $cantidad_promo = $exist->attributes['cantidad_promo'];
    $descuento_promo = $descuento_promo_nuevo ?? $exist->attributes['descuento_promo'];     
    } else {
    $id_promo = null;
    $nombre_promo = null;
    $cantidad_promo = null;
    $descuento_promo = null;             
    }

    } else {

    // si no es promo id 1 
    
    if($this->lista_precios_elegida == 0){

    // Si la id_promo no es 1, entonces busca que promo son cada una y calcula
    $promo = $this->GetDescuentoPromo($product->id,$variacion);
    
    if($promo != null){
    $cant = $exist->quantity;
    $resultado_promo = $this->CalcularDescuentoPromos($exist,$cant,$this->precio,$exist->attributes['iva'],$exist->attributes['relacion_precio_iva'],$promo,0);
    
    }
        
    $id_promo = $resultado_promo[0] ?? $exist->attributes['id_promo'];
    $nombre_promo = $resultado_promo[1] ?? $exist->attributes['nombre_promo'];
    $cantidad_promo = $resultado_promo[2] ?? $exist->attributes['cantidad_promo'];
    $descuento_promo = $resultado_promo[3] ?? $exist->attributes['descuento_promo'];
    } else {
    $id_promo = null;
    $nombre_promo = null;
    $cantidad_promo = null;
    $descuento_promo = null;          
    }

    }

    
    $this->descuento_gral = $this->setDescuentoGeneral(session('DescuentoGral')); 
    $descuento_nuevo = $this->SetDescuentoGeneralProducto($this->precio,$exist->quantity,$descuento_promo,$cantidad_promo,$this->descuento_gral);
    
        
            Cart::add(array(
                  'id' => $exist->id,
                  'name' => $exist->name,
                  'price' => $this->precio,
                  'quantity' => $exist->quantity,
                  'attributes' => array(
                  'product_id' => $exist->attributes['product_id'],
                  'image' => $exist->attributes['image'],
                  'cost' => $exist->attributes['cost'],
                  'alto' => '1',
                  'ancho' => '1',
                  'iva' => $exist->attributes['iva'],
                  'relacion_precio_iva' => $exist->attributes['relacion_precio_iva'],
                  'seccionalmacen_id' => $exist->attributes['seccionalmacen_id'],
                  'referencia_variacion' => $exist->attributes['referencia_variacion'],
                  'descuento' => $exist->attributes['descuento'],
                  'id_promo' => $id_promo,
                  'nombre_promo' => $nombre_promo,
                  'descuento_promo' => $descuento_promo,
                  'cantidad_promo' => $cantidad_promo,
                  'comercio_id' => $exist->attributes['comercio_id'],
                  'sucursal_id' => $exist->attributes['sucursal_id'],
                  'barcode' => $exist->attributes['barcode'],
                  'stock' => $exist->attributes['stock'],
                  'stock_descubierto' => $exist->attributes['stock_descubierto'],
                  'added_at' => $exist->attributes['added_at'],
                  'comentario' => $exist->attributes['comentario'],
                  'precio_original' => $this->precio_original
            ))
            );
        
        if($this->lista_precios_elegida == 0){
        if($id_promo != 1){    
              if($promo != null) { if($promo->tipo_promo == 2){
                   $this->SetPromoTipo2($promo);    
               }}
        }
        }
            
    
    
}

// 1-12-2023
public function UpdateCliente()
{
//dd("hola");

if(Auth::user()->comercio_id != 1)
$comercio_id = Auth::user()->comercio_id;
else
$comercio_id = Auth::user()->id;


$this->carro = Cart::getContent();

//** EL CORROBORA SI TIENE VARIAS LISTAS DE PRECIO EL CLIENTE *///

$this->query_id = session('IdCliente');

$this->cliente = ClientesMostrador::find($this->query_id);

// Determinar la lista de precios
if($this->query_id != 1) {
$this->lista_precios_elegida = $this->cliente->lista_precio;
session(['ListaPrecios' => $this->cliente->lista_precio]);
} else {
$this->lista_precios_elegida = 0;
session(['ListaPrecios' => 0]);    
}


$this->SetNombreLista($this->lista_precios_elegida);
//

foreach ($this->carro as $carro) {
    // code...
    $exist = Cart::get($carro->id);
    $product = Product::find($exist->attributes['product_id']); 
    $variacion = $exist->attributes['referencia_variacion'];
    // Seteamos la lista de precios 
    $this->precio_original =  $this->setListaPrecio($exist->attributes['product_id'], $this->lista_precios_elegida,$variacion);
    $this->UpdatePrecioIndividual($exist,$product,$variacion,$this->precio_original);

}

$this->CalcularTotales();

$this->emit('scan-ok', 'LISTA DE PRECIOS ACTUALIZADA');


}

public function Update_Iva($id, $product , $variacion , $sucursal , $iva = 1)
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

        $product = Product::find($product);

       $product_stock = productos_stock_sucursales::join('products','products.id','productos_stock_sucursales.product_id')
        ->where('products.id', $product->id)
        ->where('products.comercio_id', $this->casa_central_id)
        ->where('products.eliminado', 0)
        ->where('productos_stock_sucursales.referencia_variacion', $variacion)
        ->select('productos_stock_sucursales.stock')
        ->first();


        $product_precio = productos_lista_precios::join('products','products.id','productos_lista_precios.product_id')
        ->where('products.id', $product->id)
        ->where('products.comercio_id', $this->casa_central_id)
        ->where('products.eliminado', 0)
        ->where('productos_lista_precios.referencia_variacion', $variacion)
        ->select('productos_lista_precios.precio_lista as price')
        ->first();


        $exist = Cart::get($id);


        //$this->removeItem($id);
        $this->removeItemsUpdate($exist->id);
        

        $img = (count($exist->attributes) > 0 ? $exist->attributes['image'] : Product::find($product->id)->imagen);


          Cart::add(array(
          'id' => $exist->id,
          'name' => $exist->name,
          'price' => $exist->price,
          'quantity' => $exist->quantity,
          'attributes' => array(
            'image' => $img,
            'alto' => 1,
            'ancho' => 1,
            'relacion_precio_iva' =>$exist->attributes['relacion_precio_iva'],
            'iva' => $iva,
            'seccionalmacen_id' => $exist->attributes['seccionalmacen_id'],
            'comercio_id' => $exist->attributes['comercio_id'],
            'sucursal_id' => $exist->attributes['sucursal_id'],
            'barcode' => $exist->attributes['barcode'],
            'cost' => $exist->attributes['cost'],
            'referencia_variacion' => $exist->attributes['referencia_variacion'],
            'id_promo' => $exist->attributes['id_promo'],
            'nombre_promo' => $exist->attributes['nombre_promo'],
            'descuento_promo' => $exist->attributes['descuento_promo'],
            'cantidad_promo' => $exist->attributes['cantidad_promo'],
            'descuento' => $exist->attributes['descuento'],
            'product_id' => $exist->attributes['product_id'],
            'stock' => $exist->attributes['stock'],
            'added_at' => $exist->attributes['added_at'],
            'stock_descubierto' => $exist->attributes['stock_descubierto'],
            'comentario' => $exist->attributes['comentario'],
            'precio_original' => $exist->attributes['precio_original'],
//            'tipo_unidad_medida' => $exist->attributes['tipo_unidad_medida'],
//            'cantidad_unidad_medida' => $exist->attributes['cantidad_unidad_medida'],
          )));
                //Cart::add($product->id, $product->name, $product->price, $cant, $product->image);

                $this->CalcularTotales();

                $this->emit('scan-ok', 'IVA ACTUALIZADO');



}

public function Update_Iva_Gral($iva)
{
        $carro = Cart::getContent();

        foreach ($carro as $exist) {

          //$this->removeItem($exist->id);
          $this->removeItemsUpdate($exist->id);
        

          $img = (count($exist->attributes) > 0 ? $exist->attributes['image'] : Product::find($product->id)->imagen);


            Cart::add(array(
            'id' => $exist->id,
            'name' => $exist->name,
            'price' => $exist->price,
            'quantity' => $exist->quantity,
            'attributes' => array(
              'image' => $img,
              'alto' => 1,
              'ancho' => 1,
              'relacion_precio_iva' =>$exist->attributes['relacion_precio_iva'],
              'iva' => $iva,
              'referencia_variacion' => $exist->attributes['referencia_variacion'],
              'seccionalmacen_id' => $exist->attributes['seccionalmacen_id'],
              'comercio_id' => $exist->attributes['comercio_id'],
              'sucursal_id' => $exist->attributes['sucursal_id'],
              'barcode' => $exist->attributes['barcode'],
              'product_id' => $exist->attributes['product_id'],
              'stock' => $exist->attributes['stock'],
              'cost' => $exist->attributes['cost'],
              'descuento' => $exist->attributes['descuento'],
              'id_promo' => $exist->attributes['id_promo'],
              'nombre_promo' => $exist->attributes['nombre_promo'],
              'descuento_promo' => $exist->attributes['descuento_promo'],
              'cantidad_promo' => $exist->attributes['cantidad_promo'],
              'added_at' => $exist->attributes['added_at'],
              'stock_descubierto' => $exist->attributes['stock_descubierto'],
              'comentario' => $exist->attributes['comentario'],
              'precio_original' => $exist->attributes['precio_original'],
//                'tipo_unidad_medida' => $exist->attributes['tipo_unidad_medida'],
//                'cantidad_unidad_medida' => $exist->attributes['cantidad_unidad_medida'],
            )));

        }

                $this->CalcularTotales();

                $this->emit('scan-ok', 'IVA ACTUALIZADO');

}

public function Update_descuento($id, $product,$variacion ,$sucursal, $descuento = 1)
{
        $product = Product::find($product);

        $exist = Cart::get($id);


        //$this->removeItem($id);
        $this->removeItemsUpdate($exist->id);
        

        $img = (count($exist->attributes) > 0 ? $exist->attributes['image'] : Product::find($product->id)->imagen);


          Cart::add(array(
          'id' => $exist->id,
          'name' => $exist->name,
          'price' => $exist->price,
          'quantity' => $exist->quantity,
          'attributes' => array(
            'image' => $img,
            'alto' => 1,
            'ancho' => 1,
            'relacion_precio_iva' =>$exist->attributes['relacion_precio_iva'],
            'iva' => $exist->attributes['iva'],
            'referencia_variacion' => $exist->attributes['referencia_variacion'],
            'seccionalmacen_id' => $exist->attributes['seccionalmacen_id'],
            'comercio_id' => $exist->attributes['comercio_id'],
            'sucursal_id' => $exist->attributes['sucursal_id'],
            'barcode' => $exist->attributes['barcode'],
            'cost' => $exist->attributes['cost'],
            'product_id' => $exist->attributes['product_id'],
            'stock' => $exist->attributes['stock'],
            'descuento' => $descuento,
            'id_promo' => $exist->attributes['id_promo'],
            'nombre_promo' => $exist->attributes['nombre_promo'],
            'descuento_promo' => $exist->attributes['descuento_promo'],
            'cantidad_promo' => $exist->attributes['cantidad_promo'],
            'added_at' => $exist->attributes['added_at'],
            'stock_descubierto' => $exist->attributes['stock_descubierto'],
            'comentario' => $exist->attributes['comentario'],
            'precio_original' => $exist->attributes['precio_original'],
//            'tipo_unidad_medida' => $exist->attributes['tipo_unidad_medida'],
//            'cantidad_unidad_medida' => $exist->attributes['cantidad_unidad_medida'],
          )));
                //Cart::add($product->id, $product->name, $product->price, $cant, $product->image);

                $this->CalcularTotales();

                $this->emit('scan-ok', 'DESCUENTO ACTUALIZADO');

}

public function Update_descuento_gral($descuento)
{

        $carro = Cart::getContent();

        foreach ($carro as $exist) {

          $this->removeItemsUpdate($exist->id);
        //$this->removeItem($exist->id);

          $img = (count($exist->attributes) > 0 ? $exist->attributes['image'] : Product::find($product->id)->imagen);
          

        //  $iva_total = $this->CalcularIvaTotalProducto($exist->price,$exist->attributes['precio_original'],$exist->attributes['relacion_precio_iva'],$exist->attributes['iva'],$exist->attributes['descuento_promo'],$descuento,$this->metodo_pago);
          
            Cart::add(array(
            'id' => $exist->id,
            'name' => $exist->name,
            'price' => $exist->price,
            'quantity' => $exist->quantity,
            'attributes' => array(
              'image' => $img,
              'alto' => 1,
              'ancho' => 1,
              'relacion_precio_iva' =>$exist->attributes['relacion_precio_iva'],
              'iva' => $exist->attributes['iva'],
              'referencia_variacion' => $exist->attributes['referencia_variacion'],
              'seccionalmacen_id' => $exist->attributes['seccionalmacen_id'],
              'comercio_id' => $exist->attributes['comercio_id'],
              'sucursal_id' => $exist->attributes['sucursal_id'],
              'barcode' => $exist->attributes['barcode'],
              'product_id' => $exist->attributes['product_id'],
              'stock' => $exist->attributes['stock'],
              'cost' => $exist->attributes['cost'],
              'descuento' => $descuento,
              'id_promo' => $exist->attributes['id_promo'],
              'nombre_promo' => $exist->attributes['nombre_promo'],
              'descuento_promo' => $exist->attributes['descuento_promo'],
              'cantidad_promo' => $exist->attributes['cantidad_promo'],
              'added_at' => $exist->attributes['added_at'],
              'stock_descubierto' => $exist->attributes['stock_descubierto'],
              'comentario' => $exist->attributes['comentario'],
              'precio_original' => $exist->attributes['precio_original'],
//              'tipo_unidad_medida' => $exist->attributes['tipo_unidad_medida'],
//              'cantidad_unidad_medida' => $exist->attributes['cantidad_unidad_medida'],
            )));

        }

        $this->CalcularTotales();

        $this->emit('scan-ok', 'DESCUENTO ACTUALIZADO');

}

public function IncreaseQuantity($id, $product, $variacion, $sucursal, $cant)
{
        $title ='';
        
        $product = Product::find($product);

        $exist = Cart::get($id);
       
        if($exist)
                $title = 'Cantidad actualizada';
        else
                $title ='Producto agregado';

        
         $product_stock = productos_stock_sucursales::join('products','products.id','productos_stock_sucursales.product_id')
        ->where('products.id', $product->id)
        ->where('products.comercio_id', $product->comercio_id)
        ->where('products.eliminado', 0)
        ->where('productos_stock_sucursales.referencia_variacion', $variacion)
        ->select('productos_stock_sucursales.stock')
        ->first();
        
        if($exist)
        {
                if($product_stock->stock < ($exist->quantity + $cant) && $product->stock_descubierto == 'si' )
                {
                        $this->emit('no-stock','Stock insuficiente, disponibles: '.$product_stock->stock);
                        return;
                }
        }

        
       
//        Cart::add($product->id, $product->name, $product->price, $cant, $product->image);

           Cart::add(array(
          'id' => $exist->id,
          'name' => $exist->name,
          'price' => $exist->price,
          'quantity' => $cant,
          'attributes' => array(
            'image' => Product::find($product->id)->imagen,
            'alto' => 1,
            'ancho' => 1,
            'seccionalmacen_id' => $exist->attributes['seccionalmacen_id'],
            'relacion_precio_iva' =>$exist->attributes['relacion_precio_iva'],
            'comercio_id' => $exist->attributes['comercio_id'],
            'sucursal_id' => $exist->attributes['sucursal_id'],
            'stock' => $exist->attributes['stock'],
            'cost' => $exist->attributes['cost'],
            'referencia_variacion' => $exist->attributes['referencia_variacion'],
            'product_id' => $exist->attributes['product_id'],
            'iva' => $exist->attributes['iva'],
            'barcode' => $exist->attributes['barcode'],
            'descuento' => $exist->attributes['descuento'],
            'id_promo' => $exist->attributes['id_promo'],
            'nombre_promo' => $exist->attributes['nombre_promo'],
            'descuento_promo' => $exist->attributes['descuento_promo'],
            'cantidad_promo' => $exist->attributes['cantidad_promo'],
            'added_at' => $exist->attributes['added_at'],
            'stock_descubierto' => $exist->attributes['stock_descubierto'],
            'comentario' => $exist->attributes['comentario'],
            'precio_original' => $exist->attributes['precio_original'],
//            'tipo_unidad_medida' => $exist->attributes['tipo_unidad_medida'],
//            'cantidad_unidad_medida' => $exist->attributes['cantidad_unidad_medida'],
          )));

          $this->metodo_pago = session('MetodoPago');

        $this->CalcularTotales();

        $this->emit('scan-ok', $title);

}

public function decreaseQuantity($id, $product, $variacion, $sucursal, $cant)
{
    
        $item = Cart::get($id);
        Cart::remove($id);
        
        $newQty = ($item->quantity) - 1;

        if($newQty > 0)
        Cart::add(array(
        'id' => $item->id,
        'name' => $item->name,
        'price' => $item->price,
        'quantity' => $newQty,
        'attributes' => array(
          'image' => Product::find($product)->imagen,
          'alto' => 1,
          'ancho' => 1,
          'seccionalmacen_id' => $item->attributes['seccionalmacen_id'],
          'comercio_id' => $item->attributes['comercio_id'],
          'sucursal_id' => $item->attributes['sucursal_id'],
          'stock' => $item->attributes['stock'],
          'descuento' => $item->attributes['descuento'],
            'id_promo' => $exist->attributes['id_promo'],
          'nombre_promo' => $exist->attributes['nombre_promo'],
          'descuento_promo' => $exist->attributes['descuento_promo'],
          'cantidad_promo' => $exist->attributes['cantidad_promo'],
          'referencia_variacion' => $item->attributes['referencia_variacion'],
          'cost' => $item->attributes['cost'],
          'product_id' => $item->attributes['product_id'],
          'iva' => $item->attributes['iva'],
          'relacion_precio_iva' =>$item->attributes['relacion_precio_iva'],
          'barcode' => $item->attributes['barcode'],
          'added_at' => $item->attributes['added_at'],
          'stock_descubierto' => $item->attributes['stock_descubierto'],
          'comentario' => $item->attributes['comentario'],
          'precio_original' => $exist->attributes['precio_original'],
//            'tipo_unidad_medida' => $exist->attributes['tipo_unidad_medida'],
//            'cantidad_unidad_medida' => $exist->attributes['cantidad_unidad_medida'],
        )));
                //Cart::add($item->id, $item->name, $item->price, $newQty, $item->attributes[0]);


        $this->CalcularTotales();

        $this->emit('scan-ok', 'Cantidad actualizada');

}


public function updateQuantity($id, $product, $variacion ,$sucursal,  $cant = 1)
{
      $exist = Cart::get($id);
      //dd($exist);
      
      $this->tipo_usuario = User::find(Auth::user()->id);

      $this->setCasaCentralId($this->tipo_usuario , $this->comercio_id);

      $product = Product::find($product);

       //9-1-2024
       $product_stock = $this->getProductStock($product->id, $this->casa_central_id,$variacion,$this->comercio_id);
       
       $disponible = $exist->quantity + $product_stock->stock;
       //
       

        if($exist)
        {
                
                if(($cant < $exist->attributes['cantidad_promo']) && $exist->attributes['id_promo'] == 1)
                {
                        $this->emit('msg-error','La cantidad ingresada es inferior a la del descuento individual, modifica esto primero');
                        return;
                }
                
                if( ( $exist->quantity < $cant) && ($disponible < $cant) && ($product->stock_descubierto == 'si') )
                {
                        $this->emit('no-stock','Stock insuficiente, disponibles: '.$disponible);
                        $this->emit('volver-stock', $id);
                        return;
                }

                // pasa las variables $product_id,$casaCentralId,$sucursal,$variacion,$cant
                
                $dif_cant = $exist->quantity - $cant;
                $sucursal_id = $this->SetSucursalOrCero($this->casa_central_id,$this->comercio_id);
                $stock_d = $this->SetStockDisponible($product->id, $this->casa_central_id,$sucursal_id, $variacion, $dif_cant);
                $this->product_stock = $stock_d;
              

        }
  
        if($cant > 0)
        {
        $this->removeItemsUpdate($id);
        } else {
        $this->removeItems($id);    
        }
 
        $img = (count($exist->attributes) > 0 ? $exist->attributes['image'] : Product::find($product->id)->imagen);

        $promo = $this->GetDescuentoPromo($product->id,$variacion);
        if($promo != null) {if($promo->tipo_promo == 1){
        $resultado_promo = $this->CalcularDescuentoPromos($exist,$cant,$exist->price,$exist->attributes['iva'],$exist->attributes['relacion_precio_iva'],$promo,0);
        }}
        
        
        $id_promo = $resultado_promo[0] ?? $exist->attributes['id_promo'];
        $nombre_promo = $resultado_promo[1] ?? $exist->attributes['nombre_promo'];
        $cantidad_promo = $resultado_promo[2] ?? $exist->attributes['cantidad_promo'];
        $descuento_promo = $resultado_promo[3] ?? $exist->attributes['descuento_promo'];
        
        
        if($cant > 0)
        {
          Cart::add(array(
          'id' => $exist->id,
          'name' => $exist->name,
          'price' => $exist->price,
          'quantity' => $cant,
          'attributes' => array(
            'image' => $img,
            'alto' => 1,
            'ancho' => 1,
            'seccionalmacen_id' => $exist->attributes['seccionalmacen_id'],
            'relacion_precio_iva' =>$exist->attributes['relacion_precio_iva'],
            'comercio_id' => $exist->attributes['comercio_id'],
            'sucursal_id' => $exist->attributes['sucursal_id'],
            'stock' => $this->product_stock,
            'cost' => $exist->attributes['cost'],
            'referencia_variacion' => $exist->attributes['referencia_variacion'],
            'product_id' => $exist->attributes['product_id'],
            'iva' => $exist->attributes['iva'],
            'barcode' => $exist->attributes['barcode'],
            'descuento' => $exist->attributes['descuento'],
            'id_promo' => $id_promo,
            'nombre_promo' => $nombre_promo,
            'descuento_promo' => $descuento_promo,
            'cantidad_promo' => $cantidad_promo,
            'added_at' => $exist->attributes['added_at'],
            'stock_descubierto' => $exist->attributes['stock_descubierto'],
            'comentario' => $exist->attributes['comentario'],
            'precio_original' => $exist->attributes['precio_original'],
//            'tipo_unidad_medida' => $exist->attributes['tipo_unidad_medida'],
//            'cantidad_unidad_medida' => $exist->attributes['cantidad_unidad_medida'],
          )));
            
            if($this->lista_precios_elegida == 0){
            //Cart::add($product->id, $product->name, $product->price, $cant, $product->image);
            if($promo != null) { if($promo->tipo_promo == 2){
                    $this->SetPromoTipo2($promo);    
            }}}
                
                $this->CalcularTotales();

                $this->emit('scan-ok', "Producto actualizado");


        }


}

/* 8-1-2024
public function updateQuantity($id, $product, $variacion ,$sucursal,  $cant = 1)
{
       $exist = Cart::get($id);
        
       $disponible = $exist->quantity + $exist->attributes['stock'];

      // $comercio_id = $this->$comercio_id;
      //$this->$comercio_id = ;
    
      $this->tipo_usuario = User::find(Auth::user()->id);

      $this->setCasaCentralId($this->tipo_usuario , $this->comercio_id);

      $product = Product::find($product);

       $product_stock = productos_stock_sucursales::join('products','products.id','productos_stock_sucursales.product_id')
        ->where('products.id', $product->id)
        ->where('products.comercio_id', $this->casa_central_id)
        ->where('products.eliminado', 0)
        ->where('productos_stock_sucursales.referencia_variacion', $variacion)
        ->select('productos_stock_sucursales.stock')
        ->first();
      
        //$product_stock = $this->getProductStock($barcode, $this->casa_central_id, $variacion);

        $title='';
        //$product = Product::find($productId);
        
 
        $title=  $exist ? 'Cantidad actualizada' : 'Producto agregado';
 
        if($exist)
        {
          $this->tipo_usuario = User::find($sucursal);
              
              // Si es casa central 
              
              if($this->tipo_usuario->sucursal != 1) {
                
                $stock_d = $exist->quantity + $product_stock->stock;
                //($product_stock->stock < 1)
                //1,10,4
                
                //dd($stock_d < $cant && $product->stock_descubierto);
                
                if( ( $stock_d < $cant) && ($product->stock_descubierto == 'si') )
                {
                        $this->emit('msg-error','Stock insuficiente, disponibles: '.$stock_d);
                        $this->emit('volver-stock', $id);

                        //// HACE VALIDACION SI TIENE VARIAS SUCURSALES ///
                        //$this->sucursales = sucursales::where('casa_central_id', $this->comercio_id)->first();

                        //if($this->sucursales != null) {
                        //$this->emit('buscar-stock', $id);
                        //}
                        //////////////////////////////
                        return;
                }

                // pasa las variables $product_id,$casaCentralId,$sucursal,$variacion,$cant
                
                $dif_cant = $exist->quantity - $cant;
                $stock_d = $this->SetStockDisponible($product->id, $this->casa_central_id,0, $variacion, $dif_cant);
                $this->product_stock = $stock_d;
              
              } else {

                $this->productos_stock_sucursales = productos_stock_sucursales::where('sucursal_id',$sucursal)
                ->where('product_id', $product->id)
                ->first();

                $stock_d = $exist->quantity + $product_stock->stock;
                
                if( ( $stock_d < $cant) && $product->stock_descubierto == 'si' )
                {
                        $this->emit('msg-error','Stock insuficiente, disponibles: '.$stock_d);
                        $this->emit('volver-stock', $id);
                //        $this->emit('buscar-stock', $id);
                        return;
                }
                
                $dif_cant = $exist->quantity - $cant;
                $stock_d = $this->SetStockDisponible($product->id, $this->casa_central_id,$sucursal, $variacion, $dif_cant);
                $this->product_stock = $stock_d;
              }


        }
  
        if($cant > 0)
        {
        $this->removeItemsUpdate($id);
        } else {
        $this->removeItems($id);    
        }
 
        $img = (count($exist->attributes) > 0 ? $exist->attributes['image'] : Product::find($product->id)->imagen);

        if($cant > 0)
        {
          Cart::add(array(
          'id' => $exist->id,
          'name' => $exist->name,
          'price' => $exist->price,
          'quantity' => $cant,
          'attributes' => array(
            'image' => $img,
            'alto' => 1,
            'ancho' => 1,
            'seccionalmacen_id' => $exist->attributes['seccionalmacen_id'],
            'relacion_precio_iva' =>$exist->attributes['relacion_precio_iva'],
            'comercio_id' => $exist->attributes['comercio_id'],
            'sucursal_id' => $exist->attributes['sucursal_id'],
            'stock' => $this->product_stock,
            'cost' => $exist->attributes['cost'],
            'referencia_variacion' => $exist->attributes['referencia_variacion'],
            'product_id' => $exist->attributes['product_id'],
            'iva' => $exist->attributes['iva'],
            'barcode' => $exist->attributes['barcode'],
            'descuento' => $exist->attributes['descuento'],
            'added_at' => $exist->attributes['added_at'],
            'stock_descubierto' => $exist->attributes['stock_descubierto'],
            'comentario' => $exist->attributes['comentario']
          )));
                //Cart::add($product->id, $product->name, $product->price, $cant, $product->image);

                $this->CalcularTotales();

                $this->emit('scan-ok', $title);


        }


}
*/

public function removeItems($productId)
{
    $carro = Cart::getContent();
    
    //dd($carro,$productId);
    
    $exist = Cart::get($productId);
    
    if($exist != null){
        
    // si el comercio es igual a la sucursal setea la sucursal_id como 0
    if($exist->attributes['sucursal_id'] == $this->casa_central_id) { $sucursal_id = 0;  } else { $sucursal_id = $exist->attributes['sucursal_id'];}
    
    $this->SetStockDisponible($exist->attributes['product_id'],$this->casa_central_id,$sucursal_id,$exist->attributes['referencia_variacion'],$exist->quantity);
    
    $promo = $this->GetDescuentoPromo($exist->attributes['product_id'],$exist->attributes['referencia_variacion']);
    
    if($promo != null) { if($promo->tipo_promo == 2){
        $this->RemovePromo($promo,$carro);    
    }}
    
    Cart::remove($productId);
    }
    $this->CalcularTotales();
    
    // ACA HAY QUE SETEAR DE NUEVO LAS PROMOS COMBINADAS
        
    $this->emit('scan-ok', 'Producto eliminado');
}

public function removeItemsUpdate($productId)
{
    
    Cart::remove($productId);

    $this->CalcularTotales();
        
    $this->emit('scan-ok', 'Producto eliminado');
}




public function trashCart(){
    
        $carro = Cart::getContent();
        
        foreach($carro as $c){
        $this->removeItems($c->id);    
        }
        Cart::clear();
        $this->efectivo =0;
        $this->change =0;
        session(['NombreCliente' => ""]);
		session(['IdCliente' => ""]);
		session(['DescuentoGral' => 0]);
        $this->lista_precios_elegida = 0;
        session(['ListaPrecios' => $this->lista_precios_elegida]);
        $this->SetNombreLista($this->lista_precios_elegida);
        $this->query = "";
        $this->query_id = "";
		$this->descuento_gral_mostrar = 0;
        $this->descuento = 0;
        $this->total = Cart::getTotal();
        $this->itemsQuantity = Cart::getTotalQuantity();
        $this->sum_iva = 0;
        $this->sum_descuento = 0;
        $this->descuento_total = 0;
        $this->recargo_total = 0;

        $this->emit('scan-ok', 'Carrito vacío');
}

  //METODOS GENERICOS
  public function test($parametro){
    return $parametro;
  }
  //SET COMMERCIO ID
  public function setComercioId(){
    return Auth::user()->comercio_id == 1 ?  Auth::user()->id :  Auth::user()->comercio_id;
  }

  //SET CASA CENTRAL ID
  public function setCasaCentral($comercio_id){
         
      $u = User::find($comercio_id);
      return $u->casa_central_user_id;
      
  }

  //GET TIPO PAGO
  public function getTipoPago($comercio_id){
    return bancos::join('bancos_muestra_sucursales','bancos_muestra_sucursales.banco_id','bancos.id')
    ->where('bancos_muestra_sucursales.muestra', 1)
    ->where('bancos_muestra_sucursales.sucursal_id', $comercio_id)
    ->select('bancos.id','bancos.cbu','bancos.cuit','bancos.tipo','bancos.nombre','bancos.muestra_sucursales','bancos.comercio_id')
    ->groupBy('bancos.id','bancos.cbu','bancos.cuit','bancos.tipo','bancos.nombre','bancos.muestra_sucursales','bancos.comercio_id')
    ->orderBy('bancos.nombre','asc')->get();
  }

  //GET BANCOS METODO PAGO
  public function getBancosMetodoPago($comercio_id){
    return bancos::join('bancos_muestra_sucursales','bancos_muestra_sucursales.banco_id','bancos.id')
    ->where('bancos_muestra_sucursales.muestra', 1)
    ->where('bancos.tipo', 'like', 2)
    ->where('bancos_muestra_sucursales.sucursal_id', $comercio_id)
    ->orderBy('bancos.nombre','asc')
    ->get();
    
   
  }
  
    //GET BANCO DE UN METODO PAGO
  public function getBanco($metodo_pago){
    //dd($metodo_pago);
    $mp = metodo_pago::find($metodo_pago)->cuenta;
    //dd($mp);
    return $mp;
   
  }

  //GET PLATAFORMAS DE PAGO
  public function getPlataformasMetodosPago($comercio_id){	
    return bancos::join('bancos_muestra_sucursales','bancos_muestra_sucursales.banco_id','bancos.id')
    ->where('bancos_muestra_sucursales.muestra', 1)
    ->where('bancos.tipo', 'like', 3)
    ->where('bancos_muestra_sucursales.sucursal_id', $comercio_id)
    ->orderBy('bancos.nombre','asc')
    ->get();
  }

  //GET METODOS PAGO
  public function getMetodosPago($comercio_id){
    $metodos =  metodo_pago::join('bancos','bancos.id','metodo_pagos.cuenta')
    ->join('metodo_pagos_muestra_sucursales','metodo_pagos_muestra_sucursales.metodo_id','metodo_pagos.id')
    ->where('metodo_pagos_muestra_sucursales.muestra', 1)
    ->where('metodo_pagos_muestra_sucursales.sucursal_id', $comercio_id)
    ->where('metodo_pagos.eliminado',0)
    ->select('metodo_pagos.*','bancos.nombre as nombre_banco');

 //   if($this->tipo_pago != 1 &&  $this->tipo_pago != 2 && $this->tipo_pago != null) {
      $metodos = $metodos->where('metodo_pagos.cuenta', 'like', $this->tipo_pago);
 //   }

    return $metodos->orderBy('metodo_pagos.nombre','asc')->get();
  }

  //GET TODOS METODOS PAGO
  public function getMetodosPagoTodos($comercio_id){
    $metodos =  metodo_pago::join('bancos','bancos.id','metodo_pagos.cuenta')
    ->join('metodo_pagos_muestra_sucursales','metodo_pagos_muestra_sucursales.metodo_id','metodo_pagos.id')
    ->where('metodo_pagos_muestra_sucursales.muestra', 1)
    ->where('metodo_pagos_muestra_sucursales.sucursal_id', $comercio_id)
    ->where('metodo_pagos.eliminado',0)
    ->select('metodo_pagos.*','bancos.nombre as nombre_banco')
    ->orderBy('metodo_pagos.nombre','asc')->get();
    
    //dd($metodos);
    
    return $metodos;
  }


  //GET SUCURSALES DE UN COMERCIO
  public function getSucursal($comercio_id)
  {
    $s = sucursales::join('users','users.id','sucursales.sucursal_id')
    ->select('users.name','sucursales.sucursal_id')
    ->where('sucursales.casa_central_id', $comercio_id)
    ->where('eliminado',0)
    ->get();
    
    return $s;
  }
  
  //9-1-2024
  public function GetSucursalUserId($sucursal_id)
  {
    $s = sucursales::find($sucursal_id)->sucursal_id;
    return $s ?? null;
  }
  //

  //GET PRODUCTOS VARIACIONES
  public function getProductosVariacionesDatos($product_id, $ref_variacion)
  {
    return productos_variaciones_datos::join('products as P','P.id','productos_variaciones_datos.product_id')
    ->where('P.barcode',$product_id)
    ->where('productos_variaciones_datos.codigo_variacion', $ref_variacion)
    ->OrderBy('productos_variaciones_datos.created_at','desc')
    ->first();
  }

  //SET CASA CENTRAL ID
  public function setCasaCentralId($tipoUsuario, $comercioId){
      
      $u = User::find($comercioId);
      return $u->casa_central_user_id;
      
 }

  //GET PRDUCT
  public function getProduct($barcode, $casaCentralId){
      return Product::where('products.barcode', $barcode)
      ->where('products.comercio_id', $casaCentralId)
      ->where('products.eliminado', 0)
      ->first();     
  }

    //GET PRDUCT
    public function getProductVariacion($productId, $casaCentralId){
      return Product::where('products.id', $productId)
      ->where('products.comercio_id', $casaCentralId)
      ->where('products.eliminado', 0)
      ->first();    
  }



  //GET PRODUCT PRECIO
  public function getProductPrecio($barcode, $casaCentralId, $variacion = 0, $lista_id){
    return productos_lista_precios::join('products','products.id','productos_lista_precios.product_id')
    ->where('products.barcode', $barcode)
    ->where('products.comercio_id', $casaCentralId)
    ->where('products.eliminado', 0)
    ->where('productos_lista_precios.referencia_variacion',  $variacion)
    ->where('productos_lista_precios.lista_id',  $lista_id)
    ->select('productos_lista_precios.precio_lista as price')
    ->first();
  }


  //GET PRODUCT VARIACIONES DATOS
  public function getProductVariacionDatos($productId, $comercioId){
    return productos_variaciones_datos::where('product_id',$productId)
    ->where('comercio_id', $comercioId)
    ->where('eliminado',0)
    ->get();
  }

  //GET LISTA DE PRODUCTS
  public function getProductosListaPrecio($productId, $ClienteListaPrecio, $variacion){
    $lista_precio = productos_lista_precios::where('product_id', $productId)
    ->where('referencia_variacion', $variacion)
    ->where('lista_id', $ClienteListaPrecio)
    ->first();

    return $lista_precio->precio_lista;
  }

  //GET PRODUCT ATRIBUTOS
  public function getAtributos($productId, $comercioId){
     return  productos_variaciones::join('variaciones','variaciones.id','productos_variaciones.variacion_id')
     ->select('variaciones.nombre','variaciones.id as atributo_id', 'productos_variaciones.referencia_id')
     ->where('productos_variaciones.producto_id', $productId)
     ->get();
  }

  //GET PRODUCT VARIACIONES
  public function getVariaciones($comercioId){
    return variaciones::where('variaciones.comercio_id', $comercioId)->get(); 
  }


  //Set iva
  public function setIvaDatosFacturacion($atributoIva){
    if($atributoIva != 0) {
      return $exist->attributes['iva'];         
    } else {      
      return $this->iva_elegido;          
    }
   // $this->precio = $exist->price;
  }

  //Set iva
  public function setIvaSinDatosFacturacion($atributoIva){
    if($atributoIva != 0) {
      return $exist->attributes['iva'];         
    } else {      
      return 0;          
    }
     // $this->precio = $exist->price;
  }

  //Calcular precio
  public function calcularPrecio($precioLista,$ivaPorDefecto){
    return $precioLista / (1 + $ivaPorDefecto);
  }  


   // 1-12-2023
    
  //SET LISTA PRECIO
 public function setListaPrecio($productId, $clienteListaPrecio = 0, $variacion = 0){   

    if($clienteListaPrecio == 1) {
    $lista_precio =  $this->SetPrecioSucursal($productId,$variacion,$clienteListaPrecio); 
    } else {    
    $lista_precio = productos_lista_precios::where('product_id', $productId)
    ->where('referencia_variacion', $variacion)
    ->where('lista_id', $clienteListaPrecio)
    ->first();
    
    $lista_precio = ($lista_precio != null) ? $lista_precio->precio_lista : 0;
    }
    
    return $lista_precio;
 
  } 

  
     
  //SET DESCUENTO
  public function setDescuentoGeneral($descuentoGral){          
    return $descuentoGral !==  null ? $descuentoGral : 0;		
  }



  public function calcularEfectivo($total, $sumIva, $sumDescuento,$sumRecargo){  
    return $total + $sumIva - $sumDescuento + $sumRecargo;
  }

  public function calcularTotal($total, $sumIva, $sumDescuento,$sumDescuentoPromo,$sumRecargo){  
    return $total + $sumIva - $sumDescuento - $sumDescuentoPromo + $sumRecargo;
  }

  
  public function getDatosFacturacion($comercioId){
    return datos_facturacion::where('comercio_id',$comercioId)
    ->first();
  }

  public function calcularPagoTotal($a, $b, $c = 0, $d = 0, $e = 0){
    return $a + $b + $c + $d + $e;
  }

  public function setChange($metodoPago, $value, $total, $efectivo){  
       return ($metodoPago != 'Efectivo' && $value != '0' ? ($total - $efectivo) : 0 );
  }

  public function calcularIvaCarro($carro){  
     $carro->sum(function($item){
                $descuento_promo = $item->attributes['descuento_promo'] * $item->attributes['cantidad_promo'];   
				return (($item->price * $item->quantity) - $descuento_promo) * ( 1 - ($item->attributes['descuento'] /100) ) * ( 1 + $this->recargo ) * $item->attributes['iva']  ;
		});
  }

  //  DESCUENTOS
  public function sumarDescuento($carro,$recargo){  
       
        //   $this->a_recargar = 1 + $recargo;
        $promedioDescuento = $carro->avg(function ($item) {
            $descuento = $item->attributes['descuento'];
            if (!is_numeric($descuento) || $descuento === '') {
                return 0;
            } else {
                return $descuento / 100;
            }
        });
        
        $this->descuento = $promedioDescuento;

        $sum_descuento = $carro->sum(function($item){
            $descuento = $item->attributes['descuento'];
            if (!is_numeric($descuento) || $descuento === '') {
                $descuento =  0;
            } else {
                $descuento = $descuento;
            }
            $descuento_promo = $item->attributes['descuento_promo'] * $item->attributes['cantidad_promo'];
            $suma =  (($item->price * $item->quantity) - $descuento_promo) * ($descuento / 100);

            return $suma;
        });
        
        return $sum_descuento;
  }
  
 public function sumarDescuentoPromo($carro,$recargo){  

        $sum_descuento_promo = $carro->sum(function($item){
            return ($item->attributes['descuento_promo'] * $item->attributes['cantidad_promo']);
        });
        
        return $sum_descuento_promo;
  }

  
   public function sumarSubtotalConIva($carro){  

        $sum_sobtotal_con_iva = $carro->sum(function($item){
            return ($item->price * $item->quantity * (1 + $item->attributes['iva'])  );
        });
        
        return $sum_sobtotal_con_iva;
  }
  
   public function sumarDescuentoPromoConIva($carro,$recargo){  

        $sum_descuento_promo = $carro->sum(function($item){
            return ($item->attributes['descuento_promo'] * $item->attributes['cantidad_promo'] * (1 + $item->attributes['iva'])  );
        });
        
        return $sum_descuento_promo;
  }
  
  public function calcularDescuento($carro,$recargo){
      
    $this->carro->sum(function($item){
      $descuento_promo = $item->attributes['descuento_promo'] * $item->attributes['cantidad_promo'];
      return (($item->price * $item->quantity) - $descuento_promo) * ($item->attributes['descuento']/100)  ;
    });
  
      
  }

  public function calcularDescuentoTotal($pagoTotal, $descuentoRatio){  
      return $pagoTotal * ($descuentoRatio);
  }

  public function calcularAcobrar($pagoTotal, $descuentoTotal){  
    return 	$pagoTotal - $descuentoTotal;
}


  public function setTipoComprobante($comercioId, $tipoComprobante){

    $this->detalle_facturacion = datos_facturacion::where('datos_facturacions.comercio_id', $comercioId)->first();
   
    if($this->detalle_facturacion != null){
                  
      if($this->detalle_facturacion->condicion_iva == "Monotributo" && empty($tipoComprobante)){
        return "CF";
      }
                  
      if($this->detalle_facturacion->condicion_iva == "IVA Responsable inscripto" && empty($tipoComprobante)){
        return "B";
      }
                  
      if($this->detalle_facturacion->condicion_iva == "IVA exento" && empty($tipoComprobante)){
        return "B";
      }                
    } 
   /* else 
    {
      return  "CF";
    }*/
    return  "CF";
  }

  //Se woocommerce
  public function setWooCommerce($comercio_id)
  {
    $wc = wocommerce::where('comercio_id', $this->comercio_id)->first();

    if($wc == null){
        $this->wc_yes = 0;
    } else {
        $this->wc_yes = $wc->id;
    }
  }  

//METODOS SCANEARCODE //////////////////////////////////////

  //SET BARCODE VARIACION
  public function setBarcodeVariacion($barcode){
   

    $this->product = explode('/',$barcode);
    $product_id = 	$this->product[0];
    $ref_variacion = 	$this->product[1];

    //return dd($product_id );

    $pvd = $this->getProductosVariacionesDatos($product_id, $ref_variacion);
    //return dd($pvd->product_id);
   /* if($pvd !== null){
      return $pvd->product_id."|-|". $pvd->referencia_variacion;
    }else{
      return null;
    }*/

    return ($pvd !== null) ? ($pvd->product_id."|-|". $pvd->referencia_variacion) : null;
              
    // $cant = 1;
      //return $barcode;
  }

  public function productoTieneVariacion($product, $comercioId){
    if($product->producto_tipo == "v") 
    {  
      $this->productos_variaciones_datos =  $this->getProductVariacionDatos($product->id, $comercioId);
      $this->atributos = $this->getAtributos($product->id);  
      $this->product_id = $product->id;  
      $this->variaciones =  $this->getVariaciones($comercioId);  
      $this->emit('variacion-elegir', $product->id);  
      return $this->barcode;
    }  
  }

  public function checkStockYstockDesubierto($tipoUsuario, $productStock, $productStockDescubierto, $product, $sucursal){  
    
    if($tipoUsuario->sucursal === 1 && ($productStock < 1 && $productStockDescubierto->stock_descubierto == 'si') ){
      $this->emit('no-stock','Stock insuficiente, disponibles: '.$product_stock->stock);
      $this->emit('volver-stock', $product->id);
      return;
    }else{

    }

  }

  public function setProductId($variacion, $product, $comecioId){
    
    /*if($sucursal === null){
      return $variacion === 0 ?  $product->id . '-0-' . $comecioId : $product->id.'-' . $variacion;
    }else{
      return $product->id . '-' . $variacion . '-' . $sucursal;
    }
    */
    return $variacion === 0 ?  $product->id . '-0-' . $comecioId : $product->id.'-' . $variacion;
  }

  public function setProductosVariacionesName($variacion){
      $productos_variaciones_datos = productos_variaciones::join('variaciones','variaciones.id','productos_variaciones.variacion_id')
      ->select('variaciones.nombre')
      ->where('productos_variaciones.referencia_id',$variacion)
      ->get();

      $pvd = [];

      foreach ($productos_variaciones_datos as $pv) {

            array_push($pvd, $pv->nombre);

              }

      return implode(" ",$pvd);
  }

  public function setProductosVariacionesDatos($variacion, $product){
    $pdv_cost =  productos_variaciones_datos::where('referencia_variacion', $variacion)->where('product_id',$product->id)->orderBy('id','desc')->first();
    return $pdv_cost;
  }

//SAVE SALES //////////////////////////////////////

  //Validar medio de pago ok
  public function validarMedioPago(){
    //return true;
    if($this->metodo_pago == "Elegir")
    {
      $this->emit('sale-error','DEBE ELEGIR LA FORMA DE PAGO');
      return true;
    }
  }
 
//  Validar medio de pago ok
//  public function validarCash(){
// si el metodo de pago es que no acepta pago parcial 
//    if($this->pago_parcial == 0) {
//    if($this->efectivo == 0)
//    {
//      $this->emit('sale-error','DEBE INGRESAR EL MONTO A PAGAR PARA LOS PAGOS TOTALES');
//      return true;
//    }
//    }
//  }

  //Validar total productos ok
  public function validarTotalProdutos(){
    if($this->total <=0)
		{
			$this->emit('sale-error','AGREGA PRODUCTOS A LA VENTA');
			return true;
		}
  }

  //Validar cliente recordatorio ok
  public function validarClienteRecordatorio(){
      
    if($this->recordatorio != '' && $this->query_id == '') {

      $this->emit('sale-error','AGREGA UN CLIENTE A LA VENTA');
      return true;
    }
  }

  //Validar envio ok
  public function validarEnvio(){
      if ( $this->check_envio == true && (($this->nombre_envio == '') || ($this->ciudad_envio == '') || ($this->direccion_envio == '') || ($this->provincia_envio == "Elegir")))  {
        $this->emit('sale-error','COMPRUEBE LOS DATOS DE ENVIO');
        return true;    
     }
  }
  
  public function ValidacionComprobanteB(){

    $datos_facturacion = datos_facturacion::where('datos_facturacions.comercio_id', $this->comercio_id)->first();
    if($datos_facturacion != null) {
        if($datos_facturacion->condicion_iva == "IVA Responsable inscripto") {
        if($this->sum_iva > 0 && ($this->tipo_comprobante != "A") && ($this->tipo_comprobante != "B")) {    
        $this->tipo_comprobante = "B";
        }
        }
    }
  }

  //Validar tipo de comprobante ok
  public function validarIvaYTipoComprobante(){			
    if($this->sum_iva > 0 && ($this->tipo_comprobante != "A") && ($this->tipo_comprobante != "B")) {    

      $this->emit('sale-error','EL TIPO DE COMPROBANTE DEBE SER "A" O "B" ');
      return true;
    }
  }

  //Validar cliente y tipo de comprobante
  public function validarClienteYTipoDeComprobante(){			
    if($this->tipo_comprobante == "A" && $this->query_id == '')
    {
      $this->emit('sale-error','DEBE AGREGAR UN CLIENTE A LA VENTA');
      return true;	
    }
  }

  //Validar cliente
  public function validarCliente(){			
    if($this->query_id == '')
    {
      $this->emit('sale-error','AGREGA UN CLIENTE A LA VENTA');
      return true;
    }
  }

  //Validar tipo comprobante
  public function validarTipoComprobante(){			
    if($this->tipo_comprobante == '')
    {
      $this->emit('sale-error','AGREGAR EL TIPO DE COMPROBANTE');
      return true;
    }
  }



  //Validar pago parcial
  public function validarPagoParcial(){			
    /*$validationes = [
      $this->validarCliente(),
      $this->validarEstadoPedido(),
      $this->validarTipoComprobante(),
    ];

    if(!in_array(true, $validationes)){
      return true;
    }*/
    if($this->pago_parcial  == 1)
		{
        if($this->query_id == '')
        {
          $this->emit('sale-error','AGREGA UN CLIENTE A LA VENTA');
          return true;
        }

        if($this->tipo_comprobante == '')
        {
          $this->emit('sale-error','AGREGAR EL TIPO DE COMPROBANTE');
          return true;
        }

        if($this->estado_pedido == '')
        {
          $this->emit('sale-error','AGREGA UN ESTADO A LA VENTA');
          return true;
        }
      }
  }

  

  //Set query id
  public function setQueryId(){
    if($this->query_id == '' || $this->query_id == null)
    {
      $this->query_id = 1 ;
    }
  }

  //Validar tipo comprobante
  public function validarClienteYMetodoPago(){		
    if($this->query_id == 1 && $this->metodo_pago_nuevo == '3')
			{
				$this->emit('sale-error','ELIJA UN CLIENTE');
				return true;
			}
  }


  public function validarClienteYefectivoCuenta(){			
   	//Validar query id y efectivo cuenta
     if($this->query_id == 1){
        if($this->relacion_precio_iva == 1){
         $this->efectivo_cuenta = ($this->efectivo + $this->sum_iva + 0.01);   
         } else {
         $this->efectivo_cuenta = ($this->efectivo + 0.01);     
         }
        if($this->efectivo_cuenta < $this->total)
        {
          $this->emit('sale-error','DEBE ELEGIR UN CLIENTE, SI QUEDA UN PAGO A CUENTA.');
          return true;
        }
     }
  }

     //Validar tipo comprobante
     public function validarEstadoPedido(){			
      if($this->tipo_comprobante == '')
      {
        $this->emit('sale-error','AGREGAR EL TIPO DE COMPROBANTE');
        return true;
      }
    }

  //Validacion
  public function validacionDatosSalesSave(){
    $validaciones = [
    //  $this->validarCash(),
      $this->validarMedioPago(),
      $this->validarTotalProdutos(),
      $this->validarClienteRecordatorio(),
      $this->validarEnvio(),
      $this->validarIvaYTipoComprobante(),
      $this->validarClienteYTipoDeComprobante(),
      $this->validarPagoParcial(), 
      $this->setQueryId(),
      $this->validarClienteYMetodoPago(),
      $this->validarClienteYefectivoCuenta(),
    ];

 
    return in_array(true, $validaciones) ? true : false;
  }
  
  

  /*public function setComercioId(){

    $comercio_id = Auth::user()->comercio_id != 1 ? Auth::user()->comercio_id : Auth::user()->id;
    return $comercio_id;
  }*/

  /*
  public function setEstadoPedido(){
    if($this->estado_pedido == '')
		{
			return 'Entregado';
		}else {
      return $this->estado_pedido;
    }
  }
  */

  public function setEstadoPedido($estadoPedido){
    if($estadoPedido == '')
		{
			return 'Entregado';
		}else {
      return $estadoPedido;
    }
  }


  
  /*public function setCaja(){
    if($this->caja == null) {

      return cajas::where('estado',0)->where('comercio_id', $this->comercio_id)->max('id');

      } else {
      return $this->caja;
      }
  }*/
  

  public function setCaja($caja, $comercioId){
    if($caja == null) {
      return cajas::where('estado',0)->where('comercio_id', $comercioId)->max('id');

      } else {
      return $caja;
      }
  }

public function GetCajaElegida(){
 
    	$this->caja_elegida = session('CajaElegida');
		
		if($this->caja_abierta != null) {
	
		if($this->caja_elegida == null){
		$this->caja_elegida = cajas::where('estado',0)->where('eliminado',0)->where('comercio_id',$this->comercio_id)->max('id');  
		if($this->caja_elegida != null){	$this->nro_caja_elegida = cajas::find($this->caja_elegida)->nro_caja; } else {	$this->nro_caja_elegida = 0;}
		} else {
		 $this->nro_caja_elegida= cajas::find($this->caja_elegida)->nro_caja;
		}
		
		return $this->caja_elegida;
		
		}
		
}
  public function setRecordatorioFecha($recordatorio){
    if($recordatorio != ""){
      return  Carbon::now()->add($this->recordatorio, 'day');
    }    
  }

  public function calcularRecargoSaveSale($recargo_total, $sum_iva_recargo,$relacion_precio_iva){
      return $recargo_total + $sum_iva_recargo;
  }
  
  public function calcularDeuda($total,$recargo, $descuentoTotal, $efectivo, $tipo_pago,$sum_iva_pago,$sum_iva_recargo,$relacion_precio_iva){
   
   // reveer esto
   if($efectivo < $total){
   if($relacion_precio_iva == 2) {
   $deuda =  $total - $efectivo;       
   }
   if($relacion_precio_iva == 1 || $relacion_precio_iva == 0) {
   
   $iva_total = $sum_iva_recargo + $sum_iva_pago;
   $subtotal = $total - $iva_total;
  
   $alicuota_iva =  1 + ($iva_total/$subtotal);
   $pago = $efectivo * $alicuota_iva;
   
   $deuda =  $total - $pago;       
   }
       
   } else {
   $deuda =  $total - $descuentoTotal - $efectivo;    
   }

   //dd($deuda);
   return $deuda > 0 ? $deuda : 0;
   
  }

/*
  public function calculoYSetDeuda(){

    $deuda = $this->total - $this->descuento_total - $this->efectivo;
    return  $deuda > 0 ? $deuda : 0;
  }
  */

  public function calcularTotalSale($total, $recargoTotal){
    
    return $total;

  }

  public function setDateCreate($createdAtCambiado){
    return $createdAtCambiado == '' ? Carbon::parse(Carbon::now()) : $createdAtCambiado;
  
  }	

  public function setEfectivoReal($efectivo, $total){
    return $efectivo < $total ? $efectivo : $total;
  }


  public function calcularPorcentage($item, $total){
    return ($item->price  * $item->quantity ) / $total;
  }

  public function calcularRecargoItem($item,$descuento,$recargo){
    $descuento_promo = $item->attributes['descuento_promo'] * $item->attributes['cantidad_promo'];
    return (($item->price * $item->quantity) - $descuento_promo - $descuento) * $recargo;
  }


  public function calcularRecargoTotal($base,$metodoPago){  
    
    if($metodoPago != '') {
    
    // Obtiene el recargo
    $metodopago = metodo_pago::find($metodoPago);   
    
   // dd($metodopago);
    if($metodopago != null) {
    $recargo = $metodopago->recargo;
    
    // Devuelve el recargo
    //dd($base, $recargo/100);
    return ($base) * ($recargo / 100);
    
    } else {
    return 0;
    }
    
  } else {
     return 0; 
  }
    
  }


  public function calcularDescuentoItem($item){
    $descuento_promo = $item->attributes['descuento_promo'] * $item->attributes['cantidad_promo'];    
    return (($item->price * $item->quantity) - $descuento_promo) * ($item->attributes->descuento / 100 );
  }

  public function calcularRecargo($metodoPagoRecargo){
    return $metodoPagoRecargo / 100;
  }

  public function calcularSubtotalSinDescuento($cartTotal, $sumaDescuento){
    return $cartTotal - $sumaDescuento;
  }


  

  

  //SAVE SALES //////////////////////////
  public function SetSaleDB(){
      
    //dd($this->recargo_total);
    
    // setear bien la alicuota_iva 
    $alicuota_descuento_general = $this->descuento_gral_mostrar/100;
    
    return  DB::table('sales')->insertGetId([
      'datos_facturacion_id' => $this->punto_venta_elegido,
      'nro_venta' => $this->nro_venta,
      'subtotal' => $this->subtotal,
      'total' => $this->total,
      'recargo' => $this->recargo_total,
      'descuento' => $this->sum_descuento,
      'alicuota_descuento' => $alicuota_descuento_general,
      'descuento_promo' => $this->sum_descuento_promo,
      'items' => $this->itemsQuantity,
      'tipo_comprobante'  => $this->tipo_comprobante,
      'cash' => 0,
      'change' => $this->change,
      'iva' => $this->sum_iva,
      'relacion_precio_iva' => $this->relacion_precio_iva,
      'alicuota_iva' => $this->iva_elegido,
      'metodo_pago'  => $this->metodo_pago_nuevo,
      'comercio_id' => $this->comercio_id,
      'cliente_id' => $this->query_id,
      'user_id' => $this->usuario_activo,
      'observaciones' => $this->observaciones,
      'canal_venta' => $this->canal_venta,
      'estado_pago' => 'Pendiente',
      'caja' => $this->caja,
      'deuda' => $this->deuda,
      'created_at' => $this->created_at,
      'recordatorio' => $this->recordatorio,
      'status' => $this->estado_pedido,
      'nota_interna' => $this->nota_interna,
      'id_venta' => $this->idVenta
    ]);
  }

  public function setPagosRecordatorioDB($sale){
    
      return DB::table('recordatorios')->insert([
        'titulo' => 'Contactar al cliente',
        'fecha' => $this->recordatorio,
        'descripcion' => $this->nota_interna,
        'sale_id' => $sale,
        'contacto_id' => $this->query_id,
        'tipo_contacto' => 'cliente',
        'color' => 'note-social',
        'comercio_id' => $this->comercio_id,
        'estado' => 0
      ]);
    
  }

  /*public function checkEnvio($checkEnvio){
    if($checkEnvio == true){
      return ecommerce_envio::create([
        'nombre_destinatario' => $this->nombre_envio,
        'dni' => null,
        'sale_id' => $sale,
        'comercio_id' => $this->comercio_id,
        'ciudad' => $this->ciudad_envio,
        'direccion' => $this->direccion_envio,
        'depto' => null,
        'metodo_entrega' => 2,
        'telefono' => $this->telefono_envio,
        'provincia' => $this->provincia_envio,
        'pais' => 1,
        'codigo_postal' => null
      ]);
    }
  }

  public function CheckEnvioCliente($checkEnvioClente){
    if($checkEnvioClente == true){
      return ecommerce_envio::create([
        'nombre_destinatario' => $this->nombre_envio,
        'dni' => null,
        'sale_id' => $sale,
        'comercio_id' => $this->comercio_id,
        'ciudad' => $this->ciudad_envio,
        'direccion' => $this->direccion_envio,
        'depto' => null,
        'metodo_entrega' => 2,
        'telefono' => $this->telefono_envio,
        'provincia' => $this->provincia_envio,
        'pais' => 1,
        'codigo_postal' => null
      ]);
    }
  }*/

  public function checkEnvio($checkEnvio, $sale){
    if($checkEnvio == true){
    
      return ecommerce_envio::create([
        'nombre_destinatario' => $this->nombre_envio,
        'dni' => null,
        'sale_id' => $sale,
        'comercio_id' => $this->comercio_id,
        'ciudad' => $this->ciudad_envio,
        'direccion' => $this->calle_envio." ".$this->altura_envio,
        'depto' => $this->piso_envio." ".$this->depto_envio,
        'metodo_entrega' => 2,
        'telefono' => $this->telefono_envio,
        'provincia' => $this->provincia_envio,
        'pais' => 1,
        'codigo_postal' => $this->cod_postal_envio
      ]);
    }
  }

  public function setMontoPagosFacturasDBOld($pago_parcial,$monto,$recargo,$iva_pago,$iva_recargo,$relacion_precio_iva){
  
  // si el pago es total
  if($pago_parcial == 0){
  $monto_seted = $monto - $recargo - $iva_pago - $iva_recargo;     
  } else {
  
  if($relacion_precio_iva == 2) {      
  $monto_seted = $monto - $iva_pago;    
  }
  if($relacion_precio_iva == 1 || $relacion_precio_iva == 0) {      
  $monto_seted = $monto;    
  }
  
  }  
  
  //dd($monto_seted);
  return $monto_seted;
  }
  
  public function setMontoPagosFacturasDB($pago_parcial,$efectivo,$total,$recargo,$iva_pago,$iva_recargo,$relacion_precio_iva){

  $monto = $this->setEfectivoReal($efectivo, $total);
  
  if($efectivo < $total) {
  $monto_seted = $monto - $recargo;    
  } else {
  $monto_seted = $monto - $recargo - $iva_pago - $iva_recargo;     
  }
  
  //dd($monto_seted);
  return $monto_seted;
  }

public function SetPagosFacturasSucursalDB($pago_id,$monto, $recargo, $cambio, $compra_id, $banco_id,$comercio_id){
    
    $estado_pago = $this->GetPlazoAcreditacionPago($banco_id);
        
    $pago = pagos_facturas::find($pago_id);
    
    $monto = $pago->monto + $pago->recargo + $pago->iva_recargo + $pago->iva_pago;
    
    return DB::table('pagos_facturas')->insert([
        'estado_pago' => $estado_pago,
        'monto_compra' => $monto,
        'id_compra' => $compra_id,
        'caja' => null,
        'comercio_id' => $comercio_id,
        'metodo_pago'  => null,
        'banco_id' => $banco_id,
        'eliminado' => 0,
        'tipo_pago' => 1,
        'cliente_id' => $this->query_id,
        'pago_sucursal_id' => $pago_id
      ]);

    
}  
  public function setPagosFacturasDB($efectivo,$total, $recargo, $cambio, $sale, $metodoPago,$comercio_id,$relacion_precio_iva,$iva_pago,$iva_recargo){
    
    $estado_pago = $this->GetPlazoAcreditacionPago($metodoPago);
    
    $banco_id = $this->getBanco($metodoPago);
    
    $monto = $this->setMontoPagosFacturasDB($this->pago_parcial,$efectivo,$total,$recargo,$iva_pago,$iva_recargo,$relacion_precio_iva);
    //dd($monto);
    // testeado precio + iva (pago total) OK
    
    $array_pagos = [
        'estado_pago' => $estado_pago,
        'monto' => $monto,
        'recargo' => $recargo,
        'iva_pago' => $iva_pago,
        'iva_recargo' => $iva_recargo,
        'id_factura' => $sale,
        'caja' => $this->caja,
        'comercio_id' => $comercio_id,
        'metodo_pago'  => $metodoPago,
        'banco_id' => $banco_id,
        'eliminado' => 0,
        'tipo_pago' => 1,
        'cliente_id' => $this->query_id
      ];
    
    //dd($array_pagos);  
    
    return DB::table('pagos_facturas')->insertGetId($array_pagos);
    
  }

  public function setChequesDB($sale){
    return DB::table('cheques')->insert([
      'nro_cheque' =>  $this->nro_cheque_ch,
      'banco'  => $this->banco_ch,
      'emisor' =>  $this->emisor_ch,
      'cliente_id' => $this->query_id,
      'monto' =>  $this->efectivo_real,
      'sale_id' => $sale,
      'comercio_id' => $this->comercio_id,
      'status' => 'Activo',
      'fecha_emision' => $this->fecha_emision_ch,
      'fecha_cobro' =>  $this->fecha_emision_ch
    ]);
  }

public function setSaleDetailsDB($sale, $item,$compra_id){
    
   
    $iva_total = $this->sumarIVATOTALProducto($item, $this->recargo);

    $precio_original = $item->attributes->precio_original;    

    if($this->estado_pedido == "Entregado"){$estado = 1;} else {$estado = 0;}
    
    $sale_detail_id = DB::table('sale_details')->insertGetId([
      'precio_original' => $precio_original,
      'price' => $item->price,
      'recargo' => $this->recargo_item,
      'descuento' => $this->descuento_item,
      'quantity' => $item->quantity,
      'metodo_pago'  => $this->metodo_pago_nuevo,
      'product_id' => $item->attributes->product_id,
      'referencia_variacion' => $item->attributes->referencia_variacion,
      'relacion_precio_iva' => $item->attributes->relacion_precio_iva,
      'product_name' => $item->name,
      'iva' => $item->attributes->iva,
      'iva_total' => $iva_total,
      'cost' => $item->attributes->cost,
      'product_barcode' => $item->attributes->barcode,
      'seccionalmacen_id' => $item->attributes->seccionalmacen_id,
      'comercio_id' => $item->attributes->comercio_id,
      'comentario' => $item->attributes->comentario,
      'id_promo' => $item->attributes->id_promo,
      'nombre_promo' => $item->attributes->nombre_promo,
      'cantidad_promo' => $item->attributes->cantidad_promo,
      'descuento_promo' => $item->attributes->descuento_promo,
//      'tipo_unidad_medida' => $item->attributes->tipo_unidad_medida,
//      'cantidad_unidad_medida' => $item->attributes->cantidad_unidad_medida,
      'estado' => $estado,
      'sale_id' => $sale,
      'stock_de_sucursal_id' => $item->attributes->sucursal_id,
      'caja' => $this->caja,
      'canal_venta' => $this->canal_venta,
      'cliente_id' => $this->query_id
    ]);
    
    if($compra_id != null){
    $compra = compras_proveedores::find($compra_id);
    $this->SetDetalleCompraEnSucursal($compra,$item,$sale_detail_id,$estado);
    }
					    
    return $sale_detail_id;
  }
  public function setHistoricoStockDB($item, $sale, $productosStockSucursalesNuevo,$EstadoPedido){
      
   if($EstadoPedido ==  "Entregado") {
    
    return DB::table('historico_stocks')->insert([
      'tipo_movimiento' => 1,
      'producto_id' => $item->attributes->product_id,
      'sale_id' => $sale,
      'referencia_variacion' => $item->attributes->referencia_variacion,
      'cantidad_movimiento' => -$item->quantity,
      'stock' => $productosStockSucursalesNuevo,
      'usuario_id' => $this->usuario_activo,
      'comercio_id'  => $item->attributes->sucursal_id
    ]);
  }
  
  }

  public function setUpdateStockDB($tipoUsuarioSucursal, $item, $productosStockSucursalesNuevo,$productosStockRealSucursalesNuevo ){ 
    
    if($tipoUsuarioSucursal != 1){
     
      return DB::table('productos_stock_sucursales')
      ->where('product_id',$item->attributes->product_id)
      ->where('referencia_variacion',$item->attributes->referencia_variacion)
      ->where('comercio_id',$item->attributes->sucursal_id)
      ->limit(1)
      ->update([
       // 'stock' => $productosStockSucursalesNuevo,
        'stock_real' => $productosStockRealSucursalesNuevo
        ]);
    }else{
      return DB::table('productos_stock_sucursales')
							->where('product_id',$item->attributes->product_id)
							->where('referencia_variacion',$item->attributes->referencia_variacion)
							->where('sucursal_id',$item->attributes->sucursal_id)
							->limit(1)
							->update([
							 //   'stock' => $productosStockSucursalesNuevo, 
							    'stock_real' => $productosStockRealSucursalesNuevo
							    ]);
    }
  
  }


  public function setAsistenteProduccionDB($product, $item, $cantidadAsistente, $sale){
    return 	asistente_produccions::create([
      'product_name' => $product->name,
      'product_id' => $product->id,
      'product_barcode' => $product->barcode,
      'referencia_variacion' => $item->attributes->referencia_variacion,
      'cantidad' => $cantidadAsistente,
      'sale_id' => $sale,
      'estado' => 0
    ]);
  }

  public function calcularProductStockSucursalesNuevo($item, $productosStockSucursales){
      if(($productosStockSucursales->stock - $item->quantity) <= 0){
        return 0;
      }else {
        return $productosStockSucursales->stock - $item->quantity;
      }
   
  }
  
  public function calcularProductStockRealSucursalesNuevo($item, $productosStockSucursales,$estado){
     
      // Si esta entregado se descuenta la cantidad vendida del stock real
      if($estado == 'Entregado') {
      
      if(($productosStockSucursales->stock_real - $item->quantity) <= 0){
        return 0;
      }else {
        return $productosStockSucursales->stock_real - $item->quantity;
      }
      
      // Si NO esta entregado NO se descuenta la cantidad vendida del stock real
      } else {
       return $productosStockSucursales->stock_real;
      }
   
  }

  public function setCantidadAsistente($item,$ssd){
      return  $ssd > 0 ? $ssd : -$item->quantity;    
  }

  public function getMail($ventaId){
    return Sale::join('clientes_mostradors as c','c.id','sales.cliente_id')
			->select('c.email', 'sales.cash','sales.status')
			->where('sales.id', $ventaId)
			->get();
}

  public function wooCommerce($product,$item,$stock){

    $wc = wocommerce::where('comercio_id', $this->comercio_id)->first();
    
    if($wc != null){
      $woocommerce = new Client(
        $wc->url,
        $wc->ck,
        $wc->cs,
        ['version' => 'wc/v3',]
      );
    
    
     if($product->wc_canal == 1) {
         
        // Aca hace la comprobacion de si se corresponden el product_id y la variacion
        
        
        
        $data = [ "stock_quantity" => $stock ];
        
        // si el producto es simple
        
        if($product->producto_tipo == "s") {
        $this->wocommerce_product_id = 'products/'. $product->wc_product_id;
        $wc_response = $woocommerce->put($this->wocommerce_product_id , $data);
        }
       
        // si el producto es variable
       
        if($product->producto_tipo == "v") {
        
        $params = [
        'search' => $product->name
        ];
    
        $result = $woocommerce->get('products', $params);

        $buscar = 'products/'.$result[0]->id.'/variations';
        $comprobacion = $woocommerce->get($buscar);
        
        if(isset($comprobacion)) {
        $variacion = productos_variaciones_datos::where('referencia_variacion',$item->attributes->referencia_variacion)->where('eliminado',0)->first();
        
        $id_buscado = $variacion->wc_variacion_id;
        $encontrado = false;
        $noEncontrados = [];
        
        foreach ($comprobacion as $c) {
            if ($c->id == $id_buscado) {
                $encontrado = true;
                // El id coincide, realiza las acciones necesarias aquí
                // Puedes agregar código para manejar el caso cuando el id coincide
                break; // Puedes agregar un break si deseas salir del bucle después de encontrar una coincidencia
            }
        }
        
        if ($encontrado) {
            // Se encontró al menos una coincidencia
            $wc_response = $woocommerce->put('products/'.$product->wc_product_id.'/variations/'.$variacion->wc_variacion_id, $data); 
        } else {
            array_push($noEncontrados, $variacion->wc_variacion_id);
            // No se encontró ninguna coincidencia
        }

        
        }
        
        }
        
       // dd($wc_response);
        
      }
    }
    
    
    return $noEncontrados;
  }



    public function saveSalesReset(){
    
      $this->lista_precios_elegida = 0;
      session(['ListaPrecios' => $this->lista_precios_elegida]);
      $this->SetNombreLista($this->lista_precios_elegida);
      $this->sum_descuento_promo = 0;
      $this->sucursal_id_cliente = null;
      $this->usuario_activo = Auth::user()->id;
      $this->efectivo =0;
      $this->descuento =0;
      session(['metodos_pago_dividido' => null]);
      $this->monto_ap_div = [];    
      $this->metodos_pago_dividido = [];
      $this->efectivo_dividido = [];
      $this->a_cobrar = [];
      $this->recargo_div = [];
      $this->recargo_total_div = [];
      $this->iva_total_dividido = [];
      session(['EnvioElegido' => 1]);
      $this->idVenta = null;
      session(['idVenta' => null]);
      $this->check_envio = false;
      $this->check_retiro_sucursal = true;
      $this->check_envio_cliente = false;
      $this->checked_envio = "";
      $this->checked_envio_cliente = "";
      $this->checked_retiro_sucursal = "checked";
      
      $this->paso1_style = "block;";
      $this->paso2_style = "none;";
      $this->paso3_style = "none;";
      $this->nro_paso = 1;
      
      // Agregado 6-6-2023
      $this->envio_visible = "none";
      //
      
      $this->tipo_pago = 1;
      $this->metodo_pago = 1;
      $this->recordatorio = null;
      $this->nota_interna = '';
      $this->sum_descuento = 0;
      $this->metodo_pago_nuevo = 1;
      $this->change =0;
      $this->sum_iva =0;
      $this->subtotal =0;
      $this->created_at_cambiado = '';
      $this->tipo_documento = '';
      $this->fecha_pedido = Carbon::parse(Carbon::now())->format('d-m-Y H:i');
      $this->query = '';
      session(['MetodoPago' => '']);
      $descuento_gral_mostrar = 0;
      $this->descuento_gral_mostrar = 0;
      session(['DescuentoGral' => 0]);
      session(['IdCliente' => '']);
      session(['NombreCliente' => '']);
      session(['PagoDividido' => 0]);
      // Pago dividido   
      $this->es_pago_dividido = session('PagoDividido');
      $this->style_pago_dividido = "display:none;";
      $this->style_metodo_pago = "display:block;";    
        
      // Lista de precios
      $this->SetListaPreciosDefecto();
      $this->SetNombreLista($this->lista_precios_elegida);

      $df_iva = $this->getDatosFacturacion($this->comercio_id);
      if($df_iva != null) {
          $df_iva_defecto = $df_iva->iva_defecto; 
          $df_relacion_precio_iva = $df_iva->relacion_precio_iva;
      } else {
          $df_iva_defecto = 0;
          $df_relacion_precio_iva = 0;
      } 
      session(['IvaElegido' => $df_iva_defecto]);
      $this->iva_elegido = $df_iva_defecto;
      $this->relacion_precio_iva = $df_relacion_precio_iva;
      
      
      // ACA ENVIO 
      session(['EnvioVisible' => null]);
      $this->checked_envio_cliente = null;
      //				
      $this->query_id = 1;
      $this->recargo = 0;
      $this->caja = null;
      $this->recargo_total = 0;
      $this->descuento_total = 0;
      $componentName = 'Agregar cliente';
      $this->contacts = [];
      $this->highlightIndex = 0;
      $this->usuario_activo = Auth::user()->id;
      $this->date = Carbon::parse(Carbon::now())->format('Y-m-d');
      $this->canal_venta = 'Mostrador';
      $this->efectivo =0;
      $this->change =0;
      $this->sum_subtotal_con_iva = 0;
      $this->sum_descuento_promo_con_iva = 0;
      $this->banco_ch = '';
      $this->emisor_ch = '';
      $this->nro_cheque_ch = '';
      $this->fecha_cobro_ch = '';
      $this->fecha_emision_ch = '';
      $this->observaciones ='';
      $this->total = Cart::getTotal();
      $this->itemsQuantity = Cart::getTotalQuantity();

      if (Auth::user()->pago_parcial == 1) {
        $this->check = 'checked';
        $this->estado_pedido = '';

      } 
      else 
      {
        $this->check = '';
        $this->estado_pedido = 'Entregado';	
      }
      
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
    		
    //	
    
   // dd($this->lista_precios_elegida);

  }


public function SetNombreLista($lista_id) {
    
    if($lista_id == 0) {
    $this->nombre_lista_precios = "Precio base";    
    } elseif($lista_id == 1) {
    $this->nombre_lista_precios = "Precio interno - Venta a sucursal";    
    } else {
    $this->nombre_lista_precios = lista_precios::find($lista_id)->nombre;    
    }
    
}


public function SetListaPreciosDefecto() {

$usuario_lista_defecto = User::find($this->comercio_id);	

    if(session('ListaPrecios') != null) {
        $this->lista_precios_elegida = session('ListaPrecios');
    } else {
        // aca tenemos que obtener la lista de precios defecto y traerla
        $this->lista_precios_elegida = $usuario_lista_defecto->lista_defecto;
        session(['ListaPrecios' => $this->lista_precios_elegida]);
    }
    
}
 
 
public function SetStockDisponible($product_id,$casaCentralId,$sucursal,$variacion,$cant){
   
  // dd($product_id,$casaCentralId,$sucursal,$variacion,$cant);
   
    $pss = productos_stock_sucursales::where('productos_stock_sucursales.product_id', $product_id)
    ->where('productos_stock_sucursales.referencia_variacion', $variacion)
    ->where('productos_stock_sucursales.sucursal_id', $sucursal)
    ->where('productos_stock_sucursales.comercio_id', $this->casa_central_id)
    ->first();
 
    //dd($pss);
    //dd($product_id,$variacion,$sucursal,$casaCentralId);
    
    $stock_real = $pss->stock_real;
    $nuevo_stock = intval($pss->stock + $cant);
    
    $pss->update([
      'stock' => $nuevo_stock,
      'stock_real' => $stock_real
    ]);
   
    return $pss->stock;
    
}


public function SetStockDisponibleCarro($product_id,$casaCentralId,$sucursal,$variacion,$stock_disponible_carro){

    $pss = productos_stock_sucursales::where('productos_stock_sucursales.product_id', $product_id)
    ->where('productos_stock_sucursales.referencia_variacion', $variacion)
    ->where('productos_stock_sucursales.sucursal_id', $sucursal)
    ->where('productos_stock_sucursales.comercio_id', $this->casa_central_id)
    ->first();
 
    $stock_real = $pss->stock_real;
    
    $pss->update([
      'stock' => $stock_disponible_carro,
      'stock_real' => $stock_real
    ]);
   
    return $pss->stock;
    
}

public function SetAlmacenId($product_id,$casaCentralId,$sucursal,$variacion){
   
    $pss = productos_stock_sucursales::where('productos_stock_sucursales.product_id', $product_id)
    ->where('productos_stock_sucursales.referencia_variacion', $variacion)
    ->where('productos_stock_sucursales.sucursal_id', $sucursal)
    ->where('productos_stock_sucursales.comercio_id', $this->casa_central_id)
    ->first();
   
    return $pss->almacen_id;
    
}

public function ErrorRelacionPrecioIva(){
    $this->emit('msg-error','Para modificzar el IVA debe hacerlo en configuracion');
}
 
 
 
// PARA SUCURSALES


public function calcularProductStockSucursalesNuevoVentaSucursal($item, $productosStockSucursales,$estado){
      if($estado == 'Entregado') {
      return $productosStockSucursales->stock + $item->quantity;
      } else {
      return $productosStockSucursales->stock;
      }
  }
  
  public function calcularProductStockRealSucursalesNuevoVentaSucursal($item, $productosStockSucursales,$estado){
     
      // Si esta entregado se suma la cantidad vendida del stock real
      if($estado == 'Entregado') {
        return $productosStockSucursales->stock_real + $item->quantity;
      // Si NO esta entregado NO se descuenta la cantidad vendida del stock real
      } else {
       return $productosStockSucursales->stock_real;
      }
   
  }  
  
// 9-1-2024  
public function setUpdateStockDBVentaSucursal($compra,$tipoUsuarioSucursal, $item, $productosStockSucursalesNuevo,$productosStockRealSucursalesNuevo, $EstadoPedido, $sucursal_id_venta ){ 
  
    if($EstadoPedido ==  "Entregado") {
  
      $s_actualizado = DB::table('productos_stock_sucursales')
      ->where('product_id',$item->attributes->product_id)
      ->where('referencia_variacion',$item->attributes->referencia_variacion)
      ->where('comercio_id',$item->attributes->sucursal_id)
      ->where('sucursal_id',$sucursal_id_venta)
      ->limit(1)
      ->update([
        'stock' => $productosStockSucursalesNuevo,
        'stock_real' => $productosStockRealSucursalesNuevo
        ]);
      
      //dd($s_actualizado);
      $this->SetHistoricoStockCompraEnSucursal($compra,$item,$productosStockRealSucursalesNuevo);

      return $s_actualizado; 
        
    } else {
    
      $s_actualizado = DB::table('productos_stock_sucursales')
      ->where('product_id',$item->attributes->product_id)
      ->where('referencia_variacion',$item->attributes->referencia_variacion)
      ->where('comercio_id',$item->attributes->sucursal_id)
      ->where('sucursal_id',$sucursal_id_venta)
      ->limit(1)
      ->update([
        'stock' => $productosStockSucursalesNuevo,
        'stock_real' => $productosStockRealSucursalesNuevo
        ]);
        
        return $s_actualizado;
    }
  
  }
//


  public function SetNroVenta(){
      $sale = Sale::where('comercio_id',$this->comercio_id)->orderBy('id','desc')->first();
      
      if($sale != null) {
      if($sale->nro_venta != null) {
      $nro_venta = $sale->nro_venta + 1;    
      } else {
      $nro_venta = 1;    
      }
          
      } else {$nro_venta = 1;}
      
      return $nro_venta;
      
    }
    
    
    // 1-12-2023
    
    public function SetPrecioSucursal($product_id,$referencia_variacion,$lista_precios_elegida) {
    
 //  dd($product_id,$referencia_variacion,$lista_precios_elegida);
    
    $p = Product::find($product_id);
    
    if($p->producto_tipo == "s") {
        $precio_sucursales = $p->precio_interno;
        return $precio_sucursales;
    }
    
    
    if($p->producto_tipo == "v") {
        $precio_sucursales = productos_variaciones_datos::where('product_id',$product_id)
        ->where('referencia_variacion',$referencia_variacion)
        ->where('eliminado',0)->first()->precio_interno;
        
        return $precio_sucursales;
    }
        
    }
  
      
    // TODOS LOS STOCK 
    

public function getProductStockSucursales($item, $tipoUsuarioSucursal){

    if($tipoUsuarioSucursal != 1 ){
      return productos_stock_sucursales::where('product_id',$item->attributes->product_id)
      ->where('referencia_variacion',$item->attributes->referencia_variacion)
      ->where('comercio_id',$item->attributes->sucursal_id)
      ->first();
    }else{
      return  productos_stock_sucursales::where('product_id',$item->attributes->product_id)
						->where('referencia_variacion',$item->attributes->referencia_variacion)
						->where('sucursal_id',$item->attributes->sucursal_id)->first();
    }
     
  }
  
  
public function getProductStockSucursalesVentaSucursal($item, $sucursal_id){

    $stock = productos_stock_sucursales::where('product_id',$item->attributes->product_id)
      ->where('referencia_variacion',$item->attributes->referencia_variacion)
      ->where('comercio_id',$item->attributes->sucursal_id)
      ->where('sucursal_id',$sucursal_id)
      ->first();
      
    return $stock;

  }

public function getProductStock($productId = null, $casaCentralId = null, $variacion = 0, $sucursal = null){
    
      $sucursal_id = $this->SetSucursalOrCero($casaCentralId,$sucursal);
    
      return productos_stock_sucursales::join('products','products.id','productos_stock_sucursales.product_id')
      ->where('productos_stock_sucursales.product_id', $productId)
       ->where('productos_stock_sucursales.comercio_id', $casaCentralId)
      ->where('productos_stock_sucursales.referencia_variacion', $variacion)
      ->where('productos_stock_sucursales.sucursal_id', $sucursal_id)
      ->where('products.eliminado', 0)
      ->select('productos_stock_sucursales.*')
      ->first();
      
  }
  
public function SetSucursalOrCero($casa_central_id,$sucursal_id){
    
    if($casa_central_id == $sucursal_id){
        return 0;
    } else {
        return $sucursal_id;
    }
    
}

  // 9-1-2024
  public function SetCompraEnSucursal($sale){

    $venta = Sale::find($sale);
    $cliente = ClientesMostrador::where('id',$this->query_id)->first();
  	$sucursal_id_compra = sucursales::find($cliente->sucursal_id)->sucursal_id;
  	
    $nro_compra = $this->SetNroCompra($sucursal_id_compra);
    if($venta->status == "Pendiente"){$estado = 1;}
    if($venta->status == "En proceso"){$estado = 2;}
    if($venta->status == "Entregado"){$estado = 3;}
    
    $porcentaje_descuento = $venta->descuento/$this->subtotal;
    
    // setear la compra 
    return  DB::table('compras_proveedores')->insertGetId([
              'nro_compra' => $nro_compra,
              'subtotal' => $this->subtotal,
              'iva' => $this->sum_iva,
              'total' => $this->total,
              'items' => $this->itemsQuantity,
              'deuda' =>$this->deuda,
              'recargos' => $this->recargo_total,
              'observaciones' => $this->recordatorio,
              'tipo_factura' => $this->tipo_comprobante,
              'numero_factura' => null,
              'proveedor_id' => 2,
              'comercio_id' => $sucursal_id_compra,
              'status' => $estado,
              'created_at' => $this->created_at,
              'sale_casa_central' => $venta->id,
              'descuento' => $venta->descuento,
              'porcentaje_descuento' => $porcentaje_descuento
    ]);
           
          
  }
  
  public function SetDetalleCompraEnSucursal($compra,$item,$sale_detail_id,$estado){
    
    $subtotal = $item->price*$item->quantity;
    $subtotal = $subtotal - $this->descuento_item + $this->recargo_item;
    $iva_total = $subtotal*$item->attributes->iva;
    
    return  DB::table('detalle_compra_proveedores')->insertGetId([
        'producto_id' => $item->attributes->product_id,
        'referencia_variacion' => $item->attributes->referencia_variacion,
        'precio' => $item->price,
        'nombre' => $item->name,
        'barcode' => $item->attributes->barcode,
        'cantidad' => $item->quantity,
        'iva' => $iva_total,
        'alicuota_iva' => $item->attributes->iva,
        'compra_id' => $compra->id,
        'comercio_id' => $compra->comercio_id,
        'eliminado' => 0,
        'sale_detail_casa_central' => $sale_detail_id,
        'estado' => $estado
      ]);



  }
  
  public function SetHistoricoStockCompraEnSucursal($compra,$item,$stock){
    return  DB::table('historico_stocks')->insertGetId([
      'tipo_movimiento' => 9,
      'producto_id' => $item->attributes->product_id,
      'sale_id' => $compra->id,
      'referencia_variacion' => $item->attributes->referencia_variacion,
      'cantidad_movimiento' => $item->quantity,
      'stock' => $stock,
      'usuario_id' => $compra->comercio_id,
      'comercio_id'  => $compra->comercio_id,
    ]);      
  }
  
  
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




public function setCanalVentas($canal_ventas,$sucursal_id) {
    $return = ($sucursal_id != null || $sucursal_id != 0) ?  "Venta a sucursales"    : $canal_ventas;
    return $return;
}


public function SetearIVAProducto($precio_original,$exist,$df,$product,$variacion){

if($df != null) {$relacion_precio_iva = $df->relacion_precio_iva;} else {$relacion_precio_iva = 0;}

// si existe tenemos que tomar el iva que existe 
if($exist != null){
$iva = $exist->attributes['iva'];    
$precio = $exist->attributes['precio_original'];
$descuento_promo = $exist->attributes['descuento_promo'];
$descuento = $exist->attributes['descuento'];
if (!is_numeric($descuento) || $descuento === '') {
$descuento =  0;
} else {
$descuento = $descuento;
}
} 
// sino tenemos que tomar el IVA por defecto del producto
if($exist == null){
$producto_iva = productos_ivas::where('product_id',$product->id)->where('sucursal_id',$this->comercio_id)->first();
$iva = $producto_iva->iva ?? 0;     
$precio = $precio_original;
$descuento_promo = 0;
$descuento = 0;
} 

$this->setRelacionPrecioIva($relacion_precio_iva, $precio, $iva,$descuento_promo,$descuento);

}

//SET IVA Y PRECIO RELACION IVA
public function setRelacionPrecioIva($relacionPrecioIva, $precioLista, $ivaPorDefecto,$descuento_promo,$descuento){

      if($relacionPrecioIva == 2) {                        
        $this->iva = $ivaPorDefecto;
        $this->precio = $precioLista / ( 1 + $ivaPorDefecto) ;
        $this->relacion_precio_iva = $relacionPrecioIva;  
        // $this->precio,$precioLista,$relacionPrecioIva,$ivaPorDefecto, $descuento_promo, $descuento
      //  $this->iva_total  = $this->CalcularIvaTotalProducto($this->precio,$precioLista,$relacionPrecioIva,$ivaPorDefecto,$descuento_promo,$descuento,$this->metodo_pago);
      } 
      if($relacionPrecioIva == 1) {
          
        $this->iva = $ivaPorDefecto;
        $this->precio = $precioLista;
        $this->relacion_precio_iva = $relacionPrecioIva;
      //  $this->iva_total  = $this->CalcularIvaTotalProducto($this->precio,$precioLista,$relacionPrecioIva,$ivaPorDefecto,$descuento_promo,$descuento,$this->metodo_pago);
      }
      
      if($relacionPrecioIva == 0) {
        $this->iva = $ivaPorDefecto ?? 0;
        $this->precio = $precioLista;
        $this->relacion_precio_iva = $relacionPrecioIva;
     //   $this->iva_total  = 0;
      }    
      
    //  dd($this->iva,$this->precio,$this->relacion_precio_iva,$this->iva_total);
  }


public function sumarIVASUBTOTAL($carro, $alicuota_recargo){  
    $sum_iva = $carro->sum(function($item) use ($alicuota_recargo) {
          
        // Diferencia precio
        $diferencia_precio = ($item->attributes['precio_original'] - $item->price) * $item->quantity;

        // Alicuota IVA
        $iva = $item->attributes['iva'];
        
        // Subtotal 
        $subtotal_sin_iva = $item->price * $item->quantity;

        // Descuento promo
        $descuento_promo_sin_iva = $item->attributes['descuento_promo'] * $item->attributes['cantidad_promo'];
        $diferencia_descuento_promo = $descuento_promo_sin_iva * $iva;
 
        $subtotal_sin_promo = $subtotal_sin_iva - $descuento_promo_sin_iva;
        
        // Descuento gral
        $discount = $item->attributes['descuento'];

        if (!is_numeric($discount) || $discount === '') {
        $descuento =  0;
        } else {
        $descuento = $discount;
        }

        $descuento_gral = $subtotal_sin_promo * ($descuento/100);
        $diferencia_descuento_gral = $descuento_gral * $iva;
        
        if($this->pago_parcial == "1"){
        if($item->attributes['relacion_precio_iva'] == 2){ $base_recargo = $this->efectivo/(1+$iva); } else { $base_recargo = $this->efectivo; }
        } else {
        $base_recargo = $subtotal_sin_promo - $descuento_gral;    
        } 

        if($item->attributes['relacion_precio_iva'] == 2) { 
            $iva_total = $diferencia_precio - $diferencia_descuento_gral - $diferencia_descuento_promo; 
        }
        
        if($item->attributes['relacion_precio_iva'] == 1) { 
            $iva_total = ($subtotal_sin_iva - $descuento_promo_sin_iva - $descuento_gral) * $iva; 
        }

        if($item->attributes['relacion_precio_iva'] == 0) { 
            $iva_total = 0; 
        }
     
        return $iva_total;
    });
        
    return $sum_iva ?? 0;
}

public function sumarIVATOTAL($carro, $alicuota_recargo){  
    $sum_iva = $carro->sum(function($item) use ($alicuota_recargo) {
          
        // Diferencia precio
        $diferencia_precio = ($item->attributes['precio_original'] - $item->price) * $item->quantity;

        // Alicuota IVA
        $iva = $item->attributes['iva'];
        
        // Subtotal 
        $subtotal_sin_iva = $item->price * $item->quantity;

        // Descuento promo
        $descuento_promo_sin_iva = $item->attributes['descuento_promo'] * $item->attributes['cantidad_promo'];
        $diferencia_descuento_promo = $descuento_promo_sin_iva * $iva;
 
        $subtotal_sin_promo = $subtotal_sin_iva - $descuento_promo_sin_iva;
        
        // Descuento gral
        $discount = $item->attributes['descuento'];

        if (!is_numeric($discount) || $discount === '') {
        $descuento =  0;
        } else {
        $descuento = $discount;
        }

        $descuento_gral = $subtotal_sin_promo * ($descuento/100);
        $diferencia_descuento_gral = $descuento_gral * $iva;
        
        if($this->pago_parcial == "1"){
        if($item->attributes['relacion_precio_iva'] == 2){ $base_recargo = $this->efectivo/(1+$iva); } else { $base_recargo = $this->efectivo; }
        } else {
        $base_recargo = $subtotal_sin_promo - $descuento_gral;    
        } 
        
        // Recargo gral
        $recargo_gral = $base_recargo * $alicuota_recargo;
        $diferencia_recargo_gral = $recargo_gral * $iva;
       
        if($item->attributes['relacion_precio_iva'] == 2) { 
            $iva_total = $diferencia_precio - $diferencia_descuento_gral + $diferencia_recargo_gral - $diferencia_descuento_promo; 
        }
        
        if($item->attributes['relacion_precio_iva'] == 1) { 
            $iva_total = ($subtotal_sin_iva - $descuento_promo_sin_iva - $descuento_gral + $recargo_gral) * $iva; 
        }

        if($item->attributes['relacion_precio_iva'] == 0) { 
            $iva_total = 0; 
        }
     
        return $iva_total;
    });
        
    return $sum_iva ?? 0;
}

public function sumarIVATOTALProducto($item, $alicuota_recargo){
          
        // Diferencia precio
        $diferencia_precio = ($item->attributes['precio_original'] - $item->price) * $item->quantity;

        // Alicuota IVA
        $iva = $item->attributes['iva'];

        // Subtotal 
        $subtotal_sin_iva = $item->price * $item->quantity;

        // Descuento promo
        $descuento_promo_sin_iva = $item->attributes['descuento_promo'] * $item->attributes['cantidad_promo'];
        $diferencia_descuento_promo = $descuento_promo_sin_iva * $iva;
 
        $subtotal_sin_promo = $subtotal_sin_iva - $descuento_promo_sin_iva;
        
        // Descuento gral
        $descuento_gral = $subtotal_sin_promo * ($item->attributes['descuento']/100);
        $diferencia_descuento_gral = $descuento_gral * $iva;
 
        $subtotal_sin_promo_sin_desc_gral = $subtotal_sin_promo - $descuento_gral;
        
        // Recargo gral
        $recargo_gral = $subtotal_sin_promo_sin_desc_gral * $alicuota_recargo;
        $diferencia_recargo_gral = $recargo_gral * $iva;

        
        if($item->attributes['relacion_precio_iva'] == 2) { 
            $iva_total = $diferencia_precio - $diferencia_descuento_gral + $diferencia_recargo_gral - $diferencia_descuento_promo; 
        }
        
        if($item->attributes['relacion_precio_iva'] == 1) { 
            $iva_total = ($subtotal_sin_iva - $descuento_promo_sin_iva - $descuento_gral + $recargo_gral) * $iva; 
        }

        if($item->attributes['relacion_precio_iva'] == 0) { 
            $iva_total = 0; 
        }
     
        return $iva_total;
        
}

public function CalcularIvaTotalProducto($precio,$precio_lista,$relacion_precio_iva,$iva,$descuento_promo,$alicuota_descuento_gral,$metodo_pago_id){
    
    // Seteamos el recargo
    $metodo_pago = metodo_pago::find($metodo_pago_id);
    $alicuota_recargo = ($metodo_pago->recargo/100);

    $descuento_gral = ($precio - $descuento_promo) * ($alicuota_descuento_gral/100);
    $recargo_gral = ($precio - $descuento_promo - $descuento_gral) * $alicuota_recargo;

    $diferencia_descuento_gral = ($descuento_gral * (1 + $iva)) - $descuento_gral;
    $diferencia_descuento_promo = ($descuento_promo * (1 + $iva)) - $descuento_promo;
    $diferencia_recargo_gral = ($recargo_gral * (1 + $iva)) - $recargo_gral;

    //dd($precio_lista,$precio,$diferencia_descuento_promo,$diferencia_descuento_gral);
    
    if($relacion_precio_iva == 1) { $iva_total = ($precio_lista - $descuento_promo - $descuento) * $iva; }
    if($relacion_precio_iva == 2) { $iva_total = $precio_lista - $precio - $diferencia_descuento_promo - $diferencia_descuento_gral + $diferencia_recargo_gral; }
    //dd($iva_total);
    
    return $iva_total;
    
}

public function SetNroCarro(){
    
    $cantidad_items = Cart::getTotalQuantity();
    if($cantidad_items == 0){
	//Agregar en tabla sale, columna idVenta
	$this->idVenta = 'cuv' . '-' . $this->comercio_id . '-' . Carbon::now()->format('d_m_Y_H_i_s');
	session(['idVenta' => $this->idVenta]);
	}
	return $this->idVenta;
}

public function ChequearCoincidencias($sale,$sale_details_id,$pago){
    $sale = Sale::find($sale);
    $sale_detail = SaleDetail::find($sale_details_id);
    $pagos_facturas = pagos_facturas::find($pago);
    
    if($sale != null && $sale_detail != null && $pagos_facturas != null){
    dd($sale,$sale_detail,$pagos_facturas);    
    }
}


public function SetEfectivo($total,$efectivo){
    
    
    if($this->es_pago_dividido == 0){
    $efe = Cart::getTotal() + $this->sum_iva - $this->sum_descuento - $this->sum_descuento_promo + $this->recargo_total;    
    } else {
    if($efectivo < $total){
    $efe = $this->efectivo;
    } else {
    $efe = Cart::getTotal() + $this->sum_iva - $this->sum_descuento - $this->sum_descuento_promo + $this->recargo_total;
    }
    }
    
    $this->efectivo = $efe;
    return $this->efectivo;
}

							        
  public function CalcularTotales() {

	$this->MetodoPagoSession();
		
    $this->carro = Cart::getContent();
    
    // subtotal
    $this->subtotal = Cart::getTotal();

    $this->sum_subtotal_con_iva = $this->sumarSubtotalConIva($this->carro);
    
    // Descuento promo
    $this->sum_descuento_promo = $this->sumarDescuentoPromo($this->carro,$this->recargo);
    
    $this->sum_descuento_promo_con_iva = $this->sumarDescuentoPromoConIva($this->carro,$this->recargo);
    
    $this->sum_descuento = $this->sumarDescuento($this->carro,$this->recargo);
    
    $subtotal_sin_recargo = $this->subtotal - $this->sum_descuento_promo - $this->sum_descuento;
    
    $this->sum_iva_subtotal = $this->sumarIVASUBTOTAL($this->carro,$this->recargo);
    
    $this->iva_elegido_subtotal = $this->CalcularAlicuotaIvaSubtotal();

    $this->recargo_total = $this->sumarRecargoTotal($this->metodo_pago,$this->relacion_precio_iva);

    $this->sum_iva = $this->sumarIVATOTAL($this->carro,$this->recargo);

    $this->total = Cart::getTotal() + $this->sum_iva - $this->sum_descuento - $this->sum_descuento_promo + $this->recargo_total;
     
    $this->efectivo = $this->SetEfectivo($this->total,$this->efectivo);    
    
    $this->iva_elegido = $this->iva_elegido_subtotal;

    $this->sum_iva_pago = $this->sumarIVAPago($this->efectivo,$this->subtotal,$this->sum_descuento,$this->sum_descuento_promo,$this->recargo_total,$this->iva_elegido,$this->pago_parcial);

    $this->sum_iva_recargo = $this->sumarIVARecargo($this->recargo_total,$this->iva_elegido);

    $this->change = $this->SetCambio();

    //dd($this->change);
    $this->itemsQuantity = Cart::getTotalQuantity();
    
  }



// CONTROLAR ACA
public function sumarRecargoTotal($metodo_pago_id,$relacion_precio_iva){
        
        // Seteamos el recargo
        $metodo_pago = metodo_pago::find($metodo_pago_id);

		$this->recargo = ($metodo_pago->recargo/100);
        
         // Seteo del subtotal
	     $subtotal = Cart::getTotal() - $this->sum_descuento - $this->sum_descuento_promo;            
         // Seteamos la base para el calculo de recargo
        
        if($metodo_pago->id == 2){
        $recargo_total = $this->CalcularRecargoDividido();
        if($relacion_precio_iva == 2){
        $recargo_total = $recargo_total / (1 + $this->iva_elegido_subtotal);    
        } 
        if($subtotal != 0){$this->recargo = $recargo_total / $subtotal;} else {$this->recargo = 0;}
        }

        /*
        if($this->pago_parcial == 1){ // acepta pago parcial
         
         $base_calculo_recargo = $this->efectivo; 

         // Seteo del recargo total  
         if($this->efectivo != 0){
         if($relacion_precio_iva == 2){
         $recargo_total = $base_calculo_recargo * $this->recargo;
         //dd($this->iva_elegido);
         $this->recargo_total  = $recargo_total / (1 + $this->iva_elegido);   
         //dd($this->recargo_total);
         } else {
         $this->recargo_total = $base_calculo_recargo * $this->recargo;     
         }
         } else {
         $this->recargo_total = 0;
         }
         }
         */
         
         // si no acepta pago parcial
         if($this->pago_parcial == 0){ // no acepta pago parcial
         $base_calculo_recargo = $subtotal;
         $this->recargo_total = $base_calculo_recargo * $this->recargo;
         }
        
        
        //dd($this->recargo_total);
        
        return $this->recargo_total;
}

	public function SetCambio()
	{
         // Seteo del cambio    
         
         // acepta pago parcial
         if($this->pago_parcial == 1){

         // Si es precio + IVA
         $this->iva_recargo = $this->recargo_total * $this->iva_elegido;

         if($this->efectivo < $this->total){

         if($this->relacion_precio_iva == 1 || $this->relacion_precio_iva == 0) {
         $this->change = ($this->total - $this->efectivo - $this->recargo_total - $this->sum_iva_pago - $this->iva_recargo);    
         }

         if($this->relacion_precio_iva == 2) {
         $this->change = ($this->total - $this->efectivo - $this->recargo_total - $this->iva_recargo);    
         }

         } else {
         $this->change = ($this->total - $this->efectivo);    
         }    
         
         if($this->relacion_precio_iva == 1 || $this->relacion_precio_iva == 0) {
         $this->a_cobrar_parcial = $this->efectivo + $this->recargo_total + $this->sum_iva_pago + $this->iva_recargo;  
         }
         
         if($this->relacion_precio_iva == 2) {
         $this->a_cobrar_parcial = $this->efectivo + $this->recargo_total + $this->iva_recargo;  
         }
         
         }
         
         // no acepta pago parcial
         if($this->pago_parcial == 0){ 
         $this->change = ($this->total - $this->efectivo);    
         }

         //dd($this->change);
		 return $this->change;   
            
	}

  
  public function sumarIVA($subtotal,$descuento,$descuento_promo,$recargo_total,$alicuota_iva) {
  $this->sum_iva = ($subtotal - $descuento - $descuento_promo + $recargo_total) * $alicuota_iva;
  return $this->sum_iva;    
  }
  
  public function sumarIVAPago($efectivo,$subtotal,$descuento,$descuento_promo,$recargo_total,$alicuota_iva,$pago_parcial) {
  if($pago_parcial == 0){
  $this->sum_iva_pago = ($subtotal - $descuento - $descuento_promo) * $alicuota_iva;
  } 
  if($pago_parcial == 1){
  $this->sum_iva_pago = $efectivo * $alicuota_iva;    
  }
  return $this->sum_iva_pago;    
  }

  public function sumarIVARecargo($recargo_total,$alicuota_iva) {
  $this->sum_iva_recargo = $recargo_total * $alicuota_iva;
  return $this->sum_iva_recargo;    
  }
  
/*  
  public function sumarIva($carro,$metodoPago){    
      
    $this->recargo = $metodoPago;
   
    $this->sum_iva = $this->carro->sum(function($item){
    
    $subtotal = $item->price * $item->quantity;
    
    $descuento = $subtotal * ($item->attributes['descuento']/100);
    
    $recargo = ($subtotal - $descuento) * ($this->recargo);
    
    return ($subtotal - $descuento + $recargo) * $item->attributes['iva']  ;
    });  
         
  }   
*/


public function CalcularAlicuotaIvaSubtotal() {
  $base_imponible = $this->subtotal-$this->sum_descuento-$this->sum_descuento_promo;
  if(0 < $base_imponible){
  $iva = $this->sum_iva_subtotal/$base_imponible;
  } else {
  $iva = 0;
  }
  return $iva;
}

public function CalcularAlicuotaIva() {
  $base_imponible = $this->subtotal-$this->sum_descuento-$this->sum_descuento_promo+$this->recargo_total;
  if(0 < $base_imponible){
  $iva = $this->sum_iva/$base_imponible;
  } else {
  $iva = 0;
  }
  return $iva;
}

public function SetIvaElegido(){
        
        $df_iva = $this->getDatosFacturacion($this->comercio_id);
        if($df_iva != null) {
        $this->relacion_precio_iva = $df_iva->relacion_precio_iva;
        } else {
        $this->relacion_precio_iva = 0;    
        }
        
        $this->iva_elegido = $this->CalcularAlicuotaIva();
}

public function setSucursalesMount(){
    
            $this->tipo_usuario = User::find(Auth::user()->id);
			if($this->tipo_usuario->sucursal != 1) {
				$this->sucursales = $this->getSucursal($this->comercio_id);
			} else {
				$this->sucursales = $this->getSucursal($this->casa_central_id);
			}
    
}

public function SetEnvioMount(){
            if((session('EnvioElegido') == 1) || (session('EnvioElegido') == null)) {
        $this->check_envio = false;
        $this->check_retiro_sucursal = true;
        $this->check_envio_cliente = false;
        
        $this->checked_envio = "";
        $this->checked_envio_cliente = "";
        $this->checked_retiro_sucursal = "checked";
        $this->envio_visible = "none;";
        } 
        if(session('EnvioElegido') == 2) {
        $this->check_envio = false;
        $this->check_retiro_sucursal = false;
        $this->check_envio_cliente = true;
        
        $this->checked_envio = "";
        $this->checked_envio_cliente = "checked";
        $this->checked_retiro_sucursal = "";
        $this->envio_visible = "block;";
        } 
        if(session('EnvioElegido') == 3) {
        $this->check_envio = true;
        $this->check_retiro_sucursal = false;
        $this->check_envio_cliente = false;
        
        $this->checked_envio = "checked";
        $this->checked_envio_cliente = "";
        $this->checked_retiro_sucursal = "";
        $this->envio_visible = "block;";
        }

}
public function SetPagoDivididoMount(){

        $this->metodos_pago_dividido = session('metodos_pago_dividido'); // Aca seteamos si tiene pago dividido
    	if($this->metodos_pago_dividido == null){
    	    $this->metodos_pago_dividido = [];
    	}
		//
		$this->es_pago_dividido = session('PagoDividido');
        

		if($this->es_pago_dividido == 1){
		    $this->PagoDividido();
		    $this->guardarPagoDividido();
		    $this->style_pago_dividido = "display:block;";
            $this->style_metodo_pago = "display:none;";
		}    
}

public function SetCliente($cliente_id){
    $this->cliente = ClientesMostrador::find($cliente_id);
}

public function SetClienteMount(){

		$this->query_id = session('IdCliente');
		$this->query = session('NombreCliente');

		if($this->query_id == null) {
			$this->query = '';
			$this->query_id = 1;

		} else {
			$this->query = session('NombreCliente');
			$this->query_id = session('IdCliente');
		}
    
}

public function SetPagoParcialMount(){
    if (Auth::user()->pago_parcial == 1) {
	$this->check = 'checked';
	$this->estado_pedido = '';
	$this->pago_parcial = 1;

	} else {
	$this->check = '';
	$this->estado_pedido = 'Entregado';
	$this->pago_parcial = 0;
	}
}
public function SetMetodosPagosYBancosMount() {
		$this->tipos_pago = $this->getTipoPago($this->comercio_id);
		$this->bancos_metodo_pago =  $this->getBancosMetodoPago($this->comercio_id);	
		$this->plataformas_metodo_pago = $this->getPlataformasMetodosPago($this->comercio_id);
		$this->metodos = $this->getMetodosPago($this->comercio_id);
		$this->metodos_todos = $this->getMetodosPagoTodos($this->comercio_id);
}

public function SetMetodoPagoMount(){
            if($this->metodo_pago == null) {
			$this->metodo_pago = 1;
			$this->tipo_pago = 1;
			$this->metodo_pago_nuevo = 1;
			session(['MetodoPago' => 1]);
        } else {
			$this->metodo_pago = session('MetodoPago');
			$this->metodo_pago_nuevo = session('MetodoPago');
			$metodo_pago = metodo_pago::find($this->metodo_pago);
			$this->tipo_pago = $metodo_pago->cuenta;
        }
}

public function GetProductosTodos(){
            
        $lista_productos = Product::join('categories','categories.id','products.category_id')
        ->join('proveedores','proveedores.id','products.proveedor_id')
        ->select('products.*','categories.name as categoria','proveedores.nombre as proveedor')
        ->where('products.comercio_id',$this->casa_central_id);
        
        	if(strlen($this->search_lista_productos) > 0) {
            
			$lista_productos = $lista_productos->where( function($query) {
					 $query->where('products.name', 'like', '%' . $this->search_lista_productos . '%')
						->orWhere('products.barcode', 'like',$this->search_lista_productos . '%');
					});
					
        	}
        	
        	if($this->search_categorias_lista_productos != 0) {
        	$lista_productos = $lista_productos->where('category_id',$this->search_categorias_lista_productos);
        	}

        $lista_productos = $lista_productos->where('products.eliminado',0)->get();

    return $lista_productos;
}


public function resetUIProducto(){
    
		$this->ResetAgregar();

		$cart = new CartVariaciones;
		$cart->clear();
		$this->variacion_atributo = "c";
		$product = null;
		$pvd = null;
		$this->productos_lista_precios = null;
		$this->productos_stock_sucursales = null;
		$this->precio_lista = null;
		$this->stock_sucursal = null;
		$this->real_stock_sucursal = null;
		$this->almacen_id = null;
		$this->stock_sucursal_comprometido = null;
		$this->costos_variacion = null;
		$this->proveedor = 1;
		$key = null;
		$llave = null;
}


	public function MetodoPagoSession(){

		$this->metodo_pago = session('MetodoPago');
		$this->es_pago_dividido = session('PagoDividido');
        
        //dd($this->es_pago_dividido);
		
		if($this->metodo_pago == null) {
			$this->metodo_pago = 1;
			$this->metodo_pago_nuevo = 1;
			$this->efectivo =  0;
			$this->change = 0;

		} else {
			$this->metodo_pago = session('MetodoPago');
			$this->metodo_pago_nuevo = session('MetodoPago');
		}


    }

                                            
    	public function comentario($saleId)
	{
		$this->Id_cart = $saleId;

		$item = Cart::get($saleId);

		$this->comentarios = $item->attributes['comentario'];

		$this->emit('show-modal','details loaded');

	}


	public function guardarComentario($productId)
	{
		$item = Cart::get($productId);
		Cart::remove($productId);

		// si el producto no tiene imagen, mostramos una default
		$img = (count($item->attributes) > 0 ? $item->attributes['image'] : Product::find($productId)->imagen);

		$newQty = ($item->quantity);
		$coment = $this->comentarios;


          Cart::add(array(
          'id' => $item->id,
          'name' => $item->name,
          'price' => $item->price,
          'quantity' => $item->quantity,
          'attributes' => array(
            'image' => $img,
            'alto' => 1,
            'ancho' => 1,
            'relacion_precio_iva' =>$item->attributes['relacion_precio_iva'],
            'iva' => $item->attributes['iva'],
            'seccionalmacen_id' => $item->attributes['seccionalmacen_id'],
            'comercio_id' => $item->attributes['comercio_id'],
            'sucursal_id' => $item->attributes['sucursal_id'],
            'barcode' => $item->attributes['barcode'],
            'referencia_variacion' => $item->attributes['referencia_variacion'],
            'descuento' => $item->attributes['descuento'],
			'cost' => $item->attributes['cost'],
            'product_id' => $item->attributes['product_id'],
            'stock' => $item->attributes['stock'],
            'added_at' => $item->attributes['added_at'],
            'stock_descubierto' => $item->attributes['stock_descubierto'],
//            'tipo_unidad_medida' => $exist->attributes['tipo_unidad_medida'],
//            'cantidad_unidad_medida' => $exist->attributes['cantidad_unidad_medida'],
            'comentario' => $coment,
            'id_promo' => $item->attributes['id_promo'],
            'nombre_promo' => $item->attributes['nombre_promo'],
            'descuento_promo' => $item->attributes['descuento_promo'],
            'cantidad_promo' => $item->attributes['cantidad_promo'],
            'precio_original' => $item->attributes['precio_original'],
          )));


        $this->CalcularTotales();

		$this->emit('hide-modal','details loaded');
		$this->emit('scan-ok', 'Comentario agregado.');

	}



	
	public function guardarPromoIndividual($productId)
	{

		$item = Cart::get($productId);

        if($item->quantity < $this->cantidad_promo_form){
            $this->emit("msg-error","La cantidad de unidades a las que se le aplica descuento no pueden ser mayor a la cantidad de unidades en la venta");
            return;
        }

		Cart::remove($productId);

        $alicuota_descuento = $this->descuento_promo_form/100;
        $descuento = $item->price * $alicuota_descuento;
        $cantidad_promo = $this->cantidad_promo_form;
        
        $promocion_total = $descuento;
        
        
       // $iva_total = $this->CalcularIvaTotalProducto($item->price,$item->attributes['precio_original'],$item->attributes['relacion_precio_iva'],$item->attributes['iva'],$promocion_total,$item->attributes['descuento'],$this->metodo_pago);
        
          Cart::add(array(
          'id' => $item->id,
          'name' => $item->name,
          'price' => $item->price,
          'quantity' => $item->quantity,
          'attributes' => array(
            'image' => $item->attributes['image'],
            'alto' => 1,
            'ancho' => 1,
            'relacion_precio_iva' =>$item->attributes['relacion_precio_iva'],
            'iva' => $item->attributes['iva'],
        //    'iva_total' => $iva_total,
            'seccionalmacen_id' => $item->attributes['seccionalmacen_id'],
            'comercio_id' => $item->attributes['comercio_id'],
            'sucursal_id' => $item->attributes['sucursal_id'],
            'barcode' => $item->attributes['barcode'],
            'referencia_variacion' => $item->attributes['referencia_variacion'],
            'descuento' => $item->attributes['descuento'],
			'cost' => $item->attributes['cost'],
            'product_id' => $item->attributes['product_id'],
            'stock' => $item->attributes['stock'],
            'added_at' => $item->attributes['added_at'],
            'stock_descubierto' => $item->attributes['stock_descubierto'],
            'precio_original' => $item->attributes['precio_original'],
//            'tipo_unidad_medida' => $item->attributes['tipo_unidad_medida'],
//            'cantidad_unidad_medida' => $item->attributes['cantidad_unidad_medida'],
            'id_promo' => 1,
            'nombre_promo' => "Descuento manual ".$this->descuento_promo_form." %",
            'descuento_promo' => $descuento,
            'cantidad_promo' => $cantidad_promo,
            'comentario' => $item->attributes['comentario']
          )));


        $this->CalcularTotales();

		$this->emit('hide-modal-descuentos','details loaded');
		$this->emit('scan-ok', 'Descuento agregado al producto.');

	}

public function GetDescuentoPromo($product_id, $referencia_variacion){
    
    //dd($this->lista_precios_elegida);
    if($this->lista_precios_elegida == 1){
        return null;
    }
    
    $data_promo = promos_productos::join('promos','promos.id','promos_productos.promo_id')
    ->where("promos_productos.product_id", $product_id)
    ->where("promos_productos.referencia_variacion", $referencia_variacion)
    ->where("promos.activo", 1)->where("promos_productos.activo", 1)
    ->select("promos_productos.*", "promos.tipo_promo","promos.precio_promo","promos.vigencia_desde", "promos.vigencia_hasta", "promos.limitar_vigencia","promos.limitar_cantidad")
    ->first();

    // Verificar la vigencia_promo y las fechas
    if ($data_promo && $data_promo->limitar_vigencia == 1) {
        $fechaActual = now(); // Obtener la fecha y hora actual
        $vigenciaDesde = \Carbon\Carbon::parse($data_promo->vigencia_desde);
        $vigenciaHasta = \Carbon\Carbon::parse($data_promo->vigencia_hasta);

        // Verificar si la fecha actual está entre vigencia_desde y vigencia_hasta
        if ($fechaActual->between($vigenciaDesde, $vigenciaHasta)) {
            // La fecha actual está dentro del rango de vigencia
            return $data_promo;
        }
    } elseif ($data_promo && $data_promo->limitar_vigencia == 0) {
        // Vigencia_promo es igual a 0, no se aplica la verificación de fechas
        return $data_promo;
    } else {
        // La vigencia_promo no es igual a 1 o no se encontró la promoción
        return null;
    }
    
    
    
    
}


public function ComprobarAccionPromo($cantidad_promo,$exist,$promo_predeterminada,$origen){

    if($origen == 0){
    if($exist != null){
        // si tiene promo tipo 1 y la cantidad_promo es mayor a 1 es porque corresponde la promo predeterminada -----
       if($exist->attributes['id_promo'] == 1 && 0 < $cantidad_promo){
          return $promo_predeterminada;
       } 
       else if($exist->attributes['id_promo'] == 1 && 0 == $cantidad_promo){
        // $promo_predeterminada = [$idPromo,$nombrePromo,$cantidadPromo,$descuentoPromo];
           return [$exist->attributes['id_promo'],$exist->attributes['nombre_promo'],$exist->attributes['cantidad_promo'],$exist->attributes['descuento_promo']]; 
       } else {
          return $promo_predeterminada; 
       }
    } else {
        return $promo_predeterminada; 
    }
    } else {
        $porcentaje_promo = $exist->attributes['descuento_promo']/($exist->price);
        $descuento_promo_nuevo = $porcentaje_promo * $this->precio;
        return [$exist->attributes['id_promo'],$exist->attributes['nombre_promo'],$exist->attributes['cantidad_promo'],$descuento_promo_nuevo]; 
    }
    
}
	// CalcularDescuentoPromos($this->precio,$this->iva,$this->relacion_precio_iva)
public function CalcularDescuentoPromos($exist,$quantityInCart, $precio, $iva, $relacion_precio_iva,$promo,$origen) {
 
    $idPromo = null;
    $nombrePromo = null;
    $cantidadPromo = 0;  
    $descuentoPromo = 0;
    
    if($promo != null){
    
    if($promo->tipo_promo == 1){
    $idPromo = $promo->promo_id;
    $nombrePromo = $promo->nombre_promo;
    $cantidadPromo = $exist->attributes['cantidad_promo'] ?? 0;
    $descuentoPromo = $exist->attributes['descuento_promo'] ?? 0;
    
    $calculo = $quantityInCart / $promo->cantidad;    
    $cantidadPromo = floor($calculo);
    
    if (0 < $cantidadPromo) {
        // La tercera unidad se añadió, acumular descuento del % de esa unidad.
        $descuentoPorcentaje = $promo->porcentaje_descuento;
        $descuento = ($precio * $descuentoPorcentaje) / 100;
        
        //dd($descuento);
        
        // Acumular el descuento
        $descuentoPromo = $descuento;
        $cantidadPromo = $cantidadPromo; 

    }
    }

    }
    
    $promo_predeterminada = [$idPromo,$nombrePromo,$cantidadPromo,$descuentoPromo];
    $response = $this->ComprobarAccionPromo($cantidadPromo,$exist,$promo_predeterminada,$origen);
    
    return $response;
}

	public function SetPromoTipo2($promo){
	
	$idsCarro = null;
    $cantidadMinima  = null;
    $sumaSubtotal = null;
    $products_id = null;
    $totalPromo = $promo->precio_promo;
    
    $productos_promocion_original = promos_productos::where('promo_id',$promo->promo_id)->get();
    $carro = Cart::getContent();
    
    $idsCarro = $this->verificarExistenciaEnCarroPromo2($productos_promocion_original, $carro);
    
    if($idsCarro != null) {
    
    $respuesta_formulas_promos = $this->ObtenerFormulasPromo2($idsCarro,$carro,$promo);
    
    $cantidadMinima = min(array_column($respuesta_formulas_promos, 'formula_promo'));

    $this->ActualizarProductosPromo2($respuesta_formulas_promos,$promo,$cantidadMinima,$carro);
    }
	}


	public function ActualizarProductosPromo2($respuesta_formulas_promos,$promo,$cantidadPromo,$carro){

    foreach($respuesta_formulas_promos as $product_promo){
       
       $exist = Cart::get($product_promo['id']);
       if($exist->attributes['relacion_precio_iva'] == 2){$descuento_promo = $product_promo['descuento']/(1 + $exist->attributes['iva']);} else{$descuento_promo = $product_promo['descuento'];}
       
       
	   $this->removeItemsUpdate($exist->id);
       
          Cart::add(array(
          'id' => $exist->id,
          'name' => $exist->name,
          'price' => $exist->price,
          'quantity' => $exist->quantity,
          'attributes' => array(
            'image' => $exist->attributes['image'],
            'alto' => 1,
            'ancho' => 1,
            'seccionalmacen_id' => $exist->attributes['seccionalmacen_id'],
            'comercio_id' => $exist->attributes['comercio_id'],
            'sucursal_id' => $exist->attributes['sucursal_id'],
            'barcode' => $exist->attributes['barcode'],
            'product_id' => $exist->attributes['product_id'],
            'descuento' => $exist->attributes['descuento'],
            'relacion_precio_iva' =>$exist->attributes['relacion_precio_iva'],
            'referencia_variacion' =>$exist->attributes['referencia_variacion'],
            'iva' => $exist->attributes['iva'],
        //    'iva_total' => $exist->attributes['iva_total'],
            'cost' => $exist->attributes['cost'],
            'stock' => $exist->attributes['stock'],
            'added_at' => $exist->attributes['added_at'],
            'stock_descubierto' => $exist->attributes['stock_descubierto'],
            'comentario' => $exist->attributes['comentario'],
            'id_promo' => $promo->promo_id,
            'nombre_promo' => $promo->nombre_promo,
            'descuento_promo' => $descuento_promo,
            'cantidad_promo' => $cantidadPromo,
//            'tipo_unidad_medida' => $exist->attributes['tipo_unidad_medida'],
//            'cantidad_unidad_medida' => $exist->attributes['cantidad_unidad_medida'],
            'precio_original' => $exist->attributes['precio_original']
          )));
	   
    
	 }
	 
	}	
	public function ObtenerFormulasPromo2($ids_carro,$carro,$promo){
    
    $formula_cantidades = [];
    $totalPromo = $promo->precio_promo;
    
    foreach($ids_carro as $ic){
    $datos = explode("-",$ic);
    $product_id = $datos[0];
    $comercio_id = $datos[2];
    
    $p = Product::find($product_id);
    if($p->producto_tipo == "s"){$referencia_variacion = $datos[1];} else {$referencia_variacion = $datos[1]."-".$datos[2];}
    
    $productos_promocion = promos_productos::where("product_id",$product_id)->where("referencia_variacion",$referencia_variacion)->where("activo",1)->first();
    
    $item = Cart::get($ic);
    $cantidad_carrito = $item->quantity;
    $cantidad_promocion = $productos_promocion->cantidad;
    $division = $cantidad_carrito/$cantidad_promocion;
    $division = floor($division);
    $precio = productos_lista_precios::where('product_id',$product_id)->where('referencia_variacion',$referencia_variacion)->where('lista_id',0)->first()->precio_lista;
    $subtotal_producto = $cantidad_promocion * $precio;
    array_push($formula_cantidades,['id'=> $ic,'formula_promo' => $division, 'subtotal_producto' => $subtotal_producto]);
    }
    
    $sumaSubtotal = array_sum(array_column($formula_cantidades, 'subtotal_producto'));
    $descuento = 1 - ($totalPromo/$sumaSubtotal);
    
    // tenemos el % de descuento
    foreach ($formula_cantidades as &$elemento) {
        $elemento["descuento"] = $elemento["subtotal_producto"] * $descuento;
    }
    
    return $formula_cantidades;
	}
	

	public function ObtenerDescuentoPromo2($cantidadPromos,$idsCarro){
	    
	}
	
	public function ObtenerCantidadMinimaPromo2Old($productos_promocion,$carro){
    
    
    // Obtener los product_id y referencia_variacion de $productos_promocion
    $productos_promocion = $productos_promocion->pluck('product_id');
        
	// Obtenemos la cantidad mínima de productos en el carro
    $cantidadMinima = $productos_promocion->reduce(function ($min, $product_id) use ($carro) {
        $quantity = $carro->where('attributes.product_id', $product_id)->where('attributes.referencia_variacion', 0)->first()->quantity;
        return min($min, $quantity);
    }, PHP_INT_MAX);
    
    return $cantidadMinima;
	}
    public function verificarExistenciaEnCarroPromo2($productos_promocion, $carro)
    {
        // Obtener los product_id y referencia_variacion de $productos_promocion
        $idsPromocion = $productos_promocion->pluck('product_id');
        // Verificar si todos los elementos de $productos_promocion están en $carro
        $todosPresentes = $idsPromocion->every(function ($product_id) use ($carro) {
            return $carro->contains(function ($item) use ($product_id) {
                return $item->attributes['product_id'] == $product_id; 
            });
        });
    
        // Si todos los elementos están presentes, devolver los ids de $carro
        if ($todosPresentes) {
            $idsCarro = $carro->filter(function ($item) use ($idsPromocion) {
                return $idsPromocion->contains($item->attributes['product_id']);
            })->pluck('id')->toArray();
        
            return $idsCarro;
        }
    
        return null;
    }


	public function GetStockComprometido($product_id,$variacion,$comercio_id) {
 
     return SaleDetail::select(SaleDetail::raw('IFNULL(SUM(quantity),0) as stock_comprometido'))
        ->where('product_id',$product_id)
        ->where('referencia_variacion',$variacion)
        ->where('comercio_id',$this->comercio_id)
        ->where('estado',0)
        ->first()->stock_comprometido;    
    
    }

public function GetStockDisponibleCarro($stock_disponible,$exist) {
    $cantidad_carro = $exist->quantity ?? 0;
    return $stock_disponible - $cantidad_carro;
}

public function GetStockDisponible($stock_comprometido,$productId = null, $casaCentralId = null, $variacion = 0, $sucursal = null){
    

      $sucursal_id = $this->SetSucursalOrCero($casaCentralId,$sucursal);
    
      $stock_real = productos_stock_sucursales::join('products','products.id','productos_stock_sucursales.product_id')
      ->where('productos_stock_sucursales.product_id', $productId)
       ->where('productos_stock_sucursales.comercio_id', $casaCentralId)
      ->where('productos_stock_sucursales.referencia_variacion', $variacion)
      ->where('productos_stock_sucursales.sucursal_id', $sucursal_id)
      ->where('products.eliminado', 0)
      ->select(productos_stock_sucursales::raw('IFNULL(productos_stock_sucursales.stock_real,0) AS stock_real'))
      ->first()->stock_real;
      
      return $stock_real - $stock_comprometido;
      
  }
	
	
	/*
	public function cambioMetodoDiv($index, $value)
{
    $metodo_pago_div = metodo_pago::find($value);

    if ($metodo_pago_div == null) {
        $this->metodo_pago_div[$index] = 0;
    } else {
        $this->metodo_pago_div[$index] = $metodo_pago_div->recargo;
    }

    $this->recargo_div[$index] = $this->efectivo[$index] * ($this->metodo_pago_div[$index] / 100);

    $this->guardarPagoDividido();
}

public function cambioDiv($index, $value)
{
    if (empty($value)) {
        $this->efectivo[$index] = 0;
        $this->change = 0;
    } else {
        $this->efectivo[$index] = $value;

        $this->metodo_pago_div[$index] = metodo_pago::find($this->metodo_pago_ap_div[$index]);

        if ($this->metodo_pago_div[$index] == null) {
            $this->metodo_pago_div[$index] = 0;
        } else {
            $this->metodo_pago_div[$index] = $this->metodo_pago_div[$index]->recargo;
        }

        $this->recargo_div[$index] = $this->efectivo[$index] * ($this->metodo_pago_div[$index] / 100);
    }

    $this->guardarPagoDividido();
}

public function guardarPagoDividido()
{
    $this->metodo_pago_nuevo = count($this->efectivo); // Número de formas de pago

    $this->pago_dividido = 1;

    $this->efectivo_total = array_sum($this->efectivo);
    $this->change = $this->total - $this->efectivo_total;

    $this->a_cobrar_total = 0;

    foreach ($this->efectivo as $index => $monto) {
        if ($this->relacion_precio_iva == 1) {
            $this->a_cobrar[$index] = $monto + $this->recargo_div[$index] + ($this->recargo_div[$index] * $this->iva_elegido);
        } else {
            $this->a_cobrar[$index] = $monto + $this->recargo_div[$index];
        }
        $this->a_cobrar_total += $this->a_cobrar[$index];
    }

    $this->efectivo_pago_dividido = $this->efectivo_total + $this->recargo_total;

    $this->CalcularTotales();
}
*/


                                                public function CalcularRecargoDividido() {
                                            
                                                session(['metodos_pago_dividido' => $this->metodos_pago_dividido]); // Guarda en la sesión
                                                
                                                $recargo_total = 0;
                                                foreach ($this->metodos_pago_dividido as $elemento) {
                                                        if($this->relacion_precio_iva == 2){
                                                        $recargo_total += $elemento['recargo_total_div'] + $elemento['iva_recargo_dividido'];    
                                                        } else {
                                                        $recargo_total += $elemento['recargo_total_div'];    
                                                        }
                                                }
                                                return $recargo_total;
                                                }
                                        
                                            public function guardarPagoDividido() {
                                            
                                                session(['metodos_pago_dividido' => $this->metodos_pago_dividido]); // Guarda en la sesión
                                                
                                                
                                                $this->metodo_pago_nuevo = 2;
                                                $this->pago_dividido = 1;
                                            
                                                $total_efectivo = 0;
                                                $a_cobrar_total = 0;
                                                $recargo_total = 0;
                                                $iva_pago_dividido_total = 0;
                                                $iva_pago_dividido = 0;
                                                $iva_recargo_dividido = 0;
                                                foreach ($this->metodos_pago_dividido as $elemento) {
                                                        $total_efectivo += $elemento['efectivo_mostrar'];
                                                        $a_cobrar_total += $elemento['a_cobrar'];
                                                        if($this->relacion_precio_iva == 2){
                                                        $recargo_total += $elemento['recargo_total_div'] + $elemento['iva_recargo_dividido'];    
                                                        } else {
                                                        $recargo_total += $elemento['recargo_total_div'];    
                                                        }
                                                        $iva_pago_dividido_total += $elemento['iva_total_dividido'];
                                                        $iva_pago_dividido += $elemento['iva_pago_dividido'];
                                                        $iva_recargo_dividido += $elemento['iva_recargo_dividido'];
                                                        
                                                }
                                                $this->iva_pago_dividido_total = $iva_pago_dividido_total;
                                                $this->efectivo = $total_efectivo;
                                                $this->recargo_total = $recargo_total;
                                                $this->a_cobrar_total = $a_cobrar_total;
                                        		$this->change = $this->total - $this->efectivo;
                                                
                                                $this->efectivo_pago_dividido = $this->efectivo;
                                                
                                                if($this->relacion_precio_iva == 1){
                                                $this->efectivo = $this->efectivo + $this->recargo_total;
                                                } else {
                                                $this->efectivo = $this->efectivo + $this->recargo_total;    
                                                }
                                                
                                                $this->ActualizarValoresPagoDividido();  
                                        		$this->CalcularTotales();
                                                }
                                                
                                            public function ActualizarValoresPagoDividido(){
                                               
                                                // Actualiza los valores de los arrays $monto_ap_div y $metodo_pago_ap_div
                                                    foreach($this->metodos_pago_dividido as $index => $value){
                                                        //dd($this->monto_ap_div,$this->metodo_pago_ap_div,$this->metodos_pago_dividido,$value);
                                                        $this->monto_ap_div[$index] = $value['efectivo_mostrar'];
                                                        $this->metodo_pago_ap_div[$index] = $value['metodo_pago_ap_div'];
                                                        $this->recargo_div[$index] = $value['recargo_div'];
                                                        $this->recargo_total_div[$index] = $value['recargo_total_div'];
                                                        $this->iva_total_dividido[$index] = $value['iva_total_dividido'];
                                                        $this->iva_pago_dividido[$index] = $value['iva_pago_dividido'];
                                                        $this->iva_recargo_dividido[$index] = $value['iva_recargo_dividido'];
                                                        $this->a_cobrar[$index] = number_format($value['a_cobrar'],2,",",".");
                                                    }
                                             session(['metodos_pago_dividido' => $this->metodos_pago_dividido]); // Guarda en la sesión
                                             
                                            }                                            


                                            public function quitarMetodoPago($index) {
                                                if (isset($this->metodos_pago_dividido[$index])) {
                                                    unset($this->metodos_pago_dividido[$index]);
                                                }
                                                
                                                session(['metodos_pago_dividido' => $this->metodos_pago_dividido]); // Guarda en la sesión
                                                    
                                                $this->ActualizarValoresPagoDividido();
                                                $this->guardarPagoDividido();
                                            }
                                            
                                            
                                            public function agregarMetodoPago()
                                            {
                                                    $array = [
                                                        'efectivo' => 0,
                                                        'efectivo_mostrar' => 0,
                                                        'metodo_pago_ap_div' => 1,
                                                        'recargo_div' => 0,
                                                        'iva_total_dividido' => 0,
                                                        'iva_pago_dividido' => 0,
                                                        'iva_recargo_dividido' => 0,
                                                        'recargo_total_div' => 0,
                                                        'a_cobrar' => 0,
                                                        // Agrega más campos aquí según sea necesario
                                                    ];
                                                    
                                                    array_push($this->metodos_pago_dividido, $array);
                                                    
                                                    session(['metodos_pago_dividido' => $this->metodos_pago_dividido]); // Guarda en la sesión
                                                    
                                                    $this->ActualizarValoresPagoDividido();
                                                }
                                            
                                            
                                            public function cambioMetodoDiv($index, $value)
                                            {
                                                $metodo_pago_div = metodo_pago::find($value);
                                                $this->metodos_pago_dividido[$index]['metodo_pago_ap_div'] = $value;  
                                            
                                                if ($metodo_pago_div == null) {
                                                    $this->metodos_pago_dividido[$index]['efectivo_mostrar'] = 0;
                                                    $this->metodos_pago_dividido[$index]['recargo_div'] = 0; 
                                                    $this->metodos_pago_dividido[$index]['recargo_total_div'] = 0;
                                                    $this->metodos_pago_dividido[$index]['a_cobrar'] = 0;
                                                    $this->iva_total_dividido[$index]['iva_total_dividido'] = 0;
                                        
                                                } else {
                                                $this->SetearValoresindividualesPagoDividido($index,$this->metodos_pago_dividido[$index]['efectivo_mostrar'],$value,$this->iva_elegido,$this->relacion_precio_iva);
                                                }
                                            
                                            $this->guardarPagoDividido();
                                            }

											public function cambioDiv($index, $value)
											{
											    
											    //dd($index,$value);
											    
												if(empty($value)) {
                                                $this->metodos_pago_dividido[$index]['efectivo'] = 0;
                                                $this->metodos_pago_dividido[$index]['efectivo_mostrar'] = 0;
                                                $this->metodos_pago_dividido[$index]['a_cobrar'] = 0;
                                                $this->metodos_pago_dividido[$index]['recargo_total_div'] = 0;
                                                $this->metodos_pago_dividido[$index]['iva_total_dividido'] = 0;
													$this->change = 0;
												} else {
												
                                                $this->SetearValoresindividualesPagoDividido($index,$value,$this->metodos_pago_dividido[$index]['metodo_pago_ap_div'],$this->iva_elegido,$this->relacion_precio_iva);

											}
											
                                            $this->guardarPagoDividido();
											}
											
											
											public function SetearValoresindividualesPagoDividido($index,$efectivo,$metodo_pago,$iva_elegido,$relacion_precio_iva){
                                                
												$metodo_pago_div = metodo_pago::find($metodo_pago);

												if($metodo_pago_div == null){
												$recargo = 0;
												} else {
												$recargo = $metodo_pago_div->recargo;
												}
												
												$this->metodos_pago_dividido[$index]['recargo_div'] = $recargo;
												
												$this->metodos_pago_dividido[$index]['efectivo_mostrar'] = $efectivo;
												
												if($relacion_precio_iva == 0){
												$this->metodos_pago_dividido[$index]['efectivo'] = $efectivo;
												$this->metodos_pago_dividido[$index]['recargo_total_div'] = $this->metodos_pago_dividido[$index]['efectivo'] * ($recargo / 100);
                                                $this->metodos_pago_dividido[$index]['a_cobrar'] = ($this->metodos_pago_dividido[$index]['efectivo'] + $this->metodos_pago_dividido[$index]['recargo_total_div']);    
                                                $this->metodos_pago_dividido[$index]['iva_total_dividido'] =  0;
                                                $this->metodos_pago_dividido[$index]['iva_pago_dividido'] =  0;
                                                $this->metodos_pago_dividido[$index]['iva_recargo_dividido'] =  0;
                                                    
												    
												}
												if($relacion_precio_iva == 1){
												$this->metodos_pago_dividido[$index]['efectivo'] = $efectivo;   
												$this->metodos_pago_dividido[$index]['recargo_total_div'] = $this->metodos_pago_dividido[$index]['efectivo'] * ($recargo / 100);
                                                $this->metodos_pago_dividido[$index]['a_cobrar'] = ($this->metodos_pago_dividido[$index]['efectivo'] + $this->metodos_pago_dividido[$index]['recargo_total_div']) * (1 + $this->iva_elegido);
                                                $this->metodos_pago_dividido[$index]['iva_total_dividido'] = ($this->metodos_pago_dividido[$index]['efectivo'] + $this->metodos_pago_dividido[$index]['recargo_total_div']) * ($this->iva_elegido);
                                                $this->metodos_pago_dividido[$index]['iva_pago_dividido'] =  ($this->metodos_pago_dividido[$index]['efectivo']) * ($this->iva_elegido);
                                                $this->metodos_pago_dividido[$index]['iva_recargo_dividido'] =  ($this->metodos_pago_dividido[$index]['recargo_total_div']) * ($this->iva_elegido);
												    
												}
												if($relacion_precio_iva == 2){
												$this->metodos_pago_dividido[$index]['efectivo'] = $efectivo / (1 + $iva_elegido);    
												$this->metodos_pago_dividido[$index]['recargo_total_div'] = $this->metodos_pago_dividido[$index]['efectivo'] * ($recargo / 100);
                                                $this->metodos_pago_dividido[$index]['iva_total_dividido'] = ($this->metodos_pago_dividido[$index]['efectivo'] + $this->metodos_pago_dividido[$index]['recargo_total_div']) * ($this->iva_elegido);
                                                $this->metodos_pago_dividido[$index]['iva_pago_dividido'] =  $efectivo - $this->metodos_pago_dividido[$index]['efectivo'];
                                                $this->metodos_pago_dividido[$index]['iva_recargo_dividido'] =  ($this->metodos_pago_dividido[$index]['recargo_total_div']) * ($this->iva_elegido);
                                                $this->metodos_pago_dividido[$index]['a_cobrar'] = ($this->metodos_pago_dividido[$index]['efectivo'] + $this->metodos_pago_dividido[$index]['recargo_total_div']) * (1 + $this->iva_elegido);
												    
												}
                                             											    
											}
											
											
											public function ValidarPagoDivididoSaveSale(){
                    							
												if($this->efectivo_pago_dividido < $this->efectivo){
												$this->emit("msg-error","CHEQUEE LOS MONTOS DE LOS PAGOS");
												return 1;
												} else if($this->efectivo < $this->efectivo_pago_dividido){
												$this->emit("msg-error","CHEQUEE LOS MONTOS DE LOS PAGOS");
												return 1;
												} else {
												return 0;    
												}
												
											
                                            }
                                            
                                            
    public function RemovePromoIndividual($exist){
            // Llama a la función para eliminar el ítem
            $this->removeItemsUpdate($exist->id);
                    
                    
          Cart::add(array(
          'id' => $exist->id,
          'name' => $exist->name,
          'price' => $exist->price,
          'quantity' => $exist->quantity,
          'attributes' => array(
            'image' => $exist->attributes['image'],
            'alto' => 1,
            'ancho' => 1,
            'seccionalmacen_id' => $exist->attributes['seccionalmacen_id'],
            'relacion_precio_iva' =>$exist->attributes['relacion_precio_iva'],
            'comercio_id' => $exist->attributes['comercio_id'],
            'sucursal_id' => $exist->attributes['sucursal_id'],
            'stock' => $exist->attributes['stock'],
            'cost' => $exist->attributes['cost'],
            'referencia_variacion' => $exist->attributes['referencia_variacion'],
            'product_id' => $exist->attributes['product_id'],
            'iva' => $exist->attributes['iva'],
            'barcode' => $exist->attributes['barcode'],
            'descuento' => $exist->attributes['descuento'],
            'id_promo' => null,
            'nombre_promo' => null,
            'descuento_promo' => null,
            'cantidad_promo' => null,
            'added_at' => $exist->attributes['added_at'],
            'stock_descubierto' => $exist->attributes['stock_descubierto'],
            'comentario' => $exist->attributes['comentario'],
            'precio_original' => $exist->attributes['precio_original'],
//            'tipo_unidad_medida' => $exist->attributes['tipo_unidad_medida'],
//            'cantidad_unidad_medida' => $exist->attributes['cantidad_unidad_medida'],
          )));
                            
    }                                        
    public function RemovePromo($promo,$cartCollection){
    $promoProductosCollection = promos_productos::where('promo_id',$promo->promo_id)->get();
    
    // Recorre la primera colección
    foreach ($cartCollection as $exist) {
    
        // Recorre la segunda colección
        foreach ($promoProductosCollection as $item) {
            // Comprueba si el ítem en el carro coincide con el product_id y referencia_variacion en la segunda colección
            if ($exist['attributes']['product_id'] == $item->product_id && $exist['attributes']['referencia_variacion'] == $item->referencia_variacion) {
                $this->RemovePromoIndividual($exist);
                }
            }
        
    }


    }

public function QuitarPromo($item){
    
    $carro = Cart::getContent();
    $exist = Cart::get($item['id']);
  
    if($exist->attributes['id_promo'] == 1){
    $this->RemovePromoIndividual($exist);    
    } else {
    $promo = $this->GetDescuentoPromo($exist->attributes['product_id'],$exist->attributes['referencia_variacion']);
    
    if($promo != null) {$this->RemovePromo($promo,$carro); }        
    }

    
    $this->Update_descuento_gral($this->descuento_gral_mostrar);
    
}

// 2-5-2024
public function setPuntosVentaMount(){
return datos_facturacion::where('comercio_id',$this->comercio_id)->where('eliminado',0)->orderBy('predeterminado','desc')->get();    
}

public function ElegirPuntoVenta($id){
// Corroboramos si el carrito tiene productos
$itemsQuantity = Cart::getTotalQuantity();
if(0 < $itemsQuantity) {
    $this->emit("msg-error","Debe vaciar el carrito para cambiar el punto de venta");
    return;
}
$this->punto_venta_elegido = datos_facturacion::find($id) ? datos_facturacion::find($id)->id : null;
$this->datos_punto_venta_elegido =  datos_facturacion::find($id) ? datos_facturacion::find($id) : null;
}


//18-5-2024
public function GetPlazoAcreditacionPago($id){
    return $id == 1 ? 1 : 0;
}


                                            
}


