<?php

namespace App\Http\Livewire;

use App\Imports\CategoriesImport;
use App\Imports\PagosImport;
use Illuminate\Support\Facades\Auth;
use App\Imports\ProductsImport;
use App\Imports\ProductsValidationImport;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Category;
use App\Models\variaciones;
use App\Models\imagenes;
use App\Models\User;
use Carbon\Carbon;
use App\Models\importaciones;
use App\Models\pagos_facturas;
use Illuminate\Http\Request;
use App\Models\Product;

use Illuminate\Support\Facades\Storage;

//
use Illuminate\Support\Facades\Bus;
use App\Jobs\IncrementCounterJob;
use Maatwebsite\Excel\HeadingRowImport;

use App\Models\proveedores;
use App\Models\historico_stock;
use App\Models\productos_variaciones;
use App\Models\productos_variaciones_datos;
use App\Models\productos_stock_sucursales;
use App\Models\productos_lista_precios;
use App\Models\seccionalmacen;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;



use Illuminate\Support\Facades\Event;

use App\Jobs\ImportJob;
use Illuminate\Queue\InteractsWithQueue;

use App\Events\UpdateProgressImport;
use App\Listeners\UpdateProgressImportListener;


use Illuminate\Support\Facades\Artisan;

use App\Jobs\ImportStartQueue;



class ImportController extends Component
{
    use WithFileUploads;

    public $contCategories, $contProducts, $fileCategories, $fileProducts ,$saltear_errores, $fila_error, $toImport, $file, $importaciones ;
    
    public $validacion_errores = [];

    public $progress = 0;

    public $filePath;

    public $rowsImport;

    public $totalRows;

    public $importRowPosition = 0;

    public $progressByStep = 0;

    public $inicioRow;

    public $finalRow;

    public $estadoImportacion = 0;

    //
    public $progressTest;

    //public $registerImportacionId;
    private $registerImportacionId;


    public function resetVariables(){
      $this->contCategories = null;
      $this->contProducts = null;
      $this->fileCategories = null;
      $this->fileProducts = null;
      $this->saltear_errores = null;
      $this->fila_error = null;
      $this->toImport = null;
      $this->file = null;
      $this->importaciones = null;    
      $this->validacion_errores = [];  
      $this->progress = 0;  
      $this->filePath = 0;;  
      $this->rowsImport = 0;
      $this->totalRows = 0;  
      //$this->importRowPosition = 0;  
      $this->progressByStep = 0;  
      $this->inicioRow;  
      $this->finalRow;  
     //private $registerImportacionId;
    
    }
   
    public function mount()
    {
      //$this->fileProducts = '';
      //$this->checkLastImport();
      //return dd($this->estadoImportacion);
      /*if($this->estadoImportacion  === null){
        $this->estadoImportacion  = 0;
       }*/   
    }
  
    
    public function render()
    {
   
      if(Auth::user()->comercio_id != 1)
    	  $comercio_id = Auth::user()->comercio_id;
    	else
    	  $comercio_id = Auth::user()->id;
    	
    	$this->comercio_id = $comercio_id;
    	
         //setup an empty array
         $records = [];

         //path where the csv files are stored
            $path = base_path('resources/excel-pendientes');

            //loop over each file
            foreach (glob($path.'/*.xlsx') as $file) {

             //open the file and add the total number of lines to the records array
                $file = new \SplFileObject($file, 'r');
                $file->seek(PHP_INT_MAX);
                $records[] = $file->key();
            }

            //now sum all the array keys together to get the total
            $toImport = array_sum($records);
            
            $this->toImport = $toImport;

          

             return view('livewire.import.component',[
                 $this->importaciones = importaciones::where('comercio_id', $comercio_id)->orderBy('created_at','desc')->get()
                 ])
            ->extends('layouts.theme.app')
            ->section('content');

            //$this->aumentarBarra();

                      
        }


        public function saveFile(){
            if($this->validacion_errores != []) {
                $this->saltear_errores = implode(",",$this->fila_error);
            }
    
            if(Auth::user()->comercio_id != 1){
                $this->comercio_id = Auth::user()->comercio_id;
            }  	
            else{
                $this->comercio_id = Auth::user()->id;
            }
            
            $this->tipo_usuario = User::find($this->comercio_id);
    
            $this->sucursal_id = $this->comercio_id;
                    
            if($this->tipo_usuario->sucursal != 1) {    
                $this->casa_central_id = $this->comercio_id;   	
            } else {    		  
                $this->casa_central = sucursales::where('sucursal_id', $this->comercio_id)->first();
                $this->casa_central_id = $this->casa_central->casa_central_id;
            }    	
    
            $path = $this->fileProducts->getRealPath();
           
            $data = file($path);     
       
            $this->nombre_archivo = 'Import_'. $this->comercio_id .'_'. str_replace(':', '_', (Carbon::now()->format('d_m_Y_H:i:s')));       
           
                   
            $this->filePath = base_path('resources/excel-pendientes/'. $this->nombre_archivo . '.xlsx');          
     

            file_put_contents($this->filePath , $data); 
            
        }

  


public function Descargar($id) {
    
    $descarga = importaciones::find($id);
    
    $file = $descarga->nombre;
        
    $path = base_path("resources/excel-guardados/".$file.".xlsx");    

    return response()->download($path);
    
}


// --------------- FUNCION QUE LEE EL EXCEL Y VALIDA LOS DATOS ---------- //
    
    public function ValidateProducts()
    {
        
        if(Auth::user()->comercio_id != 1){
          $comercio_id = Auth::user()->comercio_id;
          $this->comercio_id = Auth::user()->comercio_id;
        }
    	
    	else {
        $comercio_id = Auth::user()->id;
        $this->comercio_id = Auth::user()->comercio_id;
      }
       	
    	
    	
        $this->validate([
            'fileProducts' => 'required|mimes:xlsx,xls'
        ]);
        $import = new ProductsValidationImport();
        $array = Excel::toArray($import, $this->fileProducts);

        $i = 1;
        
        $fila_error = [];
        $validacion_error = [];
        
        foreach($array[0] as $row) {
         
        $i++;
        
        // validamos que cada variacion tenga su codigo y viceversa //
        
        if(($row['variacion'] != null && $row['cod_variacion'] == null) || ($row['variacion'] == null && $row['cod_variacion'] != null))  {
        $error = 'FILA NRO '.$i.' -> Variacion o Cod variacion estan vacias.';
        $fe = $i;
        array_push($validacion_error, $error);
        array_push($fila_error, $fe);
        }
        
        // validamos que las variaciones ingresadas existan //
        
        if($row['variacion'] != null) {
        
         $variaciones = explode("-", $row['variacion']);
         
            foreach($variaciones as $vari => $j) {

            // si la variacion tiene un campo vacio al inicio

            if ($j[0] == ' ') {
            
            $nombre_var = ltrim($j, " ");// Eliminar el primer campo vacio
            
            } else {
            $nombre_var = $j; 
            }
            
            // si la variacion tiene un campo vacio al final
            
            if($j[strlen($j)-1] == ' '){
            
            $nombre_var = substr($nombre_var, 0, -1); // Eliminar ultimo campo
            
            } else {
            $nombre_var = $nombre_var;
            }

            //    Buscamos si existen las variaciones

            $variacion = variaciones::join('atributos','atributos.id','variaciones.atributo_id')
            ->select('variaciones.*')
            ->where('variaciones.nombre',$nombre_var)
            ->where('variaciones.comercio_id',$comercio_id)
            ->where('variaciones.eliminado', 0)
            ->where('atributos.eliminado', 0)
            ->first();
            
            if($variacion == null) {
            $error = 'FILA NRO '.$i.' -> La variacion  "'.$nombre_var.'" no existe.';
            $fe = $i;
            array_push($validacion_error, $error);
            array_push($fila_error, $fe); 
            }
            
            }
            
        }
         
        //Nombre
        if(empty($row['nombre']) || $row['nombre'] === '') {
        $error = 'FILA NRO '.$i.'-> El nombre es requerido.';
        $fe = $i;
        array_push($validacion_error, $error);
        array_push($fila_error, $fe);
        }
        
        
        //origen
        if($row['origen'] != "compra" && $row['origen'] != "produccion") {
        $error = 'FILA NRO '.$i.'-> El origen debe ser "compra" o "produccion".';
        $fe = $i;
        array_push($validacion_error, $error);
        array_push($fila_error, $fe);
        }
        
        //imagen
        if($row['imagen'] != "") {
        
        
        $imagen = imagenes::where('name',$row['imagen'])->where('comercio_id',$comercio_id)->where('eliminado',0)->first();
        
        if($imagen == null) {
        $error = 'FILA NRO '.$i.'-> El nombre de la imagen no coincide con las imagenes en la biblioteca.';
        $fe = $i;
        array_push($validacion_error, $error);
        array_push($fila_error, $fe);
        }
        }
        
             
       //Codigo
      if(empty($row['codigo']) || $row['codigo'] === '') {
      $error = 'FILA NRO '.$i.' -> El codigo es requerido.';
      $fe = $i;
      array_push($validacion_error, $error);
      array_push($fila_error, $fe);
      }
      
       //Stock
       if(!empty($row['stock']) && !is_numeric($row['stock']) ) {
        $error = 'FILA NRO '.$i.' -> El stock debe ser un numero.';
        $fe = $i;
        array_push($validacion_error, $error);
        array_push($fila_error, $fe);
       }
       
       //Precio
       if(!is_numeric($row['precio']) ) {
        $error = 'FILA NRO '.$i.' -> El precio debe ser un numero.';
        $fe = $i;
        array_push($validacion_error, $error);
        array_push($fila_error, $fe);
       }
       
       
       //Costo
       if(!empty($row['costo']) && !is_numeric($row['costo']) ) {
        $error = 'FILA NRO '.$i.' -> El costo debe ser un numero.';
        $fe = $i;
        array_push($validacion_error, $error);
        array_push($fila_error, $fe);
       }
       
       
       //Inv minimo
       if(!empty($row['inv_minimo']) &&  !is_numeric($row['inv_minimo']) ) {
        $error = 'FILA NRO '.$i.' -> El inventario minimo debe ser un numero.';
        $fe = $i;
        array_push($validacion_error, $error);
        array_push($fila_error, $fe);
       }
            
    }
        
        $this->validacion_errores = $validacion_error;
        $this->fila_error = $fila_error;
        
        
       
        $this->saveFile();

      z if($this->validacion_errores == []) {        
          $this->getDataImport();
      }else{
        $this->estadoImportacion = 1;
      }
    }

    public function DeleteFile(){
   
      if (file_exists($this->filePath)) {
        unlink($this->filePath);
        $this->validacion_errores = [];
        $this->estadoImportacion = 0;     
      } 
    }
  

    public function import()
    {      
      $this->getDataImport();

    }
      

 
   public function getDataImport()
   {
    $path =  $this->filePath;

    $file = array_slice(glob($path),0,2)[0];

    $currentLocation = $file;

    $file_array = explode("/", $file);

    $file_archive = $file_array[2];          

    $file_name = explode(".", $file_archive);           

    $comercio_id = $this->comercio_id;

    $headings = (new HeadingRowImport)->toArray($file); 

    $import = new ProductsImport();

    $rows = (Excel::toArray($import, $file))[0];


    $indexesToDelete = array_map(function ($index) {
      return $index - 2;
    }, $this->fila_error);

 

  
    $rows= array_map(function ($key, $row) use ($indexesToDelete) {
      return in_array($key, $indexesToDelete) ? null : $row;
    }, array_keys($rows), $rows);

    $rows = array_values(array_filter($rows));    

    $this->rowsImport =  $rows;

    $this->totalRows = count($rows);


    $this->progress = 0;


    $this->estadoImportacion = 2;
 
   $this->registerImportacion = importaciones::create([
    'user_id' => $this->comercio_id,
    'comercio_id' => $this->comercio_id,
    'tipo' => 'importar_catalogo',
    'estado' => 0,
    'nombre' => $this->nombre_archivo,
    'saltear_errores' => 0,
    'proceso' =>  "0/" . $this->totalRows . "/procesando",
    ]);

    //ImportJob::dispatch($this->registerImportacion->id, $this->rowsImport, $this->comercio_id, $this->filePath, $this->nombre_archivo)->onQueue(Auth::user()->id);      
    ImportJob::dispatch($this->registerImportacion->id, $this->rowsImport, $this->comercio_id, $this->filePath, $this->nombre_archivo);      

    ImportStartQueue::dispatch()->onQueue('importStartQueue');      
    //ImportStartQueue::dispatch()->onQueue('importStartQueue');   

   /* Artisan::call('queue:work', [
        '--queue' => 'default',
    ]);
    */
           
    $this->checkProgress();     
   }


    protected $listeners = [
      'getDataImport' => 'getDataImport',
      'comenzarImportacion' => 'procesarImportacion',
      'prosesarImportacion' => 'procesarImportacion',
      'checkProgress' => 'checkProgress',
      'checkLastImport' => 'checkLastImport',
      
  ];


    public function accionEvento()
    {
      $this->progressTest = session('progressbar');
    }


    public function checkLastImport()
    {   
 
      $lastImportation = importaciones::where('user_id', Auth::user()->id)
      ->latest('created_at') 
      ->first();      

      if($lastImportation !== null && $lastImportation->estado === 0){
        $this->registerImportacion = $lastImportation;
        $this->estadoImportacion = 2;          
        $this->checkProgress();
      }
    }
    
    public function checkProgress()
    {   
      $importacionActual =  importaciones::find($this->registerImportacion->id);        
      $importacionActual->proceso;                  
      $proceso = explode('/', $importacionActual->proceso);
      $filaActual = $proceso[0];
      $filaTotales = $proceso[1];  
      

      $progresoActual = ($filaActual * 100)  / $filaTotales;
      $this->emit('estatus-proceso-importacion',  $filaActual, ($filaTotales - 1));
      $this->emit('progressUpdated', $progresoActual);
     
      if($importacionActual->estado  === 2){
        $this->estadoImportacion = 3;
        //$this->resetVariables();
        return redirect()->back();
      }
    }
}
