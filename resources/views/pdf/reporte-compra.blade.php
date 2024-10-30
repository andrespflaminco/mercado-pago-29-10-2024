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
                                                                                                                                                             @foreach ($detalle_facturacion as $u)

                                                                                                                                                             <h5 class="inv-brand-name">{{$u->nombre}}</h5>
                                                                                                                                                            
                                                                                                                                                            @endforeach

                                                                                                                                                           </div>
																																						</td>
																																						<td style="border:solid 1px #c8c8c8; padding: 0px 20px 0px 25px;">

																																								@foreach($total_total as $f)

                                                                                                                                                             <h1 >

                                                                                                                                                               <b >
                                                                                                                                                               @if($f->tipo_comprobante != null && $f->cae != null)
                                                                                                                                                               {{$f->tipo_comprobante}}
                                                                                                                                                               @else
                                                                                                                                                               X
                                                                                                                                                               @endif
                                                                                                                                                              </b>
                                                                                                                                                             </h1>

                                                                                                                                                             @if($f->tipo_comprobante == 'C' && $f->cae != null)
                                                                                                                                                             <p style="font-size: 11.5px;">
                                                                                                                                                               <b> COD. 011 </b>
                                                                                                                                                             </p>
                                                                                                                                                             @endif
                                                                                                                                                             @if($f->tipo_comprobante == 'A' && $f->cae != null)
                                                                                                                                                             <p style="font-size: 11.5px;">
                                                                                                                                                               <b> COD. 01 </b>
                                                                                                                                                             </p>
                                                                                                                                                             @endif
                                                                                                                                                             @if($f->tipo_comprobante == 'B' && $f->cae != null)
                                                                                                                                                             <p style="font-size: 11.5px;">
                                                                                                                                                               <b> COD. 06 </b>
                                                                                                                                                             </p>
                                                                                                                                                             @endif
                                                                                                                                                             
                                                                                                                                                             @if($f->tipo_comprobante == NULL && $f->cae != null)
                                                                                                                                                             <p style="font-size: 11.5px;">
                                                                                                                                                               <b> </b>
                                                                                                                                                             </p>
                                                                                                                                                             @endif
                                                                                                                                                             
                                                                                                                                                              @if($f->tipo_comprobante == NULL && $f->cae == NULL)
                                                                                                                                                             <p style="font-size: 11.5px;">
                                                                                                                                                               <b>  </b>
                                                                                                                                                             </p>
                                                                                                                                                             @endif
                                                                                                                                                             


                                                                                                                                                             

																																							</th>
																																							 @if($f->cae == NULL)
																																							<td class="text-right" style="width: 45%;">
																																							     <h5 class="in-heading">DETALLE DE COMPRA</h5>
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
                                                                                                                                                               <p style="font-size: 11px;">Direccion: {{$df->direccion}} {{$df->localidad}} {{$df->provincia}} </p>
                                                                                                                                                               <p style="font-size: 11px;">Telefono: {{$df->telefono}}</p>
                                                                                                                                                               <p style="font-size: 11px;">Email: {{$df->mail}}</p>
                                                                                                                                                               @endforeach

																																						</td>

																																						<td style=" padding: 0px 20px 0px 25px;">

																																						</td>
																																						<td class="text-right" style="width: 45%; border:none; ">
																																							<br>
																																								<br>
																																						<table style="border:none;">
																																						    <tr style="border:none;">
																																						        <td class="text-left" style="width:100%;">
																																						           <p style="font-size:11px; margin-top:2px;"><b >COMPRA #:</b></p>
                                                                                                                                                                  <p style="font-size:11px; margin-top:2px;"><b style="font-size:11px;">FECHA:</b></p>
																																						        </td>
																																						        <td class="text-right" style="width:30%;">
																																						            @foreach($total_total as $tf)
                                                                                                                                                                          
                                                                                                                                                                         <p style="font-size:11px;"> {{$f->id}}</p>
                                                                                                                                                                         <p style="font-size:11px;">{{\Carbon\Carbon::parse($df->created_at)->format('d-m-Y')}}</p>
                                                                                                                                                                         
                                                                                                                                                                    @endforeach
                                                                                                                                                   
																																						        </td>
																																						    </tr>
																																						</table>

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
                                                                                    
                                                                                                                                                                   @if($f->recargo < $f->descuento)
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
																																								<td>{{$item->nombre}}</td>
																																								
																																								<td class="text-right">{{number_format($item->cantidad,2)}}</td>
																																								<td class="text-right">
																																								 $ {{number_format($item->precio,2)}}
                                           
                                                                                                                                                                </td>
                                                                                                                                                                <td>
                                                                                                                                                                    
                                                                                                                                                                </td>
                                                                                    
                                                                                                                                                                <td class="text-right">
																																								 $ {{number_format($item->precio*$item->cantidad*(1+$item->alicuota_iva),2)}}
																																								
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
																																@foreach($total_total as $t)
																																<div class="col-sm-4 col-12">
																																<p style="font-size:11px;">Forma de pago:
																																
																																{{$t->metodo_pago}}</p>
																																</div>
																																
																																@if ($t->observaciones != '')
																																<div class="col-sm-4 col-12">
																																<p style="font-size:11px;">Observaciones: </p>
																																</div>
																																<div class="col-sm-8 col-12">
																																<p style="font-size:11px;">{{$t->observaciones}}</p>
																																</div>
																																@else
																																<div class="col-sm-4 col-12">
																																<p style="font-size:11px;"> </p>
																																</div>

																																@endif
																														</div>
																														<div style="float: right !important; text-align: right !important;" class="col-sm-5 text-right order-2">
                                                																			    <div class="col-sm-11 col-11">
                                                																			 
                                                
                                                                                                                          @if($t->tipo_comprobante != "B")
                                                                                                                         <p style="font-size:11px;">Sub Total: $ {{$t->subtotal }}  </p>
                                                                                                                         @else
                                                                                                                         
                                                                                                                         <p style="font-size:11px;">Sub Total:  $ {{ ($t->total) }}  </p>
                                                                                                                         @endif
                                                                     
																														

                                                                                                                             @if($t->tipo_comprobante == "A")
                                                                                                                             
                                                        
                                                                                                                            <p class="">IVA: $ {{$t->iva}}</p>
                                                        
                                                                                                                            <h5> Total : $ {{$t->subtotal + $t->iva + $t->recargo - $t->descuento }}</h5>
                                                        
                                                                                                                             @else
                                                                                                                             
                                                        
                                                                                                                                 <h5 class="">Total :  $ {{$t->subtotal + $t->iva + $t->recargo - $t->descuento}}</h5>
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

	</body>
	</html>
