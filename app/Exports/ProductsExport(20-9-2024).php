<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\Category;
use App\Models\lista_precios;
use App\Models\sucursales;
use App\Models\User;
use App\Models\descargas;
use App\Models\variaciones;
use App\Models\seccionalmacen;
use App\Models\productos_variaciones_datos;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet; 


use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;

//use App\Traits\ListaPreciosTrait;

class ProductsExport implements FromCollection, WithHeadings, WithCustomStartCell, WithTitle, WithStyles, WithColumnFormatting, WithChunkReading
{
    
  //  use ListaPreciosTrait;
     
    private $comercio_id; // declaras la propiedad
    
    public function chunkSize(): int
    {
        return 50; // N煤mero de filas a procesar por cada chunk
    }
    
    public function __construct($comercio_id, $id_reporte) 
    {
        $this->comercio_id = $comercio_id; // asignas el valor inyectado a la propiedad
        $this->id_reporte = $id_reporte; // asignas el valor inyectado a la propiedad
    
    //    $this->comercio_id = 499;
        
    //    dd($this->comercio_id);
    }


    public function collection()
    {
 
    $this->casa_central_id = $this->comercio_id;
    
   // $this->GetConfiguracionListaPrecios();
    
    $descargas_filtros = descargas::find($this->id_reporte);
    $filtros = $descargas_filtros->datos_filtros;
    
    // categoria, almacen, proveedor
    
    $filter = explode("|",$filtros);
    $filtros_categoria = $filter[0];
    $filtros_almacen = $filter[1];
    $filtros_proveedor = $filter[2];
    $filtros_insumos = $filter[3];
    
    $existe_variable = Product::where('producto_tipo','v')->where('comercio_id',$this->casa_central_id)->where('eliminado',0)->exists();
    
    $data =[];
    
    if($existe_variable){
        $data = $this->ReporteVariable($data,$filtros_categoria,$filtros_almacen,$filtros_proveedor,$filtros_insumos);    
    } else {
        $data = $this->ReporteSimple($data,$filtros_categoria,$filtros_almacen,$filtros_proveedor,$filtros_insumos);
    }
    
    \Log::error("Se hizo el get de la exportacion");
     

    $descargas = descargas::find($this->id_reporte);
    $descargas->estado = 2;
    $descargas->save();
        
        
    return $data;
    }

    public function startCell(): string
    {
        return 'A1';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true ]],
        ];
    }

    public function title(): string
    {
        return 'Catalogo de productos';
    }


    //cabeceras del reporte
    public function headings() : array
    {

     $this->casa_central_id = $this->comercio_id;
     
      $SS = sucursales::join('users','users.id','sucursales.sucursal_id')
      ->where('casa_central_id', $this->casa_central_id)
      ->where('eliminado',0)
      ->get();

      $LP = lista_precios::where('comercio_id', $this->casa_central_id)
      ->where('eliminado',0)
      ->get();

      $header = ["NOMBRE","TIPO PRODUCTO","VARIACION","CODIGO","COD VARIACION","COSTO","PRECIO INTERNO","PRECIO"];

      $i = 8;

      foreach($LP as $lp) {
        $header[$i++] = $lp->id."_PRECIO_".$lp->nombre;
      }

      $header[$i++] = "IVA";
      
      foreach($SS as $ss) {
        $header[$i++] =  $ss->id."_IVA_".$ss->name;
      }
      
      $header[$i++] = "STOCK";

      foreach($SS as $ss) {
        $header[$i++] =  $ss->id."_STOCK_".$ss->name;
      }
      
      $header[$i++] = "ALMACEN";
      
      foreach($SS as $ss) {
        $header[$i++] =  $ss->id."_ALMACEN_".$ss->name;
      }

      array_push($header, "INV MINIMO","MANEJA STOCK","CATEGORIA","MARCA","PROVEEDOR","ORIGEN","UNIDAD DE MEDIDA","VENTA_MOSTRADOR","VENTA_ECOMMERCE","VENTA_WOCOMMERCE","ETIQUETAS","CANTIDAD POR UNIDAD","CODIGO PROVEEDOR","ES INSUMO");

      return ($header);
    }


public function columnFormats(): array
{
    $SS = sucursales::join('users','users.id','sucursales.sucursal_id')
        ->where('casa_central_id', $this->casa_central_id)
        ->where('eliminado', 0)
        ->get();
    
    $LP = lista_precios::where('comercio_id', $this->casa_central_id)
    ->where('eliminado',0)
    ->get();

    // Base columns count before the IVA column starts
    // NOMBRE (1) + TIPO PRODUCTO (1) + VARIACION (1) + CODIGO (1) + COD VARIACION (1)
    // + COSTO (1) + PRECIO INTERNO (1) + PRECIO (1) = 8
    $baseColumns = 8;

    // Adding dynamic columns from lista_precios
    $baseColumns += $LP->count();

    // Adding the IVA central column
    $columnIndex = $baseColumns + 1; // +1 for the central IVA column

    // Convert column index to Excel column letter
    $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex);

    // Array of column formats
    $formats = [];

    // Apply format for the central IVA column
    $formats[$columnLetter] = NumberFormat::FORMAT_PERCENTAGE_00;

    // Apply formats for the IVA columns of the branches
    foreach ($SS as $sucursal) {
        $columnIndex++;
        $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex);
        $formats[$columnLetter] = NumberFormat::FORMAT_PERCENTAGE_00;
    }

    return $formats;
}

    public function ReporteSimple($data, $filtros_categoria, $filtros_almacen, $filtros_proveedor,$filtros_insumos)
    {
    \Log::info("Se comenz贸 la exportaci贸n simple " . $this->comercio_id);

    
        $casa_central_id = $this->casa_central_id;
    
        $productos = DB::table('products as P')
        ->select('P.id','P.name',
        DB::raw( 'CASE WHEN P.producto_tipo = "s" THEN "simple" ELSE "variable" END as producto_tipo'),
        DB::raw( 'null as variaciones'),
        'P.barcode',
        DB::raw( 'null as codigo_variacion'),
        'P.alerts',
        'P.stock_descubierto',
        'c.name as category',
        'marcas.name as marca',
        'pr.nombre as nombre_proveedor',
        DB::raw('CASE WHEN P.tipo_producto = 1 THEN "compra" WHEN P.tipo_producto = 2 THEN "produccion" ELSE "ensamblado en la venta" END as origen'),
        DB::raw( 'CASE WHEN P.unidad_medida = 1 THEN "kilogramo" ELSE "unidad" END as unidad_medida'), 
        DB::raw( 'CASE WHEN P.mostrador_canal =1 THEN "si" ELSE "no" END as mostrador_canal'),
        DB::raw( 'CASE WHEN P.ecommerce_canal =1 THEN "si" ELSE "no" END as ecommerce_canal'),
        DB::raw( 'CASE WHEN P.wc_canal =1 THEN "si" ELSE "no" END as wc_canal'),
        'P.etiquetas',
        'P.cost',
        'P.precio_interno',
        'P.cantidad',
        'P.cod_proveedor',
        DB::raw( 'CASE WHEN P.es_insumo = 1 THEN "si" ELSE "no" END as es_insumo'))
        ->join('categories as c', 'c.id', '=', 'P.category_id')
        ->join('proveedores as pr', 'pr.id', '=', 'P.proveedor_id')
        ->join('marcas', 'marcas.id', '=', 'P.marca_id')
        ->join('unidad_medidas', 'unidad_medidas.id', '=', 'P.unidad_medida');
        // Apply category filter if selected
        if ($filtros_categoria != 0) {
            $productos = $productos->where('P.category_id', $filtros_categoria);
        }
    
        // Apply provider filter if selected
        if ($filtros_proveedor != 0) {
            $productos = $productos->where('P.proveedor_id', $filtros_proveedor);
        }
        
        if($filtros_insumos != 0){
            $productos = $productos->where('P.es_insumo', $filtros_insumos); 
        }
        $productos = $productos->where('P.comercio_id', $this->casa_central_id)
        ->where('P.eliminado', 0)
        ->get();
        
        $listaPreciosBase = DB::table('productos_lista_precios')
        ->select('product_id', 'lista_id', 'precio_lista')
        ->where('productos_lista_precios.lista_id',0)
        ->whereIn('product_id', $productos->pluck('id'))
        ->get();
        
        $listaPrecios = DB::table('productos_lista_precios')
        ->join('lista_precios','lista_precios.id','productos_lista_precios.lista_id')
        ->select('product_id', 'lista_id', 'precio_lista')
        ->where('lista_precios.eliminado',0)
        ->whereIn('product_id', $productos->pluck('id'))
        ->get();
        
    //    dd($listaPrecios);    ->ma
        
        $stocks = DB::table('productos_stock_sucursales')
        ->select('product_id', 'sucursal_id', 'stock_real','almacen_id')
        ->whereIn('product_id', $productos->pluck('id'))
        ->where('eliminado',0)
        ->get();
        
    
        $ivas = DB::table('productos_ivas')
        ->select('product_id', 'sucursal_id','iva')
        ->whereIn('product_id', $productos->pluck('id'))
        ->get();
        //dd($ivas);
        
        
        // Agrupa los precios por producto ID
        $preciosPorProductoBase = $listaPreciosBase->groupBy('product_id');
        
        // Agrupa los precios por producto ID
        $preciosPorProducto = $listaPrecios->groupBy('product_id');
        
        // Agrupa los stocks por producto ID y sucursal
        $stocksPorProducto = $stocks->groupBy('product_id');
        
        // Agrupa los ivas por producto ID
        $ivaPorProducto = $ivas->groupBy('product_id');
        
        $reporte = [];
        
        foreach ($productos as $producto) {
            
            $productoReporte = [
                'nombre' => $producto->name,
                'producto_tipo' => $producto->producto_tipo,
                'variaciones' => null,
                'barcode' => $producto->barcode,
                'codigo_variacion' => null,
                'cost' => $producto->cost,
                'precio_interno' => $producto->precio_interno,
            ];
        
            
            // Agregar precios de las listas
            $precios_base = isset($preciosPorProductoBase[$producto->id]) 
                ? $preciosPorProductoBase[$producto->id]->pluck('precio_lista', 'lista_id')->toArray() 
                : [];
        
            foreach ($precios_base as $lista_id => $precio) {
                $productoReporte['precios_' . $lista_id] = $precio;
            }            
            
            // Agregar precios de las listas
            $precios = isset($preciosPorProducto[$producto->id]) 
                ? $preciosPorProducto[$producto->id]->pluck('precio_lista', 'lista_id')->toArray() 
                : [];
        
            foreach ($precios as $lista_id => $precio) {
                $productoReporte['precios_' . $lista_id] = $precio;
            }

            // Agregar iva de las sucursales
            $ivas = isset($ivaPorProducto[$producto->id]) 
                ? $ivaPorProducto[$producto->id]->pluck('iva', 'sucursal_id')->toArray() 
                : [];
        
            foreach ($ivas as $sucursal_id => $iva) {
                $productoReporte['iva_' . $sucursal_id] = $iva;
            }

            // Agregar stock de las sucursales
            $stocks = isset($stocksPorProducto[$producto->id]) ? $stocksPorProducto[$producto->id]->pluck('stock_real', 'sucursal_id')->toArray() : [];
            $almacen_ids = isset($stocksPorProducto[$producto->id]) ? $stocksPorProducto[$producto->id]->pluck('almacen_id', 'sucursal_id')->toArray() : [];
        
            foreach ($stocks as $sucursal_id => $stock) {
                $productoReporte['stock_' . $sucursal_id] = $stock;
            }
            
                    
            foreach ($stocks as $sucursal_id => $stock) {
                $almacen = seccionalmacen::find($almacen_ids[$sucursal_id]);
                $productoReporte['almacen_' . $sucursal_id] = $almacen ? $almacen->nombre : "Sin almacen";
            }
                
            // Añadir las demás variables al mismo nivel
            $productoReporte['alerts'] = $producto->alerts;
            $productoReporte['stock_descubierto'] = $producto->stock_descubierto;
            $productoReporte['category'] = $producto->category;
            $productoReporte['marca'] = $producto->marca;
            $productoReporte['nombre_proveedor'] = $producto->nombre_proveedor;
            $productoReporte['origen'] = $producto->origen;
            $productoReporte['unidad_medida'] = $producto->unidad_medida;
            $productoReporte['mostrador_canal'] = $producto->mostrador_canal;
            $productoReporte['ecommerce_canal'] = $producto->ecommerce_canal;
            $productoReporte['wc_canal'] = $producto->wc_canal;
            $productoReporte['etiquetas'] = $producto->etiquetas;  // Aquí corregí 'etiquetas' en lugar de 'alerts'
            $productoReporte['cantidad'] = $producto->cantidad;
            $productoReporte['cod_proveedor'] = $producto->cod_proveedor;
            $productoReporte['es_insumo'] = $producto->es_insumo;
    
        
            // Añadir el producto al reporte final
            $reporte[] = $productoReporte;
        }
        
        //dd($reporte);
        
        return collect($reporte);
    }

    
    public function ReporteVariable($data, $filtros_categoria, $filtros_almacen, $filtros_proveedor,$filtros_insumos)
    {
    \Log::info("Se comenz贸 la exportaci贸n simple " . $this->comercio_id);

    
        $casa_central_id = $this->casa_central_id;
    
        $productos = DB::table('products as P')
        ->select('P.id','P.name',
        DB::raw( 'CASE WHEN P.producto_tipo = "s" THEN "simple" ELSE "variable" END as producto_tipo'),
        DB::raw( 'null as variaciones'),
        'P.barcode',
        DB::raw( 'null as codigo_variacion'),
        'P.alerts',
        'P.stock_descubierto',
        'c.name as category',
        'marcas.name as marca',
        'pr.nombre as nombre_proveedor',
        DB::raw('CASE WHEN P.tipo_producto = 1 THEN "compra" WHEN P.tipo_producto = 2 THEN "produccion" ELSE "ensamblado en la venta" END as origen'),
        DB::raw( 'CASE WHEN P.unidad_medida = 1 THEN "kilogramo" ELSE "unidad" END as unidad_medida'), 
        DB::raw( 'CASE WHEN P.mostrador_canal =1 THEN "si" ELSE "no" END as mostrador_canal'),
        DB::raw( 'CASE WHEN P.ecommerce_canal =1 THEN "si" ELSE "no" END as ecommerce_canal'),
        DB::raw( 'CASE WHEN P.wc_canal =1 THEN "si" ELSE "no" END as wc_canal'),
        'P.etiquetas',
        'P.cost',
        'P.precio_interno',
        'P.cantidad',
        'P.cod_proveedor',
        DB::raw( 'CASE WHEN P.es_insumo = 1 THEN "si" ELSE "no" END as es_insumo'))
        ->join('categories as c', 'c.id', '=', 'P.category_id')
        ->join('proveedores as pr', 'pr.id', '=', 'P.proveedor_id')
        ->join('marcas', 'marcas.id', '=', 'P.marca_id')
        ->join('unidad_medidas', 'unidad_medidas.id', '=', 'P.unidad_medida');
        // Apply category filter if selected
        if ($filtros_categoria != 0) {
            $productos = $productos->where('P.category_id', $filtros_categoria);
        }
    
        // Apply provider filter if selected
        if ($filtros_proveedor != 0) {
            $productos = $productos->where('P.proveedor_id', $filtros_proveedor);
        }
        
        if($filtros_insumos != 0){
            $productos = $productos->where('P.es_insumo', $filtros_insumos); 
        }
        $productos = $productos->where('P.comercio_id', $this->casa_central_id)
        ->where('P.eliminado', 0)
        ->get();
        
        $listaPreciosBase = DB::table('productos_lista_precios')
        ->select('product_id', 'referencia_variacion','lista_id', 'precio_lista')
        ->where('productos_lista_precios.lista_id',0)
        ->whereIn('product_id', $productos->pluck('id'))
        ->get();
        
        $listaPrecios = DB::table('productos_lista_precios')
        ->join('lista_precios','lista_precios.id','productos_lista_precios.lista_id')
        ->select('product_id','referencia_variacion', 'lista_id', 'precio_lista')
        ->where('lista_precios.eliminado',0)
        ->whereIn('product_id', $productos->pluck('id'))
        ->get();
        
        $stocks = DB::table('productos_stock_sucursales')
        ->select('product_id','referencia_variacion', 'sucursal_id', 'stock_real','almacen_id')
        ->whereIn('product_id', $productos->pluck('id'))
        ->where('eliminado',0)
        ->get();
        
    
        $ivas = DB::table('productos_ivas')
        ->select('product_id', 'sucursal_id','iva')
        ->whereIn('product_id', $productos->pluck('id'))
        ->get();
        //dd($ivas);
        
        
        // Agrupa los precios por producto ID
        $preciosPorProductoBase = $listaPreciosBase->groupBy(['product_id', 'referencia_variacion']);
        
        // Agrupa los precios por producto ID y referencia de variación
        $preciosPorProducto = $listaPrecios->groupBy(['product_id', 'referencia_variacion']);
        
        // Agrupa los stocks por producto ID y referencia de variación
        $stocksPorProducto = $stocks->groupBy(['product_id', 'referencia_variacion']);
        
        // Agrupa los ivas por producto ID y referencia de variación
        $ivaPorProducto = $ivas->groupBy('product_id');

        
        $reporte = [];
        
        foreach ($productos as $producto) {
            // Inicializar un arreglo para las variaciones
            $productoVariacionesReporte = [];
            
            if ($producto->producto_tipo == "simple") {
                $producto_variaciones_datos = null;
                $productoReporte = $this->ArmarArray($producto, $producto_variaciones_datos, $preciosPorProductoBase, $preciosPorProducto, $ivaPorProducto, $stocksPorProducto, 0);
                $reporte[] = $productoReporte; // Agregar producto simple directamente
            } 
            
            if ($producto->producto_tipo == "variable") {
                $producto_variaciones_datos = productos_variaciones_datos::where('product_id', $producto->id)->where('eliminado', 0)->get();
                
                
                // Procesar cada variación
                foreach ($producto_variaciones_datos as $producto_variaciones_dato) {
                    $productoReporte = $this->ArmarArray($producto, $producto_variaciones_dato, $preciosPorProductoBase, $preciosPorProducto, $ivaPorProducto, $stocksPorProducto, $producto_variaciones_dato->referencia_variacion);
                    $productoVariacionesReporte[] = $productoReporte; // Agregar cada variación al arreglo
                }
                
                // Agregar todas las variaciones al reporte final
                $reporte = array_merge($reporte, $productoVariacionesReporte);
            }
        }

        return collect($reporte);
    }

    public function ArmarArray($producto,$producto_variaciones_datos,$preciosPorProductoBase,$preciosPorProducto,$ivaPorProducto,$stocksPorProducto,$referencia_variacion){
    
                $productoReporte = [
                'nombre' => $producto->name,
                'producto_tipo' => $producto->producto_tipo,
                'variaciones' => $producto_variaciones_datos == null? null : $producto_variaciones_datos->variaciones,
                'barcode' => $producto->barcode,
                'codigo_variacion' => $producto_variaciones_datos == null? null : $producto_variaciones_datos->codigo_variacion,
                'cost' => $producto_variaciones_datos == null? $producto->cost : $producto_variaciones_datos->cost,
                'precio_interno' => $producto_variaciones_datos == null? $producto->precio_interno : $producto_variaciones_datos->precio_interno,
            ];
            //dd($productoReporte);
            
            // Agregar precios de las listas
            $precios_base = isset($preciosPorProductoBase[$producto->id][$referencia_variacion]) ? $preciosPorProductoBase[$producto->id][$referencia_variacion]->pluck('precio_lista', 'lista_id')->toArray() : [];
            
            foreach ($precios_base as $lista_id => $precio) {
                $productoReporte['precios_' . $lista_id] = $precio;
            }            
            
            // Agregar precios de las listas
            $precios = isset($preciosPorProducto[$producto->id][$referencia_variacion]) 
                ? $preciosPorProducto[$producto->id][$referencia_variacion]->pluck('precio_lista', 'lista_id')->toArray() : [];
        
            foreach ($precios as $lista_id => $precio) {
                $productoReporte['precios_' . $lista_id] = $precio;
            }

            // Agregar iva de las sucursales
            $ivas = isset($ivaPorProducto[$producto->id]) 
                ? $ivaPorProducto[$producto->id]->pluck('iva', 'sucursal_id')->toArray() : [];
        
            foreach ($ivas as $sucursal_id => $iva) {
                $productoReporte['iva_' . $sucursal_id] = $iva;
            }

            // Agregar stock de las sucursales
            $stocks = isset($stocksPorProducto[$producto->id][$referencia_variacion]) ? $stocksPorProducto[$producto->id][$referencia_variacion]->pluck('stock_real', 'sucursal_id')->toArray() : [];
            $almacen_ids = isset($stocksPorProducto[$producto->id][$referencia_variacion]) ? $stocksPorProducto[$producto->id][$referencia_variacion]->pluck('almacen_id', 'sucursal_id')->toArray() : [];
        
            foreach ($stocks as $sucursal_id => $stock) {
                $productoReporte['stock_' . $sucursal_id] = $stock;
            }
            
                    
            foreach ($stocks as $sucursal_id => $stock) {
                $almacen = seccionalmacen::find($almacen_ids[$sucursal_id]);
                $productoReporte['almacen_' . $sucursal_id] = $almacen ? $almacen->nombre : "Sin almacen";
            }
                
            // Añadir las demás variables al mismo nivel
            $productoReporte['alerts'] = $producto->alerts;
            $productoReporte['stock_descubierto'] = $producto->stock_descubierto;
            $productoReporte['category'] = $producto->category;
            $productoReporte['marca'] = $producto->marca;
            $productoReporte['nombre_proveedor'] = $producto->nombre_proveedor;
            $productoReporte['origen'] = $producto->origen;
            $productoReporte['unidad_medida'] = $producto->unidad_medida;
            $productoReporte['mostrador_canal'] = $producto->mostrador_canal;
            $productoReporte['ecommerce_canal'] = $producto->ecommerce_canal;
            $productoReporte['wc_canal'] = $producto->wc_canal;
            $productoReporte['etiquetas'] = $producto->etiquetas;  // Aquí corregí 'etiquetas' en lugar de 'alerts'
            $productoReporte['cantidad'] = $producto->cantidad;
            $productoReporte['cod_proveedor'] = $producto->cod_proveedor;
            $productoReporte['es_insumo'] = $producto->es_insumo;
            
            return $productoReporte;

}    


    
public function ReporteSimpleOld($data, $filtros_categoria, $filtros_almacen, $filtros_proveedor,$filtros_insumos)
{
    \Log::info("Se comenz贸 la exportaci贸n simple " . $this->comercio_id);

    $casa_central_id = $this->casa_central_id;

    // Fetching the price lists for the main branch
    $LP = lista_precios::where('comercio_id', $this->casa_central_id)->get();

    $q_lp = [];

    // If there are price lists, construct the price list queries
    if ($LP->isNotEmpty()) {
        foreach ($LP as $lp) {
            $query_LP = "(SELECT precio_lista FROM `productos_lista_precios` WHERE product_id = P.id AND eliminado = 0 AND lista_id = {$lp->id} LIMIT 1) AS PRECIO_{$lp->id}";
            array_push($q_lp, $query_LP);
        }

        $q_lp = implode(",", $q_lp);
    }

    $q_p2 = DB::raw($q_lp);

    // Fetching the branches for the main branch
    $SS = sucursales::where('casa_central_id', $this->casa_central_id)->where('eliminado', 0)->get();

    $q_ss = [];
    $a_ss = [];
    $iva_ss = [];

    // If there are branches, construct the stock, warehouse, and VAT queries
    if ($SS != []) {
        foreach ($SS as $ss) {
            $iva_sucursales = DB::raw("(SELECT COALESCE(iva, 0) FROM productos_ivas WHERE product_id = P.id AND sucursal_id = {$ss->sucursal_id} LIMIT 1) AS IVA{$ss->sucursal_id}");
            $query_SS = "(SELECT stock_real FROM productos_stock_sucursales WHERE product_id = P.id AND eliminado = 0 AND sucursal_id = {$ss->sucursal_id} LIMIT 1) AS BASE{$ss->sucursal_id}";
            $query_AS = "(SELECT seccionalmacens.nombre FROM productos_stock_sucursales 
                          JOIN seccionalmacens ON seccionalmacens.id = productos_stock_sucursales.almacen_id 
                          WHERE product_id = P.id AND productos_stock_sucursales.eliminado = 0 AND sucursal_id = {$ss->sucursal_id} LIMIT 1) AS ALMACEN_{$ss->sucursal_id}";

            array_push($q_ss, $query_SS);
            array_push($a_ss, $query_AS);
            array_push($iva_ss, $iva_sucursales);
        }

        $q_ss = implode(",", $q_ss);
        $a_ss = implode(",", $a_ss);
        $iva_ss = implode(",", $iva_ss);
    }

    $q_s2 = DB::raw($q_ss);
    $a_s2 = DB::raw($a_ss);
    $iva_sucursales = DB::raw($iva_ss);


        if( !empty($q_ss) && !empty($q_lp) ) {
                // Constructing the select statement
    $select = [
        'P.name',
        DB::raw('CASE WHEN P.producto_tipo = "s" THEN "simple" ELSE "variable" END'),
        DB::raw('null as variacion'),
        'P.barcode',
        DB::raw('null as codigo_variacion'),
        'P.cost',
        'P.precio_interno',
        DB::raw('(SELECT precio_lista FROM `productos_lista_precios` WHERE product_id = P.id AND lista_id = 0 AND eliminado = 0 LIMIT 1) AS PRECIO_BASE'),
        $q_p2,
        DB::raw("(SELECT COALESCE(iva, 0) FROM productos_ivas WHERE product_id = P.id AND sucursal_id = {$casa_central_id} LIMIT 1) AS IVA_BASE"),
        $iva_sucursales,
        DB::raw("(SELECT stock_real FROM productos_stock_sucursales WHERE product_id = P.id AND sucursal_id = 0 AND productos_stock_sucursales.eliminado = 0 LIMIT 1) AS BASE"),
        DB::raw("(SELECT seccionalmacens.nombre FROM productos_stock_sucursales 
                  JOIN seccionalmacens ON seccionalmacens.id = productos_stock_sucursales.almacen_id 
                  WHERE product_id = P.id AND sucursal_id = 0 AND productos_stock_sucursales.eliminado = 0 LIMIT 1) AS ALMACEN_BASE"),
        $q_s2,
        $a_s2,
        'P.alerts',
        'P.stock_descubierto',
        'c.name as category',
        'marcas.name as marca',
        'pr.nombre as nombre_proveedor',
        DB::raw('CASE WHEN P.tipo_producto = 1 THEN "compra" WHEN P.tipo_producto = 2 THEN "produccion" ELSE "ensamblado en la venta" END'),
        'unidad_medidas.nombre_completo',
        DB::raw('CASE WHEN P.mostrador_canal = 1 THEN "si" ELSE "no" END'),
        DB::raw('CASE WHEN P.ecommerce_canal = 1 THEN "si" ELSE "no" END'),
        DB::raw('CASE WHEN P.wc_canal = 1 THEN "si" ELSE "no" END'),
    //    'i.name as imagen',
        'P.etiquetas',
        'P.cantidad',
        'P.cod_proveedor',
        DB::raw( 'CASE WHEN P.es_insumo = 1 THEN "si" ELSE "no" END')
    ];
  }

        if(  !empty($q_ss) && empty($q_lp) ) {
                // Constructing the select statement
    $select = [
        'P.name',
        DB::raw('CASE WHEN P.producto_tipo = "s" THEN "simple" ELSE "variable" END'),
        DB::raw('null as variacion'),
        'P.barcode',
        DB::raw('null as codigo_variacion'),
        'P.cost',
        'P.precio_interno',
        DB::raw('(SELECT precio_lista FROM `productos_lista_precios` WHERE product_id = P.id AND lista_id = 0 AND eliminado = 0 LIMIT 1) AS PRECIO_BASE'),
        DB::raw("(SELECT COALESCE(iva, 0) FROM productos_ivas WHERE product_id = P.id AND sucursal_id = {$casa_central_id} LIMIT 1) AS IVA_BASE"),
        $iva_sucursales,
        DB::raw("(SELECT stock_real FROM productos_stock_sucursales WHERE product_id = P.id AND sucursal_id = 0 AND productos_stock_sucursales.eliminado = 0 LIMIT 1) AS BASE"),
        DB::raw("(SELECT seccionalmacens.nombre FROM productos_stock_sucursales 
                  JOIN seccionalmacens ON seccionalmacens.id = productos_stock_sucursales.almacen_id 
                  WHERE product_id = P.id AND sucursal_id = 0 AND productos_stock_sucursales.eliminado = 0 LIMIT 1) AS ALMACEN_BASE"),
        $q_s2,
        $a_s2,
        'P.alerts',
        'P.stock_descubierto',
        'c.name as category',
        'marcas.name as marca',
        'pr.nombre as nombre_proveedor',
        DB::raw('CASE WHEN P.tipo_producto = 1 THEN "compra" WHEN P.tipo_producto = 2 THEN "produccion" ELSE "ensamblado en la venta" END'),
        'unidad_medidas.nombre_completo',
        DB::raw('CASE WHEN P.mostrador_canal = 1 THEN "si" ELSE "no" END'),
        DB::raw('CASE WHEN P.ecommerce_canal = 1 THEN "si" ELSE "no" END'),
        DB::raw('CASE WHEN P.wc_canal = 1 THEN "si" ELSE "no" END'),
    //    'i.name as imagen',
        'P.etiquetas',
        'P.cantidad',
        'P.cod_proveedor',
        DB::raw( 'CASE WHEN P.es_insumo = 1 THEN "si" ELSE "no" END')
    ];
        }


        if( empty($q_ss) && !empty($q_lp) ) {
                // Constructing the select statement
    $select = [
        'P.name',
        DB::raw('CASE WHEN P.producto_tipo = "s" THEN "simple" ELSE "variable" END'),
        DB::raw('null as variacion'),
        'P.barcode',
        DB::raw('null as codigo_variacion'),
        'P.cost',
        'P.precio_interno',
        DB::raw('(SELECT precio_lista FROM `productos_lista_precios` WHERE product_id = P.id AND lista_id = 0 AND eliminado = 0 LIMIT 1) AS PRECIO_BASE'),
        $q_p2,
        DB::raw("(SELECT COALESCE(iva, 0) FROM productos_ivas WHERE product_id = P.id AND sucursal_id = {$casa_central_id} LIMIT 1) AS IVA_BASE"),
        DB::raw("(SELECT stock_real FROM productos_stock_sucursales WHERE product_id = P.id AND sucursal_id = 0 AND productos_stock_sucursales.eliminado = 0 LIMIT 1) AS BASE"),
        DB::raw("(SELECT seccionalmacens.nombre FROM productos_stock_sucursales 
                  JOIN seccionalmacens ON seccionalmacens.id = productos_stock_sucursales.almacen_id 
                  WHERE product_id = P.id AND sucursal_id = 0 AND productos_stock_sucursales.eliminado = 0 LIMIT 1) AS ALMACEN_BASE"),
        'P.alerts',
        'P.stock_descubierto',
        'c.name as category',
        'marcas.name as marca',
        'pr.nombre as nombre_proveedor',
        DB::raw('CASE WHEN P.tipo_producto = 1 THEN "compra" WHEN P.tipo_producto = 2 THEN "produccion" ELSE "ensamblado en la venta" END'),
        'unidad_medidas.nombre_completo',
        DB::raw('CASE WHEN P.mostrador_canal = 1 THEN "si" ELSE "no" END'),
        DB::raw('CASE WHEN P.ecommerce_canal = 1 THEN "si" ELSE "no" END'),
        DB::raw('CASE WHEN P.wc_canal = 1 THEN "si" ELSE "no" END'),
    //    'i.name as imagen',
        'P.etiquetas',
        'P.cantidad',
        'P.cod_proveedor',
        DB::raw( 'CASE WHEN P.es_insumo = 1 THEN "si" ELSE "no" END')
    ];
        }

        if( empty($q_ss) && empty($q_lp) ) {
                // Constructing the select statement
    $select = [
        'P.name',
        DB::raw('CASE WHEN P.producto_tipo = "s" THEN "simple" ELSE "variable" END'),
        DB::raw('null as variacion'),
        'P.barcode',
        DB::raw('null as codigo_variacion'),
        'P.cost',
        'P.precio_interno',
        DB::raw('(SELECT precio_lista FROM `productos_lista_precios` WHERE product_id = P.id AND lista_id = 0 AND eliminado = 0 LIMIT 1) AS PRECIO_BASE'),
        DB::raw("(SELECT COALESCE(iva, 0) FROM productos_ivas WHERE product_id = P.id AND sucursal_id = {$casa_central_id} LIMIT 1) AS IVA_BASE"),
        DB::raw("(SELECT stock_real FROM productos_stock_sucursales WHERE product_id = P.id AND sucursal_id = 0 AND productos_stock_sucursales.eliminado = 0 LIMIT 1) AS BASE"),
        DB::raw("(SELECT seccionalmacens.nombre FROM productos_stock_sucursales 
                  JOIN seccionalmacens ON seccionalmacens.id = productos_stock_sucursales.almacen_id 
                  WHERE product_id = P.id AND sucursal_id = 0 AND productos_stock_sucursales.eliminado = 0 LIMIT 1) AS ALMACEN_BASE"),
        'P.alerts',
        'P.stock_descubierto',
        'c.name as category',
        'marcas.name as marca',
        'pr.nombre as nombre_proveedor',
        DB::raw('CASE WHEN P.tipo_producto = 1 THEN "compra" WHEN P.tipo_producto = 2 THEN "produccion" ELSE "ensamblado en la venta" END'),
        'unidad_medidas.nombre_completo',
        DB::raw('CASE WHEN P.mostrador_canal = 1 THEN "si" ELSE "no" END'),
        DB::raw('CASE WHEN P.ecommerce_canal = 1 THEN "si" ELSE "no" END'),
        DB::raw('CASE WHEN P.wc_canal = 1 THEN "si" ELSE "no" END'),
    //    'i.name as imagen',
        'P.etiquetas',
        'P.cantidad',
        'P.cod_proveedor',
        DB::raw( 'CASE WHEN P.es_insumo = 1 THEN "si" ELSE "no" END')
    ];
        }
        


    // Main query to fetch the data
    $data = DB::table('productos_lista_precios AS PLP')
        ->select($select)
        ->join('products as P', 'P.id', '=', 'PLP.product_id')
        ->join('productos_stock_sucursales as PSS', 'PSS.product_id', '=', 'P.id')
        ->join('productos_ivas', 'productos_ivas.product_id', '=', 'P.id')
        ->join('categories as c', 'c.id', '=', 'P.category_id')
        ->join('proveedores as pr', 'pr.id', '=', 'P.proveedor_id')
        ->join('marcas', 'marcas.id', '=', 'P.marca_id')
        ->leftJoin('imagenes as i', 'i.url', '=', 'P.image')
        ->join('unidad_medidas', 'unidad_medidas.id', '=', 'P.unidad_medida')
        ->where('P.comercio_id', $this->casa_central_id)
        ->where('P.eliminado', 0)
        ->where('PLP.eliminado', 0)
        ->where('PSS.eliminado', 0);

    // Apply category filter if selected
    if ($filtros_categoria != 0) {
        $data = $data->where('P.category_id', $filtros_categoria);
    }

    // Apply provider filter if selected
    if ($filtros_proveedor != 0) {
        $data = $data->where('P.proveedor_id', $filtros_proveedor);
    }
    
    if($filtros_insumos != 0){
        $data = $data->where('P.es_insumo', $filtros_insumos); 
    }
    
    // Fetch the distinct data
    $data = $data->distinct()->get();

    return $data;
}


}
