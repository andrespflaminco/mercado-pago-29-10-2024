<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\lista_precios;
use App\Models\proveedores;
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
use App\Models\saldos_iniciales;

use Carbon\Carbon;

class ProveedoresImport implements WithHeadingRow,  WithValidation, SkipsOnError, OnEachRow
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
         
        // Verifica si todos los valores de la fila son nulos o están vacíos
        if (empty(array_filter($row))) {
            // Si todos los valores son nulos o vacíos, salta la fila
            return;
        }
        
         // Si no esta en el array de saltear errores
         
         if($this->saltear_errores == 0) {
             $this->saltear_errores = [];
         }
         
         if (!in_array($rowIndex, $this->saltear_errores)) {
             
         if(Auth::user()->comercio_id != 1)
         $comercio_id = Auth::user()->comercio_id;
         else
         $comercio_id = Auth::user()->id;
         $casa_central_id = Auth::user()->casa_central_user_id;

        // Aca se ven las lista de precios 
                
        $id_proveedor = $row['cod_proveedor'];
        
        $row['saldo_inicial_cta_cte'] = empty($row['saldo_inicial_cta_cte']) ? 0 :  $row['saldo_inicial_cta_cte'];

        $proveedor = proveedores::where('id_proveedor',$row['cod_proveedor'])->where('comercio_id',$comercio_id)->where('eliminado',0)->first();
        
        if($proveedor != null) {
        
        $array_update = [
            'nombre' => $row['nombre'],
            'telefono' => $row['telefono'],
            'cuit' => $row['cuit'],
            'mail' => $row['email'],
            'direccion' => $row['calle'],
            'altura' => $row['altura'],
            'piso' => $row['piso'],
            'depto' => $row['depto'],
            'codigo_postal' => $row['codigo_postal'],
            'localidad' => $row['localidad'],
            'provincia' => $row['provincia'],
            'saldo_inicial_cuenta_corriente' => $row['saldo_inicial_cta_cte'],
            ];
        
        //dd($array_update);
        
        $proveedor->update($array_update);
        
        //dd($proveedor);
        
        $saldos_inicial = saldos_iniciales::where('referencia_id',$proveedor->id)->where('tipo','proveedor')->where('concepto','Saldo inicial')->where('comercio_id',$comercio_id)->first();
        
        if($saldos_inicial != null){
        $saldos_inicial->update([
            'monto' => $row['saldo_inicial_cta_cte']
        ]);
        }
        	    
        } else {
        
        // Si esta vacio o si no lo encuentra lo tiene que crear
        
        if(empty($id_proveedor)) {
        $ultimo_id = proveedores::where('comercio_id',$comercio_id)->max('id');
        $ultimo_proveedor = proveedores::find($ultimo_id);
        
        if($ultimo_proveedor != null){
        $this->id_proveedor = $ultimo_proveedor->id_proveedor + 1;
        } else {
        $id_proveedor = 1;    
        }
        } else {
            $id_proveedor = $row['cod_proveedor'];
        }
        
           $proveedor = proveedores::create([
                  'id_proveedor' => $row['cod_proveedor'],
                  'nombre' => $row['nombre'],
                  'telefono' => $row['telefono'],
                  'mail' => $row['email'],
                  'cuit' => $row['cuit'],
                  'direccion' => $row['calle'],
                  'altura' => $row['altura'],
                  'piso' => $row['piso'],
                  'depto' => $row['depto'],
                  'codigo_postal' => $row['codigo_postal'],
                  'localidad' => $row['localidad'],
                  'provincia' => $row['provincia'],
                  'comercio_id' => $casa_central_id,
                  'creador_id' => $comercio_id,
                //  'plazo_cuenta_corriente' => $plazo_cuenta_corriente,
                  'saldo_inicial_cuenta_corriente' => $row['saldo_inicial_cta_cte'],
                //  'fecha_inicial_cuenta_corriente' => $fecha_inicial_cuenta_corriente,
                ]);
                
                
        		saldos_iniciales::create([
        		'tipo' => 'proveedor',
                'concepto' => 'Saldo inicial',
                'referencia_id' => $proveedor->id,
                'comercio_id' => $comercio_id,
                'monto' => $row['saldo_inicial_cta_cte'],
                'eliminado' => 0,
                'fecha' => Carbon::now()
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
