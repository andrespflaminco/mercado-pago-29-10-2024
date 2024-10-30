<?php

namespace App\Exports;

use App\Models\Sale;
use App\Models\SaleDetail;
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

use Illuminate\Support\Facades\DB;

use Carbon\Carbon;


class CtaCteClientesMovimientosExportPorProducto implements FromCollection, WithHeadings, WithCustomStartCell, WithTitle, WithStyles
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
    try {
        $compras_clientes = [];

        $comercio_id = Auth::user()->comercio_id != 1 ? Auth::user()->comercio_id : Auth::user()->id;

        $this->GetConfiguracionCtaCte($comercio_id);

        // Consultas
        $pagos = pagos_facturas::join('bancos', 'bancos.id', 'pagos_facturas.banco_id')
            ->leftJoin('sales', 'sales.id', 'pagos_facturas.id_factura')
            ->leftJoin('sucursales', 'pagos_facturas.comercio_id', 'sucursales.sucursal_id')
            ->leftJoin('users', 'users.id', 'sucursales.sucursal_id')
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
                pagos_facturas::raw('0 as product_name'),
                DB::raw('DATE_ADD(pagos_facturas.created_at, INTERVAL 5 MINUTE) as created_at'),
                pagos_facturas::raw('0 as monto'),
                pagos_facturas::raw('(pagos_facturas.monto + pagos_facturas.recargo + pagos_facturas.iva_pago + pagos_facturas.iva_recargo) as monto_pago'),
                'users.name as nombre_sucursal',
                Sale::raw('"" as product_name'),
                Sale::raw('0 as cantidad'),
                Sale::raw('0 as precio')
            );

        $ventas = SaleDetail::join('sales', 'sales.id', 'sale_details.sale_id')
            ->leftJoin('sucursales', 'sales.comercio_id', 'sucursales.sucursal_id')
            ->leftJoin('users', 'users.id', 'sucursales.sucursal_id')
            ->whereBetween('sales.created_at', [$this->from, $this->to])
            ->whereIn('sales.comercio_id', $this->selectedSucursales)
            ->where('sales.cliente_id', $this->cliente_id)
            ->where('sales.eliminado', 0)
            ->where('sales.status', '<>', 'Cancelado')
            ->select(
                'sales.comercio_id',
                SaleDetail::raw('"-" as nombre_banco'),
                SaleDetail::raw('0 as id_banco'),
                SaleDetail::raw('0 as url_pago'),
                SaleDetail::raw('0 as id_saldo'),
                SaleDetail::raw('0 as monto_saldo'),
                SaleDetail::raw('sales.nro_venta'),
                SaleDetail::raw('sale_details.product_name'),
                Sale::raw('0 as id_pago'),
                'sales.id as id_venta',
                'sales.created_at',
                DB::raw('
                    CASE 
                        WHEN sale_details.relacion_precio_iva = 2 
                        THEN 
                            (sale_details.precio_original * sale_details.quantity) -
                            CASE WHEN sale_details.cantidad_promo > 0 THEN 
                                sale_details.cantidad_promo * sale_details.descuento_promo * (1 + sale_details.iva)
                            ELSE 0 
                            END
                        ELSE 
                            sale_details.price * sale_details.quantity + sale_details.iva_total -
                            CASE WHEN sale_details.cantidad_promo > 0 THEN 
                                sale_details.cantidad_promo * sale_details.descuento_promo
                            ELSE 0 END
                    END AS monto
                '),
                Sale::raw('0 as monto_pago'),
                'users.name as nombre_sucursal',
                'sale_details.product_name',
                'sale_details.quantity as cantidad',
                'sale_details.precio_original as precio'
            );

        $saldos_iniciales = saldos_iniciales::join('bancos', 'bancos.id', 'saldos_iniciales.metodo_pago')
            ->leftJoin('sucursales', 'saldos_iniciales.comercio_id', 'sucursales.sucursal_id')
            ->leftJoin('users', 'users.id', 'sucursales.sucursal_id')
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
                saldos_iniciales::raw('0 as product_name'),
                saldos_iniciales::raw('0 as id_venta'),
                'saldos_iniciales.created_at',
                Sale::raw('0 as monto'),
                Sale::raw('0 as monto_pago'),
                'users.name as nombre_sucursal',
                Sale::raw('"" as product_name'),
                Sale::raw('0 as cantidad'),
                Sale::raw('0 as precio')
            );

        $union = $pagos->union($ventas)->union($saldos_iniciales);

        // Ordenar por el campo created_at modificado
        $compras_clientes = $union->orderBy('created_at', 'asc')->get();

        $saldo_acumulado = 0; // Inicializamos el saldo acumulado

        $formatted_data = $compras_clientes->map(function ($compra) use (&$saldo_acumulado) {
            $tipo_movimiento = '';
            $id = '';
        
            // Determinar el tipo de movimiento
            if ($compra->monto > 0) { 
                $tipo_movimiento = 'Venta # '. $compra->nro_venta .' - '. $compra->product_name;
                $id = $compra->nro_venta; // Mostrar el número de venta en lugar del nombre del producto
                $monto = $compra->monto;
            } elseif ($compra->monto_pago > 0) {
                $tipo_movimiento = 'Pago - '.  $compra->nombre_banco ?? '';
                $id = $compra->id_pago;
                $monto = -$compra->monto_pago;
            } elseif ($compra->monto_saldo > 0) {
                $tipo_movimiento = 'Saldo inicial';
                $id = $compra->id_saldo;
                $monto = $compra->monto_saldo;
            } elseif ($compra->monto_saldo < 0) {
                $tipo_movimiento = 'Pago de saldo inicial';
                $id = $compra->id_saldo;
                $monto = $compra->monto_saldo;
            } else {
                $monto = 0;
            }
        
            // Actualizar el saldo acumulado
            $saldo_acumulado += $monto;

            return [
                $id,
                $tipo_movimiento,
                Carbon::parse($compra->created_at)->format('d-m-Y'),
                $compra->precio > 0 ? number_format($compra->precio, 2, ",", ".") : '',
                $compra->cantidad > 0 ? $compra->cantidad : '',
                $compra->monto > 0 ? number_format($compra->monto, 2, ",", ".") : '',
                $compra->monto_pago > 0 ? number_format($compra->monto_pago, 2, ",", ".") : ($compra->monto_saldo != 0 ? number_format(-1 * $compra->monto_saldo, 2, ",", ".") : ''),
                number_format($saldo_acumulado, 2, ",", "."), // Añadimos el saldo acumulado
                $compra->monto_pago > 0 ? 'Venta # ' . $compra->nro_venta : '',
                $compra->nombre_banco ?? '-',
                $compra->nombre_sucursal ?? 'Casa central'
            ];
        });

        // Encabezados del archivo Excel
        $header = [
            'ID', 'Movimiento', 'Fecha', 'Precio', 'Cantidad', 'Venta', 'Monto', 'Saldo Acumulado',
            'Asociado a', 'Banco', 'Sucursal'
        ];

        // Añadir los encabezados al inicio de la colección de datos
        $formatted_data->prepend($header);

        return new Collection($formatted_data);
    } catch (\Exception $e) {
        Log::error('Error en la generación de la colección: ' . $e->getMessage());
        return new Collection(); // Retorna una colección vacía en caso de error
    }
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
