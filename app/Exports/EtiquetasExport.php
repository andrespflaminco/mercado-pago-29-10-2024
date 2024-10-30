<?php

namespace App\Exports;

use App\Models\Category;
use App\Models\descargas;
use App\Models\descargas_etiquetas;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;      // para trabajar con colecciones y obtener la data
use Maatwebsite\Excel\Concerns\WithHeadings;        // para definir los títulos de encabezado
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;   // para interactuar con el libro
use Maatwebsite\Excel\Concerns\WithCustomStartCell; // para definir la celda donde inicia el reporte
use Maatwebsite\Excel\Concerns\WithTitle;           // para colocar nombre a las hojas del libro
use Maatwebsite\Excel\Concerns\WithStyles;          // para dar formato a las celdas
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;


class EtiquetasExport implements FromCollection, WithHeadings, WithCustomStartCell, WithTitle, WithStyles
{
    
    protected $id_compra, $proveedor_id,$estado_pago, $dateFrom, $dateTo;

    function __construct($id) {
        $this->id = $id;
    }


    public function collection()
    {
     

        $data =[];

        
               $etiquetas = descargas::find($this->id);
            
            
                $comercio_id = $etiquetas->comercio_id;
                $filaName = $etiquetas->nombre.'.pdf';
                $id_reporte = $etiquetas->id;
                
                $datos = $etiquetas->datos_filtros;
                
                $data = explode("|",$datos);
                
                
                $nombre_producto = $data[0];
                $precio = $data[1];
                $codigo = $data[2];
                $codigo_barra = $data[3];
                $fecha_impresion = $data[4];
                $productos_elegidos = $data[6];
            
            
                $prod_elegidos = descargas_etiquetas::where('descargas_id', $this->id)->get();
                
                
               
              $q_ss = [];
              
              foreach ($prod_elegidos as $pe) {
                  
                   $query_SS = " (PLP.product_id = ".$pe->producto_id." AND  PLP.referencia_variacion = '".$pe->referencia_variacion."' )";
            
                  array_push($q_ss , $query_SS);
              
            }
            
            $q_ss = implode(" OR", $q_ss);
                
                $products = [];
            
            
            //////////////////////// PRODUCTOS /////////////////////////////////////////////////
            


                //------------------------------------  PRECIOS  ------------------------------ //
                
                
                    $q_p1 = DB::raw("IF (PLP.referencia_variacion > 0, (SELECT  CONCAT('$ ',PLP.precio_lista) as price FROM `productos_lista_precios` where referencia_variacion = PV.referencia_variacion  and lista_id = 0 LIMIT 1) ,
                    (SELECT  CONCAT('$ ',PLP.precio_lista) as price FROM `productos_lista_precios` where product_id = P.id and lista_id = 0 LIMIT 1) ) AS price");
                    
                    $ref = DB::raw("IFNULL(PV.referencia_variacion , 0) AS referencia_variacion");
                    
                    $codigo = DB::raw("IF (PLP.referencia_variacion > 0, (SELECT  CONCAT(P.barcode,'/',PV.codigo_variacion) as concatenado FROM `productos_variaciones_datos` where referencia_variacion = PV.referencia_variacion LIMIT 1) ,
                    (SELECT  P.barcode FROM `products` where PLP.product_id = P.id LIMIT 1) ) AS codigo");
                    
                
                //----------------------------------------------------------------------------- //
                
                 $select =  ['P.name','PV.variaciones',$codigo,$q_p1];
                
                //---------------------------------------------------------------------------- //       

                $products = DB::table('productos_lista_precios AS PLP')
              ->select($select)
              ->leftjoin('products as P', 'P.id', 'PLP.product_id')
              ->leftjoin('productos_variaciones_datos AS PV', 'PV.referencia_variacion', 'PLP.referencia_variacion');
              
                  
                if($productos_elegidos == '5') {
                    
                    $products = $products->whereRaw($q_ss);
                }
        
                if($productos_elegidos == '4') {
                    
                $products = $products->where('P.comercio_id',$comercio_id)->where('P.eliminado', 0);
                    
                 $products = $products->where( function($query) {
			     $query->where('PV.eliminado',0)
				->orWhere('PV.eliminado', null);
				});
				
				
                }
        
              $products = $products->distinct()->get();

                $data = $products;

        // $data = $products;
        // dd($data);
         return $data;
    }

    //cabeceras del reporte
    public function headings() : array
    {
      return ["NOMBRE","VARIACION","CODIGO","PRECIO"];
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
