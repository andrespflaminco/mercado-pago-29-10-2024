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
padding:3px;
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

            @foreach($user as $u)
            @if($u->image != null)
            <img  width="100" class="rounded" src="{{ asset('storage/users/'.$u->image) }}" alt="Logotipo">
            @endif
            @endforeach

             <div style="font-size:10px; margin-top:35px;">
            
            @foreach($datos_facturacion as $df)
            @if($df != null)


            @if($df->razon_social != null)

             <h5 style="text-align:center;">{{$df->razon_social}}</h5>

            @else

            <h5 style="text-align:center;"> DETALLE DE VENTAS </h5>

            @endif


             @if($df->cuit != null)

             CUIT: {{$df->cuit}} <br>


             @endif

              @if($df->condicion_iva != null)

             COND. IVA: {{$df->condicion_iva}}  <br>

             @endif


              @if($df->fecha_inicio_actividades != null)

             INICIO ACT: {{\Carbon\Carbon::parse($df->fecha_inicio_actividades)->format('d/m/Y')}}  <br>

             @endif

             @if($df->iibb != null)

             IIBB: {{$df->iibb}}  <br>

             @endif

             @if($df->domicilio_fiscal != null)

             DIRECCION: {{$df->domicilio_fiscal}}

             @endif

             @else

              <h5 style="text-align:center;"> DETALLE DE VENTAS </h5>

             @endif

            @endforeach
             <br><br>
             </div>


            <div style="border-top: solid 1px;">
             <br>
              {{\Carbon\Carbon::now()->format('d-m-Y H:i')}} hs.
            
             <table style=border:none;>
            <tr style=border:none;>
                <td style=border:none;>
                
                @if($sale->tipo_comprobante != null && $sale->tipo_comprobante != "CF" )

                Ticket {{$sale->tipo_comprobante}}

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
            
            @foreach($cliente as $c)
            
             <div style="border-top: solid 1px; ">
                <br>
             @if($c->nombre != null)

             {{$c->nombre}} <br>


             @endif

             @if($c->dni == null)

             DNI: 00000000

             @else

             CUIT: {{$c->dni}}

             @endif
             <br><br>
             </div>
            @endforeach
            
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
                        @if($sale->tipo_comprobante == "B")
                        {{ number_format(($item->price) * (1+$item->iva), 0,",",".") }}
                        @elseif($sale->tipo_comprobante == "C" || $sale->tipo_comprobante == "CF" || $sale->tipo_comprobante == "")
                        {{ number_format($item->price, 2,",",".") }}
                        @elseif($sale->tipo_comprobante == "A")
                        {{ number_format($item->price, 2,",",".") }}
                        @endif
                    </td>
                    <td class="precio">
                        $
                        @php
                            // Calculamos el precio base por cantidad
                            $totalPrice = $item->price * $item->quantity;
                        
                            // Calculamos el IVA si aplica
                            $totalWithIva = $totalPrice * (1 + $item->iva);
                        @endphp
                        
                        @if($sale->tipo_comprobante == "B")
                            {{ number_format($item->price < 1 ? round($totalWithIva, 0) : $totalWithIva, 0, ",", ".") }}
                        @elseif($sale->tipo_comprobante == "C" || $sale->tipo_comprobante == "CF" || $sale->tipo_comprobante == "")
                            {{ number_format($totalPrice, 0, ",", ".") }}
                        @elseif($sale->tipo_comprobante == "A")
                            {{ number_format($totalPrice, 2, ",", ".") }}
                        @endif

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
        

              
        <br>
        
        @if($venta->observaciones != null)
        <p>Observacion: {{$venta->observaciones}}</p>
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
              
              
              <p class="centrado"> ¡GRACIAS POR SU COMPRA! </p>

              <div style="margin-bottom:50px;">.</div>



    </body>
</html>
