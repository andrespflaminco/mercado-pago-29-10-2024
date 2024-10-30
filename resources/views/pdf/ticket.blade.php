<html>

@php
    $size = $size ?? 58; 
@endphp

@if($size == 58)
<style media="screen">

  @page {
margin-left: 0.5cm; 
margin-right: 0;
}

* {
font-size: 12px;
font-family: 'Times New Roman';
}

body {

left: 0;
margin-left: 0;
margin-left:0;
}

td,
th,
tr,
table {
border-top: 1px solid black;
border-collapse: collapse;
font-size: 9.5px;
padding:2px;
font-family: 'Times New Roman';
}



td.producto,
th.producto {
width: 90px;
max-width: 100px;
}

td.cantidad,
th.cantidad {
width: 30px;
max-width: 30px;
word-break: break-all;
}

td.precio,
th.precio {
width: 40px;
max-width: 50px;
word-break: break-all;
}

.centrado {
text-align: center;
align-content: center;
}

.ticket {
left: 0;
margin-left: 0;
width: 140px;
max-width: 140px;
margin-top:35px;
}

img {
max-width: inherit;
width: inherit;
}
</style>
@else
<style media="screen">
  @page {
    margin-left: 0.5cm;
    margin-right: 0;
  }

  * {
    font-size: 12px;
    font-family: 'Times New Roman';
  }

  body {
    left: 0;
    margin-left: 0;
    margin-right: 0;
  }

  td,
  th,
  tr,
  table {
    border-top: 1px solid black;
    border-collapse: collapse;
    font-size: 9.5px;
    padding: 2px;
    font-family: 'Times New Roman';
  }

  td.producto,
  th.producto {
    width: 120px;
    max-width: 150px;
  }

  td.cantidad,
  th.cantidad {
    width: 30px;
    max-width: 30px;
    word-break: break-all;
  }

  td.precio,
  th.precio {
    width: 60px;
    max-width: 70px;
    word-break: break-all;
  }

  .centrado {
    text-align: center;
    align-content: center;
  }

  .ticket {
    left: 0;
    margin-left: 0;
    width: 200px;
    max-width: 200px;
    margin-top: 35px;
  }

  img {
    max-width: inherit;
    width: inherit;
  }
</style>
@endif

        <link rel="stylesheet" href="style.css">
        <script src="script.js"></script>
    </head>
    <body>
        <div class="ticket">
            
            @if($user)
            @if($user->image != null)
            <img  width="100" class="rounded" src="{{ asset('storage/users/'.$user->image) }}" alt="Logotipo">
            @endif
            @endif

             <div style="font-size:10px; margin-top:35px;">

            @if($datos_facturacion != null)


            @if($datos_facturacion->razon_social != null)

             <h5 style="text-align:center;">{{$datos_facturacion->razon_social}}</h5>

            @else

            <h5 style="text-align:center;"> DETALLE DE VENTAS </h5>

            @endif


             @if($datos_facturacion->cuit != null)

             CUIT: {{$datos_facturacion->cuit}} <br>


             @endif

              @if($datos_facturacion->condicion_iva != null)

             COND. IVA: {{$datos_facturacion->condicion_iva}}  <br>

             @endif


              @if($datos_facturacion->fecha_inicio_actividades != null)

             INICIO ACT: {{\Carbon\Carbon::parse($datos_facturacion->fecha_inicio_actividades)->format('d/m/Y')}}  <br>

             @endif

             @if($datos_facturacion->iibb != null)

             IIBB: {{$datos_facturacion->iibb}}  <br>

             @endif

             @if($datos_facturacion->domicilio_fiscal != null)

             DIRECCION: {{$datos_facturacion->domicilio_fiscal}}

             @endif

             @else

            <h5 style="text-align:center;"> DETALLE DE VENTAS </h5>
            
             @if($user)
            <h5 style="text-align:center;"> {{$user->name}} </h5>
            
            @if($user->phone != null) {{$user->phone}}  @endif <br>
            @if($user->phone != null) {{$user->email}}  @endif <br>
            
             @endif
             @endif

             <br><br>
             </div>


            <div style="border-top: solid 1px;">
             <br>
              {{\Carbon\Carbon::now()->format('d-m-Y H:i')}} hs.
             <table style=border:none;>
            <tr style=border:none;>
                <td style=border:none;>

                @if($sale->tipo_comprobante != null && $sale->tipo_comprobante != "CF" )

                <!---- Ticket {{$sale->tipo_comprobante}} --->
                
                Detalle de venta

                @endif


                </td>
                <td style=border:none;>
                     @if($sale->nro_factura)

                     <?php
                     $porciones = explode("-", $sale->nro_factura);
                     $tipo_factura = $porciones[0]; // porción1
                      $pto_venta = $porciones[1]; // porción2
                      $nro_factura_ = $porciones[2]; // porción2
                      echo "Nro. ".str_pad($pto_venta, 3, "0", STR_PAD_LEFT)."-".str_pad($nro_factura_, 5, "0", STR_PAD_LEFT); ?>
                      @else

                      @endif


                </td>
            </tr>
             </table>

             <br><br>
             </div>



             <div style="border-top: solid 1px; ">
                <br>
             @if($cliente->nombre != null)

             {{$cliente->nombre}} <br>


             @endif

             @if($cliente->dni == null)

             DNI: 00000000

             @else

             CUIT: {{$cliente->dni}}

             @endif
             <br><br>
             </div>

            <table style="font-size:9px; with: 100%;">
            <thead>
                <tr>
                    <th class="producto">PRODUCTO</th>
                    <th class="precio">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                
                <tr>
                
                    <td class="producto">
                        {{$item->product_name}} <br>
                        {{$item->quantity}} x $
                        {{ number_format($item->precio_original, 2,",",".") }}
                        
                    </td>
                    <td class="precio">
                        $
                        {{ number_format(($item->precio_original * $item->quantity), 2,",",".") }}
                        
                    </td>
                </tr>

                @if(0 < $item->cantidad_promo)
                <tr style="border-top: none !important;">
                    <td class="producto" style="border-top: none !important; padding: 5px;">
                        - {{$item->nombre_promo}} <br> {{$item->cantidad_promo}} x $ {{$item->descuento_promo}}
                    </td>
                    <td class="precio" style="border-top: none !important; padding: 5px;">
                        - $ {{ number_format($item->cantidad_promo * $item->descuento_promo, 0,",",".") }}
                    </td>
                </tr>
                @endif

                @endforeach

                @if($sale->tipo_comprobante == "B")
                <tr>
                    <td class="producto">SUBTOTAL</td>
                    <td class="precio">$ {{ number_format(($sale->subtotal) * ((($sale->total/($sale->subtotal+$sale->recargo-$sale->descuento)))), 2 , ",",".") }}</td>
                </tr>
                <tr>
                    <td class="producto">- DESCUENTO ACUMULADOS EN PROMOCIONES</td>
                    <td class="precio">$ {{ number_format($sale->descuento_promo, 2 , ",",".") }}</td>
                </tr>
                <tr>
                    <td class="producto">- DESCUENTO</td>
                    <td class="precio">$ {{ number_format($sale->descuento * ((($sale->total/($sale->subtotal+$sale->recargo-$sale->descuento)))), 2 , ",",".") }}</td>
                </tr>
                <tr>
                    <td class="producto">+ RECARGO</td>
                    <td class="precio">$ {{ number_format($sale->recargo * ((($sale->total/($sale->subtotal+$sale->recargo-$sale->descuento)))), 2 , ",",".") }}</td>
                </tr>
                <tr>
                    <td class="producto">TOTAL</td>
                    <td class="precio">$ {{ number_format($sale->subtotal + $sale->recargo - $sale->descuento + $sale->iva - $sale->descuento_promo, 2 , ",",".")  }}</td>
                </tr>
                @endif

                @if($sale->tipo_comprobante == "C" || $sale->tipo_comprobante == "")
                <tr>
                    <td class="producto">SUBTOTAL</td>
                    <td class="precio">$ {{ number_format($sale->subtotal, 2 , ",",".") }}</td>
                </tr>
                <tr>
                    <td class="producto">- DESCUENTO ACUMULADOS EN PROMOCIONES</td>
                    <td class="precio">$ {{ number_format($sale->descuento_promo, 2 , ",",".") }}</td>
                </tr>
                <tr>
                    <td class="producto">- DESCUENTO</td>
                    <td class="precio">$ {{ number_format($sale->descuento, 2 , ",",".") }}</td>
                </tr>
                <tr>
                    <td class="producto">+ RECARGO</td>
                    <td class="precio">$ {{ number_format($sale->recargo, 2 , ",",".") }}</td>
                </tr>
                <tr>
                    <td class="producto">TOTAL</td>
                    <td class="precio">$ {{ number_format($sale->subtotal + $sale->recargo - $sale->descuento - $sale->descuento_promo, 2 , ",",".") }}</td>
                </tr>
                @endif

                @if($sale->tipo_comprobante == "CF")
                <tr>
                    <td class="producto">SUBTOTAL</td>
                    <td class="precio">$ {{number_format($sale->subtotal, 2, ",",".")}}</td>
                </tr>
                <tr>
                    <td class="producto">- DESCUENTO ACUMULADOS EN PROMOCIONES</td>
                    <td class="precio">$ {{ number_format($sale->descuento_promo, 2 , ",",".") }}</td>
                </tr>
                <tr>
                    <td class="producto">- DESCUENTO</td>
                    <td class="precio">$ {{number_format($sale->descuento, 2, ",",".")}}</td>
                </tr>
                <tr>
                    <td class="producto">+ RECARGO</td>
                    <td class="precio">$ {{number_format($sale->recargo, 2, ",",".")}}</td>
                </tr>
                <tr>
                    <td class="producto">TOTAL</td>
                    <td class="precio">$ {{number_format($sale->subtotal + $sale->recargo - $sale->descuento - $sale->descuento_promo, 2, ",",".")}}</td>
                </tr>
                @endif

                @if($sale->tipo_comprobante == "A")
                <tr>
                    <td class="producto">SUBTOTAL</td>
                    <td class="precio">$ {{number_format($sale->subtotal, 2, ",",".")}}</td>
                </tr>
                <tr>
                    <td class="producto">- DESCUENTO ACUMULADOS EN PROMOCIONES</td>
                    <td class="precio">$ {{ number_format($sale->descuento_promo, 2 , ",",".") }}</td>
                </tr>
                <tr>
                    <td class="producto">DESCUENTO</td>
                    <td class="precio">$ {{number_format($sale->descuento, 2, ",",".")}}</td>
                </tr>
                <tr>
                    <td class="producto">RECARGO</td>
                    <td class="precio">$ {{number_format($sale->recargo, 2, ",",".")}}</td>
                </tr>
                <tr>
                    <td class="producto">IVA</td>
                    <td class="precio">$ {{number_format($sale->iva, 2, ",",".")}}</td>
                </tr>
                <tr>
                    <td class="producto">TOTAL</td>
                    <td class="precio">$ {{number_format($sale->subtotal + $sale->recargo - $sale->descuento + $sale->iva - $sale->descuento_promo, 2, ",",".")}}</td>
                </tr>
                @endif
            </tbody>
        </table>

         @if($codigo_barra_afip != 0)

            <div style="text-align:center; align-content: center; margin-top:35px; width:155px;">
                <table style="border:none;">
                    <tr style="border:none;">
                    <td style="width:28px; border:none;"></td>
                    <td style="border:none;">
                    <div class="title m-b-md">
                       <img src="data:image/svg+xml;base64,{{ base64_encode($codigoQR) }}">
                    </div>
                    </td>
                    <td  style="width:28px; border:none;"></td>
                    </tr>
                </table>


                 <br>
                 CAE: {{$sale->cae}}
                 <br>
                 Vencimiento: {{\Carbon\Carbon::parse($sale->vto_cae)->format('d/m/Y')}}
                 <br>
                  <br>
                   <br>



              <br>
              </div>

        @endif
       
              
              @if($sale->observaciones != null)
              <p>Observacion: {{$sale->observaciones}}</p>
              @endif
              
              <br>

              @if($muestra_cta_cte != 0)
              
              @if($maximo_cta_cte != null)
              <p>Cupo cuenta corriente: $ {{number_format($maximo_cta_cte,2,",",".")}}</p>
              @endif
              
              @if($deuda != null)
              <p>Usado (deuda): $ {{number_format($deuda,2,",",".")}}</p>
              @endif

              @if($maximo_cta_cte != null)
              <p>Saldo cuenta corriente: $ {{number_format($maximo_cta_cte - $deuda,2,",",".")}}</p>
              @endif
              
              @endif
              
              <br><br>
               
              <p class="centrado"> ¡GRACIAS POR SU COMPRA! </p>

              <div style="margin-bottom:50px;">.</div>



    </body>
</html>
