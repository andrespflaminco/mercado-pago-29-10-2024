<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\proveedores;
use App\Models\ClientesMostrador;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;      // para trabajar con colecciones y obtener la data
use Maatwebsite\Excel\Concerns\WithHeadings;        // para definir los títulos de encabezado
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;   // para interactuar con el libro
use Maatwebsite\Excel\Concerns\WithCustomStartCell; // para definir la celda donde inicia el reporte
use Maatwebsite\Excel\Concerns\WithTitle;           // para colocar nombre a las hojas del libro
use Maatwebsite\Excel\Concerns\WithStyles;          // para dar formato a las celdas
use Carbon\Carbon;


class ProveedoresExport implements FromCollection, WithHeadings, WithCustomStartCell, WithTitle, WithStyles
{

    public function collection()
    {
      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;

        $data =[];

            $data = proveedores::where('proveedores.creador_id', $comercio_id)
            ->select('id_proveedor','nombre','cuit','telefono','mail','direccion','altura','piso','depto','localidad','codigo_postal','provincia','saldo_inicial_cuenta_corriente')
            ->where('eliminado',0)
            ->orderBy('proveedores.id_proveedor','asc')->get();


        return $data;
    }

    //cabeceras del reporte
    public function headings() : array
    {
      return ["COD PROVEEDOR","NOMBRE","CUIT","TELEFONO","EMAIL","CALLE","ALTURA","PISO","DEPTO","LOCALIDAD","CODIGO POSTAL","PROVINCIA","SALDO INICIAL CTA CTE"];
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
