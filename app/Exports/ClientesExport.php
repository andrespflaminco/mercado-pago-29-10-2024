<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\User;
use App\Models\sucursales;
use App\Models\ClientesMostrador;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;      // para trabajar con colecciones y obtener la data
use Maatwebsite\Excel\Concerns\WithHeadings;        // para definir los títulos de encabezado
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;   // para interactuar con el libro
use Maatwebsite\Excel\Concerns\WithCustomStartCell; // para definir la celda donde inicia el reporte
use Maatwebsite\Excel\Concerns\WithTitle;           // para colocar nombre a las hojas del libro
use Maatwebsite\Excel\Concerns\WithStyles;          // para dar formato a las celdas
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;

class ClientesExport implements FromCollection, WithHeadings, WithCustomStartCell, WithTitle, WithStyles
{


    protected $sucursal_id;
    public $casa_central_id;

    function __construct($sucursal_id) {
        $this->sucursal_id = $sucursal_id;

    }

public function collection()
{
    $user = Auth::user();
    $comercio_id = $user->comercio_id != 1 ? $user->comercio_id : $user->id;

    $this->tipo_usuario = User::find($comercio_id);
    
    if ($this->tipo_usuario->sucursal != 1) {
        $this->casa_central_id = $comercio_id;
    } else {
        $this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
        $this->casa_central_id = $this->casa_central->casa_central_id;
    }
    
    $this->sucursales = sucursales::join('users', 'users.id', 'sucursales.sucursal_id')
        ->select('users.name', 'sucursales.sucursal_id')
        ->where('sucursales.eliminado', 0)
        ->where('casa_central_id', $this->casa_central_id)
        ->get();

    
    $sucursalIds = $this->sucursales->pluck('sucursal_id')->toArray();
    array_push($sucursalIds, $this->casa_central_id);

    $saldos_inicial_casa_central = DB::raw("(SELECT monto FROM saldos_iniciales 
    WHERE saldos_iniciales.sucursal_id = ". $this->casa_central_id . "
    AND saldos_iniciales.referencia_id = clientes_mostradors.id 
    AND saldos_iniciales.tipo LIKE 'cliente' 
    AND saldos_iniciales.concepto LIKE 'Saldo inicial' LIMIT 1) AS SALDO_INICIAL_".$this->casa_central_id);
    
    
    if(0 < count($this->sucursales))    {
    $saldos_iniciales = [];
    foreach ($this->sucursales as $ss) {
        $saldos_inicial = DB::raw("(SELECT monto FROM saldos_iniciales 
        WHERE saldos_iniciales.sucursal_id = ". $ss->sucursal_id . "
        AND saldos_iniciales.referencia_id = clientes_mostradors.id 
        AND saldos_iniciales.tipo LIKE 'cliente' 
        AND saldos_iniciales.concepto LIKE 'Saldo inicial' LIMIT 1) AS SALDO_INICIAL_".$ss->sucursal_id);
        array_push($saldos_iniciales, $saldos_inicial);
    }

    
    $saldo_sucu = implode(", ", $saldos_iniciales);
    $saldos_sucursales = DB::raw($saldo_sucu);        
    } else {
        
    }


    $data = ClientesMostrador::leftJoin('lista_precios', 'lista_precios.id', 'clientes_mostradors.lista_precio')
        ->join('users', 'users.id', 'clientes_mostradors.creador_id')
        ->where('clientes_mostradors.comercio_id', $this->casa_central_id)
        ->where('clientes_mostradors.eliminado', 0)
        ->where('clientes_mostradors.sucursal_id', 0);

    if ($this->sucursal_id != 0) {
        $data = $data->where('clientes_mostradors.creador_id', 'like', $this->sucursal_id);    
    } else {
        $data = $data->whereIn('clientes_mostradors.creador_id', $sucursalIds);    
    }
    if(0 < count($this->sucursales)){
    $data = $data->select(
            'clientes_mostradors.id_cliente',
            'clientes_mostradors.nombre as cliente',
            'users.name as sucursal',
            'clientes_mostradors.dni',
            'clientes_mostradors.telefono',
            'clientes_mostradors.email',
            'clientes_mostradors.direccion',
            'clientes_mostradors.altura',
            'clientes_mostradors.piso',
            'clientes_mostradors.depto',
            'clientes_mostradors.barrio',
            'clientes_mostradors.localidad',
            'clientes_mostradors.provincia',
            'clientes_mostradors.codigo_postal',
            'lista_precios.nombre as lista',
            DB::raw('DATE_FORMAT(clientes_mostradors.last_sale, "%d/%m/%Y") as fecha'),
            $saldos_inicial_casa_central,
            $saldos_sucursales,
            DB::raw('DATE_FORMAT(clientes_mostradors.fecha_inicial_cuenta_corriente, "%d/%m/%Y") as fecha_cta'),
            'clientes_mostradors.plazo_cuenta_corriente',
            'clientes_mostradors.monto_maximo_cuenta_corriente',
            'clientes_mostradors.observaciones'
        )
        ->orderBy('clientes_mostradors.nombre', 'asc')
        ->get();
        
    } else {
        $data = $data->select(
            'clientes_mostradors.id_cliente',
            'clientes_mostradors.nombre as cliente',
            'users.name as sucursal',
            'clientes_mostradors.dni',
            'clientes_mostradors.telefono',
            'clientes_mostradors.email',
            'clientes_mostradors.direccion',
            'clientes_mostradors.altura',
            'clientes_mostradors.piso',
            'clientes_mostradors.depto',
            'clientes_mostradors.barrio',
            'clientes_mostradors.localidad',
            'clientes_mostradors.provincia',
            'clientes_mostradors.codigo_postal',
            'lista_precios.nombre as lista',
            DB::raw('DATE_FORMAT(clientes_mostradors.last_sale, "%d/%m/%Y") as fecha'),
            $saldos_inicial_casa_central,
            DB::raw('DATE_FORMAT(clientes_mostradors.fecha_inicial_cuenta_corriente, "%d/%m/%Y") as fecha_cta'),
            'clientes_mostradors.plazo_cuenta_corriente',
            'clientes_mostradors.monto_maximo_cuenta_corriente',
            'clientes_mostradors.observaciones'
        )
        ->orderBy('clientes_mostradors.nombre', 'asc')
        ->get();
    
    }

    return $data;
}


    //cabeceras del reporte
    public function headings() : array
    {
    
    $sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')
    ->select('users.name','sucursales.sucursal_id')
    ->where('sucursales.eliminado',0)
    ->where('casa_central_id', Auth::user()->casa_central_user_id )->get();


      $header = ["COD CLIENTE","NOMBRE","SUCURSAL ASOCIADA","CUIT","TELEFONO","EMAIL","CALLE","ALTURA","PISO","DEPTO","BARRIO","LOCALIDAD","PROVINCIA","CODIGO POSTAL","LISTA PRECIOS","ULTIMA COMPRA","SALDO INICIAL CASA CENTRAL"];

      $i = 17;
      
      foreach($sucursales as $ss) {
        $header[$i++] =  $ss->sucursal_id."_SALDO_INICIAL_".$ss->name;
      }  
      
     array_push($header,"FECHA INICIAL CUENTA CORRIENTE","DIAS DE PLAZO CUENTA CORRIENTE","MONTO MAXIMO CUENTA CORRIENTE","OBSERVACIONES");

     //dd($header);
     
     return $header;
      
    }


    public function startCell(): string
    {
        return 'A1';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true ]],
        ];
    }

    public function title(): string
    {
        return 'Catalogo de productos';
    }


}
