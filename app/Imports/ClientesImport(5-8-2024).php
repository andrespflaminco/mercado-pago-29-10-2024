<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\User;
use App\Models\sucursales;
use Carbon\Carbon;
use App\Models\lista_precios;
use App\Models\provincias;
use App\Models\saldos_iniciales;
use App\Models\ClientesMostrador;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Automattic\WooCommerce\Client;
use App\Models\wocommerce;
use Spatie\Permission\Models\Role;
use \WeDevs\ORM\Eloquent\Facades\DB;
use WorDBless\Users as WP_User;


class ClientesImport implements WithHeadingRow,  WithValidation, SkipsOnError, OnEachRow
{
    use SkipsErrors;

    public $comercio_id;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
     
         protected $saltear_errores;
    
        public function __construct($saltear_errores)
        {
            $this->saltear_errores = $saltear_errores;

        }

    public function onRow(Row $row)
     {

        if(Auth::user()->comercio_id != 1)
        $comercio_id = Auth::user()->comercio_id;
        else
        $comercio_id = Auth::user()->id;
              
        $this->comercio_id = $comercio_id;
        $this->casa_central_id = Auth::user()->casa_central_user_id;
            
        $rowIndex = $row->getIndex();
         $row      = $row->toArray();
         
         //dd($row);
         // Si no esta en el array de saltear errores
                  
         if($this->saltear_errores == 0) {
             $this->saltear_errores = [];
         }
         
         if (!in_array($rowIndex, $this->saltear_errores)) {
         

        // Aca se ven las lista de precios 
        
        $lista = $this->GetListaPrecios($row);
        
        $id_cliente = $this->GetCodigoCliente($row);
        
        $sucursal_id = $this->GetSucursal($row);
        
        
        $fechaFormateada =  $this->GetFechaFormateada($row);
        
        $array = [
                  'id_cliente' => $id_cliente,
                  'nombre' => $row['nombre'],
                  'telefono' => $row['telefono'],
                  'email' => $row['email'],
                  'direccion' => $row['calle'],
                  'localidad' => $row['localidad'],
                  'provincia' => $row['provincia'],
                  'status' => 'activo',
                  'dni' => $row['cuit'],
                  'observaciones' => $row['observaciones'],
                  'lista_precio' => $lista,
                  'altura' => $row['altura'],
                  'piso' => $row['piso'],
                  'depto' => $row['depto'],
                  'barrio' => $row['barrio'],
                  'codigo_postal' => $row['codigo_postal'],
                  'eliminado' => 0,
                  'comercio_id' => $this->casa_central_id,
                  'creador_id' => $sucursal_id,
                  'plazo_cuenta_corriente' => $row['dias_de_plazo_cuenta_corriente'],
                  'saldo_inicial_cuenta_corriente' => $row['saldo_inicial_cuenta_corriente'],
                  'monto_maximo_cuenta_corriente' => $row['monto_maximo_cuenta_corriente'],
                  'fecha_inicial_cuenta_corriente' => $fechaFormateada
                  
            ]; 
            
        // Buscar un producto con el nombre "Camiseta"
        $cliente = ClientesMostrador::updateOrCreate(
            [
            'id_cliente' => $id_cliente,
            'comercio_id' => $this->casa_central_id
            ], 
            $array // Datos a actualizar o crear
        );
        
  
    //    $saldos_iniciales = saldos_iniciales::where('referencia_id',$cliente->id)->where('concepto','Saldo inicial')->where('tipo','cliente')->first();
        
        saldos_iniciales::updateOrCreate(
            [
            'referencia_id' => $cliente->id,
            'concepto' => 'Saldo inicial',
            'tipo' => 'cliente',
            'comercio_id' => $this->casa_central_id
            ], 
            [
            'monto' =>  $row['saldo_inicial_cuenta_corriente'],
            'fecha' => $fechaFormateada            
            ]
        );  


        // Buscamos el wocommerce 
        
        $wc = wocommerce::where('comercio_id', $comercio_id)->first();

        // si tiene el wocommerce integrado --> ingresa los datos
        
      if($wc != null){
        
        if($row['lista_precios'] == 0) {$tipo_cliente = "customer";} else {$tipo_cliente = lista_precios::find($row['lista_precios'])->nombre;}
     
        $c =  ClientesMostrador::where('nombre',$row['nombre'])->where('comercio_id',$comercio_id)->first();
        
        if($c == null) {
        $host = $wc->url.'/wp-json/wp/v2/users/';
        } else {
        $host = $wc->url.'/wp-json/wp/v2/users/'.$c->wc_customer_id;    
        }
        
        
        //dd($host);
        
        $explode = explode('@', $row['email']);

        $this->username = $explode[0];

        $data = array(
          'username' => $this->username,
          'first_name' => $row['nombre'],
          'last_name' => '',
          'email' => $row['email'],
          'roles' => $tipo_cliente,
          'password' => $this->username
        );

        $data_string = json_encode($data);
        $user_pass = $wc->user.':'.$wc->pass;
        
        //dd($user_pass);
        
        $headers = array(
            'Content-Type:application/json',
            'Content-Length: ' . strlen($data_string),
            'Authorization: Basic '. base64_encode($user_pass)
        );

        $ch = curl_init($host);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

        $result = curl_exec($ch);
        
         dd($result);
        
        $result = json_decode($result);
        
        //dd($result);
        // ve si existe el id y actualiza el 'wc_customer_id'
        
        if (array_key_exists('id', $result)) {
        $group->wc_customer_id = $result->id;
        $group->save();
        }
     
        curl_close($ch);

        }
        

     }
     
     }

public function GetProvincia($row) {
            
        // Aca se actualizan las sucursales
        $provincias = provincias::where('provincia',$row['provincia'])->first();
        
        if($provincias != null){$return_provincia = $provincias->id;} else {$return_provincia = null;}
        
        return $return_provincia;
}

public function GetSucursal($row) {
            
        // Aca se actualizan las sucursales
        $sucursal = sucursales::join('users','users.id','sucursales.sucursal_id')->select('users.name','sucursales.sucursal_id')
        ->where('sucursales.eliminado',0)
        ->where('sucursales.casa_central_id', $this->casa_central_id)
        ->where('users.name', $row['sucursal_asociada'])
        ->first();
        
        
        if($sucursal != null){$return_sucursal = $sucursal->sucursal_id;} else {$return_sucursal = $this->casa_central_id;}
        
        //dd($row['sucursal_asociada'],$sucursal,$return_sucursal);
        
        return $return_sucursal;
}
public function GetFechaFormateada($row){

    $fechaNumero = $row['fecha_inicial_cuenta_corriente'];
    // Comprobación si el string sigue el formato de fecha "d/m/Y"

    if(empty($fechaNumero)){
        $fechaFormateada = Carbon::now();
    } else {
    if(preg_match("/^\d{1,2}\/\d{1,2}\/\d{4}$/", $fechaNumero)) {
    $fechaFormateada = Carbon::createFromFormat('d/m/Y', $fechaNumero)->format('Y-m-d'); 
    } else {
    $fechaCarbon = Carbon::createFromFormat('Y-m-d', '1899-12-30')->addDays($fechaNumero);
    $fechaFormateada = $fechaCarbon->format('Y-m-d');
    }
    }
     return $fechaFormateada;
}
public function GetListaPrecios($row){
            
        $lista = lista_precios::where('nombre',$row['lista_precios'])->where('comercio_id',$this->casa_central_id)->where('eliminado',0)->first();
        
        if($lista == null) {$return_lista = 0;} else { $return_lista = $lista->id;}
        // Aca actualiza la base de datos 
        
        return $return_lista;
}

public function GetCodigoCliente($row){
            
        $ultimo_id = ClientesMostrador::where('comercio_id',$this->casa_central_id)->orderBy('id_cliente','desc')->first()->id;
        $ultimo_cliente = ClientesMostrador::find($ultimo_id);
        //dd($ultimo_cliente);
        
        if($ultimo_cliente != null){
        $nuevo_id = $ultimo_cliente->id_cliente + 1;
        } else {
        $nuevo_id = 1;    
        }
        
        if(empty($row['cod_cliente'])) {$id_cliente = $nuevo_id;} else {$id_cliente = $row['cod_cliente'];}
        
        return $id_cliente;

}


    public function rules(): array
    {
        return [
            'name' => Rule::unique('categories', 'name'),
        ];
    }

    public function customValidationMessages()
    {
        //'name.required' => 'Nombre de categoría requerido',
        return [
            'name.unique' => 'Ya existe la categoría'
        ];
    }
}
