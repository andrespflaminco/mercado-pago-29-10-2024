<?php

namespace App\Http\Livewire;


// Trait

use App\Traits\WocommerceTrait;

// Imports
use App\Imports\CategoriesImport;
use App\Imports\PagosImport;
use App\Imports\ClientesImport;
use App\Imports\ProveedoresImport;
use App\Imports\ClientesValidationImport;
use App\Imports\StockSucursalImport;

// Models
use App\Models\Category;
use App\Models\pagos_facturas;
use App\Models\Product;
use App\Models\sucursales;
use App\Models\lista_precios;
use App\Models\wocommerce;
use App\Models\proveedores;

// Plugins
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;


class ImportProveedoresController extends Component
{
    use WithFileUploads;
    use WocommerceTrait;
    
    public $contCategories, $contProducts, $fileProveedores,$fileClientes, $fileStockSucursales, $comercio_id, $sucursal_id;
    public $validacion_errores = [];
    public $fila_error = [];
    
    public function render()
    {
      
        return view('livewire.import-proveedores.component')
            ->extends('layouts.theme-pos.app')
            ->section('content');
    }

// --------------- FUNCION QUE LEE EL EXCEL Y VALIDA LOS DATOS ---------- //
        
    public function ValidateProveedores()
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
            'fileProveedores' => 'required|mimes:xlsx,xls'
        ]);
        
        $import = new ClientesValidationImport();
        $array = Excel::toArray($import, $this->fileProveedores);
        
        //dd($array);
        $i = 1;

        $validacion = array(); // Array para almacenar los 'id_proveedor' repetidos
        $idProveedores = array(); // Array para almacenar los 'id_proveedor' encontrados
        
        $fila_error = [];
        $validacion_error = [];
        
        $cantidadRegistros = count($array[0]);
       
        //dd($cantidadRegistros);
       
        if($cantidadRegistros == 0){
        
            $this->emit('msg-no', "EL EXCEL ESTA VACIO");
            $this->fileProveedores = "";
            return;
        } else {
        
       
        $headerRow =  $array[0][0];
       
        // Definir las columnas esperadas
        $expectedColumns = ['cod_proveedor', 'nombre', 'cuit', 'telefono','email','calle','altura','piso','depto','localidad','codigo_postal','provincia','saldo_inicial_cta_cte'];
    
        // Verificar si los nombres de columnas del archivo coinciden con los esperados
        $uploadedColumns = array_keys($headerRow);
        $missingColumns = array_diff($expectedColumns, $uploadedColumns);
    
        if (!empty($missingColumns)) {
            // Si faltan columnas, emitimos un mensaje de error y detenemos la ejecución
            $this->emit('msg-error', 'El archivo Excel no tiene las siguientes columnas obligatorias: ' . implode(' , ', $missingColumns));
            $this->fileProveedores = "";
            return;
        }    
        
        foreach($array[0] as $row) {

         
        $i++;
        
        $idProveedor = $row['cod_proveedor'];
        // id del proveedor esta eliminado
        
        if(!empty($row['cod_proveedor'])) {
        $cm = proveedores::where('id_proveedor',$idProveedor)->where('comercio_id',$comercio_id)->where('eliminado',1)->first();
        
        if($cm != null) {
            $error = 'FILA NRO '.$i.' -> El id proveedor '.$idProveedor.' esta eliminado, debe reestaurarlo para continuar la importacion de esta fila.';
            $fe = $i;
            array_push($validacion_error, $error);
            array_push($fila_error, $fe); 
        }
        }
        
        // id del cliente que no haya repetidos en el excel

        if(!empty($row['cod_proveedor']) ) {
            if (in_array($idProveedor, $idProveedores)) {
                if (!in_array($idProveedor, $validacion)) {
                    $error = 'FILA NRO '.$i.' -> El id proveedor '.$idProveedor.' esta repetido';
                    $fe = $i;
                    array_push($validacion_error, $error);
                    array_push($fila_error, $fe); 
                    array_push($validacion, $idProveedor);
                }
            } else {
                $idProveedores[] = $idProveedor; // Almacenar 'id_proveedor' en el array
            }
        }

        // Nombre del cliente haya registro
        if(empty($row['nombre'])) {
        $error = 'FILA NRO '.$i.' -> El nombre del proveedor no puede estar vacio.';
        $fe = $i;
        array_push($validacion_error, $error);
        array_push($fila_error, $fe);
        }      
        
        // Nombre del proveedor ya existe
       if(!empty($row['nombre'])) {
       $p = proveedores::where('nombre',$row['nombre'])->where('id_proveedor','<>',$row['cod_proveedor'])->where('comercio_id',$comercio_id)->where('eliminado',0)->first();
       if($p != null) {
       $error = 'FILA NRO '.$i.' -> El nombre del proveedor ya existe, elija otro.';
       $fe = $i;
       array_push($validacion_error, $error);
       array_push($fila_error, $fe);
       }
       }      
        
        
        // email que tenga formato email si esta
        
        if (!empty($row['email'])) {
            
            $email = $row['email'];
            
            // Verificar si el email tiene un formato válido
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'FILA NRO ' . $i . ' -> El correo electrónico no tiene un formato válido.';
                $fe = $i;
                array_push($validacion_error, $error);
                array_push($fila_error, $fe);
            }
        }
        
        // telefono que sean solo numeros
        
        if(!empty($row['telefono'])){
        if(!is_numeric($row['telefono'])) {
        $error = 'FILA NRO '.$i.' -> El telefono debe contener solo numeros.';
        $fe = $i;
        array_push($validacion_error, $error);
        array_push($fila_error, $fe);
        }
        }

        
        }
        
        $this->validacion_errores = $validacion_error;
        $this->fila_error = $fila_error;
        
        if (!empty($validacion)) {
            $return = "Los 'id_cliente' repetidos son: " . implode(', ', $validacion);
        } else {
            $return = "No hay 'id_cliente' repetidos.";
        }
        
        if($validacion_error == []) {
        $this->saltear_errores = 0;
        $this->uploadProveedores();    
        } else {
            $this->saltear_errores = array_values(array_unique($this->fila_error));

        }
        

        }
        
      //  dd($validacion_error);
        


    }

    public function uploadProveedoresConErrores() {
        $this-> uploadProveedores();
    }
    
    public function uploadCancelar() {
    return redirect('import-proveedores');
    }

    public function uploadProveedores()
    {
        
		$rules  =[
			'fileProveedores' => 'required|mimes:xlsx,xls'
		];

		$messages = [
		    'fileProveedores.required' => 'Debe insertar un archivo excel'
			];

		$this->validate($rules, $messages);
		
        $import = new ProveedoresImport($this->saltear_errores);
        Excel::import($import, $this->fileProveedores);
        $this->emit('import', "REGISTROS IMPORTADOS");
        $this->fileProveedores = "";
    }
    

}
