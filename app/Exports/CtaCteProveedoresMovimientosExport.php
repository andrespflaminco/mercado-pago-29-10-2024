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


class CtaCteProveedoresMovimientosExport implements FromCollection, WithHeadings, WithCustomStartCell, WithTitle, WithStyles
{

    protected $search,$cliente_id,$from,$to;
    public $valor;
    public $selectedSucursales;

    function __construct($cliente_id,$from,$to) {
        $this->cliente_id = $cliente_id;
        $this->from = $from;
        $this->to = $to;
    }
/*
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
*/
public function collection()
{
    $compras_clientes = [];

    $comercio_id = Auth::user()->comercio_id != 1 ? Auth::user()->comercio_id : Auth::user()->id;
    
    // Inicializar saldo acumulado
    $saldo_acumulado = 0;
    
    // Formatear los datos para el Excel
    $formatted_data = $compras_clientes->map(function ($compra) use (&$saldo_acumulado){
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


public function GetData($comercio_id)
{
    $pagos_compras = pagos_facturas::join('bancos', 'bancos.id', 'pagos_facturas.banco_id')
        ->leftjoin('compras_proveedores', 'compras_proveedores.id', 'pagos_facturas.id_compra')
        ->whereBetween('pagos_facturas.created_at', [$this->from, $this->to])
        ->where('pagos_facturas.comercio_id', $comercio_id)
        ->where('pagos_facturas.tipo_pago', 1)
        ->where('pagos_facturas.eliminado', 0)
        ->where('pagos_facturas.proveedor_id', $this->proveedor_id)
        ->select(
            'bancos.nombre as nombre_banco',
            'bancos.id as id_banco',
            'pagos_facturas.url_comprobante as url_pago',
            pagos_facturas::raw('0 as id_saldo'),
            pagos_facturas::raw('0 as monto_saldo'),
            'compras_proveedores.nro_compra',
            'pagos_facturas.id as id_pago',
            'pagos_facturas.id_compra',
            'pagos_facturas.created_at',
            'pagos_facturas.monto_compra as monto_compra',
            pagos_facturas::raw('0 as monto_pago'),
            compras_proveedores::raw('0 as id_gasto'),
            compras_proveedores::raw('0 as monto_gasto'),
            compras_proveedores::raw('0 as nro_gasto')
        );

    $pagos_gastos = pagos_facturas::leftjoin('bancos', 'bancos.id', 'pagos_facturas.banco_id')
        ->leftjoin('gastos', 'gastos.id', 'pagos_facturas.id_gasto')
        ->whereBetween('pagos_facturas.created_at', [$this->from, $this->to])
        ->where('pagos_facturas.comercio_id', $comercio_id)
    //    ->where('pagos_facturas.tipo_pago', 1)
        ->where('pagos_facturas.eliminado', 0)
        ->where('pagos_facturas.proveedor_id', $this->proveedor_id)
        ->select(
            'bancos.nombre as nombre_banco',
            'bancos.id as id_banco',
            'pagos_facturas.url_comprobante as url_pago',
            pagos_facturas::raw('0 as id_saldo'),
            pagos_facturas::raw('0 as monto_saldo'),
            gastos::raw('0 as nro_compra'),
            'pagos_facturas.id as id_pago',
            pagos_facturas::raw('0 as id_compra'),
            'pagos_facturas.created_at',
            pagos_facturas::raw('0 as monto_compra'),
            'pagos_facturas.monto_gasto as monto_pago',
            'gastos.id as id_gasto',
             pagos_facturas::raw('0 as monto_gasto'),
            'gastos.id as nro_gasto'
        );

    $compras = compras_proveedores::where('compras_proveedores.comercio_id', $comercio_id)
        ->whereBetween('compras_proveedores.created_at', [$this->from, $this->to])
        ->where('compras_proveedores.proveedor_id', $this->proveedor_id)
        ->where('compras_proveedores.eliminado', 0)
        ->select(
            compras_proveedores::raw('"-" as nombre_banco'),
            compras_proveedores::raw('0 as id_banco'),
            compras_proveedores::raw('0 as url_pago'),
            compras_proveedores::raw('0 as id_saldo'),
            compras_proveedores::raw('0 as monto_saldo'),
            'compras_proveedores.nro_compra',
            compras_proveedores::raw('0 as id_pago'),
            'compras_proveedores.id as id_compra',
            'compras_proveedores.created_at',
            'compras_proveedores.total as monto_compra',
            compras_proveedores::raw('0 as monto_pago'),
            compras_proveedores::raw('0 as id_gasto'),
            compras_proveedores::raw('0 as monto_gasto'),
            compras_proveedores::raw('0 as nro_gasto')
        );

    $gastos = gastos::where('gastos.comercio_id', $comercio_id)
        ->whereBetween('gastos.created_at', [$this->from, $this->to])
        ->where('gastos.proveedor_id', $this->proveedor_id)
        ->where('gastos.eliminado', 0)
        ->select(
            gastos::raw('"-" as nombre_banco'),
            gastos::raw('0 as id_banco'),
            gastos::raw('0 as url_pago'),
            gastos::raw('0 as id_saldo'),
            gastos::raw('0 as monto_saldo'),
            gastos::raw('0 as nro_compra'),
            gastos::raw('0 as id_pago'),
            gastos::raw('0 as id_compra'),
            'gastos.created_at',
            gastos::raw('0 as monto_compra'),
            gastos::raw('0 as monto_pago'),
            'gastos.id as id_gasto',
            'gastos.monto as monto_gasto',
            'gastos.id as nro_gasto'
        );

    $saldos_iniciales = saldos_iniciales::join('bancos', 'bancos.id', 'saldos_iniciales.metodo_pago')
        ->where('saldos_iniciales.comercio_id', $comercio_id)
        ->whereBetween('saldos_iniciales.created_at', [$this->from, $this->to])
        ->where('saldos_iniciales.referencia_id', $this->proveedor_id)
        ->where('saldos_iniciales.tipo', 'proveedor')
        ->where('saldos_iniciales.eliminado', 0)
        ->select(
            'bancos.nombre as nombre_banco',
            'bancos.id as id_banco',
            saldos_iniciales::raw('0 as url_pago'),
            'saldos_iniciales.id as id_saldo',
            'saldos_iniciales.monto as monto_saldo',
            saldos_iniciales::raw('0 as nro_compra'),
            saldos_iniciales::raw('0 as id_pago'),
            saldos_iniciales::raw('0 as id_compra'),
            'saldos_iniciales.created_at',
            saldos_iniciales::raw('0 as monto_compra'),
            saldos_iniciales::raw('0 as monto_pago'),
            saldos_iniciales::raw('0 as id_gasto'),
            saldos_iniciales::raw('0 as monto_gasto'),
            saldos_iniciales::raw('0 as nro_gasto')
        );

    // Unión de las subconsultas
    $union = $pagos_compras->union($pagos_gastos)
        ->union($compras)
        ->union($gastos)
        ->union($saldos_iniciales);

    // Obtener el resultado ordenado
    $compras_proveedores = $union->orderBy('created_at', 'desc')->get();

   // dd($compras_proveedores);

    return $compras_proveedores;
}

}
