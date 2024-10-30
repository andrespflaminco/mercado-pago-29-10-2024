<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;      // para trabajar con colecciones y obtener la data
use Maatwebsite\Excel\Concerns\WithHeadings;        // para definir los títulos de encabezado
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;   // para interactuar con el libro
use Maatwebsite\Excel\Concerns\WithCustomStartCell; // para definir la celda donde inicia el reporte
use Maatwebsite\Excel\Concerns\WithTitle;           // para colocar nombre a las hojas del libro
use Maatwebsite\Excel\Concerns\WithStyles;          // para dar formato a las celdas

//use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
//use PhpOffice\PhpSpreadsheet\Shared\Date;
//use Maatwebsite\Excel\Concerns\WithColumnFormatting;
//use Maatwebsite\Excel\Concerns\WithEvents;
//use Maatwebsite\Excel\Events\BeforeExport;
//use Maatwebsite\Excel\Concerns\ShouldAutoSize;

use App\Models\SaleDetail;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SalesDetailExport implements FromCollection, WithHeadings, WithCustomStartCell, WithTitle,WithStyles
							//ShouldAutoSize, WithEvents //WithColumnFormatting
{
	// propiedades
	protected $ClienteSeleccionado, $usuarioSeleccionado,$metodopagoSeleccionado, $productoSeleccionado, $categoriaSeleccionado, $almacenSeleccionado, $f1, $f2, $sucursal_id;


	//constructor
	function __construct($usuarioSeleccionado, $ClienteSeleccionado,$metodopagoSeleccionado, $productoSeleccionado, $categoriaSeleccionado, $almacenSeleccionado, $f1, $f2, $sucursal_id) {

		$this->cliente_seleccionado = $ClienteSeleccionado;
		$this->mpago_seleccionado = $metodopagoSeleccionado;
		$this->prod_seleccionado = $productoSeleccionado;
		$this->cat_seleccionado = $categoriaSeleccionado;
		$this->alm_seleccionado = $almacenSeleccionado;
		$this->us_seleccionado = $usuarioSeleccionado;
		$this->sucursal_id = $sucursal_id;


		$this->locationUsers = explode(",", $ClienteSeleccionado);
		$this->metodopago_seleccionado = explode(",", $metodopagoSeleccionado);
		$this->producto_seleccionado = explode(",", $productoSeleccionado);
		$this->categoria_seleccionado = explode(",", $categoriaSeleccionado);
		$this->almacen_seleccionado = explode(",", $almacenSeleccionado);
		$this->usuario_seleccionado = explode(",", $usuarioSeleccionado);
		$this->dateFrom   = $f1;
		$this->dateTo     = $f2;


	}


   // método para obtener de base de datos la info a exportar
    public function collection()
    {
    	$data = [];

    		$from = Carbon::parse($this->dateFrom)->format('Y-m-d').' 00:00:00';
    		$to = Carbon::parse($this->dateTo)->format('Y-m-d').' 23:59:59';



				if(Auth::user()->comercio_id != 1)
				$comercio_id = Auth::user()->comercio_id;
				else
				$comercio_id = Auth::user()->id;


				$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
				->leftjoin('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
				->leftjoin('sales as sa','sa.id','sale_details.sale_id')
				->leftjoin('metodo_pagos as m','m.id','sa.metodo_pago')
				->leftjoin('bancos','bancos.id','m.cuenta')
				->leftjoin('users','users.id','sa.user_id')
				->leftjoin('clientes_mostradors as cl','cl.id','sale_details.cliente_id')
				->leftjoin('categories','categories.id','p.category_id')
				->select(
				    'sa.nro_venta as sale_id',
				    SaleDetail::raw("DATE_FORMAT(sale_details.created_at,'%d-%m-%Y %H:%i') as fecha"),
				    'sale_details.product_barcode as barcode',
				    'sale_details.product_name as product',
				    'categories.name as nombre_categoria',
				    'sale_details.price',
				    'sale_details.quantity',
				    SaleDetail::raw('CASE WHEN sale_details.tipo_unidad_medida = 1 THEN "KG" ELSE "UNIDAD" END AS unidad_medida'),
				    SaleDetail::raw('( ((sale_details.price*sale_details.quantity) + IFNULL(sale_details.recargo, 0) - IFNULL(sale_details.descuento, 0))*(sale_details.iva)) as iva'),
				    SaleDetail::raw('(IFNULL(sale_details.recargo, 0)) as recargo'),
				    SaleDetail::raw('(IFNULL(sale_details.descuento, 0)) as descuento'),
				    SaleDetail::raw('(IFNULL(sale_details.descuento_promo * sale_details.cantidad_promo, 0)) as descuento_promo'),
				    SaleDetail::raw('((sale_details.price*sale_details.quantity) + ( ((sale_details.price*sale_details.quantity) + IFNULL(sale_details.recargo, 0) - IFNULL(sale_details.descuento, 0) - IFNULL(sale_details.descuento_promo * sale_details.cantidad_promo, 0))*(IFNULL(sale_details.iva, 0))) + IFNULL(sale_details.recargo, 0) - IFNULL(sale_details.descuento, 0) - IFNULL(sale_details.descuento_promo * sale_details.cantidad_promo, 0)) as total'),
				    SaleDetail::raw('(IFNULL(sale_details.cost, 0)) as costo_total'),'cl.nombre as nombre_cliente','users.name as nombre_usuario','bancos.nombre as nombre_banco','m.nombre as nombre_metodo_pago','sa.status','s.nombre as almacen')
				->where('sale_details.comercio_id',$this->sucursal_id);

				if($this->prod_seleccionado > 0) {
						$data = $data->whereIn('sale_details.product_id',$this->producto_seleccionado);

				}

				if ($this->us_seleccionado > 0) {
					$data = $data->whereIn('sa.user_id',$this->usuario_seleccionado);

				}

				if ($this->cliente_seleccionado > 0) {
					$data = $data->whereIn('sale_details.cliente_id',$this->locationUsers);

				}

				if ($this->mpago_seleccionado > 0) {
						$data = $data->whereIn('sale_details.metodo_pago',$this->metodopago_seleccionado);

				}
				if($this->alm_seleccionado > 0){
						$data = $data->whereIn('sale_details.seccionalmacen_id',$this->almacen_seleccionado);
				}
				if($this->cat_seleccionado > 0){
						$data = $data->whereIn('p.category_id',$this->categoria_seleccionado);
				}

				$data = $data->whereBetween('sale_details.created_at', [$from, $to])
				->where('sale_details.eliminado', 0)
				->where('sa.eliminado', 0)
				->orderBy('sale_details.sale_id','desc')
				->get();

				return $data;

    }

    //personalizar el nombre de las cabeceras
    public function headings() :array
    {
    	return ["NRO VENTA","FECHA","CODIGO PROD","PRODUCTO","CATEGORIA","PRECIO", "CANTIDAD","UNIDAD MEDIDA","IVA","RECARGO FINANC.","DESCUENTO","DESCUENTO POR PROMOCIONES","TOTAL","COSTO","CLIENTE" ,"VENDEDOR","BANCO","METODO DE PAGO","ESTADO VENTA","ALMACEN"];
    }

    //especificar celda a partir de la cual se va llenar el excel con la información del reporte
    public function startCell(): string
    {
    	return 'A1';
    }

	// establecemos los encabezados con texto bold
    public function styles(Worksheet $sheet)
    {
    	return [
    		1    => ['font' => ['bold' => true]],
    	];
    }

    //nombre de la hoja de excel
    public function title(): string
    {
    	return 'Reporte de Ventas por Producto';
    }


	/*
    public function map($data): array
    {
    	return [
    		Date::dateTimeToExcel($data->created_at)
    	];
    }
	*/
	 //formato columnas moneda
    /*
	public function columnFormats(): array
    {
    	return [
    		'B' => '"$ "#,##0.00_-',
    		'F' => 'm/d/Y h:i:s'
    	];
    }
	*/


/*
 public function registerEvents(): array
    {
        return [
            // Handle by a closure.
            BeforeExport::class => function(BeforeExport $event) {
                $event->writer->getProperties()->setTitle('LFax');
            },
        ];
    }
*/


}
