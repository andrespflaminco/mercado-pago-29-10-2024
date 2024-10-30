
<div class="page-header">
							<div class="page-title">
								<h4>ACTUALIZACION DE COSTOS DE LA COMPRA #{{$Nro_Compra}}</h4>
								<h6>Edita y actualiza los costos de tus compras</h6>
							</div>
							<div class="page-btn">
							  
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
												<select disabled  wire:model="proveedor_id" wire:change="CambiarProveedor" class="form-control">
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
											<input disabled type="date" wire:model="fecha_compra" wire:change="CambiarFecha" class="form-control">
											</div>
										</div>
									</div>
								</div>
				
							</div>
							<div class="row">
								<div class="table-responsive">
									<table class="table">
										<thead>
											<tr>
											    <th>Codigo</th>
												<th>Producto</th>
												<th>Cantidad</th>
												<th>Costo original </th>
												<th>Actualizacion % </th>
												<th>Costo actual </th>
												<th>IVA %</th>
												<th>IVA($)</th>
												<th class="text-end">Costo total ($)	</th>
											</tr>
										</thead>
										<tbody>
										    <?php $i = 1; ?>
										    @foreach($detalles_actualizacion as $dc)
										    <tr>
												<td>
												{{$dc->barcode}}
												</td>
												<td>
												<a href="javascript:void(0);">{{$dc->nombre}}</a>
												</td>
												<td>
                                                {{$dc->cantidad}}
                                                </td>
												<td>
                                                {{$dc->precio * (1 + $dc->actualizacion) }}
												</td>
												<td>
												<div class="d-flex">
								                <div>
								                <input type="number" class="boton-editar"  {{$proveedor_id == 2 ? 'disabled' : '' }} value="{{$dc->actualizacion_nueva*100}}" id="actualizacion{{$dc->id}}"
                                                wire:change="updateActualizacionProducto({{$dc->id}}, $('#actualizacion' + {{$dc->id}}).val() )" >    
								                </div>	
								                <div>
								                %    
								                </div>
												</div>
												</td>
												<td>
												<input type="number" class="boton-editar"  {{$proveedor_id == 2 ? 'disabled' : '' }} value="{{$dc->costo_actual}}" id="costo_actual{{$dc->id}}"
                                                wire:change="updateCostoActualProducto({{$dc->id}}, $('#costo_actual' + {{$dc->id}}).val() )"  >    

												
												</td>
												<td>{{number_format($dc->alicuota_iva*100,2)}}</td>
												<td>{{($dc->precio*$dc->cantidad)*($dc->alicuota_iva)}}</td>
												
												<td class="text-end">{{number_format($dc->total_actual,2)}}</td>
											
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
												<h4>Subtotal</h4>
												<h5>$ {{number_format($subtotal,2)}}</h5>
											</li>
											<li>
												<h4>Actualizacion</h4>
												<h5>$ {{number_format($actualizacion,2)}}</h5>
											</li>
											<li>
												<h4>- Descuento</h4>
												<h5>$ {{number_format($descuento,2)}} ({{number_format($porcentaje_descuento*100,2)}}%)</h5>
											</li>
											<li>
												<h4>IVA</h4>
												<h5>$ {{number_format($iva,2)}} ({{number_format($alicuota_iva*100,2)}}%)</h5>
											</li>
											<li>
												<h4>Recargo</h4>
												<h5>$ {{number_format($recargos,2)}} </h5>
											</li>
											
											<li class="total">
												<h4>Total</h4>
												<h5>$ {{number_format($total_compra,2)}}</h5>
											</li>
											<li class="total">
												<h4>Deuda</h4>
												<h5>$ {{number_format($deuda,2)}}</h5>
											</li>
										</ul>
										
									</div>
								</div>
							</div>
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


									</div>
								</div>
								
							<br><br>
							<br><br>
							</div>
							
								<div class="col-lg-12">
									<a href="javascript:void(0)" wire:click="VerElegirTipoActualizacion" class="btn btn-cancel">VOLVER</a>
									<a href="javascript:void(0)" wire:click="GuardarActualizacion({{$ventaId}})" class="btn btn-submit">GUARDAR</a>
								</div>
							</div>
						</div>
					</div>    

