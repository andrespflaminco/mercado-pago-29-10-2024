<?php
namespace App\Traits;

use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;

use Carbon\Carbon;

// Modelos

use App\Models\productos_ivas;
use App\Models\User;
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
use App\Models\datos_facturacion;
use App\Models\Sale;
use App\Models\ClientesMostrador;
use App\Models\Product;
use App\Models\sucursales;
use App\Models\lista_precios;
use App\Models\compras_proveedores;
use App\Models\wocommerce;
use App\Models\facturacion;
use App\Models\ecommerce_envio;
use App\Models\SaleDetail;

trait ProductsConsultaTrait {

  
    public function GetPrecioProductoById($product_id,$referencia_variacion,$lista_id){
        
        if($lista_id == null){$lista_id = 0;}
        
         return productos_lista_precios::join('products','products.id','productos_lista_precios.product_id')
         ->select('productos_lista_precios.*')
         ->where('products.id', $product_id)
         ->where('products.eliminado', 0)
         ->where('referencia_variacion',$referencia_variacion)
         ->where('productos_lista_precios.lista_id',$lista_id)
         ->first()->precio_lista;
    
        
    }
    
    public function GetProductStock($product_id,$referencia_variacion, $sucursal_id) {
        
      $sucursal = $this->GetSucursalIdParaStock($this->casa_central_id,$sucursal_id);
      
      return  productos_stock_sucursales::where('productos_stock_sucursales.product_id',$product_id)
            ->where('productos_stock_sucursales.referencia_variacion',$referencia_variacion)
            ->where('productos_stock_sucursales.sucursal_id',$sucursal)
            ->where('productos_stock_sucursales.comercio_id',$this->casa_central_id)
            ->first();  
    }



    
    public function GetSucursalIdParaStock($casa_central_id,$sucursal_id){
        if($casa_central_id == $sucursal_id){
            $sucursal_id = 0;
        } else {
           $sucursal_id = $sucursal_id; 
        }
        
        return $sucursal_id;
    }
    
    public function UpdateRestarStock($product_id,$referencia_variacion,$cantidad, $sucursal, $estado_id) {
            
            $product_stock = $this->GetProductStock($product_id,$referencia_variacion, $sucursal);
            
            $stock_disponible = $product_stock->stock - $cantidad;
            
            $product_stock->update([
                'stock' => $stock_disponible,
                'stock_real' => $product_stock->stock_real
                ]);        
         
    }
    
    
    
    public function UpdateRestarStockReal($product_id,$referencia_variacion,$cantidad, $sucursal, $estado_id) {
        
            $product_stock = $this->GetProductStock($product_id,$referencia_variacion, $sucursal);
            
            $stock_real = $product_stock->stock_real - $cantidad;
            //dd($product_stock);
            
            $product_stock->update([
                'stock' => $product_stock->stock,
                'stock_real' => $stock_real
            ]);
            
            //dd($product_stock);
            return $stock_real;

    }
  
    // ACTUALIZAR EL STOCK DISPONIBLE
      
    public function UpdateSumarStock($product_id,$referencia_variacion,$cantidad, $sucursal, $estado_id) { 
        
            $product_stock = $this->GetProductStock($product_id,$referencia_variacion, $sucursal);
            
            $stock = $product_stock->stock + $cantidad;
            
            $product_stock->update([
                'stock' => $stock,
                'stock_real' => $product_stock->stock_real
                ]);    

    }

    
    // ACTUALIZAR EL STOCK REAL
    
    public function UpdateSumarStockReal($product_id,$referencia_variacion,$cantidad, $sucursal, $estado_id) {

            $product_stock = $this->GetProductStock($product_id,$referencia_variacion, $sucursal);
            
            $stock_real = $product_stock->stock_real + $cantidad;
            
            $product_stock->update([
                'stock'=> $product_stock->stock,
                'stock_real' => $stock_real,
                ]);    
                
              //  dd($product_stock);
              
              return $stock_real;
               
    }    
    
    
}
