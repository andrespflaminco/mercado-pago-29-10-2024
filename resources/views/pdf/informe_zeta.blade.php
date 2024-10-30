<html>
    <head>
      <style media="screen">
      * {
  font-size: 12px;
  font-family: 'Times New Roman';
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
  width: 100px;
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
  width: 50px;
  max-width: 50px;
  word-break: break-all;
}

.centrado {
  text-align: center;
  align-content: center;
}

.ticket {
  width: 155px;
  max-width: 155px;
}

img {
  max-width: inherit;
  width: inherit;
}
      </style>
        <link rel="stylesheet" href="style.css">
        <script src="script.js"></script>
    </head>
    <body>
        <div class="ticket">
       
             
             <div style="font-size:10px; margin-top:35px;">
            
                     
             
             <h5 style="text-align:center;">INFORME ZETA</h5>

             
                 
             @if($datos_facturacion->cuit != null)
             
             CUIT: {{$datos_facturacion->cuit}} <br>

             
             @endif
             
             GENERADO:  {{\Carbon\Carbon::now()->format('d/m/Y H:i')}} hs. <br>
             PERIODO:  {{\Carbon\Carbon::parse($dateFrom)->format('d/m/Y')}} a {{\Carbon\Carbon::parse($dateTo)->format('d/m/Y')}}

            @if($datos_facturacion->condicion_iva != null)
             
             @if($datos_facturacion->condicion_iva == "IVA Responsable inscripto")
             
             COND. IVA: Resp. Insc <br>
             
             @endif
             
             @if($datos_facturacion->condicion_iva != "IVA Responsable inscripto")
             
             COND. IVA: {{$datos_facturacion->condicion_iva}}  <br>
             
             @endif
             
              @endif
              
            @if($datos_facturacion->pto_venta !=  null)
             
             PTO VENTA: {{$datos_facturacion->pto_venta}} <br>
             
             @endif
       
             
             {{$datos_facturacion->razon_social}}
             
             <br><br>     
             </div>
      
             
             
             
             <div style="border-top: solid 1px; ">
                <br>
                TOTAL VENTAS: $ {{ number_format($sale->total + $cobro_rapido->total , 2) }} <BR>
                TOTAL NETO: $ {{ number_format($sale->subtotal + $cobro_rapido->subtotal , 2) }} <br>
                TOTAL IVA:  $ {{ number_format( $sale->iva + $cobro_rapido->iva , 2) }} <br>
                
                Ultima Factura A:
                
                @if($ultima_factura_A != null)
                
                     
                     <?php
                     $porciones = explode("-", $ultima_factura_A->nro_factura);
                     $tipo_factura = $porciones[0]; // porci贸n1
                     $pto_venta = $porciones[1]; // porci贸n2
                     $nro_factura_A = $porciones[2]; // porci贸n2
                     echo $nro_factura_A; ?>
                      
                
                @endif
                <br>
                Ultima Factura B: 

                @if($ultima_factura_B !=null)
                
                     
                     <?php
                     $porciones = explode("-", $ultima_factura_B->nro_factura);
                     $tipo_factura = $porciones[0]; // porci贸n1
                     $pto_venta = $porciones[1]; // porci贸n2
                     $nro_factura_B = $porciones[2]; // porci贸n2
                     echo $nro_factura_B; ?>
                      
                
                @endif
                
                <br>
             <br><br>
             </div>
             
              
              <div style="margin-bottom:50px;">.</div>
              
              
        
    </body>
</html>
