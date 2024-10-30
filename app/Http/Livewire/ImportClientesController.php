<?php

namespace App\Http\Livewire;


// Trait

use App\Traits\WocommerceTrait;

// Imports
use App\Imports\CategoriesImport;
use App\Imports\PagosImport;
use App\Imports\ClientesImport;
use App\Imports\ClientesValidationImport;
use App\Imports\StockSucursalImport;

// Models
use App\Models\Category;
use App\Models\pagos_facturas;
use App\Models\User;
use App\Models\sucursales;
use App\Models\lista_precios;
use App\Models\wocommerce;
use App\Models\ClientesMostrador;

// Plugins
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;


class ImportClientesController extends Component
{
    use WithFileUploads;
    use WocommerceTrait;
    
    public $contCategories, $contProducts, $saltear_errores,$fileClientes, $fileStockSucursales, $comercio_id, $sucursal_id;
    public $validacion_errores = [];
    public $fila_error = [];
    public $importar_wocommerce,$wc;
    
    public function render()
    {
    
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;
    
       $this->wc = wocommerce::where('comercio_id',$comercio_id)->first();
       
       $status_wc = $this->checkWC();
        
        // dd($status_wc);
        
        return view('livewire.import-clientes.component')
            ->extends('layouts.theme-pos.app')
            ->section('content');
    }

// --------------- FUNCION QUE LEE EL EXCEL Y VALIDA LOS DATOS ---------- //
    
    public function ValidateClientes()
    {
        
        if(Auth::user()->comercio_id != 1){
          $comercio_id = Auth::user()->comercio_id;
          $this->comercio_id = Auth::user()->comercio_id;
        }
    	
    	else {
        $comercio_id = Auth::user()->id;
        $this->comercio_id = Auth::user()->comercio_id;
      }
        
         $this->casa_central_id = Auth::user()->casa_central_user_id;
         
         
        $this->validate([
            'fileClientes' => 'required|mimes:xlsx,xls'
        ]);
        
        $import = new ClientesValidationImport();
        $array = Excel::toArray($import, $this->fileClientes);
        
        //dd($array);
        $i = 1;

        $validacion = array(); // Array para almacenar los 'id_cliente' repetidos
        $idClientes = array(); // Array para almacenar los 'id_cliente' encontrados
        
        $fila_error = [];
        $validacion_error = [];
        
        $cantidadRegistros = count($array[0]);
       
        //dd($cantidadRegistros);
       
        if($cantidadRegistros == 0){
        
            $this->emit('msg-no', "EL EXCEL ESTA VACIO");
            return;
        } else {

        $headerRow =  $array[0][0];
       
        $expectedColumns = [
            'cod_cliente', 
            'nombre', 
            'sucursal_asociada', 
            'cuit', 
            'telefono', 
            'email', 
            'calle', 
            'altura', 
            'piso', 
            'depto', 
            'barrio', 
            'localidad', 
            'provincia', 
            'codigo_postal', 
            'lista_precios', 
            'ultima_compra', 
            'saldo_inicial_casa_central', 
            'fecha_inicial_cuenta_corriente', 
            'dias_de_plazo_cuenta_corriente', 
            'monto_maximo_cuenta_corriente', 
            'observaciones'
        ];


        // Verificar si los nombres de columnas del archivo coinciden con los esperados
        $uploadedColumns = array_keys($headerRow);
        $missingColumns = array_diff($expectedColumns, $uploadedColumns);
    
        if (!empty($missingColumns)) {
            // Si faltan columnas, emitimos un mensaje de error y detenemos la ejecución
            $this->emit('msg-error', 'El archivo Excel no tiene las siguientes columnas obligatorias: ' . implode(' , ', $missingColumns));
            $this->fileProveedores = "";
            return;
        }    

        
        // Array para rastrear códigos únicos
        $codigos = [];
    
        foreach($array[0] as $row) {
         
        $i++;
        
        if(!empty($row['sucursal_asociada'])) {
            
        //Aca chequeamos que las sucursales no haya cualquier cosa
        $user = User::select('users.name','users.id')
        ->where('users.name', $row['sucursal_asociada'])
        ->where('casa_central_user_id',$this->casa_central_id)
        ->first();
        
        $sucursal = User::join('sucursales','sucursales.sucursal_id','users.id')
        ->select('users.name','users.id')
        ->where('users.name', $row['sucursal_asociada'])
        ->where('users.casa_central_user_id',$this->casa_central_id)
        ->first();
    
        
        if($user == null && $sucursal ==  null){
            $error = 'FILA NRO '.$i.' -> No se encontro el nombre de sucursal '.$row['sucursal_asociada'];
            $fe = $i;
            array_push($validacion_error, $error);
            array_push($fila_error, $fe);             
        }
        }
        
        
        $idCliente = $row['cod_cliente'];
        // id del cliente esta eliminado
        
        
        if(!empty($row['cod_cliente'])) {
        $cm = ClientesMostrador::where('id_cliente',$idCliente)->where('comercio_id',$this->casa_central_id)->where('eliminado',1)->first();
        
        if($cm != null) {
            $error = 'FILA NRO '.$i.' -> El id cliente '.$idCliente.' esta eliminado, debe reestaurarlo para continuar la importacion de esta fila.';
            $fe = $i;
            array_push($validacion_error, $error);
            array_push($fila_error, $fe); 
        }
        }
        
      // Verificar si el código está duplicado
        if(!empty($row['cod_cliente'])){
        if (in_array($row['cod_cliente'], $codigos)) {
            $error = 'FILA NRO ' . $i . ' -> El codigo ' . $row['cod_cliente'] . ' está duplicado.';
            $fe = $i;
            array_push($validacion_error, $error);
            array_push($fila_error, $fe); 
        } else {
            $codigos[] = $row['cod_cliente'];
        }
        }
        
        // validamos que las listas de precios ingresadas existan //
        
        $lista_precio = $row['lista_precios'] ;
        
        if($lista_precio != null && $lista_precio != "PRECIO BASE") {
        
            $lista = lista_precios::where('lista_precios.comercio_id',$comercio_id)->where('lista_precios.nombre', $lista_precio)->where('lista_precios.eliminado', 0)->first();
            
            if($lista == null) {
            $error = 'FILA NRO '.$i.' -> La lista de precios  "'.$lista_precio.'" no existe.';
            $fe = $i;
            array_push($validacion_error, $error);
            array_push($fila_error, $fe); 
            }
        }
                
        // cuit del cliente ya existe
       if(!empty($row['cuit'])) {
       $p = ClientesMostrador::where('nombre',$row['cuit'])->where('id_cliente','<>',$row['cod_cliente'])->where('comercio_id',$comercio_id)->where('eliminado',0)->first();
       if($p != null) {
       $error = 'FILA NRO '.$i.' -> El cuit del cliente ya existe.';
       $fe = $i;
       array_push($validacion_error, $error);
       array_push($fila_error, $fe);
       }
       }     

        // Nombre del cliente haya registro
        if(empty($row['nombre'])) {
        $error = 'FILA NRO '.$i.' -> El nombre del cliente no puede estar vacio.';
        $fe = $i;
        array_push($validacion_error, $error);
        array_push($fila_error, $fe);
        }        
        // CUIT si esta que sean solo numeros y hasta 11 numeros

        if(!empty($row['cuit'])){
            
        if(!is_numeric($row['cuit'])) {
        $error = 'FILA NRO '.$i.' -> El cuit debe ser un numero.';
        $fe = $i;
        array_push($validacion_error, $error);
        array_push($fila_error, $fe);
        } else {
            
            if (11 < strlen($row['cuit'])) {
            $error = 'FILA NRO ' . $i . ' -> El cuit no puede tener mas de 11 caracteres.';
            $fe = $i;
            array_push($validacion_error, $error);
            array_push($fila_error, $fe);
            }
        }
        
        }
        
        // email que tenga formato email si esta
        
        // si tiene wocommerce hay que ver que tenga todos los mails 
        $wc = wocommerce::where('comercio_id',$comercio_id)->first();
        
        if($wc != null && $this->importar_wocommerce == true){
             if(empty($row['email'])) {
                $error = 'FILA NRO ' . $i . ' -> El correo electrónico tiene que estar presente para sincronizarlo con wocommerce.';
                $fe = $i;
                array_push($validacion_error, $error);
                array_push($fila_error, $fe);             }
        }
        
        
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
            $return = "Los 'cod cliente' repetidos son: " . implode(', ', $validacion);
        } else {
            $return = "No hay 'cod cliente' repetidos.";
        }
        
        if($validacion_error == []) {
        $this->saltear_errores = 0;
        $this->uploadClientes();    
        } else {
            $this->saltear_errores = array_values(array_unique($this->fila_error));
            
            //$arraySinDuplicados = array_values(array_unique($this->fila_error));
            //$this->saltear_errores = implode(",",$arraySinDuplicados);
        }
        

        }
        
      //  dd($validacion_error);
        


    }

    public function uploadClientesConErrores() {
        $this-> uploadClientes();
    }
    
        public function uploadCancelar() {
    return redirect('import-clientes');
    }

    
    public function uploadClientes()
    {
        
		$rules  =[
			'fileClientes' => 'required|mimes:xlsx,xls'
		];

		$messages = [
		    'fileClientes.required' => 'Debe insertar un archivo excel'
			];

		$this->validate($rules, $messages);
		
        $import = new ClientesImport($this->saltear_errores,$this->importar_wocommerce);
        Excel::import($import, $this->fileClientes);
        $this->emit('import', "REGISTROS IMPORTADOS");
        $this->fileClientes = "";
    }
    
    public function checkWC() {
    
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;
         
    $wc = wocommerce::where('comercio_id',$comercio_id)->first();
    
    if($wc != null) {
    
    $url = $wc->url;
    $ck = $wc->ck;
    $cs = $wc->cs;
    
    $return = $this->checkCredentials($url, $ck, $cs);   
    
    }
    
    }
    
    public function ExportarExcel() {
    
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;    
    
    $hora = " ".Carbon::now()->format('d-m-Y H:i:s');
    return redirect('report/excel-clientes/'.$comercio_id.'/'. $hora." hs");
}

}
