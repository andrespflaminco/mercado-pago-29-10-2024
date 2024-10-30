<div >	                
	                @if($agregar == 0)
	                <div class="page-header">
					<div class="page-title">
							<h4>Lista de precios</h4>
							<h6>Ver listado de lista de precios</h6>
						</div>
						<div class="page-btn">
						    @if(Auth::user()->sucursal != 1)
						    @if(Auth::user()->profile != "Cajero" )
							<a href="javascript:void(0)" wire:click="Agregar()" class="btn btn-added"><img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar Lista</a>
						    @endif
						    @endif
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
											
											<th>Nombre de la lista</th>
											<th>Acciones</th>
										</tr>
									</thead>
									<tbody>
									    @foreach($data as $lista)
										<tr>
									
											<td>{{$lista->nombre}}</td>
								
											<td>
											    @if(Auth::user()->sucursal != 1)
											    @if(Auth::user()->profile != "Cajero" )
											  	<a class="me-3" href="javascript:void(0)" wire:click.prevent="Edit({{$lista->id}})" >
													<img src="{{ asset('assets/pos/img/icons/edit.svg') }}"  alt="img">
												</a>
												<a href="javascript:void(0)" onclick="Confirm('{{$lista->id}}')"  >
													<img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="img">
												</a>
												@endif
												@endif
											</td>
										</tr>
										@endforeach
									</tbody>
									
								</table>
								{{$data->links()}}
							</div>
						</div>
					</div>
					
					<!-- /product list -->
					@endif 
					
					@if($agregar == 1)
					@include('livewire.lista-precios.agregar-editar-lista')
					@endif 
					
					
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
					