<!DOCTYPE html>
<html lang="es">
<head>
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

																																							                                                       <h1 >

                                                                                                                                                               <b style="padding-top:20px;" >
                                                                                                                                                                
                                                                                                                                                               X
                                                                                                                                                              </b>
                                                                                                                                                             </h1>


                                                                                                                                                        
																																							</th>
																																							<td class="text-right" style="width: 45%;">
																																							     <h5 class="in-heading">PRESUPUESTO # {{$ventaId}}</h5>
																																							</td>

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
																																						        
                                                                                                                                                                  <p style="font-size:11px; margin-top:2px;"><b style="font-size:11px;">CUIT:</b></p>
                                                                                                                                                                <p style="font-size:11px; margin-top:2px;"> <b style="font-size:11px;">INGRESOS BRUTOS:</b></p>
                                                                                                                                                                 <p style="font-size:11px; margin-top:2px;"> <b style="font-size:11px;">INICIO ACTIVIDADES:</b></p>
                                                                                                                                                                <p style="font-size:11px; margin-top:2px;"> <b style="font-size:11px;">IVA:</b></p>
																																						        </td>
																																						        <td class="text-right" style="width:45%;">
																																						        
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
                                                                                                                                                         <p style="font-size:11px;">{{$c->direccion}},{{$c->localidad}}. {{$c->provincia}}</p>
                                                                                                                                                         <p style="font-size:11px;"> Tel: {{$c->telefono}}</p>
                                                                                                                                                       @endforeach

																																						</td>

																																						<td style=" padding: 0px 20px 0px 20px;">

																																						</td>
																																						<td class="text-right" style="width: 45%; ">
																																						    <p style="font-size:11px;" >Numero de presupuesto : # {{$ventaId}}</p>


                                                                                                                                                             @foreach($total_total as $f)
                                                                                                                                                             <p cstyle="font-size:11px;">Fecha :
                                                                                                                                                              
                                                                                                                                                                {{\Carbon\Carbon::parse($f->created_at)->format('d-m-Y')}}</p>
                                                                                                                                                                
                                                                                                                                                                <p cstyle="font-size:11px;">Validez hasta:
                                                                                                                                                                 {{\Carbon\Carbon::parse($f->created_at)->add($f->vigencia, 'days')->format('d-m-Y')}}
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
																																							<th scope="col">Fila</th>
                                                                                                                                                              <th scope="col">Producto</th>
                                                                                                                                                              <th class="text-right" scope="col">Cantidad</th>
                                                                                                                                                              <th class="text-right" scope="col">Precio</th>
                                                                                                                                                              
                                                                                                                                                              @foreach($total_total as $t)
                                                                                                                                                              
                                                                                                                                                              {{$t->descuento - $t->recargo}}
                                                                                                                                                              
                                                                                                                                                              @if($t != null)
                                                                                                                                                              
                                                                                                                                                              @if( ($t->descuento - $t->recargo)  < 0)
                                                                                                                                                              <th class="text-right" scope="col">% Recargo</th>
                                                                                                                                                              @endif
                                                                                                                                                              
                                                                                                                                                              @if( ($t->descuento - $t->recargo)  >= 0)
                                                                                                                                                              <th class="text-right" scope="col">% Bonif</th>
                                                                                                                                                              @endif
                                                                                                                                                             
                                                                                                                                                              
                                                                                                                                                              @endif
                                                                                                                                                                
                                                                                                                                                                 @if($t->tipo_comprobante == "A")
                                                                                                                                                                
                                                                                                                                                                 <th  class="text-right" scope="col">% IVA</th>
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
																																								<td>{{$item->product}}</td>
																																								<td class="text-right">{{number_format($item->cantidad,2)}}</td>
																																								
																																								<td class="text-right"> $
																																								
																																								@foreach($total_total as $t)
																																								
																																								@if($t->tipo_comprobante == "A")
																																								{{number_format($item->precio,2)}}
																																								@else
																																								{{number_format( ( ($item->precio)*(1+$item->alicuota_iva) ) ,2)}}
																																								@endif
																																								
																																								@endforeach
																																								</td>
																																								
																																								
                                                                                                                                                                <td class="text-center">
                                                                                                                                                                 {{ ($item->descuento-$item->recargo)/$item->precio*100}} %
                                                                                                                                                                 </td>
                                                                                                                                                                 
                                                                                                                                                                 @foreach($total_total as $t)
                                                                                                                                                                
                                                                                                                                                                 @if($t->tipo_comprobante == "A")
                                                                                                                                                                    <td class="text-right">
                                                                                                                                                                    {{number_format($item->alicuota_iva*100,2)}} %
                                                                                                                                                                    </td>
                                                                                                                                                                  @endif
                                                                                                                                                                    
                                                                                                                                                                  @endforeach
                                                                                                                                                                    
																																								<td class="text-right">$ 
																																								
																																									@foreach($total_total as $t)
																																								
																																						        	 @if($t->tipo_comprobante == "A")
                                                                                                                                                                    {{number_format(( ($item->precio- $item->descuento + $item->recargo) * (1+$item->alicuota_iva ) )*$item->cantidad,2)}}
                                                                                                                                                                    @else
                                                                                                                                                                    {{number_format(( ($item->precio- $item->descuento + $item->recargo) * (1+$item->alicuota_iva ) )*$item->cantidad,2)}}
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
																																@foreach($total_total as $t)
																																<div class="col-sm-4 col-12">
																																<p style="font-size:11px;">Forma de pago:
																																@if ($t->metodo_pago != '')
																															     Efectivo </p>
																																</div>
																																@else
																																{{$t->metodo_pago}}</p>
																																</div>
																																@endif
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
                                                
                                                                                                                          @if($t->tipo_comprobante == "A")
                                                                                                                         <p style="font-size:11px;">Sub Total: $ {{$t->subtotal}} </p>
                                                                                                                         @else
                                                                                                                         
                                                                                                                         @endif
                                                                                                                         
                                                                                                                         
    
																															<p style="font-size:11px;">Recargo: $
																															
																															@if($t->recargo != null)
																															{{$t->recargo}}</p>
																															@else
																															0
																															@endif
																															</p>
																														

																															<p style="font-size:11px;">Descuento: $
																															@if($t->descuento != null)
																															{{$t->descuento}}
																															@else
																															 0
																															@endif
																															</p>


                                                                                                                             @if($t->tipo_comprobante == "A")
                                                        
                                                                                                                            <p class="">IVA: $ {{$t->iva}}</p>
                                                        
                                                                                                                            <h5> Total : $ {{$t->total}}</h5>
                                                        
                                                                                                                             @else
                                                        
                                                                                                                                 <h5 class="">Total :  $ {{$t->total}}</h5>
                                                                                                                             </div>
                                                        
                                                        
                                                                                                                             @endif
                                                        
                                                                                                                               @endforeach

                                                                                                                        </div>
																														</div>
																												</div>


																										</div>
																								</div>






																									    <br><br><br><br>
																									     <br><br><br><br>
																									     <br>


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
