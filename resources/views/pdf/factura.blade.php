<!DOCTYPE html>
<html lang="es">
<head>
    
            <style>
         

            footer {
                position: fixed; 
                bottom: 5px; 
                left: 0px; 
                right: 0px;
                height: 50px; 

                }
                
                td {
                    font-size:10px !important;
                }
        </style>


	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">


		<script src="{{ asset('assets/js/loader.js') }}"></script>
		<link href="{{ asset('assets/css/loader.css') }}" rel="stylesheet" type="text/css" />


		<link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('assets/css/plugins.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('assets/css/structure.css') }}" rel="stylesheet" type="text/css" class="structure" />


		<link href="{{ asset('assets/css/elements/avatar.css') }}" rel="stylesheet" type="text/css" />

		<link href="{{ asset('plugins/sweetalerts/sweetalert.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('plugins/notification/snackbar/snackbar.min.css') }}" rel="stylesheet" type="text/css" />


		<link href="{{ asset('css/custom.css') }}" rel="stylesheet" type="text/css" />

		<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/widgets/modules-widgets.css') }}">
		<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/forms/theme-checkbox-radio.css') }}">

		 <link href="{{ asset('assets/css/apps/scrumboard.css') }}" rel="stylesheet" type="text/css" />
		 <link href="{{ asset('assets/css/apps/notes.css') }}" rel="stylesheet" type="text/css" />

		 <!--  BEGIN CUSTOM STYLE FILE  -->
		 <link href="{{ asset('assets/css/scrollspyNav.css') }} " rel="stylesheet" type="text/css" />
		 <link href="{{ asset('assets/css/components/tabs-accordian/custom-accordions.css') }} " rel="stylesheet" type="text/css" />
		 <!--  END CUSTOM STYLE FILE  -->
		 <!--  BEGIN CUSTOM STYLE FILE  -->
		 <link href="{{ asset('assets/css/scrollspyNav.css') }} " rel="stylesheet" type="text/css" />
		  <link href="{{ asset('plugins/select2/select2.min.css') }} " rel="stylesheet" type="text/css" />
		 <!--  END CUSTOM STYLE FILE  -->

		 <!--  BEGIN CUSTOM STYLE FILE  -->
		 <link href="{{ asset('assets/css/apps/invoice.css') }} " rel="stylesheet" type="text/css" />
		 <!--  END CUSTOM STYLE FILE  -->



</head>


<body style="background-color: white !important;">

													<div style="background-color: white !important; width:100%;">

															<div style="background-color: white !important;" class="doc-container">


																	<div style="background-color: white !important;" class="invoice-container">

																			<div style="background-color: white !important;" class="invoice-inbox">




																								<div style="background-color: white !important;" id="seleccion" class="invoice">
																										<div class="row">


																														<div class="table-responsive">
																																		<table class="table">
																																				<tbody class="">
																																						<tr style="border-bottom:none;">
																																						<td style="width: 40%;" class="text-left">
																																						<div class="company-info">
                                                                                                                                                             @foreach ($usuario as $u)

                                                                                                                                                             @if($u->image != null)
                                                                                                                                                             <img  width="100" class="rounded"
                                                                                                                                                             src="{{ asset('storage/users/'.$u->image) }}"
                                                                                                                                                             >
                                                                                                                                                             @else
                                                                                                                                                             <h5 class="inv-brand-name">{{$u->name}}</h5>
                                                                                                                                                             @endif

                                                                                                                                                            @endforeach

                                                                                                                                                           </div>
																																						</td>
																																						<td style="border:solid 1px #c8c8c8; padding: 0px 20px 0px 25px;">

																																								@foreach($total_total as $f)

                                                                                                                                                             <h1 >

                                                                                                                                                               <b >
                                                                                                                                                               @if($f->tipo_comprobante != null && $f->cae != null)
                                                                                                                                                               
                                                                                                                                                               @if($f->tipo_comprobante == "FB")
                                                                                                                                                               B
                                                                                                                                                               @elseif($f->tipo_comprobante == "FA")
                                                                                                                                                               A
                                                                                                                                                               @else
                                                                                                                                                               {{$f->tipo_comprobante}}
                                                                                                                                                               @endif
                                                                                                                                                               
                                                                                                                                                               @else
                                                                                                                                                               X
                                                                                                                                                               @endif
                                                                                                                                                              </b>
                                                                                                                                                             </h1>

                                                                                                                                                             @if($f->tipo_comprobante == 'FC' || $f->tipo_comprobante == 'C')
                                                                                                                                                             <p style="font-size: 11.5px;">
                                                                                                                                                               <b> COD. 011 </b>
                                                                                                                                                             </p>
                                                                                                                                                             @endif
                                                                                                                                                             @if($f->tipo_comprobante == 'FA' || $f->tipo_comprobante == 'A')
                                                                                                                                                             <p style="font-size: 11.5px;">
                                                                                                                                                               <b> COD. 01 </b>
                                                                                                                                                             </p>
                                                                                                                                                             @endif
                                                                                                                                                             @if($f->tipo_comprobante == 'FB' || $f->tipo_comprobante == 'B')
                                                                                                                                                             <p style="font-size: 11.5px;">
                                                                                                                                                               <b> COD. 06 </b>
                                                                                                                                                             </p>
                                                                                                                                                             @endif
                                                                                                                                                             
                                                                                                                                                             @if($f->tipo_comprobante == NULL)
                                                                                                                                                             <p style="font-size: 11.5px;">
                                                                                                                                                               <b> </b>
                                                                                                                                                             </p>
                                                                                                                                                             @endif
                                                                                                                                                             
                                                                                                                                                              @if($f->tipo_comprobante == NULL)
                                                                                                                                                             <p style="font-size: 11.5px;">
                                                                                                                                                               <b>  </b>
                                                                                                                                                             </p>
                                                                                                                                                             @endif
                                                                                                                                                             


                                                                                                                                                             

																																							</th>
																																							 @if($f->cae == NULL)
																																							<td class="text-right" style="width: 45%;">
																																							     <h5 class="in-heading">DETALLE DE VENTA</h5>
																																							</td>
																																							@else
																																							<td class="text-right" style="width: 45%;">
																																							     <h5 class="in-heading">FACTURA</h5>
																																							</td>
																																							@endif
																																							@endforeach

																																						</tr>

																																						<tr style="margin-top: 7%; border:none;">
																																						<td style="width: 45%; border:none;" class="text-left">
																																						<br>
																																						    @foreach($detalle_facturacion as $df)
                                                                                                                                                               <p class="inv-customer-name"> <h5> <b>{{$df->razon_social}}</b>
                                                                                                                                                               </h5> </p>
                                                                                                                                                               <p style="font-size: 11px;">Direccion: {{$df->domicilio_fiscal}} , {{$df->localidad}} ,{{$df->provincia}} </p>
                                                                                                                                                               <p style="font-size: 11px;">Telefono: {{$df->phone}}</p>
                                                                                                                                                               <p style="font-size: 11px;">Email: {{$df->email}}</p>
                                                                                                                                                               @endforeach

																																						</td>

																																						<td style=" padding: 0px 20px 0px 25px;">

																																						</td>
																																						<td class="text-right" style="width: 45%; border:none;">
																																							<br>
																																								<br>
																																						<table style="border:none;">
																																						    <tr style="border:none;">
																																						        <td class="text-left" style="width:55%;">
																																						           <p style="font-size:11px; margin-top:2px;"><b >NRO FACTURA:</b></p>
                                                                                                                                                                  <p style="font-size:11px; margin-top:2px;"><b style="font-size:11px;">CUIT:</b></p>
                                                                                                                                                                <p style="font-size:11px; margin-top:2px;"> <b style="font-size:11px;">INGRESOS BRUTOS:</b></p>
                                                                                                                                                                 <p style="font-size:11px; margin-top:2px;"> <b style="font-size:11px;">INICIO ACTIVIDADES:</b></p>
                                                                                                                                                                <p style="font-size:11px; margin-top:2px;"> <b style="font-size:11px;">IVA:</b></p>
																																						        </td>
																																						        <td class="text-right" style="width:45%;">
																																						            @foreach($total_total as $tf)
                                                                                                                                                                         @if($tf->nro_factura)
                                                                                                                                                                         <?php
                                                                                                                                                                         $porciones = explode("-", $tf->nro_factura);
                                                                                                                                                                         $tipo_factura = $porciones[0]; // porci��n1
                                                                                                                                                                         $pto_venta = $porciones[1]; // porci��n2
                                                                                                                                                                         $nro_factura_ = $porciones[2]; // porci��n2
                                                                                                                                                                         echo "<p style='font-size:11px;'>".str_pad($pto_venta, 5, "0", STR_PAD_LEFT)."-".str_pad($nro_factura_, 8, "0", STR_PAD_LEFT)."</p>"; ?>
                                                                                                                                                                         @else
                                                                                                                                                                         -
                                                                                                                                                                         @endif
                                                                                                                                                                         @endforeach

                                                                                                                                                                             @foreach($detalle_facturacion as $df)
                                                                                                                                                                           <p style="font-size:11px;">{{$df->cuit}}</p>
                                                                                                                                                                           <p style="font-size:11px;">{{$df->iibb}}</p>
                                                                                                                                                                           <p style="font-size:11px;">{{\Carbon\Carbon::parse($df->fecha_inicio_actividades)->format('d-m-Y')}}</p>
                                                                                                                                                                           <p style="font-size:11px;">{{$df->condicion_iva}}</p>
                                                                                                                                                                         @endforeach
																																						        </td>
																																						    </tr>
																																						</table>

																																						</td>

																																						</tr>
																																						<tr style="border:none;">
																																						<td style="width: 45%;" class="text-left">
																																					    @foreach($detalle_cliente as $c)
                                                                                                                                                         <p style="font-size:11px;" >Cliente: {{$c->nombre}}</p>
                                                                                                                                                         <p style="font-size:11px;">CUIT: {{$c->dni}}</p>
                                                                                                                                                         <p style="font-size:11px;">{{$c->direccion}} @if($c->localidad != null),{{$c->localidad}} @endif  @if($c->localidad != null) . {{$c->provincia}} @endif</p>
                                                                                                                                                         <p style="font-size:11px;"> @if($c->telefono != null) Tel: {{$c->telefono}} @endif</p>
                                                                                                                                                       @endforeach

																																						</td>

																																						<td style=" padding: 0px 20px 0px 20px;">

																																						</td>
																																						<td class="text-right" style="width: 45%; ">
																																						    <p style="font-size:11px;" >Numero de venta : # {{$venta->nro_venta}}</p>



                                                                                                                                                             <p cstyle="font-size:11px;">Fecha :
                                                                                                                                                               @foreach($total_total as $f)
                                                                                                                                                                {{\Carbon\Carbon::parse($f->created_at)->format('d-m-Y')}}</p>
                                                                                                                                                                @endforeach


																																						</td>

																																						</tr>
																																				</tbody>

																																		</table>
																																</div>



																												</div>


																												<div class="row inv--product-table-section">
																														<div style="width:100%;">
																																<div class="table-responsive">
																																		<table class="table">
																																				<thead class="">
																																						<tr>
																																								<th scope="col">Fila </th>
																																								<th scope="col">Producto</th>
																																								<th class="text-right" scope="col">Cantidad</th>
																																								<th class="text-right" scope="col">Precio</th>
																																								
																																								
																																								 @foreach($total_total as $f)
                                                                                    
                                                                                                                                                                   @if($f->recargo <= $f->descuento)
                                                                                                                                                                    <th class="text-right" scope="col">% Bonif</th>
                                                                                                                                                                    
                                                                                                                                                                    @else 
                                                                                                                                                                    
                                                                                                                                                                    <th class="text-right" scope="col">% Recargo</th>
                                                                                    
                                                                                                                                                                   @endif
                                                                                    
                                                                                    
                                                                                                                                                                   @endforeach
																																								
                                                                                                                                                                   @foreach($total_total as $f)
                                                                                    
                                                                                                                                                                   @if($f->tipo_comprobante == "A")
                                                                                                                                                                    <th class="text-right" scope="col">% IVA</th>
                                                                                    
                                                                                                                                                                   @endif
                                                                                    
                                                                                    
                                                                                                                                                                   @endforeach
                                                                                                                                                                 
																																								<th class="text-right" scope="col">Subtotal</th>
																																						</tr>
																																				</thead>
																																				<tbody>
																																					<?php $i = 1; ?>
																																					@foreach($detalle_venta as $item)
																																						<tr>
																																								<td><?php echo $i++; ?></td>
																																								<td>{{$item->product_name}}
																																								
																																								
																																								@foreach($total_total as $t)
			                                                                                                                                                    @if($t->tipo_comprobante == "A")
                                                                                                                    			                                
                                                                                                                    			                                @if(0 < $item->cantidad_promo != null)
                                                                                                                    			                                <br><text> - {{$item->nombre_promo}}  ({{$item->cantidad_promo}} x -${{ number_format($item->descuento_promo,1,",",".")  }}) </text>
                                                                                                                                                                @endif
                                                                                                                                                                
                                                                                                                                                                @else
                                                                                                                                                            
                                                                                                                                                            	@if(0 < $item->cantidad_promo != null)
                                                                                                                    						                    <br><text> - {{$item->nombre_promo}}  ({{$item->cantidad_promo}} x -${{ number_format($item->descuento_promo * (1 + $item->iva),1,",",".")  }}) </text>
                                                                                                                                                                @endif
    
                                                                                                                                                                
                                                                                                                                                                @endif
                                                                                                                                                                @endforeach
                                                                                                                                                                
																																								</td>
																																								
																																								<td class="text-right">{{number_format($item->quantity,0)}}</td>
																																								<td class="text-right"> $
																																								
																																								@foreach($total_total as $t)
																																								
																																								@if($t->tipo_comprobante == "A" || $t->tipo_comprobante == "FA")
																																								{{number_format($item->price,2)}}
																																								@else
																																							
																																								{{number_format( $item->price + ($item->price*$item->iva),2,",",".")}}	
																																								@endif
																																								
																																								@endforeach
																																								</td>
																																								
																																								<td class="text-center">
																																						        {{ number_format( ( $item->descuento ) /$item->price*100/$item->quantity ,2,",",".") }} %
                                                                                                                                                                </td>
                                                                                                                                                                
                                                                                                                                                                @foreach($total_total as $f)

                                                                                                                                                                   @if($f->tipo_comprobante == "A")
                                                                                                                                                                    <td class="text-center">
                                                                                                                                                                    {{$item->iva*100}} %
                                                                                                                                                                    </td>
                                                                                    
                                                                                                                                                                   @endif
                                                                                    
                                                                                    
                                                                                                                                                                   @endforeach
																																								<td class="text-right">$ 
																																								
																																									@foreach($total_total as $t)
																																								
																																								@if($t->tipo_comprobante == "A")
																																									{{number_format( (( ($item->price*$item->quantity) - $item->descuento) *(1+$item->iva)) ,2,",",".")}}
																																							
																																								
																																								@else
																																								{{number_format( (( ($item->price*$item->quantity) - $item->descuento) *(1+$item->iva)) ,2,",",".")}}
																																								@endif
																																								
																																								@endforeach
																																								
																																								<!----- Promociones ------------->
																															                                    
																															                                    @foreach($total_total as $t)
			                                                                                                                                                    @if($t->tipo_comprobante == "A")
                                                                                                                    			                                
                                                                                                                    			                                @if(0 < $item->cantidad_promo != null)
                                                                                                                    			                                <br><text> - ({{number_format($item->descuento_promo * $item->cantidad_promo,1,",",".")  }}) </text>
                                                                                                                                                                @endif
                                                                                                                                                                
                                                                                                                                                                @else
                                                                                                                                                            
                                                                                                                                                            	@if(0 < $item->cantidad_promo != null)
                                                                                                                    						                    <br><text> - ({{number_format($item->descuento_promo * $item->cantidad_promo * (1 + $item->iva),1,",",".")  }}) </text>
                                                                                                                                                                @endif
    
                                                                                                                                                                
                                                                                                                                                                @endif
                                                                                                                                                                @endforeach	
																																								
																																								</td>
																																						</tr>
																																						@endforeach
																																				</tbody>
																																		</table>
																																</div>
																														</div>
																												</div>
																												<br><br><br>

																														<div class="row inv--detail-section">

																														<div class="col-sm-7 align-self-center">
																															<div class="col-sm-12 col-12">
																																<h6 class=" inv-title">Informacion adicional:</h6>
																															</div>
																																
																																<div class="col-sm-4 col-12">
																																<p style="font-size:11px;">Forma de pago:
																																
																																{{$venta->metodo_pago}}</p>
																																</div>
																																
																																<div class="col-sm-4 col-12">
																																<p style="font-size:11px;"> </p>
																																</div>
																																
																																@if($venta->observaciones)
																																<div class="col-sm-12 col-12">
																																<h6 class=" inv-title">Observaciones:</h6>
																															    </div>
																															
																																<div class="col-sm-4 col-12">
																																<p style="font-size:11px;">
																																{{$venta->observaciones}}
																																</p>
																																</div>
																																@endif
																																
                                                                                                                        </div>
																														
																														@foreach($total_total as $t)
																														
																														<div style="float: right !important; text-align: right !important;" class="col-sm-5 text-right order-2">
                                                																			    <div class="col-sm-11 col-11">
                                                																			 
                                                                                                                        
                                                                                                                          @if($t->tipo_comprobante != "FB" && $t->tipo_comprobante != "B"  )
                                                                                                                         <p style="font-size:11px;">Sub Total: $ {{ number_format($t->subtotal - $t->descuento,2,",",".")  }}  </p>
                                                                                                                         @else
                                                                                                                         
                                                                                                                         <p style="font-size:11px;">Sub Total:  $ {{ number_format($t->total,2,",",".") }}  </p>
                                                                                                                         @endif
                                                                                                                         
                                                                                                                         @if($t->recargo != null)
    
																															<p style="font-size:11px;">Recargo: $
																															
																														{{$t->recargo}}</p>
																														@endif
																															
																														@if($t->tipo_comprobante != "B" && $t->tipo_comprobante != "FB")
																														
																														@if($t->descuento != null)

																														<p style="font-size:11px;"></p>
																														@endif
																														</p>
																															
																														@endif
																														
                                                                                                                        

                                                                                                                        @if($t->tipo_comprobante == 'A' || $t->tipo_comprobante == 'FA')
                                                                                                                        
                                                                                                                            <p class="">IVA: $ {{number_format( $t->iva ,2,",",".")}} </p>
                                                        
                                                                                                                            <h5> Total : $ 
                                                                                                                            
                                                                                                                        {{ number_format( ($t->subtotal + $t->iva + $t->recargo - $t->descuento) ,2,",",".") }}
                                                                                                                             
                                                                                                                         
                                                                                                                        @else
                                                                                                                             
                                                        
                                                                                                                                 <h5 class="">Total :  $ 
                                                                                                                             
                                                                                                                                  {{ number_format( ($t->total) ,2,",",".") }}
                                                                                                                        
                                                                                                                                </h5>
                                                                                                                             </div>
                                                        
                                                        
                                                                                                                             @endif
                                                        
                                                                                                                               @endforeach

                                                                                                                        </div>
																														</div>
																												</div>


																										</div>
																								</div>



@foreach($total_total as $cae)

                                                                                                    @if($cae->cae != null)

                                                                                                    <footer style="bottom:0; width:100%;">
                                                                                                           <div style="width:100%;" >
                                                                                                               <table style="border: solid 1px #c8c8c8; width:100%;">
                                                                                                                   <tr>
                                                                                                                       <td>
                                                                                                                         
                                                                                                                           <img src="data:image/svg+xml;base64,{{ base64_encode($codigoQR) }}">
                                                                                                                       
                                                                                                                         
                                                                                                                       </td>
                                                                                                                       <td >
                                                                                                                           <div style="margin-left:15px;">
                                                                                                                         <img src="assets/img/afip.png" style="width:150px;"><br>
                                                                                                                         <b style="font-size:12px;">Comprobante autorizado</b>  
                                                                                                                           </div>
                                                                                                                         
                                                                                                                        </td>
                                                                                                                       <td >
                                                                                                                           <br>
                                                                                                                           <div style="bottom:0; font-size:12px; margin-top:27px;" class="p-2"><b>CAE: </b>{{$cae->cae}}</div>
                                                                                                                       </td>
                                                                                                                       <td >
                                                                                                                           <br>
                                                                                                                           <div style="bottom:0; font-size:12px; margin-top:27px;"  class="p-2"><b>VENCIMIENTO CAE: </b>{{\Carbon\Carbon::parse($cae->vto_cae)->format('d/m/Y')}}</div>
                                                                                                                       </td>
                                                                                                                   </tr>
                                                                                                               </table>


                                                                                                           </div>
                                                                                                    </footer>


																						</div>
                                                                                                    @endif

                                                                                                    @endforeach
</div>
	</body>

</html>
