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
use App\Models\recetas_costos;
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
    	
    	$this->comercio_id = $comercio_id;

        
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
            
            $insumos = Product::where('barcode',$row['cod_insumo'])->where('comercio_id', $comercio_id)->where('eliminado',0)->first();
           
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
                
            $this->costo_unitario_insumo = $insumos->cost/$insumos->cantidad;
            
            $relacion_cantidad = $cantidad/$insumos->cantidad;
            
            $this->relacion_elegida_base =  $this->relacion_unidad_base_elegida->relacion;
            
            $this->relacion_producto_base = $insumos->relacion_unidad_medida;
            
            $relacion = $this->CalcularRelacion($insumos, $this->unidad_medida_selected->id);
            
            $this->relacion = $this->relacion_elegida_base*$this->relacion_producto_base;
                
            $this->cost = $this->costo_unitario_insumo ;
            
            ////////////////////////////////////////////////////////////////////////////////

            $receta = receta::updateOrCreate(
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
                    'relacion_medida' => $relacion,
                    'unidad_medida' => $unidad_medida,
                    'tipo_unidad_medida' => $this->tipo_unidad_medida_elegida,
                    'comercio_id' => $producto->comercio_id,
                    'product_id' => $producto->id,
                    'rinde' => $rinde,
                    'referencia_variacion' => $ref_variacion,
                    'relacion_cantidades' => $relacion_cantidad
                 
                  ]
            );
            
            $this->CreateOrUpdateCostos($producto->comercio_id,$producto->id,$ref_variacion);
            $this->CreateOrUpdateCostosListas($producto->comercio_id,$producto->id,$ref_variacion);
            
    
                
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



  
  public function CreateOrUpdateCostosDB($product_id,$referencia_variacion,$lista_id,$comercio_id,$cost,$rinde){
        $precio_lista = $cost/$rinde;

       $costo = recetas_costos::UpdateOrCreate(
            [
            'product_id' => $product_id,
            'referencia_variacion' => $referencia_variacion,
            'lista_id' => $lista_id,
            'comercio_id' => $comercio_id // Asegúrate de que $this->comercio_id esté definido
            ],
            [
            'costo' => $precio_lista
            ]
            );  
        
       // return $precio_lista;
        
        return $costo;
        
  }
  
  public function CreateOrUpdateCostosListas($comercio_id,$producto_id,$referencia_variacion){
        
        $costs = receta::where('recetas.product_id', $producto_id)
        ->where('recetas.referencia_variacion', $referencia_variacion)
        ->where('lista_precios.tipo',2)
        ->join('productos_lista_precios', 'recetas.insumo_id', '=', 'productos_lista_precios.product_id')
        ->join('lista_precios','lista_precios.id','productos_lista_precios.lista_id')
        ->select(
            'productos_lista_precios.lista_id',
            'recetas.product_id',
            'recetas.referencia_variacion',
            'recetas.rinde',
            'recetas.cantidad',
            'recetas.relacion_medida',
            'productos_lista_precios.precio_lista',
            receta::raw('
                CASE 
                    WHEN recetas.eliminado = 0 THEN 
                        SUM(recetas.relacion_cantidades  * recetas.relacion_medida * productos_lista_precios.precio_lista)
                    ELSE 0 
                END AS cost
            ')
        )
       ->groupBy('productos_lista_precios.lista_id',
            'recetas.product_id',
            'recetas.referencia_variacion',
            'recetas.rinde',
            'recetas.cantidad',
            'recetas.relacion_medida',
            'productos_lista_precios.precio_lista',
            'recetas.eliminado')
       ->get();
        
        
        
            foreach ($costs as $cost) {
                // Guardar o actualizar en la tabla recetas_costos
                $costo_receta_id = $this->CreateOrUpdateCostosDB($cost->product_id,$cost->referencia_variacion,$cost->lista_id,$comercio_id,$cost->cost,$cost->rinde);
                $costo_receta = recetas_costos::find($costo_receta_id->id);
                $productos_lista_precios = productos_lista_precios::where('product_id',$cost->product_id)
                ->where('referencia_variacion',$cost->referencia_variacion)
                ->where('lista_id',$cost->lista_id)
                ->first();
                
                $costo = $costo_receta->costo ?? 0;
                
                /*
                $productos_lista_precios->update([
                    'precio_lista' => $costo
                    ]);
                */

            }
  }
  
  public function CreateOrUpdateCostos($comercio_id,$producto_id,$referencia_variacion){
      
        $cost = receta::where('product_id',$producto_id)
		->where('referencia_variacion',$referencia_variacion)
		->select('recetas.cantidad','recetas.costo_unitario','recetas.relacion_medida','recetas.product_id','recetas.referencia_variacion','recetas.rinde',receta::raw(' (CASE WHEN recetas.eliminado = 0 THEN ( SUM(recetas.cantidad*recetas.costo_unitario*recetas.relacion_medida)) ELSE 0 END) AS cost'))
		->groupBy('recetas.product_id','recetas.referencia_variacion','recetas.rinde','recetas.eliminado','recetas.cantidad','recetas.costo_unitario','recetas.relacion_medida')
		->first();
		
		$costo_receta_id = $this->CreateOrUpdateCostosDB($cost->product_id,$cost->referencia_variacion,0,$comercio_id,$cost->cost,$cost->rinde);
		$costo_receta = recetas_costos::find($costo_receta_id->id);
		
		/*
		if($referencia_variacion == 0){
		$costo = $costo_receta->costo ?? 0;
		$product = Product::find($costo_receta->product_id);
	
		$product->update([
		    "cost" => $costo_receta->costo
		    ]);
		} else {
		$productos_variaciones_datos = productos_variaciones_datos::where('product_id',$costo_receta->product_id)->where('referencia_variacion',$cost->referencia_variacion)->first();
		$productos_variaciones_datos->update([
		    "cost" => $costo_receta->costo
		    ]);
		}
		*/
		
		
  }

  public function CalcularRelacion($product,$unidad_medida_elegida){
  
  $unidad_base_elegida = unidad_medida_relacion::where('unidad_medida',$unidad_medida_elegida)->first()->relacion;
  $unidad_base_producto = unidad_medida_relacion::where('unidad_medida',$product->unidad_medida)->first()->relacion;
  
  return $unidad_base_elegida/$unidad_base_producto;     
  }
  


}
