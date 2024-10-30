<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\Category;
use App\Models\insumo;
use App\Models\receta;
use App\Models\proveedores;
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


class RecetasExport implements FromCollection, WithHeadings, WithCustomStartCell, WithTitle, WithStyles
{
  protected $uid;

  function __construct($uid) {
      $this->uid = $uid;

  }

    public function collection()
    {
      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;

        $data =[];
        

            ///////////////////////////   CANTIDAD    ///////////////////////////////////////////////
        
            $cantidad = DB::raw("IF (R.referencia_variacion > 0, (SELECT  cantidad FROM `recetas` where referencia_variacion = PV.referencia_variacion AND product_id = PV.product_id LIMIT 1) ,
            (SELECT  cantidad FROM `recetas` where product_id = P.id AND referencia_variacion = 0 LIMIT 1) ) AS cantidad");


        $data = DB::table('recetas AS R')
              ->select(
                	'P.name',
                	'PV.variaciones',
                    'P.barcode',
                    'PV.referencia_variacion',
                    'insumos.name as insumo',
                    'insumos.barcode as insumo_barcode',
                    'R.cantidad',
                    'unidad_medidas.nombre as unidad_medida',
                    'R.rinde'
              )
              
              ->leftjoin('insumos','insumos.id','R.insumo_id')
              ->leftjoin('tipo_unidad_medidas','tipo_unidad_medidas.id','R.tipo_unidad_medida')
      		  ->leftjoin('unidad_medidas','unidad_medidas.id','R.unidad_medida')
              ->leftjoin('products as P','P.id','R.product_id')
              ->leftJoin('productos_variaciones_datos AS PV', function($join){
                    $join->on('PV.referencia_variacion', '=', 'R.referencia_variacion');
                    $join->on('PV.product_id', '=', 'R.product_id');
                })
              ->where('P.comercio_id', $comercio_id)
              ->where('P.eliminado', 0)
              ->where('R.eliminado',0)
              ->distinct()->get();
  

        return $data;
    }

    //cabeceras del reporte
    public function headings() : array
    {
      return ["PRODUCTO","VARIACION","COD PRODUCTO","COD VARIACION","INSUMO","COD INSUMO","CANTIDAD INSUMO","UNIDAD DE MEDIDA","RINDE"];
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
        return 'Insumos';
    }


}
