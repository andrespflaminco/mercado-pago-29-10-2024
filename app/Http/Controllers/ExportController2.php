<?php

namespace App\Http\Controllers;

use App\Exports\SalesExport;
use App\Exports\ProductsExport;
use App\Exports\HojaRutaExport;
use app\Exports\CategoryExport;
use App\Exports\SalesDetailExport;
use App\Exports\CajaExport;
use App\Exports\AsistenteExport;
use App\Exports\ProduccionExport;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\User;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Mail;


class ExportController extends Controller
{

  public $userId;
  public $clienteId;
  public $usuario;

    public function reportPDF($usuarioSeleccionado, $clienteId, $dateFrom = null, $dateTo = null)
    {
        $data = [];

        $this->cId = $clienteId;
        $this->uId = $usuarioSeleccionado;
        $this->userId = explode(",", $usuarioSeleccionado);
        $this->clienteId = explode(",", $clienteId);


       $from = Carbon::parse($dateFrom)->format('Y-m-d') . ' 00:00:00';
       $to = Carbon::parse($dateTo)->format('Y-m-d')     . ' 23:59:59';

       if($this->cId == 0)
       {

       if($this->uId == 0)
       {
         if(Auth::user()->comercio_id != 1)
         $comercio_id = Auth::user()->comercio_id;
         else
         $comercio_id = Auth::user()->id;

        $data = Sale::join('users as u','u.id','sales.user_id')
        ->select('sales.*','u.name as user')
        ->whereBetween('sales.created_at', [$from, $to])
        ->where('sales.comercio_id', $comercio_id)
        ->get();
        } else {
        $data = Sale::join('users as u','u.id','sales.user_id')
        ->select('sales.*','u.name as user')
        ->whereBetween('sales.created_at', [$from, $to])
        ->whereIn('sales.user_id', $this->userId)
        ->get();
        }

        } else {


        if($this->uId == 0)
        {
         $data = Sale::join('users as u','u.id','sales.user_id')
         ->select('sales.*','u.name as user')
         ->whereBetween('sales.created_at', [$from, $to])
         ->whereIn('sales.cliente_id', $this->clienteId)
         ->get();
        } else {
         $data = Sale::join('users as u','u.id','sales.user_id')
         ->select('sales.*','u.name as user')
         ->whereBetween('sales.created_at', [$from, $to])
         ->whereIn('sales.cliente_id', $this->clienteId)
         ->whereIn('sales.user_id', $this->userId)
         ->get();
          }

      }

    $pdf = PDF::loadView('pdf.reporte', compact('data','dateFrom','dateTo'));

Mail::send('emails/templates/send-invoice', $messageData, function ($mail) use ($pdf) {
    $mail->from('andrespasquetta@gmail.com', 'Flaminco');
    $mail->to('andrespasquetta@gmail.com');
    $mail->attachData($pdf->output(), 'test.pdf');
});
/*
    $pdf = new DOMPDF();
    $pdf->setBasePath(realpath(APPLICATION_PATH . '/css/'));
    $pdf->loadHtml($html);
    $pdf->render();
    */
    /*
    $pdf->set_protocol(WWW_ROOT);
    $pdf->set_base_path('/');
*/

        return $pdf->stream('salesReport.pdf'); // visualizar
        //$customReportName = 'salesReport_'.Carbon::now()->format('Y-m-d').'.pdf';
        //return $pdf->download($customReportName); //descargar

    }


    public function reportPDFFactura($Id)
  {
      $detalle_venta = [];
      $usuario = [];
      $detalle_cliente = [];
      $total_total = [];
      $fecha = [];

      $detalle_venta = SaleDetail::join('products as p','p.id','sale_details.product_id')
      ->select('sale_details.id','sale_details.price','sale_details.quantity','p.name as product', SaleDetail::raw('sale_details.price*sale_details.quantity as total'))
      ->where('sale_details.sale_id', $Id)
      ->where('sale_details.eliminado', 0)
      ->get();


      $detalle_cliente = Sale::join('clientes_mostradors as c','c.id','sales.cliente_id')
      ->select('c.id','c.nombre','c.telefono','c.email','c.dni','c.direccion','c.barrio','c.localidad','c.provincia','sales.observaciones','sales.metodo_pago','sales.fecha_entrega')
      ->where('sales.id', $Id)
      ->get();

      $total_total = Sale::join('metodo_pagos as m','m.id','sales.metodo_pago')
         ->select('sales.total','sales.created_at as fecha','sales.observaciones', 'm.nombre as metodo_pago')
         ->where('sales.id', $Id)
         ->get();

        $fecha = Sale::select('sales.created_at')
         ->where('sales.id', $Id)
         ->get();


         $usuario = User::join('sales as s','s.comercio_id','users.id')
          ->select('users.image','users.name')
         ->where('s.id', $Id)
         ->get();

  $pdf_factura = PDF::loadView('pdf.reporte-factura', compact('detalle_venta','Id','detalle_cliente','total_total','fecha','usuario'));


      return $pdf_factura->stream('Factura.pdf'); // visualizar

  }

   public function emailPDFFactura($Id, $email)
  {
      $detalle_venta = [];
      $detalle_cliente = [];
      $total_total = [];
      $fecha = [];
      $usuario = [];



      $detalle_venta = SaleDetail::join('products as p','p.id','sale_details.product_id')
      ->select('sale_details.id','sale_details.comentario','sale_details.price','sale_details.quantity','p.name as product', SaleDetail::raw('sale_details.price*sale_details.quantity as total'))
      ->where('sale_details.sale_id', $Id)
      ->where('sale_details.eliminado', 0)
      ->get();


      $detalle_cliente = Sale::join('clientes_mostradors as c','c.id','sales.cliente_id')
      ->select('c.id','c.nombre','c.telefono','c.email','c.dni','c.direccion','c.barrio','c.localidad','c.provincia','sales.observaciones','sales.metodo_pago','sales.fecha_entrega')
      ->where('sales.id', $Id)
      ->get();

      $total_total = Sale::join('metodo_pagos as m','m.id','sales.metodo_pago')
         ->select('sales.total','sales.created_at as fecha','sales.observaciones', 'm.nombre as metodo_pago')
         ->where('sales.id', $Id)
         ->get();

          $usuario = User::join('sales as s','s.comercio_id','users.id')
          ->select('users.image','users.name','users.email')
         ->where('s.id', $Id)
         ->get();



        $data["email"] = $email;
        $data["title"] = "Factura";
        $data["body"] = "A continuacion se adjunta el detalle de venta.";


        $fecha = Sale::select('sales.created_at')
         ->where('sales.id', $Id)
         ->get();

  $pdf = PDF::loadView('pdf.reporte-factura', compact('detalle_venta','Id','detalle_cliente','total_total','fecha','usuario'));


   Mail::send('mail', $data, function ($message) use ($data, $pdf) {
            $message->to($data["email"], $data["email"])
                ->subject($data["title"])
                ->attachData($pdf->output(), "Factura.pdf");
        });

        return redirect('reports')->with('status', 'Mail enviado correctamente.');



  }


    public function reporteExcel($usuarioSeleccionado, $clienteId,$estado_pago,$estado, $metodo_pago, $uid , $dateFrom =null, $dateTo =null)
    {
        $reportName = 'Reporte de Ventas_' . $uid . '.xlsx';

        $exportData = new SalesExport($usuarioSeleccionado,$clienteId, $estado_pago,$estado,$metodo_pago, $dateFrom, $dateTo);

        return Excel::download($exportData, $reportName);
    }

    public function reporteExcelDetalle($usuarioSeleccionado, $ClienteSeleccionado,$metodopagoSeleccionado, $productoSeleccionado, $categoriaSeleccionado, $almacenSeleccionado, $uid , $dateFrom=null, $dateTo=null)
    {
        $reportName = 'Reporte de Ventas por Producto_' . $uid . '.xlsx';

        $exportData = new SalesDetailExport($usuarioSeleccionado, $ClienteSeleccionado,$metodopagoSeleccionado, $productoSeleccionado, $categoriaSeleccionado, $almacenSeleccionado, $dateFrom, $dateTo);

        return Excel::download($exportData, $reportName);

    }

    public function reporteExcelProduccion($estadoSeleccionado, $ClienteSeleccionado,$metodopagoSeleccionado, $productoSeleccionado, $categoriaSeleccionado, $almacenSeleccionado, $dateFrom=null, $dateTo=null)
    {
        $reportName = 'Reporte de Produccion_' . uniqid() . '.xlsx';
        return Excel::download(new ProduccionExport($estadoSeleccionado, $ClienteSeleccionado,$metodopagoSeleccionado, $productoSeleccionado, $categoriaSeleccionado, $almacenSeleccionado, $dateFrom, $dateTo), $reportName);

    }


    public function reporteExcelAsistente($id_proveedor, $reportType, $buscar)
    {
        $reportName = 'Compras_' . uniqid() . '.xlsx';
        return Excel::download(new AsistenteExport($id_proveedor, $reportType, $buscar), $reportName);

    }



    public function reporteExcelHojaRuta($id_hoja_ruta, $uid)
{
    $reportName = 'Hoja de ruta' . $uid . '.xlsx';
    $exportData = new HojaRutaExport($id_hoja_ruta);
    return Excel::download($exportData, $reportName);


}

public function reporteExcelCaja($cajaid, $uid)
{
$reportName = 'Caja' . $uid . '.xlsx';
$exportData = new CajaExport($cajaid);
return Excel::download($exportData, $reportName);


}

    public function reporteExcelProducto(){
     return Excel::download(new ProductsExport, 'Productos.xlsx');
 }



 public function reporteExcelCategorias(){
  return Excel::download(new CategoryExport, 'Categorias.xlsx');
}


}
