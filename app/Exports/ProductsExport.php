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

use App\Models\productos_descuentos; // Actualizacion descuentos
use App\Models\listas_descuentos; // Actualizacion descuentos

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
    //$filtros_insumos = $filter[3];
    $filtros_insumos = 0;
    
    $existe_variable = Product::where('producto_tipo','v')->where('comercio_id',$this->casa_central_id)->where('eliminado',0)->exists();
    
    $data =[];
    
    $data = $this->ReporteVariable($data,$filtros_categoria,$filtros_almacen,$filtros_proveedor,$filtros_insumos);    
    
    /*
    if($existe_variable){
        $data = $this->ReporteVariable($data,$filtros_categoria,$filtros_almacen,$filtros_proveedor,$filtros_insumos);    
    } else {
        $data = $this->ReporteSimple($data,$filtros_categoria,$filtros_almacen,$filtros_proveedor,$filtros_insumos);
    }
    */
    
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
public function headings(): array
{
    $this->casa_central_id = $this->comercio_id;
    
    $SS = sucursales::join('users', 'users.id', 'sucursales.sucursal_id')
        ->where('casa_central_id', $this->casa_central_id)
        ->where('eliminado', 0)
        ->get();
    
    $LP = lista_precios::where('comercio_id', $this->casa_central_id)
        ->where('eliminado', 0)
        ->get();
    
    $LD = listas_descuentos::where('comercio_id', $this->casa_central_id)
        ->get();    
    
    
    // Cabecera base
    $header = [
        "NOMBRE", "TIPO PRODUCTO", "VARIACION", "CODIGO", "COD VARIACION", 
        "COSTO"
    ];

    $i = count($header); // Iniciar desde el siguiente índice disponible
    
    // Agregar columnas de descuentos
    foreach ($LD as $ld) {
        $header[$i++] = $ld->id . "_DESCUENTO COSTO_" . $ld->nro_descuento;
    }

    // Agregar columnas adicionales
    array_push(
        $header, "PORCENTAJE UTILIDAD PRECIO INTERNO", "PRECIO INTERNO","PORCENTAJE UTILIDAD PRECIO" ,"PRECIO"
    );
    

    $i = count($header); // Iniciar desde el siguiente índice disponible

    // Agregar columnas de precios y porcentaje de regla de precio por cada lista de precios
    foreach ($LP as $lp) {
        $header[$i++] = $lp->id . "_PORCENTAJE_UTILIDAD_PRECIO_" . $lp->nombre;
        $header[$i++] = $lp->id . "_PRECIO_" . $lp->nombre;
    }

    $header[$i++] = "IVA";
    
    // Agregar columnas de IVA por sucursal
    foreach ($SS as $ss) {
        $header[$i++] = $ss->id . "_IVA_" . $ss->name;
    }

    $header[$i++] = "STOCK";
    
    // Agregar columnas de stock por sucursal
    foreach ($SS as $ss) {
        $header[$i++] = $ss->id . "_STOCK_" . $ss->name;
    }

    $header[$i++] = "ALMACEN";
    
    // Agregar columnas de almacén por sucursal
    foreach ($SS as $ss) {
        $header[$i++] = $ss->id . "_ALMACEN_" . $ss->name;
    }

    // Agregar columnas adicionales
    array_push(
        $header, "INV MINIMO", "MANEJA STOCK", "CATEGORIA","SUBCATEGORIA", "MARCA", 
        "PROVEEDOR", "ORIGEN", "UNIDAD DE MEDIDA", "VENTA_MOSTRADOR", 
        "VENTA_ECOMMERCE", "VENTA_WOCOMMERCE", "ETIQUETAS", 
        "CANTIDAD POR UNIDAD", "CODIGO PROVEEDOR", "ES INSUMO"
    );

    return $header;
}

/*
public function columnFormats(): array
{
    $SS = sucursales::join('users','users.id','sucursales.sucursal_id')
        ->where('casa_central_id', $this->casa_central_id)
        ->where('eliminado', 0)
        ->get();
    
    $LP = lista_precios::where('comercio_id', $this->casa_central_id)
        ->where('eliminado', 0)
        ->get();

    // Base columns count before the IVA column starts
    $baseColumns = 8; // Contando hasta la columna de precio interno

    // Adding dynamic columns for each lista_precios (both price and percentage)
    $baseColumns += $LP->count() * 2; // Precio y porcentaje para cada lista

    $formats = [];

    // Formatting for IVA central column
    $columnIndex = $baseColumns + 1; // Índice para el primer IVA (central)
    $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex);
    $formats[$columnLetter] = NumberFormat::FORMAT_PERCENTAGE_00;

    // Formatting for IVA columns for each sucursal
    foreach ($SS as $sucursal) {
        $columnIndex++;
        $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex);
        $formats[$columnLetter] = NumberFormat::FORMAT_PERCENTAGE_00;
    }

    // También puedes agregar formatos para el porcentaje_regla_precio si es necesario

    return $formats;
}
*/

public function columnFormats(): array
{
    $formats = [];
    
    // Obtener las cabeceras generadas en el método headings
    $header = $this->headings();

    // Recorrer cada columna y aplicar el formato adecuado
    foreach ($header as $index => $col) {
        // Convertir el índice de columna a la letra correspondiente
        $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);

        // Si el nombre de la columna contiene "PORCENTAJE" o "IVA", aplicar formato de porcentaje
        if (strpos($col, 'PORCENTAJE') !== false || strpos($col, 'IVA') !== false || strpos($col, 'DESCUENTO') !== false) {
            $formats[$columnLetter] = \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00;
        }
    }

    return $formats;
}

    
    public function ReporteVariable($data, $filtros_categoria, $filtros_almacen, $filtros_proveedor,$filtros_insumos)
    {
    \Log::info("Se comenz贸 la exportaci贸n variable " . $this->comercio_id);

    
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
        'subcategorias.nombre as subcategoria',
        'marcas.name as marca',
        'pr.nombre as nombre_proveedor',
        DB::raw('CASE WHEN P.tipo_producto = 1 THEN "compra" WHEN P.tipo_producto = 2 THEN "produccion" ELSE "ensamblado en la venta" END as origen'),
        DB::raw( 'CASE WHEN P.unidad_medida = 1 THEN "kilogramo" ELSE "unidad" END as unidad_medida'), 
        DB::raw( 'CASE WHEN P.mostrador_canal =1 THEN "si" ELSE "no" END as mostrador_canal'),
        DB::raw( 'CASE WHEN P.ecommerce_canal =1 THEN "si" ELSE "no" END as ecommerce_canal'),
        DB::raw( 'CASE WHEN P.wc_canal =1 THEN "si" ELSE "no" END as wc_canal'),
        'P.etiquetas',
        'P.cost',
        'P.porcentaje_regla_precio_interno',
        'P.precio_interno',
        'P.cantidad',
        'P.cod_proveedor',
        DB::raw( 'CASE WHEN P.es_insumo = 1 THEN "si" ELSE "no" END as es_insumo'))
        ->join('categories as c', 'c.id', '=', 'P.category_id')
        ->join('subcategorias', 'subcategorias.id', '=', 'P.subcategoria_id')
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
        ->select('product_id', 'referencia_variacion','lista_id', 'precio_lista','porcentaje_regla_precio')
        ->where('productos_lista_precios.lista_id',0)
        ->whereIn('product_id', $productos->pluck('id'))
        ->get();
        
        $listaPrecios = DB::table('productos_lista_precios')
        ->join('lista_precios','lista_precios.id','productos_lista_precios.lista_id')
        ->select('product_id','referencia_variacion', 'lista_id', 'precio_lista','porcentaje_regla_precio')
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

        $descuentosPorProducto = DB::table('productos_descuentos')
        ->select('productos_descuentos.product_id','productos_descuentos.referencia_variacion', 'productos_descuentos.nro_descuento', 'productos_descuentos.descuento','productos_descuentos.lista_descuento_id')
        ->whereIn('productos_descuentos.product_id', $productos->pluck('id'))
        ->get();
        
        //dd($descuentosPorProducto);
        
        $descuentosPorProducto = $descuentosPorProducto->groupBy(['product_id', 'referencia_variacion']);
        
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
                $productoReporte = $this->ArmarArray($producto, $producto_variaciones_datos, $preciosPorProductoBase, $preciosPorProducto, $ivaPorProducto, $stocksPorProducto,$descuentosPorProducto, 0);
                $reporte[] = $productoReporte; // Agregar producto simple directamente
            } 
            
            if ($producto->producto_tipo == "variable") {
                $producto_variaciones_datos = productos_variaciones_datos::where('product_id', $producto->id)->where('eliminado', 0)->get();
                
                
                // Procesar cada variación
                foreach ($producto_variaciones_datos as $producto_variaciones_dato) {
                    $productoReporte = $this->ArmarArray($producto, $producto_variaciones_dato, $preciosPorProductoBase, $preciosPorProducto, $ivaPorProducto, $stocksPorProducto,$descuentosPorProducto, $producto_variaciones_dato->referencia_variacion);
                    $productoVariacionesReporte[] = $productoReporte; // Agregar cada variación al arreglo
                }
                
                // Agregar todas las variaciones al reporte final
                $reporte = array_merge($reporte, $productoVariacionesReporte);
            }
        }
        
        //dd($reporte);
        
        return collect($reporte);
    }

    public function ArmarArray($producto,$producto_variaciones_datos,$preciosPorProductoBase,$preciosPorProducto,$ivaPorProducto,$stocksPorProducto,$descuentosPorProducto,$referencia_variacion){
            
                $productoReporte = [
                'nombre' => $producto->name,
                'producto_tipo' => $producto->producto_tipo,
                'variaciones' => $producto_variaciones_datos == null? null : $producto_variaciones_datos->variaciones,
                'barcode' => $producto->barcode,
                'codigo_variacion' => $producto_variaciones_datos == null? null : $producto_variaciones_datos->codigo_variacion,
                'cost' => $producto_variaciones_datos == null? $producto->cost : $producto_variaciones_datos->cost,
            ];
            //dd($productoReporte);


                        
                        // Obtener los descuentos de las lista
            // Asegurarse de que $descuentos sea una colección (si es un array)
            $descuentos = isset($descuentosPorProducto[$producto->id][$referencia_variacion])
                ? collect($descuentosPorProducto[$producto->id][$referencia_variacion])  // Convertir en colección si es array
                : collect([]);  // Asegurar que sea una colección vacía si no existe
            
            // Obtener las listas de descuentos
            $lista_descuentos = listas_descuentos::where('comercio_id', $this->casa_central_id)
                ->get();  
            
            // Iterar sobre cada lista de precios
            foreach ($lista_descuentos as $lista) {
                // Buscar si existe un descuento para esta lista_id
                $descuentoItem = $descuentos->first(function ($item) use ($lista) {
                    return $item->lista_descuento_id == $lista->id; // Comprobación del descuento en la lista
                });
                
                // Si se encontró un descuento, lo asignamos
                if ($descuentoItem) {
                    $nro_descuento = $descuentoItem->nro_descuento;
                    $productoReporte['descuento_' . $nro_descuento] = $descuentoItem->descuento;
                } 
                // Si no se encontró el descuento, lo asignamos con valor 0
                else {
                    $nro_descuento = $lista->nro_descuento; // Puedes cambiar esto por un valor por defecto si es necesario
                    $productoReporte['descuento_' . $nro_descuento] = "0";
                }
            }
            
            
            /*
            // Agregar descuentos de las listas
            $descuentos = isset($descuentosPorProducto[$producto->id][$referencia_variacion])
                ? $descuentosPorProducto[$producto->id][$referencia_variacion]
                : [];
            

            // Iterar sobre los descuentos y agregarlos al reporte
            foreach ($descuentos as $descuentoItem) {
                $nro_descuento = $descuentoItem->nro_descuento;
                $productoReporte['descuento_' . $nro_descuento] = $descuentoItem->descuento;
            }
            */
            

            $productoReporte['porcentaje_regla_precio_interno'] = $producto_variaciones_datos == null? $producto->porcentaje_regla_precio_interno : $producto_variaciones_datos->porcentaje_regla_precio_interno;
            $productoReporte['precio_interno'] = $producto_variaciones_datos == null? $producto->precio_interno : $producto_variaciones_datos->precio_interno;

            
            // Agregar precios de las listas
            $precios_base = isset($preciosPorProductoBase[$producto->id][$referencia_variacion]) ? 
            $preciosPorProductoBase[$producto->id][$referencia_variacion] : [];
            
            foreach ($precios_base as $precio) {
                $lista_id = $precio->lista_id;
                $productoReporte['porcentaje_regla_precio_' . $lista_id] = $precio->porcentaje_regla_precio;
                $productoReporte['precios_' . $lista_id] = $precio->precio_lista;
            }            
            
            // Agregar precios de las listas
            $precios = isset($preciosPorProducto[$producto->id][$referencia_variacion]) 
                ? $preciosPorProducto[$producto->id][$referencia_variacion] : [];
            
            /*
            $lista_precios = lista_precios::where('comercio_id',$this->casa_central_id)->where('eliminado',0)->get();
            
            // Iterar sobre los precios para incluir el porcentaje_regla_precio
            foreach ($precios as $precioItem) {
                $lista_id = $precioItem->lista_id;
                $productoReporte['porcentaje_regla_precio_' . $lista_id] = $precioItem->porcentaje_regla_precio;
                $productoReporte['precios_' . $lista_id] = $precioItem->precio_lista;
            }
            */
            
            // Obtener las listas de precios
            $lista_precios = lista_precios::where('comercio_id', $this->casa_central_id)
                                           ->where('eliminado', 0)
                                           ->get();
            
            // Iterar sobre cada lista de precios
            foreach ($lista_precios as $lista) {
                // Buscar si existe un precio para esta lista_id
                $precioItem = $precios->first(function ($item) use ($lista) {
                    return $item->lista_id == $lista->id; // Convertir ambos a enteros para evitar errores de tipo
                });
                
                // Si se encontró un precio, lo asignamos
                if ($precioItem) {
                    $productoReporte['porcentaje_regla_precio_' . $lista->id] = $precioItem->porcentaje_regla_precio;
                    $productoReporte['precios_' . $lista->id] = $precioItem->precio_lista;
                } 
                // Si no se encontró el precio, lo creamos con valor 0
                else {
                    $productoReporte['porcentaje_regla_precio_' . $lista->id] = "0";
                    $productoReporte['precios_' . $lista->id] = "0";
                }
            }
            
            $sucursales = sucursales::join('users', 'users.id', 'sucursales.sucursal_id')
            ->where('casa_central_id', $this->casa_central_id)
            ->where('eliminado', 0)
            ->get();
            
            // Agregar iva de las sucursales
            $ivas = isset($ivaPorProducto[$producto->id]) 
                ? $ivaPorProducto[$producto->id]->pluck('iva', 'sucursal_id')->toArray() : [];
            
            // iva casa central 
            $iva_casa_central = isset($ivas[$this->casa_central_id]) ? $ivas[$this->casa_central_id] : "0"; // Valor por defecto 0 si no existe
            $productoReporte['iva_' . $this->casa_central_id] = $iva_casa_central;
                
            // iva sucursales 
            foreach ($sucursales as $sucursal) {
                $sucursal_id = $sucursal->sucursal_id;
                $iva = isset($ivas[$sucursal_id]) ? $ivas[$sucursal_id] : "0"; // Valor por defecto 0 si no existe
                $productoReporte['iva_' . $sucursal_id] = $iva;
            }
            
            // Agregar stock de las sucursales
            $stocks = isset($stocksPorProducto[$producto->id][$referencia_variacion]) ? $stocksPorProducto[$producto->id][$referencia_variacion]->pluck('stock_real', 'sucursal_id')->toArray() : [];
            $almacen_ids = isset($stocksPorProducto[$producto->id][$referencia_variacion]) ? $stocksPorProducto[$producto->id][$referencia_variacion]->pluck('almacen_id', 'sucursal_id')->toArray() : [];
            
            // stock casa central 
            $casa_central_stock = 0;
            $stock_casa_central = isset($stocks[$casa_central_stock]) ? $stocks[$casa_central_stock] : "0"; // Valor por defecto 0 si no existe
            $productoReporte['stock_' . $casa_central_stock] = $stock_casa_central;
                
            // stock sucursales 
            foreach ($sucursales as $sucursal) {
                $sucursal_id = $sucursal->sucursal_id;
                $stock = isset($stocks[$sucursal_id]) ? $stocks[$sucursal_id] : "0"; // Valor por defecto 0 si no existe
                $productoReporte['stock_' . $sucursal_id] = $stock;
            }

            /*                    
            foreach ($stocks as $sucursal_id => $stock) {
                $productoReporte['stock_' . $sucursal_id] = $stock;
            }
            */
            
            
            // almacen casa central 
            if(isset($almacen_ids[$casa_central_stock])){
            $almacen_casa_central = seccionalmacen::find($almacen_ids[$casa_central_stock]);
            $productoReporte['almacen_' . $casa_central_stock] = $almacen_casa_central ? $almacen_casa_central->nombre : "Sin almacen";
            } else {
            $productoReporte['almacen_' . $casa_central_stock] = "Sin almacen";
            }
                

            // stock sucursales 
            foreach ($sucursales as $sucursal) {
             $sucursal_id = $sucursal->sucursal_id;
             if(isset($almacen_ids[$sucursal_id])){
                $almacen_casa_central = seccionalmacen::find($almacen_ids[$sucursal_id]);
                $productoReporte['almacen_' . $sucursal_id] = $almacen_casa_central ? $almacen_casa_central->nombre : "Sin almacen";
                } else {
                $productoReporte['almacen_' . $sucursal_id] = "Sin almacen";
              }
            }
            
            /*                    
            foreach ($stocks as $sucursal_id => $stock) {
                $almacen = seccionalmacen::find($almacen_ids[$sucursal_id]);
                $productoReporte['almacen_' . $sucursal_id] = $almacen ? $almacen->nombre : "Sin almacen";
            }
            */
                
            // Añadir las demás variables al mismo nivel
            $productoReporte['alerts'] = $producto->alerts;
            $productoReporte['stock_descubierto'] = $producto->stock_descubierto;
            $productoReporte['category'] = $producto->category;
            $productoReporte['subcategoria'] = $producto->subcategoria;
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

}
