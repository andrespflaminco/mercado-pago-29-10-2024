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
use App\Models\estados;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProduccionExport implements FromCollection, WithHeadings, WithCustomStartCell, WithTitle,WithStyles
							//ShouldAutoSize, WithEvents //WithColumnFormatting
{
	// propiedades
	protected $EstadoId, $dateFrom, $dateTo, $reportType;

	//constructor
	function __construct($EstadoId, $reportType, $f1, $f2) {
		$this->EstadoId     = $EstadoId;
		$this->dateFrom   = $f1;
		$this->dateTo     = $f2;
		$this->reportType = $reportType;
	}


   // método para obtener de base de datos la info a exportar
    public function collection()
    {
    	$data = [];

    	if($this->reportType == 1){
    		$from = Carbon::parse($this->dateFrom)->format('Y-m-d').' 00:00:00';
    		$to = Carbon::parse($this->dateTo)->format('Y-m-d').' 23:59:59';
    	} else {
    		$from = Carbon::parse(Carbon::now())->format('Y-m-d').' 00:00:00';
    		$to = Carbon::parse(Carbon::now())->format('Y-m-d').' 23:59:59';
    	}


    	if($this->EstadoId == 0)
    	{
				if(Auth::user()->comercio_id != 1)
				$comercio_id = Auth::user()->comercio_id;
				else
				$comercio_id = Auth::user()->id;

    		$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
        ->join('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
				->join('estados','estados.id','sale_details.estado')
		    ->select('sale_details.id','p.name as product','sale_details.quantity','s.nombre as almacen','sale_details.created_at','estados.nombre as nombre_estado')
		    ->where('sale_details.comercio_id',$comercio_id)
        ->whereBetween('sale_details.created_at', [$from, $to])
        ->get();
    	} else {
				if(Auth::user()->comercio_id != 1)
				$comercio_id = Auth::user()->comercio_id;
				else
				$comercio_id = Auth::user()->id;

    		$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
        ->join('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
				->join('estados','estados.id','sale_details.estado')
        ->select('sale_details.id','p.name as product','sale_details.quantity','s.nombre as almacen','sale_details.created_at','estados.nombre as nombre_estado')
        ->where('sale_details.seccionalmacen_id',$this->EstadoId)
        ->where('sale_details.comercio_id',$comercio_id)
        ->whereBetween('sale_details.created_at', [$from, $to])
        ->get();
    	}

    	return $data;


    }

    //personalizar el nombre de las cabeceras
    public function headings() :array
    {
    	return ["FOLIO", "PRODUCTO","CANTIDAD","ALMACEN", "FECHA DE VENTA","ESTADO"];
    }

    //especificar celda a partir de la cual se va llenar el excel con la información del reporte
    public function startCell(): string
    {
    	return 'A2';
    }

	// establecemos los encabezados con texto bold
    public function styles(Worksheet $sheet)
    {
    	return [
    		2    => ['font' => ['bold' => true]],
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
