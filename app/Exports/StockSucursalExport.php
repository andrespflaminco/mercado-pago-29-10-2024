<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\sucursales;
use Illuminate\Support\Facades\Auth;
use App\Models\productos_lista_precios;
use App\Models\productos_stock_sucursales;
use Maatwebsite\Excel\Concerns\FromCollection;      // para trabajar con colecciones y obtener la data
use Maatwebsite\Excel\Concerns\WithHeadings;        // para definir los títulos de encabezado
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;   // para interactuar con el libro
use Maatwebsite\Excel\Concerns\WithCustomStartCell; // para definir la celda donde inicia el reporte
use Maatwebsite\Excel\Concerns\WithTitle;           // para colocar nombre a las hojas del libro
use Maatwebsite\Excel\Concerns\WithStyles;          // para dar formato a las celdas
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class StockSucursalExport implements FromCollection, WithHeadings, WithCustomStartCell, WithTitle, WithStyles
{
  protected $listaId , $id_categoria,$id_almacen,$proveedor_elegido;

  function __construct($sucursalId, $id_categoria,$id_almacen,$proveedor_elegido) {

      $this->sucursalId = $sucursalId;
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

        if($this->sucursalId == 1) {

          $query = DB::raw("IF (PSS.referencia_variacion > 0, (SELECT  stock_real FROM `productos_stock_sucursales` where referencia_variacion = PV.referencia_variacion  and sucursal_id = 0 LIMIT 1) ,
          (SELECT  stock_real FROM `productos_stock_sucursales` where product_id = P.id and sucursal_id = 0 LIMIT 1) ) AS BASE");



          $data = DB::table('productos_stock_sucursales AS PSS')
                ->select(
                    'P.name',
                    'PV.variaciones',
                    'P.barcode',
                    'PV.referencia_variacion',
                    'c.name as category',
                    'pr.nombre as nombre_proveedor',
                    'a.nombre as almacen',
                    $query
                )
                ->leftjoin('products as P', 'P.id', 'PSS.product_id')
                ->leftjoin('productos_variaciones_datos AS PV', 'PV.referencia_variacion', 'PSS.referencia_variacion')
                ->where('P.eliminado', 0)
                ->join('categories as c','c.id','P.category_id')
                ->join('seccionalmacens as a','a.id','P.seccionalmacen_id')
                ->join('proveedores as pr','pr.id','P.proveedor_id')
                ->where('P.comercio_id', $this->casa_central_id);


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


                      $query = DB::raw("IF (PSS.referencia_variacion > 0, (SELECT  stock_real FROM `productos_stock_sucursales` where referencia_variacion = PV.referencia_variacion  and sucursal_id = ".$this->sucursalId." LIMIT 1) ,
                      (SELECT  stock_real FROM `productos_stock_sucursales` where product_id = P.id and sucursal_id = ".$this->sucursalId." LIMIT 1) ) AS BASE");



                      $data = DB::table('productos_stock_sucursales AS PSS')
                            ->select(
                                'P.name',
                                'PV.variaciones',
                                'P.barcode',
                                'PV.referencia_variacion',
                                'c.name as category',
                                'pr.nombre as nombre_proveedor',
                                'a.nombre as almacen',
                                $query
                            )
                            ->leftjoin('products as P', 'P.id', 'PSS.product_id')
                            ->leftjoin('productos_variaciones_datos AS PV', 'PV.referencia_variacion', 'PSS.referencia_variacion')
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
      return ["NOMBRE","VARIACION","CODIGO","COD VARIACION","CATEGORIA","PROVEEDOR","ALMACEN","STOCK"];
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
