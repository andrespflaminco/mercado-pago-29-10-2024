<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use App\Models\insumo;
use App\Models\receta;
use App\Models\proveedores;
use App\Models\tipo_unidad_medida;
use App\Models\unidad_medida;
use App\Models\unidad_medida_relacion;
use App\Models\productos_variaciones_datos;
use App\Models\historico_stock;
use App\Models\historico_stock_insumo;
use App\Models\productos_stock_sucursales;
use App\Models\productos_lista_precios;
use App\Models\seccionalmacen;
use Illuminate\Support\Facades\Auth;
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

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RecetasImport implements OnEachRow, SkipsEmptyRows, WithHeadingRow,  WithValidation, SkipsOnError, WithBatchInserts, WithChunkReading, WithCalculatedFormulas, WithMultipleSheets
{
    use SkipsErrors;

    public $comercio_id, $prov, $proveed, $alm, $al, $categ, $movimiento_stock, $cat, $costo, $stock, $unidades, $cantidad_x_unidad, $cantidad_total, $st, $cost, $inv_minimo, $in_minimo, $inv_ideal, $in_ideal, $prec, $pre, $maneja_stock;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */





   public function onRow(Row $row)
    {

        $rowIndex = $row->getIndex();
        $row      = $row->toArray();

        $usuario_id = Auth::user()->id;

        if(Auth::user()->comercio_id != 1)
    		$comercio_id = Auth::user()->comercio_id;
    		else
    		$comercio_id = Auth::user()->id;


            //////////// CANTIDAD //////////////

            $ct = $row['cantidad_insumo'];

            if($ct == '') {
              $cantidad = 0;
            }
            else {
              $cantidad = $row['cantidad_insumo'];
            }
            
            


            
            //////////// RINDE //////////////

            $rendimiento = $row['rinde'];

            if($rendimiento == '') {
              $rinde = 0;
            }
            else {
              $rinde = $row['rinde'];
            }
            
            ///////////////////////////////////////////////////
            $producto = Product::where('barcode',$row['cod_producto'])->where('comercio_id', $comercio_id)->where('eliminado',0)->first();

            if($producto != null) { 
            
            $insumos = insumo::where('barcode',$row['cod_insumo'])->where('comercio_id', $comercio_id)->where('eliminado',0)->first();
           
           if($row['unidad_de_medida'] != '') {
            $unidad_medida = unidad_medida::where('nombre', $row['unidad_de_medida'])->first()->id;
            
            } else {
                
                $unidad_medida = $insumos->unidad_medida;
                
            }

            
            

	    	$usuario_id = Auth::user()->id;
	    	
            //////////// REF VARIACION //////////////

            $rv = $row['cod_variacion'];

            if($rv == '') {
              $ref_variacion = 0;
            }
            else {
              $ref_variacion = $row['cod_variacion'];
              
              
              $ref_variacion = productos_variaciones_datos::where('product_id', $producto->id)->where('codigo_variacion', $row['cod_variacion'])->where('eliminado',0)->first();
              
              
            //  dd($ref_variacion);
              
              $ref_variacion = $ref_variacion->referencia_variacion;
            }

		    /////////          UNIDAD DE MEDIDA DEL PRODUCTO Y RELACION    ///////////

                
            $this->unidad_medida_selected = unidad_medida::find($unidad_medida);
            
            
            $this->tipo_unidad_medida_elegida =   $this->unidad_medida_selected->tipo_unidad_medida;
                
            $this->unidad_base_elegida = tipo_unidad_medida::where('id', $this->unidad_medida_selected->tipo_unidad_medida)->select('unidad_base')->first();
                
            $this->relacion_unidad_base_elegida = unidad_medida_relacion::where('tipo_unidad_medida', $this->tipo_unidad_medida_elegida)->where('unidad_medida',  $unidad_medida)->first();
            
            
            $this->relacion_elegida_base =  $this->relacion_unidad_base_elegida->relacion;
                
            ///////////////////////////////////////////////////////////////////////////////
                
            $this->costo_unitario_insumo = $insumos->cost/$insumos->cantidad;
            
            
            $this->relacion_producto_base = $insumos->relacion_unidad_medida;
            
            $this->relacion = $this->relacion_elegida_base*$this->relacion_producto_base;
                
                
            $this->cost = $this->costo_unitario_insumo ;
            
                
            ////////////////////////////////////////////////////////////////////////////////

            $group = receta::updateOrCreate(
                [
                  'insumo_id' => $insumos->id,
                  'product_id' => $producto->id,
                  'referencia_variacion' => $ref_variacion,
                  'eliminado' => 0
                ],[
                    'insumo_id' => $insumos->id,
                    'nombre' => $insumos->name,
                    'cantidad' => $cantidad,
                    'costo_unitario' => $this->cost,
                    'relacion_medida' => $this->relacion,
                    'unidad_medida' => $unidad_medida,
                    'tipo_unidad_medida' => $this->tipo_unidad_medida_elegida,
                    'comercio_id' => $producto->comercio_id,
                    'product_id' => $producto->id,
                    'rinde' => $rinde,
                    'referencia_variacion' => $ref_variacion
                 
                  ]
            );
            
            // Agregar el actualizar los precios de los productos
    
            $this->ActualizarCostos($producto->id,$ref_variacion);    
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
            'name' => Rule::unique('products', 'name'),
            'stock' => 'numeric|nullable',
            'precio' => 'numeric|nullable',
            'costo' => 'numeric|nullable',
            'inv_ideal' => 'numeric|nullable',
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


    public function ActualizarCostos($producto_id,$referencia_variacion){

        $cost = receta::where('product_id',$producto_id)
		->where('referencia_variacion',$referencia_variacion)
		->select('recetas.product_id','recetas.referencia_variacion','recetas.rinde',receta::raw(' (CASE WHEN recetas.eliminado = 0 THEN ( SUM(recetas.cantidad*recetas.costo_unitario*recetas.relacion_medida)) ELSE 0 END) AS cost'))
		->groupBy('recetas.product_id','recetas.referencia_variacion','recetas.rinde','recetas.eliminado')
		->first();
		
		if($referencia_variacion != 0) {
		$update_product = productos_variaciones_datos::where('product_id',$producto_id)->where('referencia_variacion',$referencia_variacion)->orderBy('id','desc')->first();
		$update_product->cost = $cost->cost/$cost->rinde;
		$update_product->save();
		} else {
		 $update_product = Product::find($producto_id);
		 $update_product->cost = $cost->cost/$cost->rinde;
		 $update_product->save();
		}

    }




}
