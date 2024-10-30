<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\Category;
use App\Models\lista_precios;
use App\Models\sucursales;
use App\Models\User;
use App\Models\descargas;
use App\Models\variaciones;
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


use App\Traits\ListaPreciosTrait;

class ProductsExport implements FromCollection, WithHeadings, WithCustomStartCell, WithTitle, WithStyles, WithColumnFormatting
{
    
    use ListaPreciosTrait;
     
    private $comercio_id; // declaras la propiedad

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
    
    $this->GetConfiguracionListaPrecios();
    
    $descargas_filtros = descargas::find($this->id_reporte);
    $filtros = $descargas_filtros->datos_filtros;
    
    // categoria, almacen, proveedor
    
    $filter = explode("|",$filtros);
    $filtros_categoria = $filter[0];
    $filtros_almacen = $filter[1];
    $filtros_proveedor = $filter[2];
    
    var_dump($filtros_categoria,$filtros_almacen,$filtros_proveedor);
    
    $data =[];


///////////////////////////   COSTOS    ///////////////////////////////////////////////
    $q_c1 = DB::raw("IF (PLP.referencia_variacion > 0, (SELECT  cost FROM `productos_variaciones_datos` where referencia_variacion = PV.referencia_variacion AND eliminado = 0 LIMIT 1) ,
    (SELECT  cost FROM `products` where id = P.id AND eliminado = 0 LIMIT 1) ) AS COSTO");



///////////////////////////   PRECIOS    ///////////////////////////////////////////////
    $q_p1 = DB::raw("IF (PLP.referencia_variacion > 0, (SELECT  precio_lista FROM `productos_lista_precios` where referencia_variacion = PV.referencia_variacion  and lista_id = 0 AND eliminado = 0 LIMIT 1) ,
    (SELECT  precio_lista FROM `productos_lista_precios` where product_id = P.id and lista_id = 0 AND eliminado = 0 LIMIT 1) ) AS PRECIO_BASE");

    $q_p1_interno = DB::raw("IF (PLP.referencia_variacion > 0, (SELECT  precio_interno FROM `productos_variaciones_datos` where referencia_variacion = PV.referencia_variacion AND eliminado = 0 LIMIT 1) ,
    (SELECT  precio_interno FROM `products` where id = P.id AND eliminado = 0 LIMIT 1) ) AS PRECIO_INTERNO");


    $LP = lista_precios::where('comercio_id', $this->casa_central_id)->get();

    $q_lp = [];


    if($LP != []) {

    foreach ($LP as $lp) {

    $query_LP = "IF (PLP.referencia_variacion > 0, (SELECT  precio_lista FROM `productos_lista_precios` where referencia_variacion = PV.referencia_variacion AND eliminado = 0  and lista_id = ".$lp->id."  LIMIT 1) ,
    (SELECT  precio_lista FROM `productos_lista_precios` where product_id = P.id AND eliminado = 0 and lista_id = ".$lp->id." LIMIT 1) ) AS PRECIO_".$lp->id;

    array_push($q_lp , $query_LP);
    }

    $q_lp = implode(",", $q_lp);

    }

    $q_p2 =  DB::raw($q_lp);

    $casa_central_id = User::find($this->comercio_id)->casa_central_user_id;
    /////////////////////////// STOCKS //////////////////////////////////////////////

      $iva_central = DB::raw("(SELECT COALESCE(iva, 0) FROM productos_ivas where product_id = P.id and sucursal_id = ".$casa_central_id." LIMIT 1) AS IVA_BASE");

      $q_s1 =  DB::raw("IF (PSS.referencia_variacion > 0, (SELECT  stock_real FROM productos_stock_sucursales where referencia_variacion = PV.referencia_variacion  and sucursal_id = 0 AND eliminado = 0 LIMIT 1) ,
      (SELECT  stock_real FROM productos_stock_sucursales where product_id = P.id and sucursal_id = 0 AND productos_stock_sucursales.eliminado = 0 LIMIT 1) ) AS BASE");
      
      $a_s1 =  DB::raw("IF (PSS.referencia_variacion > 0, (SELECT  seccionalmacens.nombre FROM productos_stock_sucursales 
      JOIN seccionalmacens ON seccionalmacens.id = productos_stock_sucursales.almacen_id
      WHERE referencia_variacion = PV.referencia_variacion  AND sucursal_id = 0 AND productos_stock_sucursales.eliminado = 0 LIMIT 1) ,
      (SELECT  seccionalmacens.nombre FROM productos_stock_sucursales JOIN seccionalmacens ON seccionalmacens.id = productos_stock_sucursales.almacen_id
      WHERE product_id = P.id and sucursal_id = 0 AND productos_stock_sucursales.eliminado = 0 LIMIT 1) ) AS ALMACEN_BASE");      
      
      //dd($q_s1);
      //$q_s1 = intval($q_s1);

      $SS = sucursales::where('casa_central_id', $this->casa_central_id)->where('eliminado',0)->get();

      $q_ss = [];
      $a_ss = [];
      $iva_ss = [];

      if($SS != []) {


      foreach ($SS as $ss) {

      
      $iva_sucursales = DB::raw("(SELECT COALESCE(iva, 0) FROM productos_ivas where product_id = P.id and sucursal_id = ".$ss->sucursal_id." LIMIT 1) AS IVA".$ss->sucursal_id);
      
      $query_SS = "IF (PSS.referencia_variacion > 0, (SELECT  stock_real FROM productos_stock_sucursales where referencia_variacion = PV.referencia_variacion AND eliminado = 0  and sucursal_id = ".$ss->sucursal_id." LIMIT 1) ,
      (SELECT  stock_real FROM productos_stock_sucursales where product_id = P.id AND eliminado = 0 and sucursal_id = ".$ss->sucursal_id." LIMIT 1) ) AS BASE".$ss->sucursal_id;

      $query_AS = "IF (PSS.referencia_variacion > 0, (SELECT  seccionalmacens.nombre FROM productos_stock_sucursales 
      JOIN seccionalmacens ON seccionalmacens.id = productos_stock_sucursales.almacen_id
      WHERE referencia_variacion = PV.referencia_variacion AND productos_stock_sucursales.eliminado = 0  and sucursal_id = ".$ss->sucursal_id." LIMIT 1) ,
      (SELECT  seccionalmacens.nombre FROM productos_stock_sucursales 
      JOIN seccionalmacens ON seccionalmacens.id = productos_stock_sucursales.almacen_id 
      WHERE product_id = P.id AND productos_stock_sucursales.eliminado = 0 and sucursal_id = ".$ss->sucursal_id." LIMIT 1) ) AS ALMACEN_".$ss->sucursal_id;

      //intval($query_SS);
      
      array_push($q_ss , $query_SS);
      array_push($a_ss , $query_AS);
      array_push($iva_ss, $iva_sucursales);
      }

      $q_ss = implode(",", $q_ss);
      $a_ss = implode(",", $a_ss);
      $iva_sucu = implode(",", $iva_ss);

      }

      $q_s2 =  DB::raw($q_ss);
      $a_s2 =  DB::raw($a_ss);
      $iva_sucursales =  DB::raw($iva_sucu);


        if( !empty($q_ss) && !empty($q_lp) ) {
        $select =  ['P.name',DB::raw( 'CASE WHEN P.producto_tipo = "s" THEN "simple" ELSE "variable"
                END'),'PV.variaciones','P.barcode','PV.codigo_variacion',$q_c1, $q_p1_interno,$q_p1, $q_p2,$iva_central, $iva_sucursales, $q_s1, $a_s1,$q_s2,$a_s2, 'P.alerts', 'P.stock_descubierto','c.name as category','marcas.name as marca','pr.nombre as nombre_proveedor','a.nombre as almacen',DB::raw('CASE WHEN P.tipo_producto = 1 THEN "compra" WHEN P.tipo_producto = 2 THEN "produccion" ELSE "ensamblado en la venta" END'),DB::raw( 'CASE WHEN P.unidad_medida = 1 THEN "si" ELSE "no"
                END'),  DB::raw( 'CASE WHEN P.mostrador_canal =1 THEN "si" ELSE "no"
                END'),DB::raw( 'CASE WHEN P.ecommerce_canal =1 THEN "si" ELSE "no"
                END'),DB::raw( 'CASE WHEN P.wc_canal =1 THEN "si" ELSE "no"
                END'),'i.name as imagen','P.etiquetas',DB::raw( 'CASE WHEN P.unidad_medida = 1 THEN "si" ELSE "no"
                END'),'P.cantidad','unidad_medidas.nombre_completo','P.cod_proveedor'];
        }

        if(  !empty($q_ss) && empty($q_lp) ) {

        $select =  ['P.name',DB::raw( 'CASE WHEN P.producto_tipo = "s" THEN "simple" ELSE "variable"
                END'),'PV.variaciones','P.barcode','PV.codigo_variacion',$q_c1, $q_p1_interno,$q_p1,$iva_central,$iva_sucursales,$q_s1, $a_s1,$q_s2,$a_s2, 'P.alerts', 'P.stock_descubierto','c.name as category','marcas.name as marca','pr.nombre as nombre_proveedor','a.nombre as almacen', DB::raw('CASE WHEN P.tipo_producto = 1 THEN "compra" WHEN P.tipo_producto = 2 THEN "produccion" ELSE "ensamblado en la venta" END'),DB::raw( 'CASE WHEN P.unidad_medida = 1 THEN "si" ELSE "no"
                END'), DB::raw( 'CASE WHEN P.mostrador_canal =1 THEN "si" ELSE "no"
                END'),DB::raw( 'CASE WHEN P.ecommerce_canal =1 THEN "si" ELSE "no"
                END'),DB::raw( 'CASE WHEN P.wc_canal =1 THEN "si" ELSE "no"
                END'),'i.name as imagen','P.etiquetas',DB::raw( 'CASE WHEN P.unidad_medida = 1 THEN "si" ELSE "no"
                END'),'P.cantidad','unidad_medidas.nombre_completo','P.cod_proveedor'];
        }


        if( empty($q_ss) && !empty($q_lp) ) {
        $select =  ['P.name',DB::raw( 'CASE WHEN P.producto_tipo = "s" THEN "simple" ELSE "variable"
                END'),'PV.variaciones','P.barcode','PV.codigo_variacion', $q_c1, $q_p1_interno,$q_p1,$q_p2,$iva_central, $q_s1,$a_s1, 'P.alerts', 'P.stock_descubierto','c.name as category','marcas.name as marca','pr.nombre as nombre_proveedor','a.nombre as almacen',DB::raw('CASE WHEN P.tipo_producto = 1 THEN "compra" WHEN P.tipo_producto = 2 THEN "produccion" ELSE "ensamblado en la venta" END'), DB::raw( 'CASE WHEN P.unidad_medida = 1 THEN "si" ELSE "no"
                END'),DB::raw( 'CASE WHEN P.mostrador_canal =1 THEN "si" ELSE "no"
                END'),DB::raw( 'CASE WHEN P.ecommerce_canal =1 THEN "si" ELSE "no"
                END'),DB::raw( 'CASE WHEN P.wc_canal =1 THEN "si" ELSE "no"
                END'),'i.name as imagen','P.etiquetas','P.cantidad','unidad_medidas.nombre_completo','P.cod_proveedor'];
        }

        if( empty($q_ss) && empty($q_lp) ) {
        $select =  ['P.name',DB::raw( 'CASE WHEN P.producto_tipo = "s" THEN "simple" ELSE "variable"
                END'),'PV.variaciones','P.barcode','PV.codigo_variacion',$q_c1, $q_p1_interno,$q_p1,$iva_central,$q_s1,$a_s1, 'P.alerts', 'P.stock_descubierto','c.name as category','marcas.name as marca','pr.nombre as nombre_proveedor','a.nombre as almacen',DB::raw('CASE WHEN P.tipo_producto = 1 THEN "compra" WHEN P.tipo_producto = 2 THEN "produccion" ELSE "ensamblado en la venta" END'),DB::raw( 'CASE WHEN P.unidad_medida = 1 THEN "si" ELSE "no"
                END'), DB::raw( 'CASE WHEN P.mostrador_canal =1 THEN "si" ELSE "no"
                END'),DB::raw( 'CASE WHEN P.ecommerce_canal =1 THEN "si" ELSE "no"
                END'),DB::raw( 'CASE WHEN P.wc_canal =1 THEN "si" ELSE "no"
                END'),'i.name as imagen','P.etiquetas','P.cantidad','unidad_medidas.nombre_completo','P.cod_proveedor'];
        }


      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $data = DB::table('productos_lista_precios AS PLP')
              ->select($select)
              ->leftjoin('products as P', 'P.id', 'PLP.product_id')
              ->leftjoin('productos_stock_sucursales as PSS','PSS.product_id','P.id')
              ->leftjoin('productos_ivas','productos_ivas.product_id','P.id')
              ->leftjoin('productos_variaciones_datos AS PV', 'PV.referencia_variacion', 'PSS.referencia_variacion')
              ->join('categories as c','c.id','P.category_id')
        	  ->join('seccionalmacens as a','a.id','P.seccionalmacen_id')
        	  ->join('proveedores as pr','pr.id','P.proveedor_id')
              ->join('marcas','marcas.id','P.marca_id') // 6-6-2024
        	  ->leftjoin('imagenes as i','i.url','P.image')
        	  ->leftjoin('unidad_medidas','unidad_medidas.id','P.unidad_medida')
              ->where('P.comercio_id', $this->casa_central_id)
              ->where('P.eliminado', 0)
              ->where('PLP.eliminado', 0) 
              ->where('PSS.eliminado', 0) 
              ->where( function($query) {
			     $query->where('PV.eliminado',0)
				->orWhere('PV.eliminado', null);
				});
				
			  if($filtros_categoria != 0){
			      $data = $data->where('P.category_id',$filtros_categoria);     
			  }
			  if($filtros_proveedor != 0){
			     $data = $data->where('P.proveedor_id',$filtros_proveedor);
			  }

              $data = $data->distinct()->get();

        

        $descargas = descargas::find($this->id_reporte);
        $descargas->estado = 2;
        $descargas->save();
        
        
        return $data;
    }

    //cabeceras del reporte
    public function headings() : array
    {

     $this->casa_central_id = $this->comercio_id;
     
      $SS = sucursales::join('users','users.id','sucursales.sucursal_id')
      ->where('casa_central_id', $this->casa_central_id)->where('eliminado',0)->get();

      $LP = lista_precios::where('comercio_id', $this->casa_central_id)->get();

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

      $header[$i++] = "ALMACEN";

      foreach($SS as $ss) {
        $header[$i++] =  $ss->id."_STOCK_".$ss->name;
      }
      
      foreach($SS as $ss) {
        $header[$i++] =  $ss->id."_ALMACEN_".$ss->name;
      }

      array_push($header, "INV MINIMO","MANEJA STOCK","CATEGORIA","MARCA","PROVEEDOR","ETIQUETA","ORIGEN","PESABLE","VENTA_MOSTRADOR","VENTA_ECOMMERCE","VENTA_WOCOMMERCE","IMAGEN","ETIQUETAS","CANTIDAD POR UNIDAD","UNIDAD DE MEDIDA","CODIGO PROVEEDOR");

      return ($header);
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


public function columnFormats(): array
{
    $SS = sucursales::join('users','users.id','sucursales.sucursal_id')
        ->where('casa_central_id', $this->casa_central_id)
        ->where('eliminado', 0)
        ->get();
    
    $LP = lista_precios::where('comercio_id', $this->casa_central_id)->get();

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


}
