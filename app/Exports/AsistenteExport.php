<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;      // para trabajar con colecciones y obtener la data
use Maatwebsite\Excel\Concerns\WithHeadings;        // para definir los títulos de encabezado
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;   // para interactuar con el libro
use Maatwebsite\Excel\Concerns\WithCustomStartCell; // para definir la celda donde inicia el reporte
use Maatwebsite\Excel\Concerns\WithTitle;           // para colocar nombre a las hojas del libro
use Maatwebsite\Excel\Concerns\WithStyles;          // para dar formato a las celdas
use Carbon\Carbon;

class AsistenteExport implements FromCollection, WithHeadings, WithCustomStartCell, WithTitle, WithStyles
{

    protected $id_proveedor, $reportType, $buscar;

    function __construct($id_proveedor, $reportType , $buscar) {
        $this->reportType = $reportType;
        $this->buscar = $buscar;
        $this->proveedor = $id_proveedor;

    }


    public function collection()
    {
        $data =[];

        if(Auth::user()->comercio_id != 1)
        $comercio_id = Auth::user()->comercio_id;
        else
        $comercio_id = Auth::user()->id;


        if($this->reportType == 0){



        if($this->proveedor != 0){



        $data = Product::join('categories as c','c.id','products.category_id')
        ->join('seccionalmacens as a','a.id','products.seccionalmacen_id')
        ->join('proveedores as pr','pr.id','products.proveedor_id')
        ->select('products.cod_proveedor','products.name','c.name as category','pr.nombre as nombre_proveedor','products.stock','products.inv_ideal', Product::raw('products.inv_ideal-products.stock as comprar'))
        ->where('products.comercio_id', 'like', $comercio_id)
        ->where('pr.id', 'like', $this->proveedor)
        ->orderBy('products.name','asc')
        ->get();

      } else {
        $data = Product::join('categories as c','c.id','products.category_id')
        ->join('seccionalmacens as a','a.id','products.seccionalmacen_id')
        ->join('proveedores as pr','pr.id','products.proveedor_id')
        ->select('products.cod_proveedor','products.name','c.name as category','pr.nombre as nombre_proveedor','products.stock','products.inv_ideal', Product::raw('products.inv_ideal-products.stock as comprar'))
        ->where('products.comercio_id', 'like', $comercio_id)
        ->orderBy('products.name','asc')
        ->get();
      }

    } else {
      if($this->proveedor != 0){



      $data = Product::join('categories as c','c.id','products.category_id')
      ->join('seccionalmacens as a','a.id','products.seccionalmacen_id')
      ->join('proveedores as pr','pr.id','products.proveedor_id')
      ->select('products.cod_proveedor','products.name','c.name as category','pr.nombre as nombre_proveedor','products.stock','products.inv_ideal', Product::raw('products.inv_ideal-products.stock as comprar'))
      ->where('products.comercio_id', 'like', $comercio_id)
      ->where('pr.id', 'like', $this->proveedor)
      ->whereRaw('products.stock < products.inv_ideal')
      ->orderBy('products.name','asc')
      ->get();

    } else {
      $data = Product::join('categories as c','c.id','products.category_id')
      ->join('seccionalmacens as a','a.id','products.seccionalmacen_id')
      ->join('proveedores as pr','pr.id','products.proveedor_id')
      ->select('products.cod_proveedor','products.name','c.name as category','pr.nombre as nombre_proveedor','products.stock','products.inv_ideal', Product::raw('products.inv_ideal-products.stock as comprar'))
      ->where('products.comercio_id', 'like', $comercio_id)
      ->whereRaw('products.stock < products.inv_ideal')
      ->orderBy('products.name','asc')
      ->get();
    }
    }





        return $data;
    }

    //cabeceras del reporte
    public function headings() : array
    {
      return ["CODIGO PROD. P/ PROVEEDOR","PRODUCTO","CATEGORIA","PROVEEDOR","CANTIDAD","INV. IDEAL","A COMPRAR"];
    }


    public function startCell(): string
    {
        return 'A2';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            2 => ['font' => ['bold' => true ]],
        ];
    }

    public function title(): string
    {
        return 'Reporte de Ventas';
    }


}
