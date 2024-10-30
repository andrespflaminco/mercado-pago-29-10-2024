<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;

use App\Exports\ProductsExport;
use Livewire\Component;
use Notification;
use App\Notifications\NotificarCambios;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\HeadingRowImport;
use Barryvdh\DomPDF\Facade as PDF;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\descargas;
use App\Models\descargas_etiquetas;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Product;

class ExportEtiquetas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:etiquetas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

    ini_set('memory_limit', '-1');
    set_time_limit(3000000000);
    
    $descargas = descargas::where('estado',0)->where('tipo','exportar_etiquetas')->get();

    foreach($descargas as $re) {

    $etiquetas = descargas::find($re->id);

    $etiquetas->estado = 1;
    $etiquetas->save();

    $comercio_id = $re->comercio_id;
    $filaName = $re->nombre.'.pdf';
    $id_reporte = $re->id;
    
    $datos = $re->datos_filtros;
    
    $data = explode("|",$datos);
    
    
    $nombre_producto = $data[0];
    $precio = $data[1];
    $codigo = $data[2];
    $codigo_barra = $data[3];
    $fecha_impresion = $data[4];
    $productos_elegidos = $data[6];

    if($productos_elegidos == 2){
        $prod_elegidos = descargas_etiquetas::where('descargas_id', $re->id)->get();
        $q_ss = $this->GetProductosElegidos($prod_elegidos);
    }

 $select =  ['P.name','P.id as producto_id','PV.variaciones',DB::raw("IFNULL(PV.referencia_variacion , 0) AS referencia_variacion"),'P.barcode','PV.codigo_variacion',DB::raw("IF (PLP.referencia_variacion > 0, (SELECT  precio_lista FROM `productos_lista_precios` where referencia_variacion = PV.referencia_variacion  and lista_id = 0 LIMIT 1) ,
    (SELECT  precio_lista FROM `productos_lista_precios` where product_id = P.id and lista_id = 0 LIMIT 1) ) AS price")];

      
 $products = [];

 $products = DB::table('productos_lista_precios AS PLP')
              ->select($select)
              ->leftjoin('products as P', 'P.id', 'PLP.product_id')
              ->leftjoin('productos_variaciones_datos AS PV', 'PV.referencia_variacion', 'PLP.referencia_variacion');
              
                  
                if($productos_elegidos == '2') {
                    
                    $products = $products->whereRaw($q_ss);
                }
        
                if($productos_elegidos == '1') {
                    
                $products = $products->where('P.comercio_id',$comercio_id)->where('P.eliminado', 0);
                    
                 $products = $products->where( function($query) {
			     $query->where('PV.eliminado',0)
				->orWhere('PV.eliminado', null);
				});
				
				
                }
        
              $products = $products->distinct()->get();

              if($productos_elegidos == '1') {  
            
                $prod_elegidos = $products;
                  
               }

    $pdf = PDF::loadView('pdf.etiquetas', compact('products','nombre_producto','precio','codigo','codigo_barra','fecha_impresion','comercio_id','id_reporte','prod_elegidos','productos_elegidos'));
    
    Storage::put('etiquetas/'.$filaName, $pdf->output());
    
    $etiquetas = descargas::find($re->id);

    $etiquetas->estado = 2;
    $etiquetas->save();

//   ENVIAR NOTIFICACION CUANDO ESTE LISTO PARA EXPORTAR    //

	$esquema = User::find($re->user_id);

	$notificacion = [
	'titulo' => 'PDF de etiquetas',
	'contenido' => 'Listo para Descargar'
	];

	Notification::sendNow($esquema, new NotificarCambios($notificacion));

    
 //   return $pdf_factura->file('Etiquetas.pdf'); // descargar


    }

    }

    
    
    public function GetProductosElegidos($prod_elegidos){


      $q_ss = [];
      
      foreach ($prod_elegidos as $pe) {
          
           $query_SS = " (PLP.product_id = ".$pe->producto_id." AND  PLP.referencia_variacion = '".$pe->referencia_variacion."' )";
    
          array_push($q_ss , $query_SS);
      
    }
    
    $q_ss = implode(" OR", $q_ss);
    return $q_ss;
    }
    
    
}
