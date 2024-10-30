
					<!-- /add -->
					<div class="card">
						<div class="card-body">
							<div class="row">
							<div class="col-lg-6">
							<br>
							<h6>Vendedor: {{$datos_vendedor->name}}</h6>
							<p>Desde: {{ \Carbon\Carbon::parse($from)->format('d/m/Y') }} - Hasta: {{ \Carbon\Carbon::parse($to)->format('d/m/Y') }}</p>

							<br>
                            
							</div>
							
							<div class="col-lg-6">
							<div style="border: solid 1px #eee; border-radius: 5px; text-align: center; color: #5f6f7d;" >
							<br>
							<h6>Total Comisiones:  $ {{  number_format($total_comisiones,2,",",".")  }}</h6>
							<br>							    
							</div>

                            
							</div>
							
							<div class="col-lg-12">
							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
											<th>Nro Venta</th>
                                            <th>Total venta</th>
                                        	<th>% Comision</th>
                                        	<th>Comision</th>
                                        	<th>Acciones</th>
										</tr>
									</thead>
									<tbody>
									    @foreach( $listado_ventas as $venta)
												<tr>
													<td>{{$venta->nro_venta}}</td>
													<td>$ {{ number_format($venta->total,2)}}</td>
													<td>{{ number_format($venta->alicuota_comision*100,0)}} %</td>
													<td>$ {{ number_format($venta->comision,2)}}</td>
													<td>
        												<a target="_blank" href="https://app.flamincoapp.com.ar/reports?venta_id={{$venta->id}}" class="me-3" href="javascript:void(0)">
        													<img src="{{ asset('assets/pos/img/icons/eye.svg') }}" alt="img">
        												</a>
													</td>
												</tr>
										@endforeach
									</tbody>
								</table>
							</div>
									
							</div>

								
							<div class="col-lg-12">
							<br><br>
                            <a href=" {{ url('comisiones-resumen') }}" class="btn btn-cancel">VOLVER</a>
									
							</div>
							</div>
						</div>
					</div>
					<!-- /add -->
