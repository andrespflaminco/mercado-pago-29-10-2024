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

                                                                                                                                                             @if($user->image != null)
                                                                                                                                                             <img  width="100" class="rounded"
                                                                                                                                                             src="{{ asset('storage/users/'.$user->image) }}"
                                                                                                                                                             >
                                                                                                                                                             @else
                                                                                                                                                             <h5 class="inv-brand-name">{{$user->name}}</h5>
                                                                                                                                                             @endif


                                                                                                                                                           </div>
																																						</td>
																																						<td style="border:solid 1px #c8c8c8; padding: 0px 20px 0px 25px;">

																																								@foreach($sale as $f)

                                                                                                                                                             <h1 >

                                                                                                                                                               <b >
                                                                                                                                                               @if($f->tipo_comprobante != NULL)
                                                                                                                                                               {{$f->tipo_comprobante}}
                                                                                                                                                               @else
                                                                                                                                                               X
                                                                                                                                                               @endif
                                                                                                                                                              </b>
                                                                                                                                                             </h1>

                                                                                                                                                             @if($f->tipo_comprobante == 'C')
                                                                                                                                                             <p style="font-size: 10.5px;">
                                                                                                                                                               <b> COD. 011 </b>
                                                                                                                                                             </p>
                                                                                                                                                             @endif
                                                                                                                                                             @if($f->tipo_comprobante == 'A')
                                                                                                                                                             <p style="font-size: 10.5px;">
                                                                                                                                                               <b> COD. 01 </b>
                                                                                                                                                             </p>
                                                                                                                                                             @endif
                                                                                                                                                             @if($f->tipo_comprobante == 'B')
                                                                                                                                                             <p style="font-size: 10.5px;">
                                                                                                                                                               <b> COD. 06 </b>
                                                                                                                                                             </p>
                                                                                                                                                             @endif
                                                                                                                                                             @if($f->tipo_comprobante == NULL)
                                                                                                                                                             <p style="font-size: 10.5px;">
                                                                                                                                                               <b> X </b>
                                                                                                                                                             </p>
                                                                                                                                                             @endif


                                                                                                                                                             @endforeach

																																							</th>
																																							<td class="text-right" style="width: 45%;">
																																							      @foreach($sale as $cae)

                                                                                                                                                               @if($cae->cae != null)
																																							    
																																							     <h5 class="in-heading">FACTURA</h5>
																																							     @else
																																							      <h5 class="in-heading">DETALLE DE VENTA</h5>
																																							     @endif
																																							     
																																							      @endforeach
																																							</td>

																																						</tr>

																																						<tr style="margin-top: 7%; border:none;">
																																						<td style="width: 45%; border:none;" class="text-left">
																																						<br>
                                                                                   <p class="inv-customer-name"> Razon social: {{$datos_facturacion->razon_social}} </p>
                                                                               <p style="font-size: 11px;">Direccion: {{$datos_facturacion->domicilio_fiscal}} 
                                                                               <BR> {{$datos_facturacion->localidad}} ,{{$datos_facturacion->provincia}} </p>
                                                                               <p style="font-size: 11px;">Telefono: {{$datos_facturacion->phone}}</p>
                                                                               <p style="font-size: 11px;">Email: {{$datos_facturacion->email}}</p>

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
																																						            @foreach($sale as $tf)
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

                                                                                                                                                                           <p style="font-size:11px;">{{$datos_facturacion->cuit}}</p>
                                                                                                                                                                           <p style="font-size:11px;">{{$datos_facturacion->iibb}}</p>
                                                                                                                                                                           <p style="font-size:11px;">{{\Carbon\Carbon::parse($datos_facturacion->fecha_inicio_actividades)->format('d-m-Y')}}</p>
                                                                                                                                                                           <p style="font-size:11px;">{{$datos_facturacion->condicion_iva}}</p>
  																																					        </td>
																																						    </tr>
																																						</table>

																																						</td>

																																						</tr>
																																						<tr style="border:none;">
																																						<td style="width: 45%;" class="text-left">
                                                                             <p style="font-size:11px;" >Cliente: {{$cliente->nombre}}</p>
                                                                             <p style="font-size:11px;">CUIT: {{$cliente->dni}}</p>
                                                                             
                                                                             @if($cliente->direccion != null)
                                                                           <p style="font-size:11px;">{{$cliente->direccion}},{{$cliente->localidad}}. {{$cliente->provincia}}</p>
                                                                           @endif
                                                                           
                                                                           @if($cliente->telefono)
                                                                             <p style="font-size:11px;"> Tel: {{$cliente->telefono}}</p>
                                                                             @endif

																																						</td>

																																						<td style=" padding: 0px 20px 0px 20px;">

																																						</td>
																																						<td class="text-right" style="width: 45%; ">
																																						   


                                                                                                                                                             <p cstyle="font-size:11px;">Fecha :
                                                                                                                                                               @foreach($sale as $f)
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
																																								<th scope="col">Concepto</th>
																																								<th class="text-right" scope="col">Subtotal</th>
																																						</tr>
																																				</thead>
																																				<tbody>
																																					<?php $i = 1; ?>
																																					@foreach($items as $item)
																																						<tr>
																																								<td><?php echo $i++; ?></td>
																																								<td>{{$item->product_name}}</td>
																																								<td class="text-right"> $

																																								@foreach($sale as $t)

																																								@if($t->tipo_comprobante == "A")
																																								{{number_format($item->price,2)}}
																																								@else
																																								{{ number_format(($item->price + $item->iva) ,2)}}
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
																																@foreach($sale as $t)
																																<div class="col-sm-4 col-12">
																																<p style="font-size:11px;">Forma de pago:
																																@if ($t->metodo_pago != '')
																															     Efectivo </p>
																																</div>
																																@else
																																
																																{{$t->metodo_pago}}</p>
																																</div>
																																@endif

																																<div class="col-sm-4 col-12">
																																<p style="font-size:11px;"> </p>
																																</div>

																														</div>
																														<div style="float: right !important; text-align: right !important;" class="col-sm-5 text-right order-2">
                                                																			    <div class="col-sm-11 col-11">

                                                                                                                          @if($t->tipo_comprobante == "A")
                                                                                                                         <p style="font-size:11px;">Sub Total: $ {{$t->subtotal}} </p>
                                                                                                                         @else

                                                                                                                         @endif

																															</p>


                                                                                                                             @if($t->tipo_comprobante == "A")

                                                                                                                            <p class="">IVA: $ {{$t->iva}}</p>

                                                                                                                            <h5> Total : $ {{$t->total }}</h5>

                                                                                                                             @else

                                                                                                                                 <h5 class="">Total :  $ {{$t->total }}</h5>
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


                                                                                            @foreach($sale as $cae)

                                                                                                    @if($cae->cae != null)

                                                                                                    <footer style="bottom:0; width:100%;">
                                                                                                           <div style="width:100%;" >
                                                                                                               <table style="border: solid 1px #c8c8c8; width:100%; padding:10px;">
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
