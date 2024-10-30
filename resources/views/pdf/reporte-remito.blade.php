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

																																								@foreach($total_total as $f)

                                                                                                                                                             <h1 >

                                                                                                                                                               <b >
                                                                                                                                                               @if($f->tipo_comprobante != NULL && $f->cae != null)
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
                                                                                                                                                             


                                                                                                                                                             @endforeach

																																							</th>
																																							<td class="text-right" style="width: 45%;">
																																							     <h5 class="in-heading">REMITO</h5>
																																							</td>

																																						</tr>

																																						<tr style="margin-top: 7%; border:none;">
																																						<td style="width: 45%; border:none;" class="text-left">
																																						<br>
																																						    @foreach($detalle_facturacion as $df)
                                                                                                                                                               <p class="inv-customer-name"> <h5> <b>{{$df->razon_social}}</b>
                                                                                                                                                               </h5> </p>
                                                                                                                                                               <p style="font-size: 11px;">@if($df->domicilio_fiscal != null) Direccion: {{$df->domicilio_fiscal}} @endif @if($df->localidad != null), {{$df->localidad}} @endif @if($df->provincia != null),{{$df->provincia}}  @endif  </p>
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
                                                                                                                                                                           <p style="font-size:11px;">@if($df->fecha_inicio_actividades) {{\Carbon\Carbon::parse($df->fecha_inicio_actividades)->format('d-m-Y')}} @endif</p>
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
																																						    <p style="font-size:11px;" >Numero de remito : # {{$ventaId}}</p>



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
																																								<th scope="col">Observaciones</th>
																																								<th class="text-right" scope="col">Cantidad</th>
																																						</tr>
																																				</thead>
																																				<tbody>
																																					<?php 
																																					$i = 1; 
																																					$total_cantidad = 0;
																																					?>
																																					@foreach($detalle_venta as $item)
																																					<?php $total_cantidad += $item->quantity; ?>
																																						<tr>
																																								<td><?php echo $i++; ?></td>
																																								<td>{{$item->product}}</td>
																																								<td>{{$item->comentario}}</td>
																																								
																																								<td class="text-right">{{number_format($item->quantity,2)}}</td>
																																								
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
																																@else
																																{{$t->metodo_pago}}</p>
																																@endif
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
																																
																																
																																
																																    <!---- Si es un cliente de con envio a domicilio ---->
                                                                                                                          <br><br>
                                                                                                                         @if($ecommerce_envio_form != null)
                                                                                                                         <div style="border: solid 1px #eee; padding:5px; width:100%;">
                                                                                                                           <div class="col-sm-12 col-12">
                                                                                                                         <h6 class=" inv-title">
                                                                                                                            Detalles de Entrega:
                                                                                                                            </h6>
                                                                                                                         </div>
                                                                                                                          <div class="col-sm-12 col-12">
                                                                                                                             <p class=" inv-subtitle">Tipo de entrega : @if($ecommerce_envio_form->metodo_entrega == 1)  Retira por el local @else Entrega a Domicilio    @endif </p>
                                                                                                                         </div>
                                                                                                                       
                                                                                                                         <div class="col-sm-12 col-12">
                                                                                                                             <p class=" inv-subtitle">Destinatario: {{$ecommerce_envio_form->nombre_destinatario}}  </p>
                                                                                                                         </div>
                                                                                                                         
                                                                                                                         @if($ecommerce_envio_form->metodo_entrega == 2)
                                                                                                                         <div class="col-sm-12 col-12">
                                                                                                                             <p class=" inv-subtitle">Direccion: {{$ecommerce_envio_form->direccion}},{{$ecommerce_envio_form->ciudad}}. {{$ecommerce_envio_form->nombre_provincia}} </p>
                                                                                                                         </div>
                                                                                                                         
                                                                                                                         @endif
                                                                                                                           <div class="col-sm-12 col-12">
                                                                                                                             <p class=" inv-subtitle">Telefono: {{$ecommerce_envio_form->telefono}} </p>
                                                                                                                         </div>
                                                                                                                         
                                                                                                                         </div>
                                                                                                                        
                                                                                                                         <br><br>
                                                                                                                          @endif
                                                                                                                         <!------------------------------------------------>
                                                    																	</div>
                                                    																	<div style="float: right !important; text-align: right !important;" class="col-sm-5 text-right order-2">
                                                                                                    					<div class="col-sm-11 col-11">
                                                                                                                        <h5>Cantidad total: {{$total_cantidad}}</h5>
                                                                                                            
                                                                                                                        @endforeach
                                                    
                                                                                                                        </div>
                                                    																	</div>
                                                    																	</div>
                                                    
                                                    
                                                    																	</div>
                                                    																	</div>
                                                    
                                                    



	</body>
	</html>
