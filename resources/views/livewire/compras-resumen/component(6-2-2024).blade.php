  <div>	          
  
  	
		@include('livewire.compras-resumen.agregar-pago')
		@include('livewire.gastos.estado-pedido-pos')
	    @include('livewire.reports.variaciones')
	    
	                @if($accion == 0)
                        <div class="page-header">
							<div class="page-title">
								<h4>COMPRAS</h4>
								<h6>Listado de compras</h6>
							</div>
							<div class="page-btn">
							    @if(Auth::user()->sucursal != 1 )
								<a href="{{ url('compras') }}" class="btn btn-added">
									<img src="{{ asset('assets/pos/img/icons/plus.svg') }}" alt="img">Agregar nueva compra
								</a>							    
							    @else
								<a href="{{ url('compras-elegir') }}" class="btn btn-added">
									<img src="{{ asset('assets/pos/img/icons/plus.svg') }}" alt="img">Agregar nueva compra
								</a>
								@endif
							</div>
						</div>
						
						<div class="row">
						<div class="col-lg-3 col-sm-6 col-12">
							<div class="dash-widget">
								<div class="dash-widgetimg">
									<span style="background-color: #63738112 !important;">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#8ea0af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clipboard"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg></span>
								</div>
								<div class="dash-widgetcontent">
									<h5  style="margin-bottom: 0px !important;"><span class="counters" data-count="{{ $suma_compras_cantidades ?: 0 }}">{{$suma_compras_cantidades ?: 0}}</span></h5>
									<h6>Cantidad de compras</h6>
								</div>
							</div>
						</div>
						<div class="col-lg-3 col-sm-6 col-12">
							<div class="dash-widget dash2">
								<div class="dash-widgetimg">
									<span><text style="font-size:24px; color: #00cfe8">P</text></span>
								</div>
								<div class="dash-widgetcontent">
									<h5   style="margin-bottom: 0px !important;" >$ {{number_format($suma_compras_pagas,2)}} </h5>
									<h6>Compras Pagas</h6>
								</div>
							</div>
						</div>
						<div class="col-lg-3 col-sm-6 col-12">
							<div class="dash-widget dash3">
								<div class="dash-widgetimg">
								<span>
								    <text style="font-size:24px; color: #ed6a6b;">D</text>
								</span>
								</div>
								<div class="dash-widgetcontent">
									<h5  style="margin-bottom: 0px !important;" >$ {{number_format($suma_compras_deuda,2)}} </span></h5>
									<h6>Compras Adeudadas</h6>
								</div>
							</div>
						</div>
						<div class="col-lg-3 col-sm-6 col-12">
							<div class="dash-widget dash1">
								<div class="dash-widgetimg">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#00cd5c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>		</div>
								<div class="dash-widgetcontent">
									<h5 style="margin-bottom: 0px !important;" >$ {{number_format($suma_compras_totales,2)}} </h5>
									<h6>Compras totales</h6>
								</div>
							</div>
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
    									<input type="text" autocomplete="off" wire:model="search" placeholder="Buscar.." class="form-control"	>
    									<div hidden class="search-input">
    										<a class="btn btn-searchset"><img src="{{ asset('assets/pos/img/icons/search-white.svg') }}" alt="img"></a>
    									</div>
    								</div>
									<div class="wordset">
										<ul>
											<li hidden>
												<a data-bs-toggle="tooltip" data-bs-placement="top" title="pdf"><img src="{{ asset('assets/pos/img/icons/pdf.svg') }}"  alt="img"></a>
											</li>
											<li>
												<a data-bs-toggle="tooltip" wire:click="ExportarReporte('{{ ( ($search == '' ? '0' : $search) . '/' . ($proveedor_elegido == '' ? '0' : $proveedor_elegido)  .  '/' . ($estado_pago == '' ? '0' : ($estado_pago == 'Pago' ? '1' : '2')) . '/'  . $dateFrom . '/' . $dateTo) }}')" data-bs-placement="top" title="excel"><img src="{{ asset('assets/pos/img/icons/excel.svg') }}" alt="img"></a>
											</li>
											<li hidden>
												<a data-bs-toggle="tooltip" data-bs-placement="top" title="print"><img src="{{ asset('assets/pos/img/icons/printer.svg') }}" alt="img"></a>
											</li>
										</ul>
									</div>
								</div>
								<!-- /Filter -->
								<div class="card" @if(!$mostrarFiltros) hidden @endif >
									<div class="card-body pb-0">
										<div class="row">
										
											<div class="col-lg col-sm-6 col-12">
												<div class="form-group">
													<label>Proveedor</label>
                            						<select wire:model='proveedor_elegido' class="form-control">
                            							<option value="Elegir" disabled >Elegir</option>
                            							<option value="0" >Todos</option>
                            							<option value="2" >Casa central</option>
                            							@foreach($prov as $pr)
                            							<option value="{{$pr->id}}">{{$pr->nombre}}</option>
                            							@endforeach
                            						</select>
												</div>
											</div>
										
											<div class="col-lg col-sm-6 col-12">
											<div class="form-group">
                    						<label>Estado de pago</label>
                    
                    								<select wire:model="estado_pago" class="form-control">
                    									<option value="">Todos</option>
                    									<option value="Pendiente">Pendiente</option>
                    									<option value="Pago">Pagos</option>
                    
                    							</select>
												</div>
											</div>
											
											<div class="col-lg col-sm-6 col-12">
											<div class="form-group">
                    						<label>Rango de fechas</label>
						                    <input type="text" id="date-range-picker" name="date_range" />
											</div>
											</div>
											
																						
                    						<div hidden class="col-lg col-sm-6 col-12">
                    						<div class="form-group">
                    						<label>Etiquetas</label>
                    						
											</div>
											</div>
											
											
											<div class="col-lg-2 col-sm-6 col-12">
												<div class="form-group">
												    <label style="margin-top: 28px !important;"></label>
													<a class="btn btn-light" wire:click="LimpiarFiltros">LIMPIAR</a>
												</div>
											</div>
										</div>
									</div>
								</div>
								<!-- /Filter -->
								<div class="table-responsive">
									<table class="table">
										<thead>
											<tr>
												<th>
													<label class="checkboxs">
														<input type="checkbox" id="select-all">
														<span class="checkmarks"></span>
													</label>
												</th>
												<th>Nro compra</th>
												<th>Nombre del proveedor</th>
												<th>Fecha</th>
												<th>Estado</th>
												<th>Subtotal</th>
												<th>Descuento</th>
												<th>IVA</th>
												<th>Total</th>
												<th>Etiquetas</th>
												<th>Pago</th>
												<th>Deuda</th>
												<th>Estado de pago</th>
												<th>Acciones</th>
											</tr>
										</thead>
										<tbody>
										    @foreach($data as $compra)
											<tr>
												<td>
													<label class="checkboxs">
														<input type="checkbox">
														<span class="checkmarks"></span>
													</label>
												</td>
												<td class="text-bolds">{{$compra->nro_compra}}</td>
												<td class="text-bolds">{{$compra->nombre_proveedor}}</td>
												<td>{{\Carbon\Carbon::parse($compra->created_at)->format('d-m-Y')}}</td>
												<td>
												   
												    @if($compra->status == 1)
                                                    <span class="badges bg-lightyellow">Pendiente</span>
                                                    @endif
                                                    @if($compra->status == 2)
                                                    <span class="badges bg-lightyellow">En proceso</span>
                                                    @endif
                                                    @if($compra->status == 4)
                                                    <span class="badges bg-lightred">Cancelado</span>
                                                    @endif
                                                    @if($compra->status == 3)
                                                    <span class="badges bg-lightgreen">Entregado</span>
                                                    @endif
												</td>
												<td>$ {{$compra->subtotal}}</td>
												<td>$ {{$compra->descuento}}</td>
												<td>$ {{$compra->iva}}</td>
												<td>$ {{$compra->total}}</td>
												<td>
											    <select class="select2" id="{{$compra->id}}" multiple="multiple" data-relacion-id="{{ $compra->id }}"> 
                                                    <!-- Aquí puedes incluir opciones predefinidas o dejarlo vacío -->
                                                @if($compra->nombre_etiqueta != null)
                                                <option value="{{ $compra->nombre_etiqueta }}" selected>
                                                    {{ $compra->nombre_etiqueta }}
                                                </option>
                                                @endif
                                                </select>
											  	</td>
												<td>$ {{number_format($compra->total-$compra->deuda,2)}}</td>
												<td>$ {{$compra->deuda}}</td>
												<td>
												  
                                                    @if(1 < $compra->deuda)
                                                    <span class="badges bg-lightred">Impago</span>
                                                    @endif
                                                    @if($compra->deuda < 1)
                                                    <span class="badges bg-lightgreen">Pago</span>
                                                    @endif
												    
												
												</td>
												<td>
												<a class="me-3" href="javascript:void(0)" wire:click.prevent="RenderFactura({{$compra->id}})">
													<img src="{{ asset('assets/pos/img/icons/edit.svg') }}" alt="img">
												</a>
												<a hidden class="me-3" href="javascript:void(0)" wire:click.prevent="Edit({{$compra->id}})" >
													<img src="{{ asset('assets/pos/img/icons/edit.svg') }}"  alt="img">
												</a>
												<a href="javascript:void(0)" onclick="ConfirmEliminar({{$compra->id}})" >
													<img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="img">
												</a>
												</td>
											</tr>
											@endforeach
										</tbody>
									</table>
									
								</div>
								<br>
								{{$data->links()}}
							</div>
						</div>
						<!-- /product list -->
					@endif
					
					@if($accion == 1)
						@include('livewire.compras-resumen.agregar-editar-compra')
					@endif
	                
	                @if($accion == 2)
					@include('livewire.compras-resumen.actualizar-compra')
					@endif
					
					@if($accion == 3)
					@include('livewire.compras-resumen.elegir-recalcular-deuda')
					@endif
	                    
					
</div>


<script type="text/javascript">

    function ConfirmEliminar(id) {

    swal({
      title: 'CONFIRMAR',
      text: 'QUIERE ELIMINAR LA COMPRA?',
      showCancelButton: true,
      cancelButtonText: 'Cancelar',
      cancelButtonColor: '#fff',
      confirmButtonColor: '#3B3F5C',
      confirmButtonText: 'Aceptar'
    }).then(function(result) {
      if (result.value) {
        window.livewire.emit('EliminarCompra', id)
        swal.close()
      }

    })
  }
  

</script>
<script type="text/javascript">


 function ConfirmDelete(id) {
                                
swal({
title: 'CONFIRMAR',
text: 'QUIERE ELIMINAR EL PRODUCTO DE LA COMPRA?',
type: 'warning',
showCancelButton: true,
cancelButtonText: 'Cancelar',
cancelButtonColor: '#fff',
confirmButtonColor: '#3B3F5C',
confirmButtonText: 'Aceptar'
}).then(function(result) {
if (result.value) {
    window.livewire.emit('EliminarProducto', id)
    swal.close()
}
                        
})
}

 function ConfirmDeleteLast(id) {
                                
swal({
title: 'CONFIRMAR',
text: 'AL ELIMINAR ESTE PRODUCTO LA COMPRA QUEDARA VACIA Y SE ELIMINARA POR COMPLETO. ESTA ACCION ES IRREVERSIBLE. DESEA CONTINUAR?',
type: 'warning',
showCancelButton: true,
cancelButtonText: 'Cancelar',
cancelButtonColor: '#fff',
confirmButtonColor: '#3B3F5C',
confirmButtonText: 'Aceptar'
}).then(function(result) {
if (result.value) {
    window.livewire.emit('EliminarProductoLast', id)
    swal.close()
}
                        
})
}


function ConfirmPago(id) {

  swal({
    title: 'CONFIRMAR',
    text: '多CONFIRMAS ELIMINAR EL PAGO?',
    type: 'warning',
    showCancelButton: true,
    cancelButtonText: 'Cerrar',
    cancelButtonColor: '#fff',
    confirmButtonColor: '#3B3F5C',
    confirmButtonText: 'Aceptar'
  }).then(function(result) {
    if (result.value) {
      window.livewire.emit('deletePago', id)
      swal.close()
    }

  })
}

function MensajeAgregarPago(id) {

  swal({
    title: 'LA COMPRA NO PRESENTA DEUDA',
    text: '¿DESEA AGREGAR UN PAGO DE TODOS MODOS?',
    type: 'warning',
    showCancelButton: true,
    cancelButtonText: 'Cerrar',
    cancelButtonColor: '#fff',
    confirmButtonColor: '#3B3F5C',
    confirmButtonText: 'Aceptar'
  }).then(function(result) {
    if (result.value) {
      window.livewire.emit('AgregarPago', id)
      swal.close()
    }

  })
}

</script>
<script>
    document.addEventListener('livewire:load', function () {
        Livewire.hook('message.processed', function () {
           $('.select2').select2({
                data: @json($etiqueta_json),
                tags: true, // Habilitar la creación de etiquetas si es necesario
                maximumSelectionLength: 1 // Establece el límite de selección a 2 elementos
            });
            
        });
    });
    
document.addEventListener('DOMContentLoaded', function(){


            $('.select2').select2({
            tags: true,
            data: @json($etiqueta_json),
            language: 'es',
            maximumSelectionLength: 1 // Establece el límite de selección a 2 elementos
            });
            
            
            
            $('.select2').on('change', function(e) {
                var selectedId = $(this).attr('id');
                var selectedValue = $(this).select2('val');
                @this.emit('etiquetaSeleccionada', { value: selectedValue, id: selectedId } );
            });

            
            livewire.on('scan-code', action => {
                $('#code').val('')
            })
            
            
						window.livewire.on('sale-ok', Msg => {
								$('#theModal2').modal('hide')
								noty(Msg)
								
								
						})
						
								window.livewire.on('variacion-elegir', Msg => {
                        			$('#Variaciones').modal('show')
                        		})
                        
                        		window.livewire.on('variacion-elegir-hide', Msg => {
                        			$('#Variaciones').modal('hide')
                        		})
                        		

								window.livewire.on('agregar-cliente', Msg => {
										$('#theModal-cliente').modal('show')
								})

								window.livewire.on('agregar-pago', Msg =>{
										$('#AgregarPago').modal('show')
								})



								window.livewire.on('agregar-pago-hide', Msg =>{
										$('#AgregarPago').modal('hide')
								})

								window.livewire.on('pago-dividido', Msg =>{
										$('#PagoDividido').modal('show')
								})

								window.livewire.on('pago-dividido-hide', Msg =>{
										$('#PagoDividido').modal('hide')
								})


								window.livewire.on('hide-modal2', Msg =>{
										$('#modalDetails2').modal('hide')
								})

								window.livewire.on('cerrar-factura', Msg =>{
										$('#theModal1').modal('hide')
								})

								window.livewire.on('modal-show', msg => {
									$('#theModal1').modal('show')
								})


								window.livewire.on('abrir-hr-nueva', msg => {
									$('#theModal').modal('show')
								})

								window.livewire.on('hide-modal3', Msg =>{
										$('#modalDetails3').modal('hide')
								})


								window.livewire.on('modal-hr-hide', Msg =>{
										$('#theModal').modal('hide')
								})

								window.livewire.on('hr-added', Msg => {
									noty(Msg)
								})
								
								window.livewire.on('msg', Msg => {
									noty(Msg)
								})

								window.livewire.on('modal-estado', Msg =>{
										$('#modalDetails-estado-pedido').modal('show')
								})

								window.livewire.on('modal-estado-hide', Msg =>{
										$('#modalDetails-estado-pedido').modal('hide')
								})

								window.livewire.on('hr-asignada', Msg => {
									noty(Msg)
								})

								window.livewire.on('pago-agregado', Msg => {
									noty(Msg)
								})

								window.livewire.on('pago-actualizado', Msg => {
									noty(Msg)
								})

								window.livewire.on('pago-eliminado', Msg => {
									noty(Msg)
								})
								window.livewire.on('msg', Msg => {
									noty(Msg)
								})
								//events
								window.livewire.on('product-added', Msg => {
									$('#theModal').modal('hide')
									noty(Msg)
								})

								window.livewire.on('no-stock', Msg => {
									noty(Msg, 2)
								})

								//eventos
								window.livewire.on('show-modal', Msg =>{
										$('#modalDetails').modal('show')
								})

								var total = $('#suma_totales').val();
								$('#ver_totales').html('Ventas: '+total);


    });

</script>