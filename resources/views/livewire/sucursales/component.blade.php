<div>	                
	                <div class="page-header">
					<div class="page-title">
							<h4>Sucursales</h4>
							<h6>Ver listado de mis sucursales</h6>
						</div>

						<div class="page-btn">
						    
                            @if(Auth::user()->plan == 1 && $count_sucursales >= 0)
                            <a href="javascript:void(0)" class="btn btn-added" onclick="MejorarPlan()">
                                <img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar sucursal
                            </a>
                            @elseif(Auth::user()->plan == 2 && $count_sucursales >= 4)
                            <a href="javascript:void(0)" class="btn btn-added" onclick="MejorarPlan()">
                                <img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar sucursal
                            </a>
                            @elseif(Auth::user()->plan == 3 && $count_sucursales >= 9)
                            <a href="javascript:void(0)" class="btn btn-added" onclick="MejorarPlan()">
                                <img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar sucursal
                            </a>
                            @elseif(Auth::user()->plan == 4 && $count_sucursales >= 24)
                            <a href="javascript:void(0)" class="btn btn-added" onclick="MejorarPlan()">
                                <img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar sucursal
                            </a>
                            @else
                            <a href="javascript:void(0)" class="btn btn-added" wire:click="Agregar">
                                <img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar sucursal
                            </a>
                            @endif
                            
						</div>
						
					</div>
					
                    
					<!-- /product list -->
					<div class="card">
				
						<div class="card-body">
							<div class="table-top mb-0">
								<div class="search-set">
									<div class="search-path">
										<a class="btn btn-filter" id="filter_search">
											<img src="{{ asset('assets/pos/img/icons/filter.svg') }}"  alt="img">
											<span><img src="{{ asset('assets/pos/img/icons/closes.svg') }}" alt="img"></span>
										</a>
									</div>
									<input type="text" autocomplete="off" wire:model="search_sucursal" placeholder="Buscar.." class="form-control"	>
									
									
									@if(auth()->user()->sucursal != 1)	
    								<div style="margin-left: 15px !important;" class="col-lg-4 col-sm-6 col-12">
                                	<div class="form-group">
                                		<label style="margin-bottom: 4px !important;">Tipo</label>
                                		<select wire:model.lazy="tipo_filtro" class="form-control">
                                			<option value="all" selected>Todas</option>
                                			<option value="Sucursal propia">Sucursal propia</option>
                                			<option value="Franquicia">Franquicia</option>
                                		</select>
                                	</div>
    								</div>
    								@endif
	
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
										    <th hidden>
												<label class="checkboxs">
											    <input name="Todos" type="checkbox" value="1" onclick="CheckTodosLote()" class="check_todos"/>    
                            					<span class="checkmarks"></span>
											    </label>
											</th>
										    <th>Nombre</th>
											<th>Email</th>
											<th>Tipo</th>
											<th>Lista precio para la Compra a la casa central</th>
											<th>Lista precio para la Venta</th>
											<th>Acciones</th>
										</tr>
									</thead>
									<tbody>
									    @foreach($data as $d)
										<tr>
										    <td hidden>
												<label class="checkboxs">
												    <input type="checkbox" wire:model.defer="id_check" tu-attr-id="{{($d->id)}}"  class="mis-checkboxes" value="{{$d->id}}">
													<span class="checkmarks"></span>
												</label>
											</td>
									        <td>{{$d->id}} - {{$d->name}}</td>
											<td>{{$d->email}}</td>
											<td>{{$d->tipo}}</td>
											<td>{{$d->nombre_lista}}</td>
											<td>
											@if($d->lista_defecto == 0)
											Precio base
											@else
											@foreach($lista_precios as $lpr)
											@if($lpr->id == $d->lista_defecto)
											{{$lpr->nombre}}
											@endif
											@endforeach
											
											@endif
											</td>
											<td>
												<a class="me-3" href="javascript:void(0)" wire:click="Edit({{$d->id}})" >
													<img src="{{ asset('assets/pos/img/icons/edit.svg') }}"  alt="img">
												</a>
												<a href="javascript:void(0)" onclick="Confirm('{{$d->id}}')"  >
													<img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="img">
												</a>
											</td>
											
										
										</tr>
										@endforeach
									</tbody>
								</table>
								{{$data->links()}}
							</div>
						</div>
					</div>
					
					

	@include('livewire.sucursales.form')

	</div>
					
	
<script>
	document.addEventListener('DOMContentLoaded', function() {

		window.livewire.on('show-modal', msg => {
			$('#theModal').modal('show')
		});
		window.livewire.on('data-added', msg => {
			$('#theModal').modal('hide')
			noty(msg)
		});
		window.livewire.on('data-updated', msg => {
			$('#theModal').modal('hide')
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
</script>