<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;

use App\Imports\CategoriesImport;
use App\Imports\PagosImport;
use App\Imports\ProductsImport;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Category;
use App\Models\importaciones;
use App\Models\pagos_facturas;
use Illuminate\Http\Request;
use App\Models\Product;

class ImportProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:products';

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


       $importar_catalogo = importaciones::where('estado',0)->where('tipo','importar_catalogo')->get();
       
       foreach($importar_catalogo as $re) {
           
       $importar_catalogos = importaciones::find($re->id);
       
       $importar_catalogos->estado = 1;
       $importar_catalogos->save();    
       
       
       //set the path for the csv files
       $path = base_path("resources/excel-pendientes/".$re->nombre.".xlsx");

          //run 2 loops at a time
          foreach (array_slice(glob($path),0,2) as $file) {

            $currentLocation = $file;

            $file_array = explode("/", $file);
           $file_archive = $file_array[6];
//            $file_archive = $file_array[2];
            $file_name = explode(".", $file_archive);
            $comercio_id = $file_name[0];

            $headings = (new HeadingRowImport)->toArray($file);
            $import = new ProductsImport($re->comercio_id, $headings, $importar_catalogos->saltear_errores);
            Excel::import($import, $file);
    
    
            $newLocation = "resources/excel-guardados/".$re->nombre.".xlsx";

            $moved = rename($currentLocation, $newLocation);
            
           $importar_catalogos->estado = 2;
           $importar_catalogos->save(); 

         }
     
       
       }
       
    }
}
