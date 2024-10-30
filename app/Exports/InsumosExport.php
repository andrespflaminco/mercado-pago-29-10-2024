<?php

namespace App\Exports;

use App\Models\sucursales;
use App\Models\Product;
use App\Models\Category;
use App\Models\insumo;
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
use DB;


class InsumosExport implements FromCollection, WithHeadings, WithCustomStartCell, WithTitle, WithStyles
{
  protected $uid;

  function __construct($uid) {
      $this->uid = $uid;

  }
public function collection()
{
    // Obtiene el comercio_id según la condición
    $comercio_id = (Auth::user()->comercio_id != 1) ? Auth::user()->comercio_id : Auth::user()->id;
    $this->casa_central_id = Auth::user()->casa_central_user_id;

    // Construye las consultas para la casa central
    $query_SC = "IFNULL((SELECT stock FROM insumos_stock_sucursales WHERE insumo_id = insumos.id AND sucursal_id = {$this->casa_central_id} LIMIT 1), 0) AS BASE{$this->casa_central_id}";
    $query_CC = "IFNULL((SELECT (insumos_stock_sucursales.stock * insumos.cantidad) FROM insumos_stock_sucursales WHERE insumo_id = insumos.id AND sucursal_id = {$this->casa_central_id} LIMIT 1), 0) AS cantidad_BASE{$this->casa_central_id}";

    $SS = sucursales::where('casa_central_id', $this->casa_central_id)->where('eliminado', 0)->get();

    $stock_sucu = [];

    if(!$SS->isEmpty()) {
        foreach ($SS as $ss) {
            $query_SS = "IFNULL((SELECT stock FROM insumos_stock_sucursales WHERE insumo_id = insumos.id AND sucursal_id = {$ss->sucursal_id} LIMIT 1), 0) AS BASE{$ss->sucursal_id},IFNULL((SELECT (insumos_stock_sucursales.stock * insumos.cantidad) FROM insumos_stock_sucursales WHERE insumo_id = insumos.id AND sucursal_id = {$ss->sucursal_id} LIMIT 1), 0) AS cantidad_BASE{$ss->sucursal_id}";

            $stock_sucu[] = $query_SS;
        }

        $stock_sucursal = implode(", ", $stock_sucu);
    }

    $stock_central = DB::raw($query_SC);
    $cantidad_central = DB::raw($query_CC);


    if (!empty($stock_sucu)) {
    $select = [
        'insumos.barcode',
        'insumos.name',
        'insumos.cost',
        'insumos.cantidad as cant',
        $stock_central,
        $cantidad_central,
        DB::raw($stock_sucursal),
        'unidad_medidas.nombre as unidad_medida',
        'proveedores.nombre as nombre_proveedor'
    ];        

    } else {
    $select = [
        'insumos.barcode',
        'insumos.name',
        'insumos.cost',
        'insumos.cantidad as cant',
        $stock_central,
        $cantidad_central,
        'unidad_medidas.nombre as unidad_medida',
        'proveedores.nombre as nombre_proveedor'
    ];        
    }

    $data = insumo::join('proveedores', 'proveedores.id', '=', 'insumos.proveedor_id')
                  ->join('tipo_unidad_medidas', 'tipo_unidad_medidas.id', '=', 'insumos.tipo_unidad_medida')
                  ->join('unidad_medidas', 'unidad_medidas.id', '=', 'insumos.unidad_medida')
                  ->select($select)
                  ->where('insumos.comercio_id', 'like', $comercio_id)
                  ->where('insumos.eliminado', 0)
                  ->orderBy('insumos.name', 'asc')
                  ->get();

    return $data;
}

    //cabeceras del reporte
    public function headings() : array
    {
      $this->casa_central_id = Auth::user()->casa_central_user_id;
      $SS = sucursales::join('users','users.id','sucursales.sucursal_id')->select('users.name','users.id')->where('casa_central_id', $this->casa_central_id)->where('eliminado',0)->get();
      
      $header = ["CODIGO","NOMBRE","COSTO","CANTIDAD X UNIDAD","STOCK CENTRAL","CANTIDAD TOTAL CENTRAL"];

      $i = 6;
      foreach($SS as $ss) {
        $header[$i++] =  $ss->id." STOCK ".$ss->name;
        $header[$i++] =  $ss->id." CANTIDAD TOTAL ".$ss->name;
      }
      
      
      array_push($header, "UNIDAD DE MEDIDA","PROVEEDOR");
     
      return $header;
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
