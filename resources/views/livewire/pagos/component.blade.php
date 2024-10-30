<div >	                

	                <div class="page-header">
					<div class="page-title">
							<h4>Ingresos y Egresos</h4>
							<h6>Ver listado de movimientos de dinero <button hidden wire:click="SetIvaProductos">.</button></h6>
						</div>
						<div class="page-btn">               											    
                			<a href="{{ url('movimiento-dinero-cuentas') }}" class="btn btn-added"><img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Movimiento entre cuentas</a>
						</div>
					</div>
					
                    
					<!-- /product list -->
					<div class="card">
				
						<div class="card-body">
							<div class="table-top">
								<div class="search-set">
									<div class="search-path">
										@include('common.boton-filtros')
									</div>
									<button hidden class="btn btn-dark" wire:click="SetearSaldosIniciales">Setear</button>
									<div hidden class="search-input">
										<a class="btn btn-searchset"><img src="{{ asset('assets/pos/img/icons/search-white.svg') }}" alt="img"></a>
									</div>
								</div>
                                <div class="wordset">
									<ul>
										<li>
										    <a 
											style="font-size:12px !important; padding:5px !important; background: #198754 !important;" 
											class="btn btn-cancel" 
											wire:click="ExportarExcel"  title="Descargar listado de movimientos de dinero"  data-bs-placement="top" title="exportar excel"> 
											<svg style="margin-right: 5px;"  xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
											Exportar </a>
											
											<a hidden href="javascript:void(0)" wire:click="ExportarExcel"  title="Descargar listado de movimientos de dinero"><img  src="{{ asset('assets/pos/img/icons/excel.svg') }}" alt="img"></a>
										</li>
									</ul>
								</div>
							</div>
							                											    
                		     <!-- /Filter -->
							    
							<div class="card mb-0"  @if(!$mostrarFiltros) hidden @endif >
								<div class="card-body pb-0">
									<div class="row">
										<div class="col-lg-12 col-sm-12">
											<div class="row">
										         
										         <div class="row">
												
												<div class="col-lg-3 col-sm-6 col-12">
													<div class="form-group">
														<label>Buscar</label>
                                    					<input type="text" autocomplete="off" wire:model="search" placeholder="Buscar.." class="form-control"	>
													</div>
												</div>   
												
												@if(Auth::user()->id == Auth::user()->casa_central_user_id)							
												<div class="col-lg-3 col-sm-6 col-12">
													<div class="form-group">
														<label>Sucursal</label>
                                    					<select wire:model='sucursal_id' class="form-control">
                                    						<option value="0" >Todas</option>
                                    						@foreach($sucursales_lista as $s)
                                    							<option value="{{$s->id}}" >{{$s->name}}</option>
                                    						@endforeach
                                    					</select>
													</div>
												</div>
												@endif
												
												<div class="col-lg-3 col-sm-6 col-12">
													<div class="form-group">
														<label>Tipo movimiento</label>
                                    					<select wire:model='tipo_movimiento_filtro' class="form-control">
                                    						<option value="">Todas</option>
                                    						<option value="ingreso">Ingreso</option>
                                    						<option value="egreso">Egreso</option>
                                    						
                                    					</select>
													</div>
												</div>   
												
												<div class="col-lg-3 col-sm-6 col-12">
													<div class="form-group">
														<label>Operacion</label>
                                    					<select wire:model='operacion_filtro' class="form-control">
                                    						<option value="">Todas</option>
                                    						<option value="Venta">Venta</option>
                                    						<option value="Compra">Compra</option>
                                    						<option value="Gastos">Gastos</option>
                                    						<option value="Ingresos">Ingresos</option>
                                    						<option value="Pago saldo cliente">Pago saldo cliente</option>
                                    						<option value="Pago saldo proveedor">Pago saldo proveedor</option>
                                    						<option value="Movimiento">Movimiento entre cuentas</option>
                                    						
                                    						
                                    						
                                    					</select>
													</div>
												</div>   
												
												<div class="col-lg-3 col-sm-6 col-12">
													<div class="form-group">
														<label>Cuenta</label>
                                    					<select wire:model='banco_filtro' class="form-control">
                                    						<option value="" >Todas</option>
                                    						<option value="1" >Efectivo</option>
                                    						@foreach($bancos as $b)
                                    							<option value="{{$b->id}}" >{{$b->nombre}}</option>
                                    						@endforeach
                                    					</select>
													</div>
												</div>
												<div hidden class="col-lg-3 col-sm-6 col-12">
													<div class="form-group">
														<label>Metodo de pago</label>
                                    					<select wire:model='metodo_pago_filtro' class="form-control">
                                    						<option value="">Todas</option>
                                    						@foreach($metodo_pago as $mp)
                                    							<option value="{{$mp->id}}" >{{$mp->nombre_metodo}}</option>
                                    						@endforeach
                                    					</select>
													</div>
												</div>
												
												<div class="col-lg-3 col-sm-6 col-12">
													<div class="form-group">
														<label>Estado de pago</label>
                                    					<select wire:model='estado_pago' class="form-control">
                                    						<option value="">Todas</option>
                                    						<option value="1">Acreditado</option>
                                    						<option value="0">Pendiente</option>
                                    					</select>
													</div>
												</div>
												
												<div class="col-lg-3 col-sm-6 col-12">
												<div class="form-group">
												<label>Fecha</label>
                                                <input type="text" id="date-range-picker" name="date_range" />
        
												</div>
												</div>
												
												
												
												<div class="col-lg-3 col-sm-6 col-12">
													<div class="form-group">
													    <label style="margin-top: 28px !important;"></label>
													    <button style="background: white !important; " class="btn btn-light ms-auto" wire:click="LimpiarFiltros()" >
													     LIMPIAR
													    </button>
													</div>
												</div>
										         </div>
												

												

											</div>
										</div>
									</div>
								</div>
							</div>
							    
							
							 
							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
											<th>
										    </th>
											<th>Id</th>
											<th>Estado</th>
											<th>Fecha</th>
                                            <th>Tipo</th>
                                            <th>Referencia externa</th>
                                            <th>Pago ($)</th>
                                            <th>Comisiones / Deduccion ($)</th>
                                            <th>Monto final ($)</th>
                                            <th>Sucursal</th>
                                            <th>Banco</th>
                                            <th>Metodo pago</th>
                                            <th>Nro caja</th>
                                            <th>Cliente</th>
                                            <th>Proveedor</th>
                                            <th>Nro comprobante</th>
                                            <th>Link comprobante</th>
										</tr>
									</thead>
									<tbody>
									    @foreach($datos_pagos as $dp)
										<tr>
											<td>
										    
										    @if($dp->estado_pago != 3)
										    <a style="color: black !important; background: #FAFBFE !important; padding: 1px 8px; border-radius: 8px; border: 1px solid #E9ECEF;" href="javascript:void(0)" class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"></a>
                                            <div class="dropdown-menu">
                                            <button wire:click="VerPago({{$dp->id}},'{{$dp->tipo}}')" class="dropdown-item">Ver</button>
                                            <button hidden wire:click="CambiarEstadoPago({{$dp->id}})" class="dropdown-item">Cambiar Estado</button>
                                            @if($dp->tipo != "ingreso_retiro")
                                            <button wire:click="EditarPago({{$dp->id}},'{{$dp->tipo}}')" class="dropdown-item">Editar</button>
                                            @endif
                                            
                                            <button hidden wire:click="CambiarEstadoPago({{$dp->id}})" class="dropdown-item">Eliminar</button>
                                            </div>    
											@endif
											
											</td>
											<td>{{ $dp->id }}</td>
											<td>
	                                        @if($dp->estado_pago == 0)
                                            <a href="javascript:void(0)" class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span style="cursor:pointer;" class="badges bg-lightred">Pendiente</span>
                                            </a>
                                            <div class="dropdown-menu">
                                            <button wire:click="CambiarEstadoPago({{$dp->id}},0,'{{$dp->tipo}}')" class="dropdown-item">Pendiente <svg style="margin-left: 8px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#637381" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg></button>
                                            <button wire:click="CambiarEstadoPago({{$dp->id}},1,'{{$dp->tipo}}')" class="dropdown-item">Acreditado</button>
                                            </div> 
                                            @endif
                                            @if($dp->estado_pago == 1)
                                            <a href="javascript:void(0)" class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span style="cursor:pointer;" class="badges bg-lightgreen">Acreditado</span>
                                            </a>
                                            <div class="dropdown-menu">
                                            <button wire:click="CambiarEstadoPago({{$dp->id}},0,'{{$dp->tipo}}')" class="dropdown-item">Pendiente</button>
                                            <button wire:click="CambiarEstadoPago({{$dp->id}},1,'{{$dp->tipo}}')" class="dropdown-item">Acreditado <svg style="margin-left: 8px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#637381" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg></button>
                                            </div> 
                                            
                                            @endif
                                            @if($dp->estado_pago == 2)
                                            <span class="badges bg-lightgrey">Saldo inicial</span>
                                            @endif
                                            @if($dp->estado_pago == 3)
                                            <span class="badges bg-lightgreen">Acreditado</span>
                                            @endif
											</td>
                                            <td>{{\Carbon\Carbon::parse($dp->created_at)->format('d/m/Y')}}</td>
                                            <td>{{ $dp->tipo_movimiento }}</td>
                                            <td>{{ $dp->tipo }} {{ $dp->referencia_id }}</td>
                                            <td>$ {{ number_format($dp->monto,2,",",".") }}</td>
                                            <td>$ {{ number_format($dp->deducciones,2,",",".") }}</td>
                                            <td>$ {{ number_format($dp->monto-$dp->deducciones,2,",",".") }}</td>
                                            <td>{{ $dp->sucursal }}</td>
                                            <td>{{ $dp->banco }}</td>
                                            <td>{{ $dp->metodo_pago }}</td>
                                            <td>{{ $dp->caja }}</td>
                                            <td>{{ $dp->cliente }}</td>
                                            <td>{{ $dp->proveedor }}</td>
                                            <td>{{ $dp->nro_comprobante }}</td>
                                            <td>
                                            
                                            @if($dp->url_comprobante != null)
														<a href="{{ asset('storage/comprobantes/' . $dp->url_comprobante) }}" target="_blank" >
														<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
														</a>
											@endif
                                            </td>
								
										</tr>
										@endforeach
									</tbody>
								</table>
								<br>
								{{$datos_pagos->links()}}
								<br>
							</div>
						</div>
					</div>

					@include('livewire.pagos.cambiar-estado')
					@include('livewire.pagos.ver-pago')
					
					</div>
					
<script>
					    
	function Confirm(id) {

		swal({
			title: 'CONFIRMAR',
			text: '¿CONFIRMAS ELIMINAR EL REGISTRO?',
			type: 'warning',
			showCancelButton: true,
			cancelButtonText: 'Cerrar',
			cancelButtonColor: '#fff',
			confirmButtonColor: '#3B3F5C',
			confirmButtonText: 'Aceptar'
		}).then(function(result) {
			if (result.value) {
				window.livewire.emit('deleteRow', id)
				swal.close()
			}

		})
	}
</script>

<script type="text/javascript">

	function RestaurarCategoria(id) {

    swal({
      title: 'CONFIRMAR',
      text: 'QUIERE RESTAURAR LA CATEGORIA?',
      showCancelButton: true,
      cancelButtonText: 'Cancelar',
      cancelButtonColor: '#fff',
      confirmButtonColor: '#3B3F5C',
      confirmButtonText: 'Aceptar'
    }).then(function(result) {
      if (result.value) {
        window.livewire.emit('RestaurarCategoria', id)
        swal.close()
      } 

    })
  }

</script>
<script>
    	document.addEventListener('DOMContentLoaded', function() {

		window.livewire.on('cambiar-estado', msg => {
			$('#CambiarEstado').modal('show')
		});
		window.livewire.on('cambiar-estado-hide', msg => {
			$('#CambiarEstado').modal('hide')
		});
		window.livewire.on('ver-pago', msg => {
			$('#VerPago').modal('show')
		});
		window.livewire.on('ver-pago-hide', msg => {
			$('#VerPago').modal('hide')
		});
		window.livewire.on('noty-estado', msg => {
			noty(msg)
		});



	});

</script>