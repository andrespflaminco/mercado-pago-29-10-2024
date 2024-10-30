<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use App\Models\productos_variaciones_datos;
use App\Models\insumo;
use App\Models\receta;
use App\Models\proveedores;
use App\Models\insumos_stock_sucursales;
use App\Models\tipo_unidad_medida;
use App\Models\unidad_medida;
use App\Models\unidad_medida_relacion;
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

class InsumosImport implements OnEachRow, SkipsEmptyRows, WithHeadingRow,  WithValidation, SkipsOnError, WithBatchInserts, WithChunkReading, WithCalculatedFormulas, WithMultipleSheets
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
        
            $casa_central_id = Auth::user()->casa_central_user_id;

        //    si existe
        //    $unidades = $this->unidades($row);
            $costo = $this->costo($row);
            $cantidad_x_unidad = $this->cantidad_x_unidad($row);
            $proveedor = $this->proveedor($row,$casa_central_id);
	    	$usuario_id = Auth::user()->id;
            
		    /////////          UNIDAD DE MEDIDA DEL PRODUCTO     ///////////
            $this->SetUnidadesMedida($row);


            $insumos = insumo::updateOrCreate(
                [

                  'barcode'            => $row['codigo'],
                  'comercio_id'         => $comercio_id,
                  'eliminado' => 0,
                ],[
                  'name'            => $row['nombre'],
                  'barcode'         => $row['codigo'],
                  'cost'            => $costo,
                  'stock'           => 0,
                  'cantidad' => $cantidad_x_unidad,
                  'proveedor_id'     => $proveedor,
                  'unidad_medida' => $this->unidad_medida,
            	  'tipo_unidad_medida' =>  $this->tipo_unidad_medida_producto,
            	  'relacion_unidad_medida' => $this->relacion_producto_base
                  ]
            );
          
          $this->UpdateOrCreateStock($row,$insumos,$casa_central_id);
          
          $this->costo_unitario = $insumos->cost/$insumos->cantidad;

		  $this->UpdateRecetas($insumos);


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


            public function unidades($row){
            $st = $row['unidades'];
            if($st == '') {
            return 0;
            } else {
            return $row['unidades'];
            }
                
            }

            public function costo($row){
            $costo = $row['costo'];
            if($costo == '') {
              return 0;
            } else {
              return $row['costo'];
            }
            }

            public function cantidad_x_unidad($row){
            $cxu = $row['cantidad_x_unidad'];
            if($cxu == '') {
              $cantidad_x_unidad = 0;
            }
            else {
              $cantidad_x_unidad = $row['cantidad_x_unidad'];
            }
            return $cantidad_x_unidad;
            }


            public function proveedor($row,$casa_central_id){

            $prov = $row['proveedor'];

            if($prov == '') {
              $proved = "Sin proveedor";
            }
            else {
              $proved = $row['proveedor'];
            }

            $proveedor = proveedores::updateOrCreate(
                [
                  'nombre'   => $proved,
                  'comercio_id'     => $casa_central_id,
                  'creador_id'     => $casa_central_id
                ],[
                  'nombre'   => $proved,
                  'comercio_id'     => $casa_central_id,
                  'creador_id'     => $casa_central_id
                  ]
            );
            
            $proveedor_id = $proveedor->id;
            return $proveedor_id;
            }
            
            
            public function SetHistoricoStock($row,$comercio_id,$unidades,$usuario_id){
            
            $insumos = insumo::where('barcode',$row['codigo'])->where('comercio_id', $comercio_id)->where('eliminado',0)->first();

            $movimiento_stock = $insumos->stock - $unidades;

            $historico = historico_stock_insumo::create([
              'tipo_movimiento' => 8,
              'producto_id' => $insumos->id,
              'cantidad_movimiento' => $movimiento_stock,
              'stock' => $unidades,
              'usuario_id' => $usuario_id,
              'comercio_id'  => $comercio_id
            ]);


            }
            
            public function SetUnidadesMedida($row){
            
            $insumos = insumo::where('barcode',$row['codigo'])->where('eliminado',0)->first();
            if($insumos != null){
            $this->unidad_medida_producto =  $insumos->unidad_medida;
		    $this->unidad_medida = $insumos->unidad_medida;
		    $this->tipo_unidad_medida_producto =  $insumos->tipo_unidad_medida;
            $this->unidad_base = tipo_unidad_medida::where('id', $this->tipo_unidad_medida_producto)->select('unidad_base')->first();
            $this->relacion_unidad_base = unidad_medida_relacion::where('tipo_unidad_medida', $this->tipo_unidad_medida_producto)->where('unidad_medida',  $this->unidad_medida_producto)->first();
            $this->relacion_producto_base = 1/($this->relacion_unidad_base->relacion);
            } else {
            $unidad_medida = unidad_medida::where('nombre',$row['unidad_de_medida'])->first();    
            $this->unidad_medida_producto =  $unidad_medida->id;
		    $this->unidad_medida = $unidad_medida->id;
		    $this->tipo_unidad_medida_producto =  $unidad_medida->tipo_unidad_medida;
            $this->unidad_base = tipo_unidad_medida::where('id', $this->tipo_unidad_medida_producto)->select('unidad_base')->first();
            $this->relacion_unidad_base = unidad_medida_relacion::where('tipo_unidad_medida', $this->tipo_unidad_medida_producto)->where('unidad_medida',  $this->unidad_medida_producto)->first();
            $this->relacion_producto_base = 1/($this->relacion_unidad_base->relacion);
            }
            
            
            }
    public function  UpdateOrCreateStockDB($r,$insumos,$sucursal_id,$casa_central_id){
;
      insumos_stock_sucursales::updateOrCreate(
                [ 
                'sucursal_id' => $sucursal_id,
                'insumo_id' => $insumos->id,
                'comercio_id' => $casa_central_id
                ],
                [
                'stock' => $r,
                'sucursal_id' => $sucursal_id,
                'insumo_id' => $insumos->id,
                'comercio_id' => $casa_central_id
                ]
            );   
                    
    }
    

    public function UpdateOrCreateStock($row,$insumos,$casa_central_id){

        foreach ($row as $key => $r) {
          $var = explode("_", $key);
    
          ////// STOCK CASA CENTRAL ////
            if (is_array($var) && $var[0] == "stock" && $var[1] == "central") {
            if(empty($value)){$value = 0;}
               $this->UpdateOrCreateStockDB($r,$insumos,$casa_central_id,$casa_central_id);           
            }
          
          
          
          ///// STOCK SUCURSALES   /////
          if (count($var) > 2) {
            if (is_array($var) && $var[1] == "stock" && is_numeric($var[0])) {
            $this->UpdateOrCreateStockDB($r,$insumos,$var[0],$casa_central_id);
            }
          }
        }

    }
            
            public function UpdateRecetas($insumos){
                    		  
    		$recetas = receta::where('insumo_id', $insumos->id )->get();
    
            $productos_recetas = [];
    		foreach($recetas as $r) {
    		    
                
    			$receta = receta::find($r->id);
    
                $this->relacion_unidad_base_receta = unidad_medida_relacion::where('tipo_unidad_medida', $receta->tipo_unidad_medida)->where('unidad_medida',  $receta->unidad_medida)->first();
           
                $this->relation = $this->relacion_unidad_base_receta->relacion*$this->relacion_producto_base;
    			
    			$receta->update([
    				'costo_unitario' => $this->costo_unitario,
    				'relacion_medida' => $this->relation
    			]);
    			
    			array_push($productos_recetas , $receta->product_id."|".$receta->referencia_variacion);
    			
    		}
    		
		    $this->UpdateProductosRecetas($productos_recetas);
            }
            
            public function UpdateProductosRecetas($productos_recetas){
            
            foreach ($productos_recetas as $pr) {
    		$productos_recetas = explode("|",$pr);
    		
    		$producto_id = $productos_recetas[0];
    		$referencia_variacion = $productos_recetas[1];
    		
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
}