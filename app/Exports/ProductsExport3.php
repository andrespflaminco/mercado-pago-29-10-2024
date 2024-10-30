<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\productos_lista_precios;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;      // para trabajar con colecciones y obtener la data
use Maatwebsite\Excel\Concerns\WithHeadings;        // para definir los títulos de encabezado
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;   // para interactuar con el libro
use Maatwebsite\Excel\Concerns\WithCustomStartCell; // para definir la celda donde inicia el reporte
use Maatwebsite\Excel\Concerns\WithTitle;           // para colocar nombre a las hojas del libro
use Maatwebsite\Excel\Concerns\WithStyles;          // para dar formato a las celdas
use Carbon\Carbon;


class ProductsExport implements FromCollection, WithHeadings, WithCustomStartCell, WithTitle, WithStyles
{

    public function collection()
    {
      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;

        $data =[];

            $data = Product::join('categories as c','c.id','products.category_id')
        		->join('seccionalmacens as a','a.id','products.seccionalmacen_id')
            ->join('productos_lista_precios','productos_lista_precios.product_id','products.id')
        		->join('proveedores as pr','pr.id','products.proveedor_id')
        		->select('products.name','products.barcode','products.cost','productos_lista_precios.precio_lista','products.stock','products.inv_ideal','products.alerts','products.stock_descubierto','c.name as category','pr.nombre as nombre_proveedor','a.nombre as almacen',Product::raw( 'CASE WHEN products.mostrador_canal =1 THEN "si" ELSE "no"
            END'),Product::raw( 'CASE WHEN products.ecommerce_canal =1 THEN "si" ELSE "no"
            END'),Product::raw( 'CASE WHEN products.wc_canal =1 THEN "si" ELSE "no"
            END'),'products.descripcion','products.image')
        		->where('products.comercio_id', 'like', $comercio_id)
            ->where('products.eliminado', 0)
            ->where('productos_lista_precios.lista_id', 0)
        	  ->orderBy('products.name','asc')
            ->get();


        return $data;
    }

    //cabeceras del reporte
    public function headings() : array
    {
      return ["NOMBRE","CODIGO","COSTO","PRECIO","STOCK","INV_IDEAL","INV_MINIMO","MANEJA_STOCK","CATEGORIA","PROVEEDOR","ALMACEN","VENTA_MOSTRADOR","VENTA_ECOMMERCE","VENTA_WOCOMMERCE","DESCRIPCION","IMAGEN"];
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
