<div class="page-header">
						<div class="page-title">
							<h4>Factura {{$NroFactura}} </h4>
							<h6></h6>
						</div>
					</div>
					<div class="card">
						<div class="card-body">
							<div class="row">
								<div class="col-lg-4 col-sm-6 col-12">
									<div class="form-group">
										<label>Cliente</label>
										<div class="row">
											<div class="col-lg-10 col-sm-10 col-10">
											<select disabled wire:model="cliente_id" class="form-control">
											<option value="1">Consumidor final</option>
											@foreach($clientes as $c)
											<option value="{{$c->id}}">{{$c->nombre}}</option>
											@endforeach
											</select>
											</div>
										</div>
									</div>
								</div>
								<div class="col-lg-2 col-sm-6 col-12">
                                
								</div>
																    
								<div class="col-lg-6 col-sm-6 col-12 ">
								    
								<div style="padding-top:18px; text-align: right !important;" class="form-group text-right">
								<label > </label>
								@if($nro_nota_credito == null)
                                
                                @can("anular factura")
                                <button type="button" class="btn btn-warning" style="  min-width: 130px; margin-bottom: 0 !important;  margin-right: 15px; margin-bottom: 0 !important;  padding: 0.375rem 0.75rem !important;" onclick="ConfirmAnularFactura('{{$factura_id}}')">
                                ANULAR FACTURA
                                </button>
                                @endcan
                                     
                                @else
                                <button class="btn btn-danger" style="cursor:default !important; ">Nota de credito: {{$nro_nota_credito}}</button>
                                
                                @endif
                                
					           	<a class="btn" style="box-shadow: none; border: solid 1px #515365; background:transparent; margin-left: 10px !important;" href="javascript:void(0)" wire:click="MailModalVerVenta({{$factura_id}})" >
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-mail"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                Enviar
                                </a>
                    
                                <button style="color: #212529; box-shadow: none; border: solid 1px #515365; background:transparent; margin-left: 10px !important;" class="btn btn-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                 <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-printer action-print" data-toggle="tooltip" data-placement="top" data-original-title="Imprimir"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                                Imprimir 
                                </button>
                        
                                <div class="dropdown-menu">
                                      <a class="dropdown-item" target="_blank" href="{{ url('imprimir-factura/pdf' . '/' . $factura_id ) }}">IMPRIMIR A4</a>
                                      <a class="dropdown-item" target="_blank" href="{{ url('ticket-factura' . '/' . $factura_id ) }}">IMPRIMIR TICKET</a>
                                </div>
                                
                                
								</div>
								</div>
								
							</div>
							<div class="row">
								<div class="table-responsive">
									<table class="table">
										<thead>
											<tr>
												<th>Fila</th>
												<th>Codigo</th>
												<th>Producto</th>
												<th>Cantidad</th>
												<th>Precio</th>
												<th>IVA</th>
												<th>Precio + IVA</th>
												<th>Descuento</th>
												@if($relacion_precio_iva == 2)
												<th> Total con IVA ($)	</th>
												@else
												<th> Total ($)	</th>
												@endif
											</tr>
										</thead>
										<tbody>
										   <?php $i = 1; ?>
                                            @foreach($detalle_venta as $item)
                                            <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td>{{$item->product_barcode}}</td>
                                            <td>{{$item->product_name}}</td>
                                            <td> {{number_format($item->quantity,0)}} </td>
                                            <td>$ {{number_format($item->price,0)}}</td>
                                            <td> {{number_format($item->iva,2)}} %</td>
                                            <td> 

                                            @if($item->relacion_precio_iva == 2)
                                            $ {{number_format(( $item->price *(1 + $item->iva)) ,0)}}

                                            @else
                                            
                                            $ {{number_format($item->price,0)}}

                                            
                                            @endif
                                            
                                            </td>
                                            <td class="text-center">
                                            <div class="input-group mb-0">
                                            <input disabled style="max-width: 80px;" type="text" class="boton-editar"  value="{{ number_format(($item->descuento)/$item->price*100/$item->quantity,2) }}" > 
                                            <div class="input-group-append">
                                             <span class="input-group-text input-gp">
                                             %
                                             </span>
                                            </div>
                                            </div>   
                                            
                                            </td>
                                            <!------
                                            <td class="text-center">
                                            {{$item->iva*100}} % 
                                            </td>
                                            ----->
                                            
                                            <td>

                                            @if($item->relacion_precio_iva == 2)
                                            $ {{number_format( (( ($item->price*$item->quantity) - $item->descuento ) *(1 + $item->iva)) ,0)}}

                                            @else
                                            
                                            $ {{number_format( (( ($item->price*$item->quantity) - $item->descuento) *(1)) ,0)}}

                                            
                                            @endif

                                            </td>
                                           
                                            </tr>
                                          @endforeach
										</tbody>
									</table>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-12 float-md-right">
									<div class="total-order">
										<ul>
											<li>
												<h4>
												Subtotal 
												</h4>
												<h5>$ {{number_format($subtotal_factura,2)}} </h5>
											</li>
											<li>
												<h4>
												IVA 
												</h4>
												<h5>$ {{number_format($iva_factura,2)}} ({{number_format($alicuota_iva,2)}}%)</h5>
											</li>

											<li class="total">
												<h4>Total</h4>
												<h5>$ {{number_format($total_factura,2)}}</h5>
											</li>
										</ul>
									</div>
								</div>
							</div>
							<div  class="row">
							    <div class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label>Tipo de factura </label>
											<select disabled wire:model="tipo_factura" class="form-control">
											<option value="CF">CF</option>
											<option value="A">A</option>
											<option value="B">B</option>
											<option value="C">C</option>
											</select>
								</div>
								</div>
								
									<div class="row">
								
								<div class="col-lg-12">
									<a href="javascript:void(0)" wire:click="CerrarModal" class="btn btn-cancel">Volver</a>
								</div>
							
						</div>
					</div>
				</div>
			</div>
        </div>