
<div class="page-header">
						<div class="page-title">
							<h4>Compra #{{$Nro_Compra}}</h4>
							<h6></h6>
						</div>
					</div> 
<div class="card">
						<div class="card-body">
							<div class="row">
								<div class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label>Proveedor</label>
										<div class="row">
											<div class="col-lg-10 col-sm-10 col-10">
												<select {{$proveedor_id == 2 ? 'disabled' : '' }} wire:model="proveedor_id" wire:change="CambiarProveedor" class="form-control">
												    <option value="1">Sin proveedor</option>
												    @if(auth()->user()->sucursal == 1)
												    <option value="2">Casa central</option>
												    @endif
													@foreach($prov as $proveedores)
													<option value="{{$proveedores->id}}">{{$proveedores->nombre}}</option>
													@endforeach
												</select>
											</div>
										</div>
									</div>
								</div>
								
								<div class="col-lg-3 col-sm-6 col-12">
								<div class="form-group">
										<label>Fecha</label>
										<div class="row">
											<div class="col-lg-10 col-sm-10 col-10">
											<input {{ (auth()->user()->sucursal == 1 && $proveedor_id == 2) ? 'disabled' : '' }} type="date" wire:model="fecha_compra" wire:change="CambiarFecha" class="form-control">
											</div>
										</div>
									</div>
								</div>
								<div class="col-lg-3 col-sm-6 col-12">
								<div class="form-group">
								<label>Etiquetas</label>
								<select {{$proveedor_id == 2 ? 'disabled' : '' }} class="select2" id="{{$NroVenta}}" multiple="multiple" data-relacion-id="{{ $NroVenta }}"> 
                                <!-- Aquí puedes incluir opciones predefinidas o dejarlo vacío -->
                                    @if($nombre_etiqueta_seleccionada != null)
                                    <option value="{{ $nombre_etiqueta_seleccionada }}" selected>
                                        {{ $nombre_etiqueta_seleccionada }}
                                    </option>
                                    @endif
                                </select>
								</div>
								</div>
								
								<div class="col-lg-3 col-sm-6 col-12">
								<div class="form-group">
								<label>Recalcular deuda</label>    
								<button class="btn btn-light" wire:click="VerElegirTipoActualizacion()">Recalcular</button>
								</div>
								</div>
								
								<div hidden class="col-lg-3 col-sm-6 col-12">
								<div class="form-group">
								<label>Descargar</label>    
								<a class="btn btn-light" wire:click="ExportarCompra({{$NroVenta}})">Exportar compra en Excel</a>
								</div>
								</div>

								<div class="col-lg-12 col-sm-6 col-12">
									<div class="form-group">
										<label>Agregar producto</label>
										<div class="input-groupicon">
											<input  {{$proveedor_id == 2 ? 'disabled' : '' }} wire:model="query_product" wire:keydown.escape="resetProduct" wire:keydown.tab="resetProduct" wire:keydown.enter="selectProduct" type="text" placeholder="Scanear/Buscar producto...">
											<div class="addonset">
												<img src="{{ asset('assets/pos/img/icons/scanners.svg') }}" alt="img">
											</div>
										</div>
										

                                @if(!empty($query_product))
                                    <div class="fixed top-0 bottom-0 left-0 right-0" wire:click="reset"></div>
                    
                                    <div style="position:absolute;" class="absolute z-10 w-full bg-white rounded-t-none shadow-lg list-group">
                                        @if(!empty($products_s))
                                            @foreach($products_s as $i => $product)
                                            <a style="z-index: 9999;" href="javascript:void(0)"
                                            wire:click="selectProduct({{$product['id']}})"
                                            class="btn btn-light" title="Seleccionar producto">{{ $product['barcode'] }} - {{ $product['name'] }}
                                            </a>
                    
                                            @endforeach
                    
                                        @else
                    
                                        @endif
                                    </div>
                                @endif

									</div>
								</div>
							</div>
							<!--------- Codigo viejo
							<div class="row">
								<div class="table-responsive">
									<table class="table">
										<thead>
											<tr>
											    <th>Codigo</th>
												<th>Producto</th>
												<th>Cantidad</th>
												<th>Costo </th>
												<th>IVA %</th>
												<th>IVA($)</th>
												<th class="text-end">Costo total ($)	</th>
												@if($proveedor_id == 2)
												<th style="text-align:text-center !important;">Estado</th>
												@endif
												<th></th>
											</tr>
										</thead>
										<tbody>
										    <?php $i = 1; ?>
										    @foreach($dci as $dc)
										    <tr>
												<td>
												{{$dc->barcode}}
												</td>
												<td>
												<a href="javascript:void(0);">{{$dc->nombre}}</a>
												</td>
												<td>
                                                <input  type="number" type="number" class="boton-editar"  {{$proveedor_id == 2 ? 'disabled' : '' }} value="{{number_format($dc->cantidad,0)}}" id="qty{{$dc->id}}"
                                                wire:change="updateQty('{{$dc->id}}|{{$dc->referencia_variacion}}', $('#qty' + {{$dc->id}}).val() )" min="1" onchange="Update({{$dc->id}});" >
                                                </td>
												<td>
												<div>
                                                <input type="number" class="boton-editar"  {{$proveedor_id == 2 ? 'disabled' : '' }} value="{{$dc->precio }}" id="price{{$dc->id}}"
                                                wire:change="updatePrice({{$dc->id}}, $('#price' + {{$dc->id}}).val() )" min="1" >
                                                </div>    
												</td>
												<td>{{number_format($dc->alicuota_iva*100,2)}}</td>
												<td>{{($dc->precio*$dc->cantidad)*($dc->alicuota_iva)}}</td>
												
												<td class="text-end">{{number_format(($dc->precio*$dc->cantidad)*(1+$dc->alicuota_iva),2)}}</td>
												<td >
												    @if($dci->count() == 1)
													<a  {{$proveedor_id == 2 ? 'hidden' : '' }} onclick="ConfirmDeleteLast({{$dc->id}})"><img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="svg"></a>
													@else
													<a  {{$proveedor_id == 2 ? 'hidden' : '' }} onclick="ConfirmDelete({{$dc->id}})"><img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="svg"></a>
													@endif
												</td>
												@if($proveedor_id == 2)
												<td style="text-align:text-center !important;">
												
												@if($dc->estado == 1)
                                                <p @if(!$columns['entrega_parcial']) style="display: none;" @endif>
                                                <text style="border: solid 1px #28a745; border-radius:4px; padding: 4px 15px; color: #28a745;">Entregado</text>    
                                                </p>
                                                @else
                                                <p @if(!$columns['entrega_parcial']) style="display: none;" @endif>
                                                <text style="border: solid 1px #ffc107; border-radius:4px; padding: 4px 15px; color: #ffc107;">No entregado</text>    
                                                </p>
                                                @endif
                                                
												</td>
												@endif
											</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-12 float-md-right">
									<div class="total-order">
									    @foreach ($total as $t)
										<ul>
											<li>
												<h4>Subtotal</h4>
												<h5>$ {{number_format($t->subtotal,2)}}</h5>
											</li>
										
											<li>
												<h4>- Descuento</h4>
												<h5>$ {{number_format($t->descuento,2)}} ({{number_format($t->porcentaje_descuento*100,2)}}%)</h5>
											</li>
											<li>
												<h4>IVA</h4>
												<h5>$ {{number_format($t->iva,2)}} ({{number_format($t->alicuota_iva*100,2)}}%)</h5>
											</li>
											<li>
												<h4>Recargo</h4>
												<h5>$ {{number_format($t->recargos,2)}} </h5>
											</li>
											
											<li class="total">
												<h4>Total</h4>
												<h5>$ {{number_format($t->total,2)}}</h5>
											</li>
											<li class="total">
												<h4>Deuda</h4>
												<h5>$ {{number_format($t->deuda,2)}}</h5>
											</li>
										</ul>
										@endforeach
									</div>
								</div>
							</div>
							
							--------->
														<div class="row">
								<div class="table-responsive">
									<table class="table">
										<thead>
											<tr>
											    <th>Codigo</th>
												<th>Producto</th>
												<th>Cantidad</th>
												<th>Costo </th>
												<th>Actualizacion </th>
												<th>IVA %</th>
												<th>IVA($)</th>
												<th class="text-end">Costo total ($)	</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
										    <?php $i = 1; ?>
										    @foreach($dci as $dc)
										    <tr>
												<td>
												{{$dc->barcode}}
												</td>
												<td>
												<a href="javascript:void(0);">{{$dc->nombre}}</a>
												</td>
												<td>
                                                <input  type="number" type="number" class="boton-editar"  {{$proveedor_id == 2 ? 'disabled' : '' }} value="{{number_format($dc->cantidad,0)}}" id="qty{{$dc->id}}"
                                                wire:change="updateQty('{{$dc->id}}|{{$dc->referencia_variacion}}', $('#qty' + {{$dc->id}}).val() )" min="1" onchange="Update({{$dc->id}});" >
                                                </td>
												<td>
												<div>
                                                <input type="number" class="boton-editar"  {{$proveedor_id == 2 ? 'disabled' : '' }} value="{{$dc->precio }}" id="price{{$dc->id}}"
                                                wire:change="updatePrice({{$dc->id}}, $('#price' + {{$dc->id}}).val() )" min="1" >
                                                </div>    
												</td>
												<td>{{number_format($dc->actualizacion*100,0)}} %</td>
												<td>{{number_format($dc->alicuota_iva*100,2)}}</td>
												<td>{{($dc->precio*$dc->cantidad)*($dc->alicuota_iva)}}</td>
												
												<td class="text-end">
												@if($dc->precio_final == 0)    
												    {{number_format(($dc->precio*$dc->cantidad)*(1+$dc->alicuota_iva)*(1+$dc->actualizacion),2)}}
												@else
												    {{number_format(($dc->precio_final*$dc->cantidad)*(1+$dc->alicuota_iva),2)}}
												@endif
												</td>
												<td >
												    @if($dci->count() == 1)
													<a  {{$proveedor_id == 2 ? 'hidden' : '' }} onclick="ConfirmDeleteLast({{$dc->id}})"><img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="svg"></a>
													@else
													<a  {{$proveedor_id == 2 ? 'hidden' : '' }} onclick="ConfirmDelete({{$dc->id}})"><img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="svg"></a>
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
									    @foreach ($total as $t)
										<ul>
											<li>
												<h4>Subtotal</h4>
												<h5>$ {{number_format($t->subtotal,2)}}</h5>
											</li>
											<li>
												<h4>Actualizacion</h4>
												<h5>$ {{number_format($t->actualizacion,2)}}</h5>
											</li>
											<li>
												<h4>- Descuento</h4>
												<h5>$ {{number_format($t->descuento,2)}} ({{number_format($t->porcentaje_descuento*100,2)}}%)</h5>
											</li>
											<li>
												<h4>IVA</h4>
												<h5>$ {{number_format($t->iva,2)}} ({{number_format($t->alicuota_iva*100,2)}}%)</h5>
											</li>
											<li>
												<h4>Recargo</h4>
												<h5>$ {{number_format($t->recargos,2)}} </h5>
											</li>
											
											<li class="total">
												<h4>Total</h4>
												<h5>$ {{number_format($t->total,2)}}</h5>
											</li>
											<li class="total">
												<h4>Deuda</h4>
												<h5>$ {{number_format($t->deuda,2)}}</h5>
											</li>
										</ul>
										@endforeach
									</div>
								</div>
							</div>
							<div  class="row">
								<div class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label>Descuento</label>
									<div class="input-group input-group-md mb-0" style="width:100%;">
            						<input  {{$proveedor_id == 2 ? 'disabled' : '' }} type="number" id="descuento" wire:model="porcentaje_descuento"
            						wire:keydown.enter="updateDescuentoGral($('#descuento').val() )"
            						wire:change="updateDescuentoGral($('#descuento').val() )"
            						class="form-control text-center" min="0" value="${{floatval($porcentaje_descuento)}}"
            						>
            						<div class="input-group-append">
            						<span class="input-group-text" style="background-color: #e9ecef; color: #212529; border: 1px solid #ced4da;">
            						 %
            						</span>
            						</div>
            						</div>
									</div>
								</div>
								<div class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label>IVA</label>
											<select {{ (auth()->user()->sucursal == 1 && $proveedor_id == 2) ? 'disabled' : '' }}  wire:model="iva_compra" wire:change="UpdateIvaGral"  class="form-control">
											<option value="0.000">Sin IVA</option>
											<option value="0.105">10.5%</option>
											<option value="0.210">21%</option>
											<option value="0.270">27%</option>
											</select>
									</div>
								</div>
								<div hidden class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label>Costo de envio</label>
										<input type="number" class="form-control">
									</div>
								</div>
								<div hidden class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label>Estado</label>
										<select class="form-control">
											<option>Elegir estado</option>
											<option>Pendiente</option>
											<option>Entregado</option>
											<option>Cancelado</option>
										</select>
									</div>
								</div>
								
								
								@foreach ($total as $t)
								<div class="col-lg-12">
									<div class="form-group">
										<label>Observaciones</label>
										<textarea  {{$proveedor_id == 2 ? 'disabled' : '' }} wire:model="observacion" wire:change="CambiarObservacion()" class="form-control"></textarea>
									</div>
								</div>
								@endforeach
								
								<!----------- Codigo viejo
								<div class="row">
								<div class="col-lg-8 col-sm-8 col-12">
								<br>    
								<label><strong>Pagos</strong></label>
								<br>
								<div class="table-responsive">
										<table class="table mb-0">
											<thead>
												<tr>
													<th>Caja</th>
                                                    <th>Fecha</th>
                                                    <th>Metodo de pago</th>
                                                    <th>Pago</th>
                                                    <th>Total</th>
                                                    <th></th>
												</tr>
											</thead>
											<tbody>
											  @foreach($pagos2 as $p2)
                                              @if ($p2->count() > 0)
												<tr>
													<td>
													
													@if($p2->nro_caja != null)
                                                    Caja # {{$p2->nro_caja}}
                                                    @else
                                                    No asociado a caja
                                                    @endif
                                                    
                                                    </td>
													<td>{{\Carbon\Carbon::parse( $p2->fecha_pago)->format('d-m-Y')}}</td>
													<td>{{$p2->metodo_pago}}</td>
													<td>$ {{number_format($p2->monto_compra,2) }}</td>
													<td>$ {{number_format($p2->monto_compra,2) }}</td>
													<td>
													@foreach($total as $t)
                            
                                                    @if($t->sale_casa_central == null)
                                                    <a href="javascript:void(0)" wire:click="EditPago({{$p2->id}})" >
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                    </a>
                
                
                                                    <a href="javascript:void(0)" onclick="ConfirmPago({{$p2->id}})" >
                                                       <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                                                    </a>
                                                    @endif
                                                     
                                                    @endforeach
                                                    </td>
												</tr>
											  @else
                                              No hay pagos relacionados con esta compra
                                              
                                              @endif
                                              
                                              @endforeach
											</tbody>
											<tfoot>
                                              <tr>
                                                  <th>Total </th>
                                                  <th> </th>
                                                  <th> </th>
                                                  <th> </th>
                                                  <th>$ {{number_format($suma_monto,2)}}</th>
                                                  <th> </th>
                                              </tr>
                                          </tfoot>
										</table>
									   @foreach($total as $t)
                                       @if($t->sale_casa_central == null)
                                       <div class="form-group">
                                           
                                       @if(0 <= $t->deuda)
                                       <a href="javascript:void(0);" wire:click.prevent="AgregarPago({{$ventaId}})">Agregar pago </a>
                                       @else
                                       <a href="javascript:void(0);" onclick="MensajeAgregarPago({{$ventaId}})">Agregar pago </a>
                                       @endif
                                       </div>
                                       @endif
                                       @endforeach

									</div>
								</div>
								
							<br><br>
							<br><br>
							</div>
							    ------------->
							    
							    								
								<div class="row">
								<div class="col-lg-8 col-sm-8 col-12">
								<br>    
								<label><strong>Pagos</strong></label>
								<br>
								<div class="table-responsive">
										<table class="table mb-0">
											<thead>
												<tr>
													<th>Caja</th>
                                                    <th>Fecha</th>
                                                    <th>Metodo de pago</th>
                                                    <th>Pago</th>
                                                    <th>Actualizacion</th>
                                                    <th>Total</th>
                                                    <th></th>
												</tr>
											</thead>
											<tbody>
											  @foreach($pagos2 as $p2)
                                              @if ($p2->count() > 0)
												<tr>
													<td>
													
													@if($p2->nro_caja != null)
                                                    Caja # {{$p2->nro_caja}}
                                                    @else
                                                    No asociado a caja
                                                    @endif
                                                    
                                                    </td>
													<td>{{\Carbon\Carbon::parse( $p2->fecha_pago)->format('d-m-Y')}}</td>
													<td>{{$p2->metodo_pago}}</td>
													<td>$ {{number_format($p2->monto_compra,2) }}</td>
													<td>$ {{number_format($p2->actualizacion*100,0) }} %</td>
													<td>$ {{number_format($p2->monto_compra*(1+$p2->actualizacion),2) }}</td>
													<td>
													@foreach($total as $t)
                            
                                                    @if($t->sale_casa_central == null)
                                                    <a href="javascript:void(0)" wire:click="EditPago({{$p2->id}})" >
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                    </a>
                
                
                                                    <a href="javascript:void(0)" onclick="ConfirmPago({{$p2->id}})" >
                                                       <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                                                    </a>
                                                    @endif
                                                     
                                                    @endforeach
                                                    </td>
												</tr>
											  @else
                                              No hay pagos relacionados con esta compra
                                              
                                              @endif
                                              
                                              @endforeach
											</tbody>
											<tfoot>
                                              <tr>
                                                  <th>Total </th>
                                                  <th> </th>
                                                  <th> </th>
                                                  <th></th>
                                                  <th> </th>
                                                  <th>$ {{number_format($suma_monto,2)}}</th>
                                                  <th> </th>
                                              </tr>
                                          </tfoot>
										</table>
									   @foreach($total as $t)
                                       @if($t->sale_casa_central == null)
                                       <div class="form-group">
                                           
                                       @if(0 <= $t->deuda)
                                       <a href="javascript:void(0);" wire:click.prevent="AgregarPago({{$ventaId}})">Agregar pago </a>
                                       @else
                                       <a href="javascript:void(0);" onclick="MensajeAgregarPago({{$ventaId}})">Agregar pago </a>
                                       @endif
                                       </div>
                                       @endif
                                       @endforeach

									</div>
								</div>
								
							<br><br>
							<br><br>
							</div>
							
								<div class="col-lg-12">
									<a href="javascript:void(0)" wire:click="CerrarModal" class="btn btn-cancel">Volver</a>
								</div>
							</div>
						</div>
					</div>    

