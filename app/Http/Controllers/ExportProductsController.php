<?php

namespace App\Http\Controllers;

use App\Exports\ProductsExport;
use App\Models\Product;
use App\Models\User;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;


class ExportProductsController extends Controller
{

    public function reporteExcel()
    {
        $reportName = 'Reporte de productos_' . uniqid() . '.xlsx';

        return Excel::download(new Product($reportName );
    }

}
