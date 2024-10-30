<?php

namespace App\Exports;

use App\Models\Sale;
use App\Models\saldos_iniciales;
use App\Models\ClientesMostrador;
use App\Models\sucursales;
use App\Models\pagos_facturas;
use App\Models\configuracion_ctas_ctes;

use Illuminate\Support\Facades\Auth;


use Maatwebsite\Excel\Concerns\FromCollection;      // para trabajar con colecciones y obtener la data
use Maatwebsite\Excel\Concerns\WithHeadings;        // para definir los títulos de encabezado
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;   // para interactuar con el libro
use Maatwebsite\Excel\Concerns\WithCustomStartCell; // para definir la celda donde inicia el reporte
use Maatwebsite\Excel\Concerns\WithTitle;           // para colocar nombre a las hojas del libro
use Maatwebsite\Excel\Concerns\WithStyles;          // para dar formato a las celdas
use Illuminate\Support\Collection;

use Carbon\Carbon;


class CtaCteClientesMovimientosExport implements FromCollection, WithHeadings, WithCustomStartCell, WithTitle, WithStyles
{

    protected $search,$cliente_id,$from,$to;
    public $valor;
    public $selectedSucursales;

    function __construct($cliente_id,$from,$to) {
        $this->cliente_id = $cliente_id;
        $this->from = $from;
        $this->to = $to;
    }

public function GetConfiguracionCtaCte($comercio_id){
        $configuracion_ctas_ctes = configuracion_ctas_ctes::where('comercio_id',$comercio_id)->first();
        
        if($configuracion_ctas_ctes == null){
        $this->valor = "por_sucursal";
        } else {
        $this->valor = $configuracion_ctas_ctes->valor;
        }

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;
    
    $sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')
    ->select('users.name','sucursales.sucursal_id')
    ->where('sucursales.casa_central_id', auth()->user()->casa_central_user_id)
    ->where('eliminado',0)
    ->get();
    
    if($this->valor == "por_sucursal"){
    $this->selectedSucursales = [$comercio_id]; // Inicializar con el ID del usuario actual
    }

    if($this->valor == "compartido"){
    $this->selectedSucursales = [auth()->user()->casa_central_user_id]; // Inicializar con el ID del usuario actual
    
    foreach ($sucursales as $sucursal) {
        $this->selectedSucursales[] = $sucursal->sucursal_id; // Agregar cada sucursal_id al array
    }
    }
    
}

public function collection()
{
    $compras_clientes = [];

    $comercio_id = Auth::user()->comercio_id != 1 ? Auth::user()->comercio_id : Auth::user()->id;
    
    $this->GetConfiguracionCtaCte($comercio_id);


    $pagos = pagos_facturas::join('bancos', 'bancos.id', 'pagos_facturas.banco_id')
        ->leftJoin('sales', 'sales.id', 'pagos_facturas.id_factura')
        ->leftJoin('sucursales', 'pagos_facturas.comercio_id', 'sucursales.sucursal_id')
        ->leftjoin('users', 'users.id', 'sucursales.sucursal_id')
        ->whereBetween('pagos_facturas.created_at', [$this->from, $this->to])
        ->whereIn('pagos_facturas.comercio_id', $this->selectedSucursales)
        ->where('pagos_facturas.tipo_pago', 1)
        ->where('pagos_facturas.eliminado', 0)
        ->where('pagos_facturas.cliente_id', $this->cliente_id)
        ->select(
            'pagos_facturas.comercio_id',
            'bancos.nombre as nombre_banco',
            'bancos.id as id_banco',
            'pagos_facturas.url_comprobante as url_pago',
            Sale::raw('0 as id_saldo'),
            Sale::raw('0 as monto_saldo'),
            'sales.nro_venta',
            'pagos_facturas.id as id_pago',
            pagos_facturas::raw('0 as id_venta'),
            pagos_facturas::raw('DATE_ADD(pagos_facturas.created_at, INTERVAL 30 SECOND) as created_at'),
            pagos_facturas::raw('0 as monto'),
            pagos_facturas::raw(' (pagos_facturas.monto + pagos_facturas.recargo + pagos_facturas.iva_pago + pagos_facturas.iva_recargo)  as monto_pago'),
            'users.name as nombre_sucursal'
        );

    $ventas = Sale::leftJoin('sucursales', 'sales.comercio_id', 'sucursales.sucursal_id')
        ->leftjoin('users', 'users.id', 'sucursales.sucursal_id')
        ->whereBetween('sales.created_at', [$this->from, $this->to])
        ->whereIn('sales.comercio_id', $this->selectedSucursales)
        ->where('sales.cliente_id', $this->cliente_id)
        ->where('sales.eliminado', 0)
        ->where('sales.status', '<>', 'Cancelado')
        ->select(
            'sales.comercio_id',
            Sale::raw('"-" as nombre_banco'),
            Sale::raw('0 as id_banco'),
            Sale::raw('0 as url_pago'),
            Sale::raw('0 as id_saldo'),
            Sale::raw('0 as monto_saldo'),
            'sales.nro_venta',
            Sale::raw('0 as id_pago'),
            'sales.id as id_venta',
            'sales.created_at',
            'sales.total as monto',
            Sale::raw('0 as monto_pago'),
            'users.name as nombre_sucursal'
        );

    $saldos_iniciales = saldos_iniciales::join('bancos', 'bancos.id', 'saldos_iniciales.metodo_pago')
        ->leftJoin('sucursales', 'saldos_iniciales.comercio_id', 'sucursales.sucursal_id')
        ->leftjoin('users', 'users.id', 'sucursales.sucursal_id')
        ->whereBetween('saldos_iniciales.created_at', [$this->from, $this->to])
        ->whereIn('saldos_iniciales.sucursal_id', $this->selectedSucursales)
        ->where('saldos_iniciales.referencia_id', $this->cliente_id)
        ->where('saldos_iniciales.tipo', 'cliente')
        ->where('saldos_iniciales.eliminado', 0)
        ->select(
            'saldos_iniciales.comercio_id',
            'bancos.nombre as nombre_banco',
            'bancos.id as id_banco',
            saldos_iniciales::raw('0 as url_pago'),
            'saldos_iniciales.id as id_saldo',
            'saldos_iniciales.monto as monto_saldo',
            saldos_iniciales::raw('0 as nro_venta'),
            saldos_iniciales::raw('0 as id_pago'),
            saldos_iniciales::raw('0 as id_venta'),
            'saldos_iniciales.created_at',
            Sale::raw('0 as monto'),
            Sale::raw('0 as monto_pago'),
            'users.name as nombre_sucursal'
        );

    $union = $pagos->union($ventas)->union($saldos_iniciales);

    $compras_clientes = $union->orderBy('created_at', 'asc')->get();

    // Inicializar saldo acumulado
    $saldo_acumulado = 0;
    
    
    // Formatear los datos para el Excel
    $formatted_data = $compras_clientes->map(function ($compra) use (&$saldo_acumulado){
        $monto = 0;
        $tipo_movimiento = '';
        if ($compra->monto > 0) {
            $tipo_movimiento = 'Venta #'. $compra->nro_venta;
            $monto = $compra->monto;
        } elseif ($compra->monto_pago > 0) {
            $tipo_movimiento = 'Pago';
            $monto = -$compra->monto_pago;
        } elseif ($compra->monto_saldo > 0) {
            $tipo_movimiento = 'Saldo inicial';
            $monto = -1*$compra->monto_saldo;
        } elseif ($compra->monto_saldo < 0) {
            $tipo_movimiento = 'Pago de saldo inicial';
            $monto = $compra->monto_saldo;
        }

        // Actualizar el saldo acumulado
        $saldo_acumulado += $monto;
        
        return [
            $compra->id_pago ?? $compra->id_venta ?? $compra->id_saldo,
            $tipo_movimiento,
            \Carbon\Carbon::parse($compra->created_at)->format('d-m-Y'),
            $compra->monto > 0 ? number_format($compra->monto, 2, ",", ".") : '',
            $compra->monto_pago > 0 ? number_format($compra->monto_pago, 2, ",", ".") : ($compra->monto_saldo != 0 ?  number_format(-1*$compra->monto_saldo, 2, ",", ".") : ''),
            $saldo_acumulado,
            $compra->id_pago > 0 ? 'Venta # ' . $compra->nro_venta : '',
            $compra->nombre_banco ?? '-',
            $compra->nombre_sucursal ?? 'Casa central'
        ];
    });

    // Encabezados del archivo Excel
    $header = [
        'ID', 'Movimiento', 'Fecha', 'Venta', 'Monto','Saldo acumulado' ,'Asociado a', 'Banco', 'Sucursal'
    ];

    // Añadir los encabezados al inicio de la colección de datos
    $formatted_data->prepend($header);

    return new Collection($formatted_data);
}


    public function headings(): array
    {
        return [];
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
        return 'Reporte de Ventas';
    }


}
