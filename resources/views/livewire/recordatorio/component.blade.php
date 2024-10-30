
<div class="row sales layout-top-spacing" style="width:100%;">

	<div class="layout-px-spacing"  style="width:100%;">

			<div class="page-header">
					<div class="page-title">
							<h3>Recordatorios</h3>
					</div>
			</div>

			<div style="background: white;
    border-radius: 6px; padding:20px;" class="row app-notes layout-top-spacing" id="cancel-row">
					<div class="col-lg-12">
							<div class="app-hamburger-container">
									<div class="hamburger"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu chat-menu d-xl-none"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg></div>
							</div>

							<div class="app-container">

									<div class="app-note-container">

											<div class="app-note-overlay"></div>

											<div class="tab-title">
													<div class="row">
														<div class="col-md-12 col-sm-12 col-12 text-center">
																<a  wire:click="AbrirModal" class="btn btn-secondary" href="javascript:void(0);">+ Nuevo Recordatorio</a>
														</div>
															<div class="col-md-12 col-sm-12 col-12 mt-5">
																	<ul class="nav nav-pills d-block" id="pills-tab3" role="tablist">
																			<li class="nav-item">
																					<a class="nav-link list-actions {{$active_hoy}}" id="all-notes" href="javascript:void(0)" wire:click="FiltroFecha(1)"> Hoy</a>
																			</li>
																			<li class="nav-item">
																					<a class="nav-link list-actions {{$active_semana}}" id="all-notes" href="javascript:void(0)" wire:click="FiltroFecha(2)"> Esta semana</a>
																			</li>
																			<li class="nav-item">
																					<a class="nav-link list-actions {{$active_mes}} " id="all-notes" href="javascript:void(0)" wire:click="FiltroFecha(3)"> Este mes</a>
																			</li>
																			<li class="nav-item">
																					<a class="nav-link list-actions {{$active_3meses}} " id="all-notes" href="javascript:void(0)" wire:click="FiltroFecha(4)"> Proximos 3 meses</a>
																			</li>
																	</ul>

																	<hr/>


																	<ul class="nav nav-pills d-flex group-list" id="pills-tab" role="tablist">
																			<li  style=" {{$color == 'note-personal' ? 'background: #c8c8c8;' : ''  }} border:solid 1px #eee;  padding:5px; width: 25%; margin-left:10px; cursor: pointer; margin-top:10px; border-radius:6px;" class="nav-item">
																					<a class="g-dot-primary" wire:click="filtrar('note-personal')" id="note-personal"></a>
																			</li>

																			<li style="{{$color == 'note-social' ? 'background: #c8c8c8;' : ''  }} border:solid 1px #eee; padding:5px; width: 25%; margin-left:10px; cursor: pointer; margin-top:10px; border-radius:6px;" class="nav-item">
																					<a class="g-dot-success" id="note-social" wire:click="filtrar('note-social')"></a>
																			</li>
																			<li style="{{$color == 'note-work' ? 'background: #c8c8c8;' : ''  }} border:solid 1px #eee; padding:5px; width: 25%; margin-left:10px; cursor: pointer; margin-top:10px; border-radius:6px;" class="nav-item">
																					<a class="g-dot-warning" id="note-work" wire:click="filtrar('note-work')"></a>
																			</li>
																			<li style="{{$color == 'note-important' ? 'background: #c8c8c8;' : ''  }} border:solid 1px #eee; padding:5px; width: 25%; margin-left:10px; cursor: pointer; margin-top:10px; border-radius:6px;" class="nav-item">
																					<a class="g-dot-danger" id="note-important" wire:click="filtrar('note-important')"></a>
																			</li>
																			<li style="{{$color == 'note-dark' ? 'background: #c8c8c8;' : ''  }} border:solid 1px #eee; padding:5px; width: 25%; margin-left:10px; cursor: pointer; margin-top:10px; border-radius:6px;" class="nav-item">
																					<a class="g-dot-dark" id="note-dark" wire:click="filtrar('note-dark')"></a>
																			</li>
																			<li style="{{$color == 'note-green' ? 'background: #c8c8c8;' : ''  }} border:solid 1px #eee; padding:5px; width: 25%; margin-left:10px; cursor: pointer; margin-top:10px; border-radius:6px;" class="nav-item">
																					<a class="g-dot-green" id="note-green" wire:click="filtrar('note-green')"></a>
																			</li>
																			<li style="{{$color == 'todos' ? 'background: #c8c8c8;' : ''  }} border:solid 1px #eee; padding:5px; width: 87%; text-align: center; margin-left:10px; cursor: pointer; margin-top:10px; border-radius:6px;" class="nav-item">
																					<a style="padding:0;" wire:click="filtrar('todos')">
																						TODOS
																					</a>
																			</li>
																	</ul>
															</div>
													</div>
											</div>


											<div id="ct" class="note-container note-grid">
												<div hidden class="note-item all-notes note-fav">
														<div class="note-inner-content">
																<div class="note-content">
																		<p class="note-title" data-noteTitle="Receive Package">Receive Package</p>
																		<p class="meta-time">11/01/2019</p>
																		<div class="note-description-content">
																				<p class="note-description" data-noteDescription="Facilisis curabitur facilisis vel elit sed dapibus sodales purus.">Facilisis curabitur facilisis vel elit sed dapibus sodales purus.</p>
																		</div>
																</div>
																<div class="note-action">
																		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-star fav-note"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
																		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 delete-note"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
																</div>
																<div class="note-footer">
																		<div class="tags-selector btn-group">
																				<a class="nav-link dropdown-toggle d-icon label-group" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="true">
																						<div class="tags" style="margin-top:10px;">
																								<div class="g-dot-personal"></div>
																								<div class="g-dot-work"></div>
																								<div class="g-dot-social"></div>
																								<div class="g-dot-important"></div>
																								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-vertical"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
																						</div>
																				</a>
																				<div class="dropdown-menu dropdown-menu-right d-icon-menu">
																						<a class="note-personal label-group-item label-personal dropdown-item position-relative g-dot-personal" href="javascript:void(0);"> Personal</a>
																						<a class="note-work label-group-item label-work dropdown-item position-relative g-dot-work" href="javascript:void(0);"> Work</a>
																						<a class="note-social label-group-item label-social dropdown-item position-relative g-dot-social" href="javascript:void(0);"> Social</a>
																						<a class="note-important label-group-item label-important dropdown-item position-relative g-dot-important" href="javascript:void(0);"> Important</a>
																				</div>
																		</div>
																</div>
														</div>
												</div>
												@foreach($recordatorio as $r)

													<div class="note-item all-notes {{$r->color}}">
															<div class="note-inner-content">
																	<div class="note-content">
																		<div class="row">
																			<p class="col-7" data-noteTitle="Receive Package"> <b>{{$r->titulo}} </b> </p>
																			<p class="col-4">
																			<b>
																				{{\Carbon\Carbon::parse($r->fecha)->format('d/m/Y')}}
																			</b> </p>
																		</div>


																			<div style="margin-top:10px;" class="note-description-content">

																				@if($r->tipo_contacto == "cliente")

																				@foreach($datos_clientes as $dc)

																				@if($dc->id == $r->contacto_id)

																					<p>{{$dc->nombre}} - {{$dc->telefono}}</p>
																					<p>{{$dc->email}}</p>

																				@endif

																				@endforeach

																				@endif

																				@if($r->tipo_contacto == "proveedor")

																				@foreach($datos_proveedores as $dp)

																				@if($dp->id == $r->contacto_id)

																					<p>{{$dp->nombre}} - {{$dp->telefono}}</p>
																					<p>{{$dp->email}}</p>

																				@endif

																				@endforeach

																				@endif

																					@if($r->sale_id)
																					<p>*
																					<a href="javascript:void(0)" wire:click="RenderFactura('{{$r->sale_id}}')">
																						Venta # {{$r->sale_id}} - {{$r->descripcion}}
																					</a> </p>
																					@else
																					<p>* {{$r->descripcion}}</p>
																					@endif
																			</div>
																	</div>
																	<div class="note-action">

																		@if($r->tipo_contacto == "proveedor")

																		@foreach($datos_proveedores as $dp)

																		@if($dp->id == $r->contacto_id)

																    	<a style="margin-left:15px; padding-top:10px;" href="https://wa.me/{{$dp->telefono}}?text=" target="_blank" >

																		@endif

																		@endforeach

																		@endif

																		@if($r->tipo_contacto == "cliente")

																		@foreach($datos_clientes as $dc)

																		@if($dc->id == $r->contacto_id)

																    	<a style="margin-left:15px; padding-top:10px;" href="https://wa.me/{{$dc->telefono}}?text=" target="_blank" >

																		@endif

																		@endforeach

																		@endif





																			<svg style="margin-top:10px;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>

																		</a>
																	</div>

																																		<div class="note-footer">
																																				<div class="tags-selector btn-group">
																																						<a class="nav-link dropdown-toggle d-icon label-group" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="true">
																																								<div class="tags" style="margin-top:5px;">
																																										<div class="g-dot-personal"></div>
																																										<div class="g-dot-work"></div>
																																										<div class="g-dot-social"></div>
																																										<div class="g-dot-important"></div>
																																										<div class="g-dot-green"></div>
																																										<div class="g-dot-dark"></div>
																																									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-vertical"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
																																								</div>
																																						</a>
																																						<div style="min-width: 1.5rem !important; width: 53px;" class="dropdown-menu dropdown-menu-right d-icon-menu">
																																								<a class="note-personal label-group-item label-personal dropdown-item position-relative g-dot-personal" href="javascript:void(0);" wire:click="CambiarColor('note-personal',{{$r->id}})">
																																									<p  style="visibility:hidden;">.</p>
																																								</a>
																																								<a class="note-work label-group-item label-work dropdown-item position-relative g-dot-work" href="javascript:void(0);" wire:click="CambiarColor('note-work',{{$r->id}})"> <p style="visibility:hidden;">.</p>
																																								</a>
																																								<a class="note-social label-group-item label-social dropdown-item position-relative g-dot-social" href="javascript:void(0);" wire:click="CambiarColor('note-social', {{$r->id}})"> <p style="visibility:hidden;">.</p>
																																								</a>
																																								<a class="note-important label-group-item label-important dropdown-item position-relative g-dot-important" href="javascript:void(0);" wire:click="CambiarColor('note-important', {{$r->id}})"> <p style="visibility:hidden;">.</p>
																																								</a>
																																								<a class="note-important label-group-item label-important dropdown-item position-relative g-dot-dark" href="javascript:void(0);" wire:click="CambiarColor('note-dark',{{$r->id}})"> <p style="visibility:hidden;">.</p>
																																								</a><a class="note-important label-group-item label-important dropdown-item position-relative g-dot-green" href="javascript:void(0);" wire:click="CambiarColor('note-green', {{$r->id}})"> <p style="visibility:hidden;">.</p>
																																								</a>
																																						</div>
																																				</div>
																																		</div>
																	<div class="note-footer">
																			<div class="tags-selector btn-group">
																					<a class="nav-link dropdown-toggle d-icon label-group" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="true">
																							<div class="tags">
																							    @if($r->sale_id)
																							    <button class="btn btn-sm btn-light" href="javascript:void(0)" wire:click="RenderFactura('{{$r->sale_id}}')">Ver Venta</button>
																							    @endif

																							</div>
																						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
																					</a>
																					<div class="dropdown-menu dropdown-menu-right d-icon-menu">
																						<a class="note-personal label-group-item label-personal dropdown-item position-relative " href="javascript:void(0);" wire:click="Edit({{$r->id}})">
																							<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#438eff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>

																							Editar </a>


																							<a class="note-personal label-group-item label-personal dropdown-item position-relative " href="javascript:void(0);" wire:click="Completado({{$r->id}})">
																								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#8dbf42" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>

																								Marcar como completado </a>
																							<a class="note-work label-group-item label-work dropdown-item position-relative" wire:click="ReprogramarModal({{$r->id}})" href="javascript:void(0);">
																								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="violet" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-corner-up-right"><polyline points="15 14 20 9 15 4"></polyline><path d="M4 20v-7a4 4 0 0 1 4-4h12"></path></svg>

																								 Reprogramar recordatorio </a>
																					</div>
																			</div>
																	</div>

															</div>
													</div>

													@endforeach
											</div>

									</div>

							</div>

							<!-- Modal -->


					</div>
			</div>

			</div>

@include('livewire.recordatorio.color')
	@include('livewire.recordatorio.form')
	@include('livewire.recordatorio.form-venta')
	@include('livewire.recordatorio.cambiar-estado')
	@include('livewire.recordatorio.contacto')
	
</div>


<script>
	document.addEventListener('DOMContentLoaded', function() {

		window.livewire.on('recordatorio', msg => {
			$('#ModalRecordatorio').modal('show')
		});
		window.livewire.on('recordatorio-hide', msg => {
			$('#ModalRecordatorio').modal('hide')
		});
		
		window.livewire.on('venta', msg => {
			$('#ModalVenta').modal('show')
		});
		window.livewire.on('venta-hide', msg => {
			$('#ModalVenta').modal('hide')
		});

		window.livewire.on('recordatorio-added', msg => {
			$('#ModalRecordatorio').modal('hide')
			noty(msg)
		});

		window.livewire.on('contacto', msg => {
			$('#AgregarContacto').modal('show')
		});
		window.livewire.on('contacto-hide', msg => {
			$('#AgregarContacto').modal('hide')
		});
		
		window.livewire.on('color', msg => {
			$('#AgregarColor').modal('show')
		});
		window.livewire.on('color-hide', msg => {
			$('#AgregarColor').modal('hide')
		});
		
		
		
		
		window.livewire.on('cambiar-estado', id => {
			$('#ModalCambiarEstado').modal('show')
		});
		window.livewire.on('cambiar-estado-hide', id => {
			$('#ModalCambiarEstado').modal('hide')
		});

	});



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
	
	function TomarValor(id) {
	    
	   window.livewire.emit('ElegirColor', id)
	    
	}
</script>
