<?php

namespace App\Exports;

use App\Models\Category;
use App\Models\gastos;
use App\Models\descargas;
use App\Models\descargas_etiquetas;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;      // para trabajar con colecciones y obtener la data
use Maatwebsite\Excel\Concerns\WithHeadings;        // para definir los títulos de encabezado
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;   // para interactuar con el libro
use Maatwebsite\Excel\Concerns\WithCustomStartCell; // para definir la celda donde inicia el reporte
use Maatwebsite\Excel\Concerns\WithTitle;           // para colocar nombre a las hojas del libro
use Maatwebsite\Excel\Concerns\WithStyles;          // para dar formato a las celdas
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;


class GastosExport implements FromCollection, WithHeadings, WithCustomStartCell, WithTitle, WithStyles
{
    
    protected $search,$categoria_filtro,$etiquetas_filtro,$forma_pago_filtro, $dateFrom, $dateTo;


    function __construct($search,$categoria_filtro,$etiquetas_filtro,$forma_pago_filtro, $f1, $f2) {
        
       // dd($search,$categoria_filtro,$etiquetas_filtro,$forma_pago_filtro, $f1, $f2);
        
        $this->search = $search;
        $this->categoria_filtro = $categoria_filtro;
        $this->etiquetas_filtro = $etiquetas_filtro;
        $this->forma_pago_filtro = $forma_pago_filtro;
        $this->dateFrom = $f1;
        $this->dateTo = $f2;

    }
    
    
    public function collection()
    {
      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;

    if($this->dateFrom !== '' || $this->dateTo !== '')
    {
      $from = Carbon::parse($this->dateFrom)->format('Y-m-d').' 00:00:00';
      $to = Carbon::parse($this->dateTo)->format('Y-m-d').' 23:59:59';

    }
    
    $gastos =[];

    $gastos = gastos::leftjoin('etiqueta_gastos','etiqueta_gastos.id','gastos.etiqueta_id')
    ->join('bancos','bancos.id','gastos.cuenta')
    ->join('gastos_categorias','gastos_categorias.id','gastos.categoria')
    ->select('gastos.nombre','gastos_categorias.nombre as nombre_categoria','gastos.monto',gastos::raw('DATE_FORMAT(gastos.created_at, "%d/%m/%Y") AS fecha_formateada'),'etiqueta_gastos.nombre as nombre_etiqueta','bancos.Nombre as nombre_banco')
    ->where('gastos.comercio_id', 'like', $comercio_id);

    if($this->search != 0) {

		$gastos = $gastos->where('gastos.nombre', 'like', '%' . $this->search . '%');

    }
    if($this->categoria_filtro != 0) {

      $gastos = $gastos->where('gastos.categoria',$this->categoria_filtro);

    }

    if($this->etiquetas_filtro != 0) {

      $gastos = $gastos->where('gastos.etiqueta_id',$this->etiquetas_filtro);

    }

    if($this->forma_pago_filtro != 0) {
        
     // dd($this->forma_pago_filtro);
      $gastos = $gastos->where('gastos.cuenta',$this->forma_pago_filtro);

    }



    $gastos = $gastos->whereBetween('gastos.created_at', [$from,$to]);
	$gastos = $gastos->orderBy('gastos.created_at','desc')
	->get();
        
        
    // $data = $products;
    // dd($data);
    return $gastos;
    }

    //cabeceras del reporte
    public function headings() : array
    {
      return ["NOMBRE","CATEGORIA","MONTO","FECHA","ETIQUETA","METODO DE PAGO"];
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
