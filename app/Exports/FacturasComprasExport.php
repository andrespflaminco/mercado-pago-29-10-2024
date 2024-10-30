<?php

namespace App\Exports;

use App\Models\facturacion;
use App\Models\detalle_facturacion;
use App\Models\Sale;
use App\Models\compras_proveedores;

use App\Models\ClientesMostrador;
use Illuminate\Support\Facades\Auth;


use Maatwebsite\Excel\Concerns\FromCollection;      // para trabajar con colecciones y obtener la data
use Maatwebsite\Excel\Concerns\WithHeadings;        // para definir los títulos de encabezado
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;   // para interactuar con el libro
use Maatwebsite\Excel\Concerns\WithCustomStartCell; // para definir la celda donde inicia el reporte
use Maatwebsite\Excel\Concerns\WithTitle;           // para colocar nombre a las hojas del libro
use Maatwebsite\Excel\Concerns\WithStyles;          // para dar formato a las celdas
use Carbon\Carbon;


class FacturasComprasExport implements FromCollection, WithHeadings, WithCustomStartCell, WithTitle, WithStyles
{

    protected $sucursal_id,$tipo_comprobante_buscar,$proveedor_id,$dateFrom, $dateTo;

    function __construct($sucursal_id,$tipo_comprobante_buscar,$proveedor_id, $f1, $f2) {
        
        //dd($sucursal_id,$tipo_comprobante_buscar,$facturas_repetidas, $clienteId,$estado_pago, $f1, $f2);
        $this->sucursal_id = $sucursal_id;
        $this->proveedor_id = $proveedor_id;
        $this->tipo_comprobante_buscar = $tipo_comprobante_buscar;

        $this->proveedor_seleccionado = explode(",", $proveedor_id);

        $this->dateFrom = $f1;
        $this->dateTo = $f2;

    }


    public function collection()
    {
        $data =[];

            if($this->dateFrom !== '' || $this->dateTo !== '')
            {
              $from = Carbon::parse($this->dateFrom)->format('Y-m-d').' 00:00:00';
              $to = Carbon::parse($this->dateTo)->format('Y-m-d').' 23:59:59';

            }


              if(Auth::user()->comercio_id != 1)
              $comercio_id = Auth::user()->comercio_id;
              else
              $comercio_id = Auth::user()->id;

             $facturasRepetidas = facturacion::select('sale_id')
                ->groupBy('sale_id')
                ->havingRaw('COUNT(sale_id) > 1')
                ->pluck('sale_id');
                    
            $facturasNoRepetidas = facturacion::select('sale_id')
                ->groupBy('sale_id')
                ->havingRaw('COUNT(sale_id) < 2')
                ->pluck('sale_id');
                

           $data = compras_proveedores::join('proveedores', 'proveedores.id', '=', 'compras_proveedores.proveedor_id')
    ->select(
        'compras_proveedores.created_at as fecha_facturacion',
        compras_proveedores::raw('(CASE WHEN compras_proveedores.tipo_factura = "Elegir" THEN "" ELSE compras_proveedores.tipo_factura END) as tipo'),
        'compras_proveedores.numero_factura',
        'proveedores.nombre',
        'proveedores.cuit',
        compras_proveedores::raw('(compras_proveedores.subtotal - compras_proveedores.descuento) as subtotal'),
        'compras_proveedores.iva',
        'compras_proveedores.total'
    )
    ->whereBetween('compras_proveedores.created_at', [$from, $to])
    ->where('compras_proveedores.comercio_id', $this->sucursal_id);
    
    if ($this->proveedor_id > 0) {
        $data = $data->whereIn('compras_proveedores.proveedor_id', $this->proveedor_seleccionado);
    }
    
    if ($this->tipo_comprobante_buscar != "0") {
        $cadenaLimpia = trim($this->tipo_comprobante_buscar);
        $data = $data->where('compras_proveedores.tipo_factura', $cadenaLimpia);
    }
    
    $data = $data->whereRaw('compras_proveedores.numero_factura IS NOT NULL')
        ->groupBy(
            'compras_proveedores.created_at',
            'tipo',
            'compras_proveedores.numero_factura',
            'proveedores.nombre',
            'proveedores.cuit',
            'subtotal',
            'compras_proveedores.iva',
            'compras_proveedores.total',
            'compras_proveedores.descuento',
            'compras_proveedores.subtotal'
        )
        ->get();
    
                       
                   //->toSql();
                   
                    //dd($data);
                    
                    return $data;


    }



    //cabeceras del reporte
    public function headings() : array
    {
      return ["FECHA","TIPO","NUMERO","NOMBRE PROVEEDOR","CUIT PROVEEDOR","NETO GRAVADO","IVA","TOTAL"];
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
