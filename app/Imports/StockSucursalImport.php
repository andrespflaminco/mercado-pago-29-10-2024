<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use App\Models\productos_lista_precios;
use App\Models\proveedores;
use App\Models\historico_stock;
use App\Models\seccionalmacen;
use App\Models\productos_stock_sucursales;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class StockSucursalImport implements OnEachRow, SkipsEmptyRows, WithHeadingRow,  WithValidation, SkipsOnError, WithBatchInserts, WithChunkReading, WithCalculatedFormulas, WithMultipleSheets
{
    use SkipsErrors;

    public $comercio_id, $prov, $proveed, $alm, $al, $categ, $movimiento_stock, $cat, $costo, $stock, $st, $cost, $inv_minimo, $in_minimo, $inv_ideal, $in_ideal, $prec, $pre, $maneja_stock;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

     public function __construct($sucursal_id)
        {
         $this->sucursal_id = $sucursal_id; // errro en en linea
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

        $casa_central_id = Auth::user()->casa_central_user_id;
        
        //////////// PRECIO //////////////

        $pre = $row['stock'];

        if($pre == '') {
          $prec = 0;
        }
        else {
          $prec = $row['stock'];
        }


        if($this->sucursal_id == 1) {
        $this->sucursal_id = 0;
        $this->sucursal_id_h = $comercio_id;
        } else {
            $this->sucursal_id = $this->sucursal_id;
            $this->sucursal_id_h = $this->sucursal_id;
        
        }

          $product = Product::where('barcode', $row['codigo'])->where('comercio_id',$casa_central_id)->where('eliminado',0)->first();
          
          $productos_stock_sucursales = productos_stock_sucursales::where( 'product_id', $product->id )->where('sucursal_id', $this->sucursal_id)->first();
          
          // diferencia vieja
          $diferencia = $productos_stock_sucursales->stock_real - $productos_stock_sucursales->stock;
          
          // stock real y disponible nuevo
          $stock_real_nuevo = $prec;
          $stock_nuevo = $prec - $diferencia;
          
          // cantidad movida
          $cantidad_movimiento = $productos_stock_sucursales->stock_real - $prec;
            
          if($product != null) {
                
          $group = productos_stock_sucursales::updateOrCreate(
              [

                'product_id'            => $product->id,
                'sucursal_id'            => $this->sucursal_id_h,
              ],[
                'stock_real'           => $stock_real_nuevo,
                'stock'           => $stock_nuevo,
               ]
          );
          
    		$historico_stock = historico_stock::create([
					'tipo_movimiento' => 8,
					'producto_id' => $product->id,
					'cantidad_movimiento' => $cantidad_movimiento,
					'stock' => $stock_real,
					'usuario_id' => $comercio_id,
					'comercio_id'  => $this->sucursal_id_h
				]);


        }
        

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
        return 1500;
    }
    
    public function chunkSize(): int
    {
        return 1500;
    }







}
