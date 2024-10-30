<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use App\Models\Sale;
use App\Exports\SalesExport;
use App\Exports\SalesDetailExport;
use App\Exports\ProduccionExport;
use Maatwebsite\Excel\Facades\Excel;


class ExportReportsController extends Controller
{
    public function reportPDF($userId, $reportType, $dateFrom=null, $dateTo=null)
    {
    	$data = [];

    	if($reportType == 1){
    	$from = Carbon::parse($dateFrom)->format('Y-m-d').' 00:00:00';
    	$to = Carbon::parse($dateTo)->format('Y-m-d').' 23:59:59';
    } else {
    	$from = Carbon::parse(Carbon::now())->format('Y-m-d').' 00:00:00';
    	$to = Carbon::parse(Carbon::now())->format('Y-m-d').' 23:59:59';
	}


    	if($userId == 0)
		{
    		$data = Sale::join('users as u', 'u.id', 'sales.user_id')
    				->select('sales.*', 'u.name as user')
    				->whereBetween('sales.created_at', [$from, $to])
    				->get();
    	} else {
    		$data = Sale::join('users as u', 'u.id', 'sales.user_id')
    				->select('sales.*', 'u.name as user')
    				->whereBetween('sales.created_at', [$from, $to])
    				->where('user_id', $userId)
    				->get();
    	}

        $user =$userId == 0 ? 'Todos': \App\Models\User::find($userId)->name;
    	 $pdf = PDF::loadView('pdf.reporte', compact('data','reportType','user','dateFrom','dateTo'));

        //return $pdf->download('salesReport.pdf');
        return $pdf->stream('salesReport.pdf');

    }




    public function reporteExcel($userId, $reportType, $dateFrom=null, $dateTo=null)
    {
        $reportName = 'Reporte de Ventas_' . uniqid() . '.xlsx';
        return Excel::download(new SalesExport($userId, $reportType, $dateFrom, $dateTo), $reportName);

    }

    public function reporteExcelDetalle($userId, $reportType, $dateFrom=null, $dateTo=null)
    {
        $reportName = 'Reporte de Ventas por Producto_' . uniqid() . '.xlsx';
        return Excel::download(new SalesDetailExport($userId, $reportType, $dateFrom, $dateTo), $reportName);

    }

    public function reporteExcelProduccion($EstadoId, $reportType, $dateFrom=null, $dateTo=null)
    {
        $reportName = 'Reporte de Produccion por Producto_' . uniqid() . '.xlsx';
        return Excel::download(new ProduccionExport($EstadoId, $reportType, $dateFrom, $dateTo), $reportName);

    }




}
