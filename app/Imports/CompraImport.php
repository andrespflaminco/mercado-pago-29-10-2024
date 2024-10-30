<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use App\Services\Cart;
use App\Models\productos_lista_precios;
use App\Models\proveedores;
use App\Models\historico_stock;
use App\Models\productos_variaciones;
use App\Models\variaciones;
use App\Models\atributos;
use App\Models\User;
use App\Models\sucursales;
use App\Models\productos_variaciones_datos;
use App\Models\seccionalmacen;
use Illuminate\Support\Facades\Auth;
use App\Models\wocommerce;
use Automattic\WooCommerce\Client;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CompraImport implements OnEachRow, SkipsEmptyRows, ShouldQueue, WithHeadingRow,  WithValidation, SkipsOnError, WithBatchInserts, WithChunkReading, WithCalculatedFormulas, WithMultipleSheets
{
    use SkipsErrors;

    public $comercio_id, $prov, $proveed, $alm, $al, $categ, $movimiento_stock, $cat, $costo, $stock, $st, $cost, $inv_minimo, $in_minimo, $inv_ideal, $in_ideal, $prec, $pre, $maneja_stock, $stock_descubierto, $manage_stock;
    public $msg_error = [];
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
          public function __construct($actualizar_costos)
        {
         $this->actualizar_costos = $actualizar_costos; // actualizar los costos 
        }



   public function onRow(Row $row)
    {
        
            $rowIndex = $row->getIndex();
            $row      = $row->toArray();

            $usuario_id = Auth::user()->id;

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
    		
            $product = Product::where('barcode', $row['codigo'])->where('comercio_id',$this->casa_central_id)->where('eliminado',0)->first();
            
            if($product) {
            
            $variacion = $row['codigo_variacion'] ?? 0;
            $costo = $row['costo'] ?? $product->cost;
            $iva = $row['iva'] ?? 0;
            $cantidad = $row['cantidad'] ?? 0;
            
            $producto_variacion = productos_variaciones_datos::where('product_id', $product)->where('codigo_variacion', $variacion)->where('comercio_id',$this->casa_central_id)->where('eliminado',0)->first();
            

            if($producto_variacion != null) {
                $nombre_variacion = $producto_variacion->variaciones;
                $referencia_variacion = $producto_variacion->referencia_variacion;
            
                if($this->actualizar_costos == true) {
    		        $producto_variacion->cost = $costo;
    		        $producto_variacion->save();
    		    }
    		
    		
            } else {
                $nombre_variacion = "";
                $referencia_variacion = 0;
                
                if($this->actualizar_costos == true) {
    		        $product->cost = $costo;
    		        $product->save();
    		    }
            
            }
            $cart = new Cart;
            
            
            $this->id_cart = $product->id.'-'.$variacion;
            $this->name = $product->name." ".$nombre_variacion;

              $product = array(
                  "id" => $this->id_cart,
                  "barcode" => $product->barcode,
                  "name" => $this->name,
                  "product_id" => $product->id,
                  "referencia_variacion" => $referencia_variacion,
                  "price" => 0,
                  "iva" => $iva,
                  "cost" => $costo,
                  "qty" => $cantidad,
              );
              
        
              $cart->addProduct($product);
              
              
            } else {
                array_push($msg_error, $row['codigo']);
            }
        
             return redirect('compras');
        
          }
        
        

        

    public function sheets(): array
    {
        return [
            0 => $this,
        ];
    }


    //encabezados del archivo excel
    public function rules(): array
    {
        return [
            'precio' => 'numeric|nullable',
        ];
    }

    public function customValidationMessages()
{
    return [
        'stock.numeric' => 'La columna stock debe ser solo numeros.',
        'costo.numeric' => 'La columna costos debe ser solo numeros.',
        'precio.numeric' => 'La columna precios debe ser solo numeros.',
        'inv_ideal.numeric' => 'La columna inventario ideal debe ser solo numeros.',
    ];
}

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }










}
