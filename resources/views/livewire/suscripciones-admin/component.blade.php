<div>
    	                <div class="page-header">
					<div class="page-title">
							<h4>Cobros de suscripciones</h4>
							<h6>Ver listado de suscripciones</h6>
						</div>
						<div class="page-btn">               											    
         		        <a href="javascript:void(0)" wire:click="ChequearEstadoPagoSuscripcionesVencidas()" class="btn btn-added">Actualizar</a>
					
						</div>
					</div>
					
                    
					<!-- /product list -->
					<div class="card">
				
						<div class="card-body">
							<div class="table-top">
								<div class="search-set">
									<div class="search-path">
										<a class="btn btn-filter" id="filter_search">
											<img src="{{ asset('assets/pos/img/icons/filter.svg') }}"  alt="img">
											<span><img src="{{ asset('assets/pos/img/icons/closes.svg') }}" alt="img"></span>
										</a>
									</div>
									<input type="text" autocomplete="off" wire:model="search" placeholder="Buscar.." class="form-control"	>
									<div hidden class="search-input">
										<a class="btn btn-searchset"><img src="{{ asset('assets/pos/img/icons/search-white.svg') }}" alt="img"></a>
									</div>
								</div>
								<div hidden class="wordset">
									<ul>
										<li>
											<a data-bs-toggle="tooltip" data-bs-placement="top" title="pdf"><img  src="{{ asset('assets/pos/img/icons/pdf.svg') }}"  alt="img"></a>
										</li>
										<li>
											<a data-bs-toggle="tooltip" data-bs-placement="top" title="excel"><img  src="{{ asset('assets/pos/img/icons/excel.svg') }}" alt="img"></a>
										</li>
										<li>
											<a data-bs-toggle="tooltip" data-bs-placement="top" title="print"><img  src="{{ asset('assets/pos/img/icons/printer.svg') }}" alt="img"></a>
										</li>
									</ul>
								</div>
							</div>
	
							 
							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
										
											<th>Cliente</th>
											<th>Plan elegido</th>
											<th>Estado suscripcion</th>
											<th>Estado de pago</th>
											<th>Pago hasta</th>
											<th>Acciones</th>
										</tr>
									</thead>
									<tbody>
									    @foreach($suscripcion as $s)
										<tr>
										
											<td >
											{{$s->comercio}}
											</td>
											<td>
											{{$s->plan_elegido}}
											</td>
											<td>
											{{$s->suscripcion_status}}
											</td>
											<td>
											{{$s->cobro_status}}
											</td>
											<td>
											     {{\Carbon\Carbon::parse($s->proximo_cobro)->format('d-m-Y')}}
											</td>
											<td>
												<a class="me-3 btn btn-dark text-white" href="javascript:void(0)" wire:click.prevent="ChequearEstadoPago({{$s->id}})" >
													CHEQUEAR
												</a>
											    
											</td>
										</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
					</div>
</div>