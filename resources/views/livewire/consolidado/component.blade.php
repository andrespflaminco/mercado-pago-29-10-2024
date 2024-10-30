<div>	  
	<div class="page-header">
		<div class="page-title">
			<h4>Saldos disponibles</h4>
			<h6>Ver listado de disponibilidades</h6>
		</div>
		<div class="page-btn  d-lg-flex d-sm-block">
			<a hidden href="javascript:void(0)" class="btn btn-added" wire:click.prevent="AgregarCajaAnteriorModal()">
				<img src="{{ asset('assets/pos/img/icons/plus.svg') }}" alt="img" class="me-1">AGREGAR CAJA ANTERIOR
			</a>
		</div>
	</div>
	
	<!-- /product list -->
	<div class="card">
		<div class="card-body">
			<div class="row">
				<!----- BANCOS ------->
				<div class="col-6">
					<div class="table-responsive" style="height:220px !important;">
						<table class="table" style="margin: 20px !important;">
							<thead style="background: #1987541c !important;">
								<tr>
									<th>A COBRAR</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>
										<a style="color: #637381 !important;" href="https://app.flamincoapp.com.ar/ctacte-clientes{{ $es_sucursal == 0 ? '&sucursal_id=0' : '' }}" target="_blank"> Saldo en cta cte de clientes</a>
									</td>
									<td> $ {{ number_format($cta_cte_clientes,2,",",".") }}</td>
								</tr>
								@foreach($bancos_pendiente as $bp)
								<tr>
									<td>
										<a style="color: #637381 !important;" href="https://app.flamincoapp.com.ar/pagos?estado_pago=0&banco={{$bp->id}}&tipo_movimiento=ingreso{{ $es_sucursal == 0 ? '&sucursal_id=0' : '' }}" target="_blank"> {{$bp->banco}} </a>
									</td>
									<td>$ {{ number_format($bp->total+$bp->recargo+$bp->total_ingreso_retiro,2,",",".") }}</td>
								</tr>
								@endforeach
								@foreach($plataformas_pendiente as $pp)
								<tr>
									<td>
										<a style="color: #637381 !important;" href="https://app.flamincoapp.com.ar/pagos?estado_pago=0&banco={{$pp->id}}&tipo_movimiento=ingreso{{ $es_sucursal == 0 ? '&sucursal_id=0' : '' }}" target="_blank"> {{$pp->banco}} </a>
									</td>
									<td>$ {{ number_format($pp->total+$pp->recargo+$pp->total_ingreso_retiro,2,",",".") }}</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
				
				<!-------- / A COBRAR  --------->
				
				<!-------- A PAGAR ------->
				<div class="col-6">
					<div class="table-responsive" style="height:220px !important;">
						<table class="table" style="margin: 20px !important;">
							<thead style="background: #ea54552b !important;">
								<tr>
									<th>A PAGAR</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>
										<a style="color: #637381 !important;" href="https://app.flamincoapp.com.ar/ctacte-proveedores{{ $es_sucursal == 0 ? '&sucursal_id=0' : '' }}" target="_blank"> Saldo en cta cte de proveedores</a>
									</td>
									<td>$ {{ number_format($cta_cte_proveedores,2,",",".") }}</td>
								</tr>
								@foreach($bancos_pendiente_pagar as $bpp)
								<tr>
									<td>
										<a style="color: #637381 !important;" href="https://app.flamincoapp.com.ar/pagos?estado_pago=0&banco={{$bpp->id}}&tipo_movimiento=egreso{{ $es_sucursal == 0 ? '&sucursal_id=0' : '' }}" target="_blank"> {{$bpp->banco}} </a>
									</td>
									<td>$ {{ number_format($bpp->total_compra+$bpp->total_gasto,2,",",".") }}</td>
								</tr>
								@endforeach
								@foreach($plataformas_pendiente_pagar as $ppp)
								<tr>
									<td>
										<a style="color: #637381 !important;" href="https://app.flamincoapp.com.ar/pagos?estado_pago=0&banco={{$ppp->id}}&tipo_movimiento=egreso{{ $es_sucursal == 0 ? '&sucursal_id=0' : '' }}" target="_blank"> {{$ppp->banco}} </a>
									</td>
									<td>$ {{ number_format($ppp->total_compra+$ppp->total_gasto,2,",",".") }}</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
				
				<!----- / A PAGAR -------->
				
				<br><br>
				
				<!-------- DISPONIBILIDADES ------->
				<div class="col-12 mt-4">
					<div class="table-responsive" style="height:220px !important;">
						<table class="table">
							<thead style="background: #1414320d !important;">
								<tr>
									<th>DISPONIBILIDADES</th>
									<th></th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<!-------- EFECTIVO  ------------->
									<td>
										<div class="table-responsive" style="height:190px !important;">
											<table class="table" style="margin: 20px !important;">
												<thead style="background: transparent !important;">
													<tr>
														<th> <b>EFECTIVO</b></th>
														<th></th>
													</tr>
												</thead>
												<tbody>
													@foreach($efectivo_disponible as $ed)
													<tr>
														<td>
															<a style="color: #637381 !important;" href="https://app.flamincoapp.com.ar/pagos?estado_pago=1&banco=1" target="_blank"> Caja </a>
														</td>
														<td>$ {{ number_format($ed->total-$ed->total_compras-$ed->total_gastos+$ed->recargo+$ed->total_ingreso_retiro,2,",",".") }}</td>
													</tr>
													@endforeach
												</tbody>
											</table>
										</div>
									</td>
									
									<!---------- BANCOS -------------->
									<td>
										<div class="table-responsive" style="height:190px !important;">
											<table class="table" style="margin: 20px !important;">
												<thead style="background: transparent !important;">
													<tr>
														<th><b>BANCOS</b></th>
														<th></th>
													</tr>
												</thead>
												<tbody>
													@foreach($bancos_disponible as $b)
													<tr>
														<td>
															<a style="color: #637381 !important;" href="https://app.flamincoapp.com.ar/pagos?estado_pago=1&banco={{$b->id}}{{ $es_sucursal == 0 ? '&sucursal_id=0' : '' }}" target="_blank"> {{$b->banco}} </a>
														</td>
														<td>$ {{ number_format($b->saldo_inicial+$b->total-$b->total_compras-$b->total_gastos+$b->recargo+$b->total_ingreso_retiro,2,",",".") }}</td>
													</tr>
													@endforeach
												</tbody>
											</table>
										</div>
									</td>
									
									<!---------- PLATAFORMAS -------------->
									<td>
										<div class="table-responsive" style="height:190px !important;">
											<table class="table" style="margin: 20px !important;">
												<thead style="background: transparent !important;">
													<tr>
														<th><b>PLATAFORMAS</b></th>
														<th></th>
													</tr>
												</thead>
												<tbody>
													@foreach($plataformas_disponible as $pl)
													<tr>
														<td>
															<a style="color: #637381 !important;" href="https://app.flamincoapp.com.ar/pagos?estado_pago=1&banco={{$pl->id}}{{ $es_sucursal == 0 ? '&sucursal_id=0' : '' }}" target="_blank"> {{$pl->banco}} </a>
														</td>
														<td>$ {{ number_format($pl->saldo_inicial+$pl->total-$pl->total_compras-$pl->total_gastos+$pl->recargo+$pl->total_ingreso_retiro,2,",",".") }}</td>
													</tr>
													@endforeach
												</tbody>
											</table>
										</div>
									</td>
									
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				
				<!-------- / DISPONIBILIDADES -------->
			</div>
		</div>
	</div>
</div>

	