<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\sucursales;
use Illuminate\Support\Facades\Auth;
use App\Models\productos_lista_precios;
use Maatwebsite\Excel\Concerns\FromCollection;      // para trabajar con colecciones y obtener la data
use Maatwebsite\Excel\Concerns\WithHeadings;        // para definir los títulos de encabezado
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;   // para interactuar con el libro
use Maatwebsite\Excel\Concerns\WithCustomStartCell; // para definir la celda donde inicia el reporte
use Maatwebsite\Excel\Concerns\WithTitle;           // para colocar nombre a las hojas del libro
use Maatwebsite\Excel\Concerns\WithStyles;          // para dar formato a las celdas
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class ListaPreciosExport implements FromCollection, WithHeadings, WithCustomStartCell, WithTitle, WithStyles
{
  protected $listaId,$id_categoria,$id_almacen,$proveedor_elegido;

  function __construct($listaId, $id_categoria,$id_almacen,$proveedor_elegido) {

      $this->listaId = $listaId;
      $this->id_categoria =  $id_categoria;
      $this->id_almacen =  $id_almacen;
      $this->proveedor_elegido = $proveedor_elegido;

  }

    public function collection()
    {


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

        $data =[];

        if($this->listaId == 1) {



        $query = DB::raw("IF (PLP.referencia_variacion > 0, (SELECT  precio_lista FROM `productos_lista_precios` where referencia_variacion = PV.referencia_variacion  and lista_id = 0 LIMIT 1) ,
        (SELECT  precio_lista FROM `productos_lista_precios` where product_id = P.id and lista_id = 0 LIMIT 1) ) AS BASE");

        $cost = DB::raw("IF (PLP.referencia_variacion > 0, (SELECT  cost FROM `productos_variaciones_datos` where referencia_variacion = PV.referencia_variacion LIMIT 1) ,
        (SELECT  cost FROM `products` where id = P.id LIMIT 1) ) AS COST");

        $precio_interno = DB::raw("IF (PLP.referencia_variacion > 0, (SELECT  cost FROM `productos_variaciones_datos` where referencia_variacion = PV.referencia_variacion LIMIT 1) ,
        (SELECT  precio_interno FROM `products` where id = P.id LIMIT 1) ) AS PRECIO_INTERNO");

        $tipo_producto = DB::raw( 'CASE WHEN P.producto_tipo = "s" THEN "simple" ELSE "variable" END');
        
        $data = DB::table('productos_lista_precios AS PLP')
              ->select(
                  'P.name',
                  'PV.variaciones',
                  $tipo_producto,
                  'P.barcode',
                  'PV.referencia_variacion',
                  'c.name as category',
                  'pr.nombre as nombre_proveedor',
                  $query
              )
              ->leftjoin('products as P', 'P.id', 'PLP.product_id')
              ->leftjoin('productos_variaciones_datos AS PV', 'PV.referencia_variacion', 'PLP.referencia_variacion')
              ->join('categories as c','c.id','P.category_id')
              ->join('seccionalmacens as a','a.id','P.seccionalmacen_id')
              ->join('proveedores as pr','pr.id','P.proveedor_id')
              ->where('P.comercio_id', $this->casa_central_id)
              ->where('P.eliminado', 0);

              if($this->id_categoria != 0) {

              $data = $data->where('P.category_id', 'like', $this->id_categoria);

              }

              if($this->id_almacen != 0) {

              $data = $data->where('P.seccionalmacen_id', 'like', $this->id_almacen);

              }

              if($this->proveedor_elegido != 1) {

              $data = $data->where('P.proveedor_id', 'like', $this->proveedor_elegido);

              }

              $data = $data->distinct()->get();

          } else {


                    $query = DB::raw("IF (PLP.referencia_variacion > 0, (SELECT  precio_lista FROM `productos_lista_precios` where referencia_variacion = PV.referencia_variacion  and lista_id = ".$this->listaId." LIMIT 1) ,
                    (SELECT  precio_lista FROM `productos_lista_precios` where product_id = P.id and lista_id = ".$this->listaId." LIMIT 1) ) AS BASE");
                    
                    $cost = DB::raw("IF (PLP.referencia_variacion > 0, (SELECT  cost FROM `productos_variaciones_datos` where referencia_variacion = PV.referencia_variacion LIMIT 1) ,
                    (SELECT  cost FROM `products` where id = P.id LIMIT 1) ) AS COST");
            
                    $precio_interno = DB::raw("IF (PLP.referencia_variacion > 0, (SELECT  cost FROM `productos_variaciones_datos` where referencia_variacion = PV.referencia_variacion LIMIT 1) ,
                    (SELECT  precio_interno FROM `products` where id = P.id LIMIT 1) ) AS PRECIO_INTERNO");

                    $tipo_producto = DB::raw( 'CASE WHEN P.producto_tipo = "s" THEN "simple" ELSE "variable" END');

                    $data = DB::table('productos_lista_precios AS PLP')
                          ->select(
                              'P.name',
                              'PV.variaciones',
                               $tipo_producto,
                              'P.barcode',
                              'PV.referencia_variacion',
                              'c.name as category',
                              'pr.nombre as nombre_proveedor',
                              $query
                          )
                          ->leftjoin('products as P', 'P.id', 'PLP.product_id')
                          ->leftjoin('productos_variaciones_datos AS PV', 'PV.referencia_variacion', 'PLP.referencia_variacion')
                          ->join('categories as c','c.id','P.category_id')
                          ->join('seccionalmacens as a','a.id','P.seccionalmacen_id')
                          ->join('proveedores as pr','pr.id','P.proveedor_id')
                          ->where('P.comercio_id', $this->casa_central_id)
                          ->where('P.eliminado', 0);

                          if($this->id_categoria != 0) {

                          $data = $data->where('P.category_id', 'like', $this->id_categoria);

                          }

                          if($this->id_almacen != 0) {

                          $data = $data->where('P.seccionalmacen_id', 'like', $this->id_almacen);

                          }

                          if($this->proveedor_elegido != 1) {

                          $data = $data->where('P.proveedor_id', 'like', $this->proveedor_elegido);

                          }

                          $data = $data->distinct()->get();

          }


        return $data;
    }

    //cabeceras del reporte
    public function headings() : array
    {
      return ["NOMBRE","VARIACION","TIPO PRODUCTO","CODIGO","COD VARIACION","CATEGORIA","PROVEEDOR","PRECIO"];
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


}
