<?php

namespace App\Http\Livewire;

use App\Models\Sale;
use App\Models\pagos_facturas;
use App\Models\hoja_ruta;
use App\Models\SaleDetail;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;

use Livewire\Component;

class FacturaController extends Component
{

  public $componentName, $pageTitle, $selected_id, $ventaId, $factura_id, $suma_monto, $suma_cash, $data, $nuevo_pago, $IdVenta, $monto_input, $monto_final, $data_cash, $data_monto, $data_total, $sumas, $id_venta, $estado, $venta_Id;


  public function render($ventaId)
     {

       if(Auth::user()->comercio_id != 1)
       $comercio_id = Auth::user()->comercio_id;
       else
       $comercio_id = Auth::user()->id;

       $this->data_monto = Sale::leftjoin('pagos_facturas as p','p.id_factura','sales.id')
       ->select('sales.cash','sales.created_at as fecha_factura','p.monto as monto','p.created_at as fecha_pago')
       ->where('sales.id', $ventaId)
       ->where('p.eliminado',0)
       ->get();


       $this->data_cash = Sale::select('sales.cash','sales.created_at as fecha_factura')
       ->where('sales.id', $ventaId)
       ->get();

       $this->data_total = Sale::select('sales.total')
       ->where('sales.id', $ventaId)
       ->get();

       $this->hr = hoja_ruta::join('sales','sales.hoja_ruta','hoja_rutas.id')->select('hoja_rutas.id')->where('sales.id', $ventaId)->first();


       return view('livewire.factura.component', [
         'ventaId' => $ventaId,
         'hojar' => $this->hr,
         'suma_monto' => $this->data_monto->sum('monto'),
         'suma_cash' => $this->data_cash->sum('cash'),
         'tot' => $this->data_total->sum('total'),
         'detalle_venta' => SaleDetail::join('products as p','p.id','sale_details.product_id')
         ->select('sale_details.id','sale_details.comentario','sale_details.price','sale_details.quantity','p.name as product', SaleDetail::raw('sale_details.price*sale_details.quantity as total'))
         ->where('sale_details.sale_id', $ventaId)
         ->where('sale_details.eliminado', 0)
         ->get(),
         'total_total' => Sale::join('metodo_pagos as m','m.id','sales.metodo_pago')
         ->select('sales.total','sales.created_at as fecha','sales.observaciones','sales.nota_interna', 'm.nombre as metodo_pago')
         ->where('sales.id', $ventaId)
         ->get(),
         'usuario' => User::select('users.image','users.name')
         ->where('users.id', $comercio_id)
         ->get(),
         'fecha' => Sale::select('sales.created_at')
         ->where('sales.id', $ventaId)
         ->get(),
         'detalle_cliente' => Sale::join('clientes_mostradors as c','c.id','sales.cliente_id')
         ->select('c.id','c.nombre','c.telefono','c.email','c.dni','c.direccion','c.barrio','c.localidad','c.provincia','sales.observaciones','sales.metodo_pago','sales.fecha_entrega')
         ->where('sales.id', $ventaId)
         ->get(),
         'mail' => Sale::join('clientes_mostradors as c','c.id','sales.cliente_id')
          ->select('c.email', 'sales.cash','sales.status')
          ->where('sales.id', $ventaId)
          ->get(),
          'pagos1' => Sale::select('sales.cash','sales.created_at as fecha_factura')
          ->where('sales.id', $ventaId)
          ->get(),
          'pagos2' => Sale::join('pagos_facturas as p','p.id_factura','sales.id')
          ->select('sales.cash','sales.created_at as fecha_factura','p.id','p.monto','p.created_at as fecha_pago')
          ->where('sales.id', $ventaId)
          ->where('p.eliminado',0)
          ->get(),
          'listado_hojas_ruta' => hoja_ruta::where('hoja_rutas.comercio_id', $comercio_id)->where('hoja_rutas.fecha', '>', Carbon::now())->orderBy('hoja_rutas.fecha','desc')->get(),
          'hoja_ruta' => hoja_ruta::join('sales','sales.hoja_ruta','hoja_rutas.id')->select('hoja_rutas.*')->where('sales.id', $ventaId)->get(),

       ]);

                //
     }



     public function GuardarPagoModal(Request $request)
    {


      $data = $request->all();

      $monto = $data['monto'];
      $id_factura = $data['id_factura'];
      $id = $data['id'];
      $tipo = $data['tipo'];
      $eliminado = $data['eliminado'];

      //insert using array at once
      $rows = [];
      foreach($id as $key => $input) {

        if($eliminado[$key] != 0) {

          $pagos = pagos_facturas::find($id[$key]);

          $pagos->update([
            'monto' => 0,
            'eliminado' => isset($eliminado[$key]) ? $eliminado[$key] : '',
            'id_factura' => $id_factura,
          ]);


        }


        if($tipo[$key] != 0 && $eliminado[$key] != 1) {

        array_push($rows, [
          'monto' => isset($monto[$key]) ? $monto[$key] : '', //add a default value here
          'backup_monto' => isset($monto[$key]) ? $monto[$key] : '', //add a default value here
          'id_factura' => $id_factura,
        ]);


      pagos_facturas::insert($rows);


    } if($tipo[$key] != 1 && $eliminado[$key] != 1) {


      $pagos = pagos_facturas::find($id[$key]);

  		$pagos->update([
        'monto' => isset($monto[$key]) ? $monto[$key] : '', //add a default value here
        'backup_monto' => isset($monto[$key]) ? $monto[$key] : '', //add a default value here
        'id_factura' => $id_factura,
  		]);


    }

    }

return redirect('factura/'.$id_factura)->with('status', 'Cambio en el pago guardado correctamente.');


  }

  public function GuardarHojaDeRuta(Request $request)
  {


    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $ultimo = hoja_ruta::where('hoja_rutas.comercio_id', 'like', $comercio_id)->select('hoja_rutas.id')->latest('nro_hoja')->first();

    $hoja = $ultimo->id + 1;


    $product = hoja_ruta::create([
      'nro_hoja' => $hoja,
      'fecha' => Carbon::parse($request->fecha)->format('Y-m-d'),
      'nombre' => $request->nombre,
      'tipo' => $request->tipo,
      'observaciones' => $request->observaciones_hr,
      'turno' => $request->turno,
      'comercio_id' => $comercio_id
    ]);

    $Hruta = Sale::find($request->id_factura);

    $Hruta->update([
      'hoja_ruta' => $hoja
    ]);

     return redirect('factura/'.$request->id_factura)->with('status', 'Hoja de ruta agregada y pedido asignado correctamente.');




  }





    public function GuardarPago(Request $request)
   {
       $post = new pagos_facturas;
       $post->id_factura = $request->id_factura;
       $post->monto = $request->monto;
       $post->save();

       $this->data_monto = Sale::leftjoin('pagos_facturas as p','p.id_factura','sales.id')
       ->select('sales.cash','sales.created_at as fecha_factura','p.monto as monto','p.created_at as fecha_pago')
       ->where('sales.id', $request->id_factura)
       ->get();

       $this->data_cash = Sale::select('sales.cash','sales.created_at as fecha_factura')
       ->where('sales.id', $request->id_factura)
       ->get();

       $this->data_total = Sale::select('sales.total')
       ->where('sales.id', $request->id_factura)
       ->get();

       $suma_monto = $this->data_monto->sum('monto');
       $suma_cash = $this->data_cash->sum('cash');
       $tot = $this->data_total->sum('total');

       $sumas = $suma_monto + $suma_cash;


       if($sumas >= $tot) {

         $metodo = Sale::find($request->id_factura);


         $metodo->update([
           'estado_pago' => 'Pago'
         ]);
         $metodo->save();
       }

       return redirect('factura/'.$request->id_factura)->with('status', 'Pago guardado correctamente.');
   }

    public function GuardarCambioEstado($estado, $venta_Id)
   {


       $this->data_monto = Sale::leftjoin('pagos_facturas as p','p.id_factura','sales.id')
       ->select('sales.cash','sales.created_at as fecha_factura','p.monto as monto','p.created_at as fecha_pago')
       ->where('sales.id', $venta_Id)
       ->get();

       $this->data_cash = Sale::select('sales.cash','sales.created_at as fecha_factura')
       ->where('sales.id', $venta_Id)
       ->get();

       $this->data_total = Sale::select('sales.total')
       ->where('sales.id', $venta_Id)
       ->get();

       $suma_monto = $this->data_monto->sum('monto');
       $suma_cash = $this->data_cash->sum('cash');
       $tot = $this->data_total->sum('total');

       $sumas = $suma_monto + $suma_cash;


         $metodo = Sale::find($venta_Id);


         $metodo->update([
           'status' => $estado
         ]);
         $metodo->save();



         if($estado == "Cancelado")
         {
           $items = SaleDetail::where('sale_details.sale_id',$venta_Id)->get();

             foreach ($items as  $item) {
               //update stock
               $product = Product::find($item->product_id);
               $product->stock = $product->stock + $item->quantity;
               $product->save();
             }


         }

       return redirect('factura/'.$venta_Id)->with('status', 'Cambio de estado guardado correctamente.');
   }

   public function HojaRutaElegida($HojaRutaElegida, $ventaId)
   {

     if($HojaRutaElegida != 0) {
       $Hruta = Sale::find($ventaId);

       $Hruta->update([
         'hoja_ruta' => $HojaRutaElegida
       ]);

        return redirect('factura/'.$ventaId)->with('status', 'Pedido agregado a hoja de ruta correctamente.');

     } else {
       $Hruta = Sale::find($ventaId);

       $Hruta->update([
         'hoja_ruta' => null
       ]);


       return redirect('factura/'.$ventaId)->with('status', 'Pedido agregado a hoja de ruta correctamente.');


     }


   }


}
