<?php

namespace App\Http\Livewire;

use App\Imports\CategoriesImport;
use App\Imports\PagosImport;
use App\Imports\InsumosImport;
use App\Imports\ProductsImport;
use App\Imports\SaleImport;
use App\Imports\SaleDetailsImport;
use App\Imports\ProductsValidationImport;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Category;
use App\Models\pagos_facturas;
use App\Models\Product;
use App\Models\User;
use App\Models\unidad_medida;
use App\Models\sucursales;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ImportInsumosController extends Component
{
    use WithFileUploads;

    public $contCategories, $contProducts, $fileCategories, $fileProducts, $fileVentas, $fileDetalleVentas, $fileInsumos, $estadoImportacion;
    public $validacion_errores = [];

    public function mount(){
        $this->estadoImportacion = 1;
        $this->validacion_errores == [];
    }
    
    
    public function render()
    {
        return view('livewire.import-insumos.component')
            ->extends('layouts.theme-pos.app')
            ->section('content');
    }

    public function uploadInsumos()
    {

      ini_set('memory_limit', '1024M');
      set_time_limit(3000000);

        $this->validate([
            'fileInsumos' => 'required|mimes:xlsx,xls'
        ]);
        $import = new InsumosImport();
        Excel::import($import, $this->fileInsumos);
        //$this->contProducts = $import->getRowCount();
        $this->fileInsumos = '';
        $this->emit('import', "REGISTROS IMPORTADOS");
    }

	public function ExportarInsumos() {
	return redirect('insumos/excel/'. Carbon::now()->format('d_m_Y_H_i_s'));
	}
	
	
	  // --------------- FUNCION QUE LEE EL EXCEL Y VALIDA LOS DATOS ---------- //

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
      'fileInsumos' => 'required|mimes:xlsx,xls'
    ]);

    $import = new ProductsValidationImport();
    $array = Excel::toArray($import, $this->fileInsumos);

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

      // Despues valida que si hay sucursales sean las correctas
      $result_diferencias = $this->ValidateSucursales($row,$sucursales);    
      // Verificar si hay diferencias y mostrar un mensaje si es necesario
      if (!empty($result_diferencias)) {

        if($i == 2){
        foreach($result_diferencias as $rd) {
        $error = 'LA SUCURSAL ' . $rd . ' NO EXISTE. CORROBORE LOS TITULOS DE LOS STOCK.';
        $fe = $i;
        array_push($validacion_error, $error);
        array_push($fila_error, $fe);
        }
        
        }
        
       }
       
       if(empty($result_titulos) && empty($result_diferencias) )  {
      

      // VALIDACIONES PARA TODAS LAS ACCIONES
      
      // Verificar si el código está duplicado
        if (in_array($row['codigo'], $codigos)) {
            $error = 'FILA NRO ' . $i . ' -> El codigo ' . $row['codigo'] . ' está duplicado.';
            $fe = $i;
            array_push($validacion_error, $error);
            array_push($fila_error, $fe);
        } else {
            $codigos[] = $row['codigo'];
        }
        
      // validamos que los codigos tengan como maximo 14 caracteres //

      if (($row['codigo'] != null) && (14 < strlen($row['codigo']))) {
        $error = 'FILA NRO ' . $i . ' -> El codigo no puede contener mas de 20 digitos.';
        $fe = $i;
        array_push($validacion_error, $error);
        array_push($fila_error, $fe);
      }

      //Codigo
      if (empty($row['codigo']) || $row['codigo'] === '' || $row['codigo'] == null ) {
        $error = 'FILA NRO ' . $i . ' -> El codigo es requerido.';
        $fe = $i;
        array_push($validacion_error, $error);
        array_push($fila_error, $fe);
      }

      //Nombre
      if (empty($row['nombre']) || $row['nombre'] === '' || $row['nombre'] == null) {
        $error = 'FILA NRO ' . $i . '-> El nombre es requerido.';
        $fe = $i;
        array_push($validacion_error, $error);
        array_push($fila_error, $fe);
      }

      
     //Precio
      if (!is_numeric($row['costo']) ) {
        $error = 'FILA NRO ' . $i . ' -> El costo debe ser un numero.';
        $fe = $i;
        array_push($validacion_error, $error);
        array_push($fila_error, $fe);
      }
      
      //cantidad x unidad
      if (!is_numeric($row['cantidad_x_unidad']) ) {
        $error = 'FILA NRO ' . $i . ' -> La cantidad por unidad debe ser un numero.';
        $fe = $i;
        array_push($validacion_error, $error);
        array_push($fila_error, $fe);
      }
      
      //cantidad x unidad
      if (!is_numeric($row['stock_central']) ) {
        $error = 'FILA NRO ' . $i . ' -> El stock de la casa central debe ser un numero.';
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

      }
       
    }
    
    }

    $this->validacion_errores = $validacion_error;
    $this->fila_error = $fila_error;


    if ($this->validacion_errores == []) {
      $this->uploadInsumos();
    } else {
      $this->estadoImportacion = 1;
    }
  }

  public function ValidateTitulos($row)
  {
    $titulos = array_keys($row); 
  
    $titulos_correcto = [
        "nombre",
        "codigo",
        "stock_central",
        "unidad_de_medida"
    ];

    $diferencias = array_diff($titulos_correcto, $titulos);

    $error_titulos = [];
    
    // titulos para todos

    if (!in_array("nombre", $titulos)) {
        $return = "La columna 'nombre' no está presente en el excel.";
        array_push($error_titulos, $return);
    } 
    
    if (!in_array("codigo", $titulos)) {
        $return = "La columna 'codigo' no está presente en el excel.";
        array_push($error_titulos, $return);
    } 

    if (!in_array("costo", $titulos)) {
        $return = "La columna 'costo' no está presente en el excel.";
        array_push($error_titulos, $return);
    } 

    if (!in_array("stock_central", $titulos)) {
        $return = "La columna 'stock_central' no está presente en el excel.";
        array_push($error_titulos, $return);
    } 
    if (!in_array("unidad_de_medida", $titulos)) {
        $return = "La columna 'unidad_de_medida' no está presente en el excel.";
        array_push($error_titulos, $return);
    } 
    
    return $error_titulos;
  }
  
  
  public function ValidateSucursales($row,$sucursales)
  {
      
      //dd($sucursales);
      
      $titulos = array_keys($row);
      
      // Identificar títulos con números
        $titulos_con_numeros = array_filter($titulos, function ($titulo) {
            return preg_match('/\d/', $titulo); // Verificar si el título contiene al menos un número
        });
      
      // Encontrar títulos que siguen el patrón "stock" después del número inicial
        $titulos_con_patron = array_filter($titulos_con_numeros, function ($titulo) {
            return preg_match('/^\d+_stock/', $titulo); // Verificar el patrón
        });
      
      // Obtener los números antes del primer guion bajo
        $numeros_antes_del_guion = array_map(function ($titulo) {
            preg_match('/^(\d+)_/', $titulo, $matches); // Buscar el número antes del guion bajo
            return $matches[1]; // Obtener el número encontrado
        }, $titulos_con_patron);

        $sucursales_id = [];
        foreach($sucursales as $s) {
            array_push($sucursales_id,$s->sucursal_id);
        }
        
        // Encontrar diferencias entre los dos arrays
        $diferencias = array_diff($numeros_antes_del_guion, $sucursales_id);
        
        return $diferencias;

          
  }
}
