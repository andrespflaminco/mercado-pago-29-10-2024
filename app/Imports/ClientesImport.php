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


use Illuminate\Support\Facades\Log;

class ClientesImport implements WithHeadingRow,  WithValidation, SkipsOnError, OnEachRow
{
    use SkipsErrors;

    public $comercio_id;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
     
         protected $saltear_errores,$importar_wocommerce;
    
        public function __construct($saltear_errores,$importar_wocommerce)
        {
            $this->saltear_errores = $saltear_errores;
            $this->importar_wocommerce = $importar_wocommerce;

        }


public function onRow(Row $row)
{
    
    // Verifica si todos los valores de la fila son nulos o est¨¢n vac¨ªos
    if (empty(array_filter($row))) {
        // Si todos los valores son nulos o vac¨ªos, salta la fila
        return;
    }
        
    try {
        
        if (Auth::user()->comercio_id != 1)
            $comercio_id = Auth::user()->comercio_id;
        else
            $comercio_id = Auth::user()->id;

        $this->comercio_id = $comercio_id;
        $this->casa_central_id = Auth::user()->casa_central_user_id;

        $rowIndex = $row->getIndex();
        $row = $row->toArray();

        // Si no esta en el array de saltear errores
        if ($this->saltear_errores == 0) {
            $this->saltear_errores = [];
        }

        if (!in_array($rowIndex, $this->saltear_errores)) {
            // Lista de precios
            $lista = $this->GetListaPrecios($row);
            $id_cliente = $this->GetCodigoCliente($row);
            $sucursal_id = $this->GetSucursal($row);

            $sucursales = sucursales::join('users', 'users.id', 'sucursales.sucursal_id')
                ->select('users.name', 'sucursales.sucursal_id')
                ->where('sucursales.eliminado', 0)
                ->where('casa_central_id', $this->casa_central_id)
                ->get();

            $fechaFormateada = $this->GetFechaFormateada($row);

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
                'saldo_inicial_cuenta_corriente' => 0,
                'monto_maximo_cuenta_corriente' => $row['monto_maximo_cuenta_corriente'],
                'fecha_inicial_cuenta_corriente' => $fechaFormateada
            ];

            $cliente = ClientesMostrador::updateOrCreate(
                [
                    'id_cliente' => $id_cliente,
                    'comercio_id' => $this->casa_central_id
                ],
                $array // Datos a actualizar o crear
            );

            $this->UpdateOrCreateSaldoInicial($cliente, $this->casa_central_id, $row['saldo_inicial_casa_central']);

            // Inicializar saldos para todas las sucursales a 0
            $saldos_iniciales = [];
            foreach ($sucursales as $sucursal) {
                $saldos_iniciales[$sucursal->sucursal_id] = 0;
            }

            // Actualizar saldos iniciales con los valores del Excel
            foreach ($row as $key => $value) {
                $var = explode("_", $key);
                if (count($var) > 2) {
                    if (is_array($var) && $var[1] == "saldo" && is_numeric($var[0])) {
                        $sucursal_id = $var[0];
                        $saldo_inicial = $value;
                        $saldos_iniciales[$sucursal_id] = $saldo_inicial;
                    }
                }
            }

            // Guardar los saldos iniciales en la base de datos
            foreach ($saldos_iniciales as $sucursal_id => $saldo_inicial) {
                $this->UpdateOrCreateSaldoInicial($cliente, $sucursal_id, $saldo_inicial);
            }

            // Actualizar el saldo inicial total
            $saldo_inicial_total = array_sum($saldos_iniciales);
            $cliente->update([
                'saldo_inicial_cuenta_corriente' => $saldo_inicial_total
            ]);

            // IntegraciÃ³n con WooCommerce
            $wc = wocommerce::where('comercio_id', $comercio_id)->first();

            if ($wc != null && $this->importar_wocommerce == true) {
                if ($row['lista_precios'] == 0) {
                    $tipo_cliente = "customer";
                } else {
                    $tipo_cliente = lista_precios::find($row['lista_precios'])->nombre;
                }

                $c = ClientesMostrador::where('nombre', $row['nombre'])
                    ->where('comercio_id', $comercio_id)
                    ->first();

                if ($c == null) {
                    $host = $wc->url . '/wp-json/wp/v2/users/';
                } else {
                    $host = $wc->url . '/wp-json/wp/v2/users/' . $c->wc_customer_id;
                }

                $explode = explode('@', $row['email']);
                $this->username = $explode[0];

                $data = [
                    'username' => $this->username,
                    'first_name' => $row['nombre'],
                    'last_name' => '',
                    'email' => $row['email'],
                    'roles' => $tipo_cliente,
                    'password' => $this->username
                ];

                $data_string = json_encode($data);
                $user_pass = $wc->user . ':' . $wc->pass;

                $headers = [
                    'Content-Type:application/json',
                    'Content-Length: ' . strlen($data_string),
                    'Authorization: Basic ' . base64_encode($user_pass)
                ];

                $ch = curl_init($host);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                $result = curl_exec($ch);

                if ($result === false) {
                    throw new \Exception('Curl error: ' . curl_error($ch));
                }

                $result = json_decode($result, true);

                if (isset($result['id'])) {
                    $cliente->wc_customer_id = $result['id'];
                    $cliente->save();
                }

                curl_close($ch);
            }
        }
    } catch (\Exception $e) {
      
          // Registra el error con m¨¢s detalles
            /*
            Log::error('Error processing row: ' . $e->getMessage(), [
                'row' => $row, // La fila que caus¨® el error
                'rowIndex' => $rowIndex, // El ¨ªndice de la fila
                'comercio_id' => $this->comercio_id // El ID del comercio
            ]);
            */
            
       Log::error('Error processing row: ' . $e->getMessage() . ' in file ' . $e->getFile() . ' on line ' . $e->getLine());

    
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
    // ComprobaciÃ³n si el string sigue el formato de fecha "d/m/Y"

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
            
        $ultimo_cliente = ClientesMostrador::where('comercio_id',$this->casa_central_id)->orderBy('id_cliente','desc')->first();
       
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
        //'name.required' => 'Nombre de categorÃ­a requerido',
        return [
            'name.unique' => 'Ya existe la categorÃ­a'
        ];
    }
    
    
    public function UpdateOrCreateSaldoInicial($cliente,$sucursal_id,$saldo_inicial){
        saldos_iniciales::updateOrCreate(
        [
        'referencia_id' => $cliente->id,
        'concepto' => 'Saldo inicial',
        'tipo' => 'cliente',
        'comercio_id' => $this->casa_central_id,
        'sucursal_id' => $sucursal_id
        ],
        [
        'monto' => $saldo_inicial,
        'eliminado' => 0
        ]
        );        
    }

}
