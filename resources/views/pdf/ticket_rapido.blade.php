
<head>
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
  font-size: 11px;
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
    </head>
    <body style="margin:0;">
        
            <div class="ticket">
        
             <p class="centrado">  {{$user->name}} </p>
             
             <div style="font-size:10px; margin-top:30px;">
                 
             @if($datos_facturacion->cuit != null)
             
             CUIT: {{$datos_facturacion->cuit}} <br>

             
             @endif

            @if($datos_facturacion->condicion_iva != null)
             
             @if($datos_facturacion->condicion_iva == "IVA Responsable inscripto")
             
             COND. IVA: Resp. Insc <br>
             
             @endif
             
             @if($datos_facturacion->condicion_iva != "IVA Responsable inscripto")
             
             COND. IVA: {{$datos_facturacion->condicion_iva}}  <br>
             
             @endif
             
              @endif
              
            @if($datos_facturacion->pto_venta !=  null)
             
             ESTABLECIMIENTO: {{$datos_facturacion->pto_venta}} <br>
             
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
             
             {{$datos_facturacion->razon_social}}
             
             <br><br>     
             </div>
            
            
            <div style="border-top: solid 1px;">
             <br>
              {{\Carbon\Carbon::now()->format('d-m-Y H:i')}} hs.
             <table style=border:none;>
            <tr style=border:none;>
                <td style=border:none;>
                    
                @if($sale->tipo_comprobante != null && $sale->tipo_comprobante != "CF"  && $sale->nro_factura != null )
                 
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
             
             
             
             <div style="border-top: solid 1px; ">
                <br>
             @if($cliente->nombre != null)
             
             {{$cliente->nombre}} <br>
             
             
             @endif
             
             @if($cliente->dni == null || $cliente->dni == 0 )
             
             DNI: 00000000
             
             @else
             
             CUIT: {{$cliente->dni}}
             
             @endif
             <br><br>
             </div>
             
            <table style="font-size:10px;">
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
                        ( {{$item->quantity}} x 
                        
                        $ 
                        
                        @if($sale->tipo_comprobante == "B" || $sale->tipo_comprobante == "CF" || $sale->tipo_comprobante == "" )
                        
                        
                        {{   number_format( $item->price + $item->iva ,0)  }}
                        
                        @endif
                        
                         @if($sale->tipo_comprobante == "A" || $sale->tipo_comprobante == "C")
                        
                        {{   number_format($item->price , 2)  }}
                        
                        @endif
                        
                        )
                        </td>
                        <td class="precio">
                        
                        $ 
                        
                        @if($sale->tipo_comprobante == "B" || $sale->tipo_comprobante == "CF" || $sale->tipo_comprobante == "")
                        
                        
                        {{   number_format( $item->price + $item->iva , 0 ) }}
                        
                        @endif
                        
                         @if($sale->tipo_comprobante == "A"  || $sale->tipo_comprobante == "C" )
                        
                        {{   number_format($item->price * $item->quantity , 2)  }}
                        
                        @endif
                        
                        
                        </td>
                    </tr>
                    @endforeach
                  
                     @if($sale->tipo_comprobante == "B" || $sale->tipo_comprobante == "CF" || $sale->tipo_comprobante == "" || $sale->tipo_comprobante == "C")
                        
                        
                          <tr>
                        <td class="producto">TOTAL</td>
                        <td class="precio">$ {{$sale->subtotal + $sale->iva}} </td>
                        
                    </tr>
                        
                        @endif
                        
                         @if($sale->tipo_comprobante == "A"   )
                        
                         <tr>
                        <td class="producto">SUBTOTAL</td>
                        <td class="precio">$ {{$sale->subtotal}}</td>
                    </tr>
                    <tr>
                        <td class="producto">IVA</td>
                        <td class="precio">$ {{$sale->iva}}</td>
                    </tr>
                    <tr>
                        <td class="producto">TOTAL</td>
                        <td class="precio">$ {{$sale->subtotal + $sale->iva}} </td>
                        
                    </tr>
                        
                        @endif
                        
                        
                   
                    
                </tbody>
            </table>
         
         @if($codigo_qr != "0")
         
            <div style="text-align:center; align-content: center; margin-top:35px; width:145px;">
                <table style="border:none;">
                    <tr style="border:none;">
                    <td style="width:2px; border:none;"></td>
                    <td style="border:none;">
                    <div class="title m-b-md">
                       <img src="data:image/svg+xml;base64,{{ base64_encode($codigoQR) }}">
                    </div>
                    </td>
                    <td  style="width:2px; border:none;"></td>
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
        <br><br>
              <p class="centrado"> ¡GRACIAS POR SU COMPRA! </p>
              
              <div style="margin-bottom:50px;">.</div>
              
              
        
    </body>
