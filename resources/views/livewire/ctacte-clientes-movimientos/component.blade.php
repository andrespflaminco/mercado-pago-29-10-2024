<div>	                
                    
	                <div {{$id_venta != 0 ? 'hidden' : ''}}  class="page-header">
					<div class="page-title">
							<h4>Movimiento de cuenta corriente Cliente</h4>
							<h6>Ver detalle de movimientos del cliente</h6>
						</div>
						<div class="page-btn">               											    
                		
						    
						</div>
					</div>

				<select style="background-color: transparent !important; border:none !important;" wire:model="forma_calcular_totales">
				    <option value="Total adeudado">Total adeudado</option>
				    <option value="Deuda en Fecha filtrada">Deuda en Fecha filtrada</option>
				</select>
						    
                <div class="row">
						<div class="col-lg-3 col-sm-6 col-12">
							<div class="dash-widget">
								<div class="dash-widgetcontent">
									<h5  style="margin-bottom: 0px !important;"> $ {{ number_format($monto_saldo_inicial,2,",",".")  ?: 0}}</h5>
									<h6>Saldo inicial</h6>
								</div>
							</div>
						</div>
						<div class="col-lg-3 col-sm-6 col-12">
							<div class="dash-widget dash2">
								<div class="dash-widgetcontent">
									<h5   style="margin-bottom: 0px !important;" >$ {{number_format($venta_total,2,",",".")}} </h5>
									<h6>Ventas</h6>
								</div>
							</div>
						</div>
						<div class="col-lg-3 col-sm-6 col-12">
							<div class="dash-widget dash3">
								<div class="dash-widgetcontent">
									<h5  style="margin-bottom: 0px !important;" >$ {{number_format($pagos_total,2,",",".")}} </h5>
									<h6>Pagos</h6>
								</div>
							</div>
						</div>
						<div class="col-lg-3 col-sm-6 col-12">
							<div class="dash-widget dash1">
								<div class="dash-widgetcontent">
									<h5 style="margin-bottom: 0px !important;" >$ {{number_format($venta_total  + $monto_saldo_inicial - $pagos_total,2,",",".")}} </h5>
									<h6>Deuda</h6>
								</div>
							</div>
						</div>
					
					    </div>					
                    
					<!-- /product list -->
					<div {{$id_venta != 0 ? 'hidden' : ''}}  class="card">
				
						<div class="card-body">
							<div class="row">
									
									<div class="col-lg-3 col-sm-11">
									 <div class="form-group">
									 <label style="margin-left: 50px;" class="mb-0">Cliente</label>
								    <div class="input-group">
            						<div class="input-group-prepend">
            							<a class="btn btn-filter" style="background: #333 !important; color:white !important; margin-right: 15px;" href="{{ url('ctacte-clientes') }}" >
											<
									    </a>
            						</div>
								     <input readonly style="background: white !important; padding: .375rem .75rem; font-size: 1rem; font-weight: 400; line-height: 1.5;" type="text" value="{{$datos_cliente->nombre}}" class="form-control" />
                                   		    
									</div>									    
									</div>
									</div>
									
								    <div class="col-lg-3 col-sm-12">    
								    <div class="form-group">
								    <label class="mb-0">Operacion</label>
                                    <select style="min-width: 200px !important;" class="form-control" wire:model="filtro_operacion">
                                        <option value="0">Todos</option>
                                        <option value="1">Venta</option>
                                        <option value="2">Pago</option>
                                    </select>
                                    </div>
                                    </div>


								    <div class="col-lg-3 col-sm-12">
                                    <div class="form-group">
								    <label class="mb-0">Fecha</label>
                                    <input style="padding: .375rem .75rem; font-size: 1rem; font-weight: 400; line-height: 1.5;" type="text" id="date-range-picker" name="date_range" />
                                    </div>
                                    </div>
                                    
                                    <div class="col-lg-3 col-sm-12">
                                     <div class="form-group">
                                    <label class="mb-0">Exportar</label>
                                   	<a  style="font-size:12px !important; padding:5px !important; background: #198754 !important;" class="btn btn-cancel" wire:click="ExportarExcel('{{ $cliente_id }}')" data-bs-placement="top" title="exportar excel"> 
									<svg style="margin-right: 5px;"  xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
									Excel venta
									</a>
									
									<a  style="font-size:12px !important; padding:5px !important; background: #198754 !important;" class="btn btn-cancel" wire:click="ExportarExcelPorProducto('{{ $cliente_id }}')" data-bs-placement="top" title="exportar excel"> 
									<svg style="margin-right: 5px;"  xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
									Excel por productos
									</a>
											
			                      	<a target="_blank" style="font-size:12px !important; padding:5px !important; background: #dc3545 !important;" class="btn btn-cancel" wire:click="ExportarPDF('{{ $cliente_id }}')" data-bs-placement="top" title="exportar excel"> 
									<svg style="margin-right: 5px;"  xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
									PDF 
									</a>
									</div>
									</div>
                                    
    
								</div>
							
                            @if($id_venta == 0)
							<div class="table-responsive">
                                <table class="table">
                                    <thead>
                                 
                                        <tr>
                                            <th></th>
                                            <th>ID</th>
											<th>TIPO DE MOVIMIENTO</th>
											<th>FECHA</th>
											<th>VENTA</th>
											<th>PAGO</th>
											<th>ASOCIADO A</th>
											<th>MEDIO DE PAGO</th>
											<th>SUCURSAL</th>
											<th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
										@foreach($datos_cta_cte as $compra)
										
										@if($compra->monto > 0 || $compra->monto_pago > 0 || $compra->monto_saldo) 
											<tr>

														<td>
														{{$compra->sucursales_agregan_pago}}
                                                        
                                                        <a style="color: black !important; background: #FAFBFE !important; padding: 1px 8px; border-radius: 8px; border: 1px solid #E9ECEF;" href="javascript:void(0)" class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"></a>
                                                     	<div class="dropdown-menu">
                                                     	    
                                                    	<!----- BOTONES DE LA COMPRA ----->
                                                    
                                                    	@if($compra->monto > 0)
														<button wire:click="RenderFactura({{$compra->id_venta}})" class="dropdown-item">Ver venta</button>
														<button wire:click="VerPagos({{$compra->id_venta}})" class="dropdown-item">Ver pagos asociados</button>
														<button {{ ($compra->sucursal_id != $comercio_id) && ($sucursales_agregan_pago == 0) ? 'hidden' : ''}}  wire:click="AgregarPago({{$compra->id_venta}},1)" class="dropdown-item">Agregar pago</button>
														@endif
														
														<!----- / BOTONES DE LA COMPRA ----->
                                                    	
														@if($compra->id_saldo > 0)
													
														<!----- BOTONES DE SALDO INICIAL ----->
                                                    	
														@if($compra->monto_saldo > 0)
														<button wire:click="RenderSaldo({{$compra->id_saldo}})"  class="dropdown-item">Ver saldo inicial</button>
														<button {{ ($compra->sucursal_id != $comercio_id) && ($sucursales_agregan_pago == 0) ? 'hidden' : ''}}  wire:click="AgregarPagoSaldo()" class="dropdown-item">Agregar pago</button>
														@else
														<button wire:click="RenderSaldo({{$compra->id_saldo}})"  class="dropdown-item">Ver pago</button>
														<button {{ ($compra->sucursal_id != $comercio_id) && ($sucursales_agregan_pago == 0) ? 'hidden' : ''}}   wire:click="EditPagoSaldo({{$compra->id_saldo}})"  class="dropdown-item">Editar pago</button>
														<button {{ ($compra->sucursal_id != $comercio_id) && ($sucursales_agregan_pago == 0) ? 'hidden' : ''}}   onclick="ConfirmPagoSaldo({{$compra->id_saldo}})" class="dropdown-item">Eliminar pago</button>
														@endif
														@endif
                                                        
                                                    	<!----- / BOTONES DE SALDO INICIAL ----->
                                                    
                                                    
                                                    	<!----- BOTONES DEL PAGO ----->
                                                    
                                                        @if($compra->id_pago > 0)
                                                        <button wire:click="RenderPago({{$compra->id_pago}})"  class="dropdown-item">Ver pago</button>
                                                        <button {{ ($compra->sucursal_id != $comercio_id) && ($sucursales_agregan_pago == 0) ? 'hidden' : ''}}   wire:click="EditPago({{$compra->id_pago}},1)"   class="dropdown-item">Editar pago</button>
                                                        <button {{ ($compra->sucursal_id != $comercio_id) && ($sucursales_agregan_pago == 0) ? 'hidden' : ''}}   onclick="ConfirmPago({{$compra->id_pago}},1)" class="dropdown-item">Eliminar pago</button>
                                                      	@endif
														@if($compra->url_pago > 0)
														<a href="{{ asset('storage/comprobantes/' . $compra->url_pago) }}" target="_blank"  class="dropdown-item">Ver comprobante</a>
														@endif
                                                   
                                                    	<!----- / BOTONES DEL PAGO ----->
                                                   
                                                    	</div>
                                                    	
                                                    	</td>
                                                    	 
                                                        <td>
														@if($compra->monto > 0)
														{{$compra->nro_venta}}
														@endif


														@if($compra->monto_pago > 0)
														{{$compra->nro_pago}}
														@endif
													
													
														</td>
														
                                                    	<td>
														@if($compra->monto > 0)
														Venta 
														@endif


														@if($compra->monto_pago > 0)
														Pago
														@endif
																
																
														@if($compra->monto_saldo > 0)
														Saldo inicial
														@endif
																
														@if($compra->monto_saldo < 0)
														Pago de saldo inicial
														@endif

                                                    	
                                                    	
														</td>
                                                       
														
                                                        <td>
															{{\Carbon\Carbon::parse($compra->created_at)->format('d-m-Y')}}
														</td>
														
														<td>
														    @if($compra->monto > 0)
															 $ {{number_format($compra->monto,2,",",".") }}
															@endif
															
															@if($compra->monto_saldo > 0)
															$ {{number_format($compra->monto_saldo,2,",",".") }}
															@endif
														</td>
														<td>
														    @if($compra->monto_pago > 0)
															 $ {{number_format($compra->monto_pago,2,",",".") }}
															 @endif
															 @if($compra->monto_saldo < 0)
															  $ {{number_format($compra->monto_saldo*-1,2,",",".") }}
															 @endif
														</td>
														<td>
															@if($compra->id_pago > 0)
																 Venta # {{$compra->nro_venta}}
															@endif
														</td>

														<td>
							                            {{$compra->nombre_banco}}
							                            </td>
							                            
							                            <td> 
							                            
							                            @if($compra->nombre_sucursal != null) {{ $compra->nombre_sucursal }} @else Casa central @endif
							                            </td>
													
														<td>
														@if($compra->url_pago > 0)
														<a href="{{ asset('storage/comprobantes/' . $compra->url_pago) }}" target="_blank" >
														<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
														</a>
														@endif
														
														</td>
													</tr>
										@endif
										@endforeach
									</tbody>
                                </table>
                            </div>
                            @endif
                            
						</div>
					</div>
					
					@if($id_venta != 0)
					@include('livewire.ctacte-clientes-movimientos.ver-venta')
					@endif

					
					@include('livewire.ctacte-clientes-movimientos.agregar-editar-saldo-inicial')
					@include('livewire.ctacte-clientes-movimientos.ver-pago')
					@include('livewire.ctacte-clientes-movimientos.agregar-pago')	
					@include('livewire.ctacte-clientes-movimientos.ver-pagos-asociados')
					
					
					</div>
		
		            
		<script>
		        document.addEventListener('DOMContentLoaded', function(){
            		            
                    window.livewire.on('agregar-pago', Msg =>{
                        $('#AgregarPago').modal('show')
                    })
            
                    window.livewire.on('agregar-pago-hide', Msg =>{
                        $('#AgregarPago').modal('hide')
                    })
		            window.livewire.on('show-ver-pagos', Msg =>{
                        $('#VerPagos').modal('show')
                    })
                    
                    window.livewire.on('hide-ver-pagos', Msg =>{
                        $('#VerPagos').modal('hide')
                    })
                    
                    window.livewire.on('agregar-pago', Msg =>{
                        $('#AgregarPago').modal('show')
                    })
                    
                    window.livewire.on('agregar-pago-hide', Msg =>{
                        $('#AgregarPago').modal('hide')
                    })
                    
                    window.livewire.on('ver-pago', Msg =>{
                        $('#VerPago').modal('show')
                    })
                    
                    window.livewire.on('ver-pago-hide', Msg =>{
                        $('#VerPago').modal('hide')
                    })
                    
                    window.livewire.on('ver-pago-saldo-inicial', Msg =>{
                        $('#AgregarEditarSaldoInicial').modal('show')
                    })
                    
                    window.livewire.on('ver-pago-saldo-inicial-hide', Msg =>{
                        $('#AgregarEditarSaldoInicial').modal('hide')
                    })
                    
                    
                    
                    
		        });
		        
		        
		        function ConfirmPagoSaldo(id) {
                
                  swal({
                    title: 'CONFIRMAR',
                    text: '¿CONFIRMAS ELIMINAR EL PAGO?',
                    type: 'warning',
                    showCancelButton: true,
                    cancelButtonText: 'Cerrar',
                    cancelButtonColor: '#fff',
                    confirmButtonColor: '#3B3F5C',
                    confirmButtonText: 'Aceptar'
                  }).then(function(result) {
                    if (result.value) {
                      window.livewire.emit('deletePagoSaldo', id)
                      swal.close()
                    }
                
                  })
                }
                
                function ConfirmPago(id,origen) {
                
                  swal({
                    title: 'CONFIRMAR',
                    text: '¿CONFIRMAS ELIMINAR EL PAGO?',
                    type: 'warning',
                    showCancelButton: true,
                    cancelButtonText: 'Cerrar',
                    cancelButtonColor: '#fff',
                    confirmButtonColor: '#3B3F5C',
                    confirmButtonText: 'Aceptar'
                  }).then(function(result) {
                    if (result.value) {
                      window.livewire.emit('deletePago', [id,origen])
                      swal.close()
                    }
                
                  })
                }
		</script>