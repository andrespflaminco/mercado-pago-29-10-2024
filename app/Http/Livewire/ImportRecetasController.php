<?php

namespace App\Http\Livewire;

use App\Imports\CategoriesImport;
use App\Imports\PagosImport;
use App\Imports\RecetasImport;
use App\Imports\InsumosImport;
use App\Imports\ProductsImport;
use App\Imports\ProductsValidationImport;
use App\Imports\SaleImport;
use App\Imports\SaleDetailsImport;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Category;
use App\Models\pagos_facturas;
use App\Models\Product;
use App\Models\unidad_medida;
use App\Models\insumo;
use App\Models\sucursales;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ImportRecetasController extends Component
{
    use WithFileUploads;

    public $contCategories, $contProducts, $fileCategories, $fileProducts, $fileVentas, $fileDetalleVentas, $fileInsumos, $fileRecetas;
        public $validacion_errores = [];

    public function render()
    {
        return view('livewire.import-recetas.component')
            ->extends('layouts.theme-pos.app')
            ->section('content');
    }

    public function uploadRecetas()
    {


      ini_set('memory_limit', '1024M');
      set_time_limit(3000000);

        $this->validate([
            'fileRecetas' => 'required|mimes:xlsx,xls'
        ]);
        $import = new RecetasImport();
        Excel::import($import, $this->fileRecetas);
        //$this->contProducts = $import->getRowCount();
        $this->fileRecetas = '';
        $this->emit('import', "REGISTROS IMPORTADOS");
    }

  public function ValidateProducts()
  {

    if (Auth::user()->comercio_id != 1) {
      $comercio_id = Auth::user()->comercio_id;
      $this->comercio_id = Auth::user()->comercio_id;
    } else {
      $comercio_id = Auth::user()->id;
      $this->comercio_id = Auth::user()->comercio_id;
    }

    $this->tipo_usuario = User::find($comercio_id);

    if ($this->tipo_usuario->sucursal != 1) {
      $this->casa_central_id = $comercio_id;
    } else {

      $this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
      $this->casa_central_id = $this->casa_central->casa_central_id;
    }
    
    // Obtenemos las sucursales
    $sucursales = sucursales::where('casa_central_id',$this->casa_central_id)->get();
    
    $this->validate([
      'fileRecetas' => 'required|mimes:xlsx,xls'
    ]);

    $import = new ProductsValidationImport();
    $array = Excel::toArray($import, $this->fileRecetas);

    //dd($array);
    
    $i = 1;

    $fila_error = [];
    $validacion_error = [];
    // Array para rastrear códigos únicos
    $codigos = [];
    
    foreach ($array[0] as $row) {
      $i++;
      // primero valida los titulos del excel 
      $result_titulos = $this->ValidateTitulos($row);
      
      if(!empty($result_titulos)) {
        if($i == 2){
        foreach($result_titulos as $rt) {
        $error = $rt;
        $fe = $i;
        array_push($validacion_error, $error);
        array_push($fila_error, $fe);
        }
        }
                  
      }
    
    
      

      // VALIDACIONES PARA TODAS LAS ACCIONES

      // validamos que los codigos tengan como maximo 14 caracteres //
    
      /*
      if (($row['cod_producto'] != null) && (14 < strlen($row['cod_producto']))) {
        $error = 'FILA NRO ' . $i . ' -> El codigo no puede contener mas de 20 digitos.';
        $fe = $i;
        array_push($validacion_error, $error);
        array_push($fila_error, $fe);
      }
    */
    
      //Codigo
      if (empty($row['cod_producto']) || $row['cod_producto'] === '' || $row['cod_producto'] == null ) {
        $error = 'FILA NRO ' . $i . ' -> El cod_producto es requerido.';
        $fe = $i;
        array_push($validacion_error, $error);
        array_push($fila_error, $fe);
      }

      //Nombre
      if (empty($row['producto']) || $row['producto'] === '' || $row['producto'] == null) {
        $error = 'FILA NRO ' . $i . '-> El producto es requerido.';
        $fe = $i;
        array_push($validacion_error, $error);
        array_push($fila_error, $fe);
      }

      
     // cod insumo
        $in = Product::where('barcode',$row['cod_insumo'])->first();
       // dd($row['cod_insumo'],$in);
        if($in == null){
        $error = 'FILA NRO ' . $i . ' -> El cod insumo no existe.';
        $fe = $i;
        array_push($validacion_error, $error);
        array_push($fila_error, $fe);            
        }
      
      //cantidad x unidad
      if (!is_numeric($row['cantidad_insumo']) ) {
        $error = 'FILA NRO ' . $i . ' -> La cantidad por unidad debe ser un numero.';
        $fe = $i;
        array_push($validacion_error, $error);
        array_push($fila_error, $fe);
      }
      
      //unidad de medida
      if (empty($row['unidad_de_medida']) ) {
        $error = 'FILA NRO ' . $i . ' -> Debe elegir la unidad de medida.';
        $fe = $i;
        array_push($validacion_error, $error);
        array_push($fila_error, $fe);
      }
      
      //unidad de medida
      if (!empty($row['unidad_de_medida']) ) {
        $un = unidad_medida::where('nombre',$row['unidad_de_medida'])->first();
        if($un == null){
        $error = 'FILA NRO ' . $i . ' -> La unidad de medida no existe, revisa las nomenclaturas de unidades de medida aceptadas.';
        $fe = $i;
        array_push($validacion_error, $error);
        array_push($fila_error, $fe);            
        }

      //unidad de medida
      if (empty($row['rinde']) ) {
        $error = 'FILA NRO ' . $i . ' -> Debe colocar la cantidad de productos que se producen con esta receta (el rinde).';
        $fe = $i;
        array_push($validacion_error, $error);
        array_push($fila_error, $fe);
      }
      
      if (!is_numeric($row['rinde']) ) {
        $error = 'FILA NRO ' . $i . ' -> El rinde tiene que ser numerico.';
        $fe = $i;
        array_push($validacion_error, $error);
        array_push($fila_error, $fe);
      }
      
      }
       
    
    
    }

    $this->validacion_errores = $validacion_error;
    $this->fila_error = $fila_error;


    if ($this->validacion_errores == []) {
      $this->uploadRecetas();
    } else {
      $this->estadoImportacion = 1;
    }
  }

  public function ValidateTitulos($row)
  {
      
    $titulos = array_keys($row); 
    
    $error_titulos = [];
    
    // titulos para todos

    if (!in_array("producto", $titulos)) {
        $return = "La columna 'producto' no está presente en el excel.";
        array_push($error_titulos, $return);
    } 
    
    if (!in_array("cod_producto", $titulos)) {
        $return = "La columna 'cod producto' no está presente en el excel.";
        array_push($error_titulos, $return);
    } 

    if (!in_array("variacion", $titulos)) {
        $return = "La columna 'variacion' no está presente en el excel.";
        array_push($error_titulos, $return);
    } 
    if (!in_array("cod_variacion", $titulos)) {
        $return = "La columna 'cod variacion' no está presente en el excel.";
        array_push($error_titulos, $return);
    } 
    
    if (!in_array("insumo", $titulos)) {
        $return = "La columna 'insumo' no está presente en el excel.";
        array_push($error_titulos, $return);
    } 
    if (!in_array("cod_insumo", $titulos)) {
        $return = "La columna 'cod insumo' no está presente en el excel.";
        array_push($error_titulos, $return);
    } 
    
    if (!in_array("cantidad_insumo", $titulos)) {
        $return = "La columna 'cantidad insumo' no está presente en el excel.";
        array_push($error_titulos, $return);
    } 

    if (!in_array("unidad_de_medida", $titulos)) {
        $return = "La columna 'unidad de medida' no está presente en el excel.";
        array_push($error_titulos, $return);
    } 

    if (!in_array("rinde", $titulos)) {
        $return = "La columna 'rinde' no está presente en el excel.";
        array_push($error_titulos, $return);
    } 

    return $error_titulos;
  }
  
		public function ExportarRecetas() {

		    return redirect('recetas/excel/'. Carbon::now()->format('d_m_Y_H_i_s'));
		    }


	
	
}
