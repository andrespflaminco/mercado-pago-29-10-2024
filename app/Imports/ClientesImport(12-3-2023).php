<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\User;
use App\Models\sucursales;
use App\Models\lista_precios;
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

         $rowIndex = $row->getIndex();
         $row      = $row->toArray();
         
         // Si no esta en el array de saltear errores
                  
         if($this->saltear_errores == 0) {
             $this->saltear_errores = [];
         }
         
         if (!in_array($rowIndex, $this->saltear_errores)) {
             
             
         $usuario_id = Auth::user()->id;

         if(Auth::user()->comercio_id != 1)
         $comercio_id = Auth::user()->comercio_id;
         else
         $comercio_id = Auth::user()->id;
              
         $this->comercio_id = $comercio_id;
              
         $this->tipo_usuario = User::find($comercio_id);
            
         if($this->tipo_usuario->sucursal != 1) {
         $this->casa_central_id = $comercio_id;
         } else {
            
         $this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
         $this->casa_central_id = $this->casa_central->casa_central_id;
               
         }
         
        // Aca se ven las lista de precios 
        
        $lista = lista_precios::where('nombre',$row['lista_precios'])->where('comercio_id',$this->casa_central_id)->where('eliminado',0)->first();
        
        if($lista == null) {$l = 0;} else { $l = $lista->id;}
        // Aca actualiza la base de datos 
        
        $ultimo_id = ClientesMostrador::where('comercio_id',$this->casa_central_id)->max('id_cliente');
        $ultimo_cliente = ClientesMostrador::find($ultimo_id);
        
        if($ultimo_cliente != null){
        $nuevo_id = $ultimo_cliente->id_cliente + 1;
        } else {
        $nuevo_id = 1;    
        }

        if(empty($row['cod_cliente'])) {$id_cliente = $nuevo_id;} else {$id_cliente = $row['cod_cliente'];}

        
        // Aca se actualizan las sucursales
        $sucursal_excel = sucursales::join('users','users.id','sucursales.sucursal_id')->select('users.name','sucursales.sucursal_id')
        ->where('sucursales.eliminado',0)
        ->where('sucursales.casa_central_id', $this->casa_central_id)
        ->where('users.name', $row['sucursal'])
        ->first();

        $sucursal_id_excel = $sucursal_excel->sucursal_id;
        
        // Aca buscamos el cliente
        if($this->tipo_usuario->sucursal != 1) {
        $cliente = ClientesMostrador::where('id_cliente',$id_cliente)->where('comercio_id',$this->casa_central_id)->first();
        //$this->QueryCasaCentral($sucursal_id_excel,$cliente,$nuevo_id,$l,$comercio_id,$row);
        
        if($cliente != null) {
            $cliente->update([
                  'nombre' => $row['nombre'],
                  'telefono' => $row['telefono'],
                  'email' => $row['email'],
                  'direccion' => $row['calle'],
                  'barrio' => null,
                  'localidad' => $row['localidad'],
                  'provincia' => $row['provincia'],
                  'status' => 'activo',
                  'dni' => $row['cuit'],
                  'observaciones' => $row['observaciones'],
                  'lista_precio' => $l,
                  'altura' => $row['altura'],
                  'piso' => $row['piso'],
                  'depto' => $row['depto'],
                  'codigo_postal' => $row['codigo_postal'],
                  'eliminado' => 0,
                  'creador_id' => $sucursal_id_excel
            ]);   
            
           // dd($cliente);
            
        } else {

            ClientesMostrador::create([
                  'id_cliente'   => $nuevo_id,
                  'nombre' => $row['nombre'],
                  'telefono' => $row['telefono'],
                  'email' => $row['email'],
                  'direccion' => $row['calle'],
                  'barrio' => null,
                  'localidad' => $row['localidad'],
                  'provincia' => $row['provincia'],
                  'status' => 'activo',
                  'dni' => $row['cuit'],
                  'observaciones' => $row['observaciones'],
                  'lista_precio' => $l,
                  'comercio_id' => $comercio_id,
                  'creador_id' => $comercio_id,
                  'pais' => 1,
                  'altura' => $row['altura'],
                  'piso' => $row['piso'],
                  'depto' => $row['depto'],
                  'codigo_postal' => $row['codigo_postal'],
                  'creador_id' => $sucursal_id_excel
                ]);
        }
        } else {
        $cliente = ClientesMostrador::where('id_cliente',$id_cliente)->where('comercio_id',$this->casa_central_id)->first();  
        
        // aca tiene que corroborar que el cliente ya haya sido de esa sucursal antes de importar el excel 
        
        // si el cliente existe en la casa central
        if($cliente != null) {
            
            // si el cliente ya era de este comercio lo actualiza, sino no.
            
            if($cliente->creador_id == $comercio_id) {
            $cliente->update([
                  'nombre' => $row['nombre'],
                  'telefono' => $row['telefono'],
                  'email' => $row['email'],
                  'direccion' => $row['calle'],
                  'barrio' => null,
                  'localidad' => $row['localidad'],
                  'provincia' => $row['provincia'],
                  'status' => 'activo',
                  'dni' => $row['cuit'],
                  'observaciones' => $row['observaciones'],
                  'lista_precio' => $l,
                  'altura' => $row['altura'],
                  'piso' => $row['piso'],
                  'depto' => $row['depto'],
                  'codigo_postal' => $row['codigo_postal'],
                  'eliminado' => 0
            ]);                   
            }

        } else {
            
            // Si la sucursal que aparece en el excel es la sucursal en cuestion, crea el registro
            
            if($sucursal_id_excel == $comercio_id) {
            ClientesMostrador::create([
                  'id_cliente'   => $nuevo_id,
                  'nombre' => $row['nombre'],
                  'creador_id' => $comercio_id,
                  'telefono' => $row['telefono'],
                  'email' => $row['email'],
                  'direccion' => $row['calle'],
                  'barrio' => null,
                  'localidad' => $row['localidad'],
                  'provincia' => $row['provincia'],
                  'status' => 'activo',
                  'dni' => $row['cuit'],
                  'observaciones' => $row['observaciones'],
                  'lista_precio' => $l,
                  'comercio_id' => $comercio_id,
                  'creador_id' => $comercio_id,
                  'pais' => 1,
                  'altura' => $row['altura'],
                  'piso' => $row['piso'],
                  'depto' => $row['depto'],
                  'codigo_postal' => $row['codigo_postal'],
                ]);
            }
            
            
            
        }
        }
        
        }
                


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


public function QueryCasaCentral($sucursal_id_excel,$cliente,$nuevo_id,$l,$comercio_id,$row){

        if($cliente != null) {
            $cliente->update([
                  'nombre' => $row['nombre'],
                  'telefono' => $row['telefono'],
                  'email' => $row['email'],
                  'direccion' => $row['calle'],
                  'barrio' => null,
                  'localidad' => $row['localidad'],
                  'provincia' => $row['provincia'],
                  'status' => 'activo',
                  'dni' => $row['cuit'],
                  'observaciones' => $row['observaciones'],
                  'lista_precio' => $l,
                  'altura' => $row['altura'],
                  'piso' => $row['piso'],
                  'depto' => $row['depto'],
                  'codigo_postal' => $row['codigo_postal'],
                  'eliminado' => 0,
                  'creador_id' => $sucursal_id_excel
            ]);   
            
           // dd($cliente);
            
        } else {

            ClientesMostrador::create([
                  'id_cliente'   => $nuevo_id,
                  'nombre' => $row['nombre'],
                  'telefono' => $row['telefono'],
                  'email' => $row['email'],
                  'direccion' => $row['calle'],
                  'barrio' => null,
                  'localidad' => $row['localidad'],
                  'provincia' => $row['provincia'],
                  'status' => 'activo',
                  'dni' => $row['cuit'],
                  'observaciones' => $row['observaciones'],
                  'lista_precio' => $l,
                  'comercio_id' => $comercio_id,
                  'creador_id' => $comercio_id,
                  'pais' => 1,
                  'altura' => $row['altura'],
                  'piso' => $row['piso'],
                  'depto' => $row['depto'],
                  'codigo_postal' => $row['codigo_postal'],
                  'creador_id' => $sucursal_id_excel
                ]);
        }
        
}





public function QuerySucursal($sucursal_id_excel,$cliente,$nuevo_id,$l,$comercio_id,$row){

        if($sucursal_id_excel == $comercio_id) {
        if($cliente != null) {
            $cliente->update([
                  'nombre' => $row['nombre'],
                  'telefono' => $row['telefono'],
                  'email' => $row['email'],
                  'direccion' => $row['calle'],
                  'barrio' => null,
                  'localidad' => $row['localidad'],
                  'provincia' => $row['provincia'],
                  'status' => 'activo',
                  'dni' => $row['cuit'],
                  'observaciones' => $row['observaciones'],
                  'lista_precio' => $l,
                  'altura' => $row['altura'],
                  'piso' => $row['piso'],
                  'depto' => $row['depto'],
                  'codigo_postal' => $row['codigo_postal'],
                  'eliminado' => 0
            ]);   
        } else {

            ClientesMostrador::create([
                  'id_cliente'   => $nuevo_id,
                  'nombre' => $row['nombre'],
                  'creador_id' => $comercio_id,
                  'telefono' => $row['telefono'],
                  'email' => $row['email'],
                  'direccion' => $row['calle'],
                  'barrio' => null,
                  'localidad' => $row['localidad'],
                  'provincia' => $row['provincia'],
                  'status' => 'activo',
                  'dni' => $row['cuit'],
                  'observaciones' => $row['observaciones'],
                  'lista_precio' => $l,
                  'comercio_id' => $comercio_id,
                  'creador_id' => $comercio_id,
                  'pais' => 1,
                  'altura' => $row['altura'],
                  'piso' => $row['piso'],
                  'depto' => $row['depto'],
                  'codigo_postal' => $row['codigo_postal'],
                ]);
        }
        }
        
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
