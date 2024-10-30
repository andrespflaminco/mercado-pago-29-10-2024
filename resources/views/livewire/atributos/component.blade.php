<div>	                
	                <div class="page-header">
					<div class="page-title">
							<h4>Atributos</h4>
							<h6>Ver listado de atributos y variaciones</h6>
						</div>
						<div class="page-btn">
						    @if(Auth::user()->sucursal != 1)
						    @if(Auth::user()->profile != "Cajero" )
						    <a href="javascript:void(0)" class="btn btn-added" wire:click="Agregar"><img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar atributo</a>
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
							
							<div class="table-responsive" style="width: 100%;">
                            <table style="width: 100%; table-layout: auto !important;">
                                <thead style="background: #FAFBFE; border-bottom: 1px solid #E9ECEF;">
                                    <tr> 
                                        <th style="width: 30% !important; padding: 10px; color: #212B36; font-weight: 600; border-bottom-width: 1px; background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg);">Atributo</th>
                                        <th style="width: 5% !important; padding: 10px; color: #212B36; font-weight: 600; border-bottom-width: 1px; background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg);"></th>
                                        <th style="width: 55% !important; padding: 10px; color: #212B36; font-weight: 600; border-bottom-width: 1px; background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg);">Nombre</th>
                                        <th style="width: 10% !important; padding: 10px; color: #212B36; font-weight: 600; border-bottom-width: 1px; background-color: var(--bs-table-bg); box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg);"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categories as $a)
                                    <tr>
                                        <td style="width: 30% !important; padding: 10px; color: #637381; font-weight: 500; border-bottom: 1px solid #E9ECEF;  vertical-align: middle; ">{{ $a->nombre }}</td>
                                        <td style="width: 5% !important; padding: 10px; color: #637381; font-weight: 500; border-bottom: 1px solid #E9ECEF;  vertical-align: middle; ">
                                            @if(Auth::user()->sucursal != 1 && Auth::user()->profile != "Cajero")
                                                @if($a->id != 1)
                                                    <a class="me-3" href="javascript:void(0)" wire:click="Edit({{ $a->id }})">
                                                        <img src="{{ asset('assets/pos/img/icons/edit.svg') }}" alt="img">
                                                    </a>
                                                @endif
                                                <a hidden href="javascript:void(0)" onclick="Confirm('{{ $a->id }}')">
                                                    <img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="img">
                                                </a>
                                            @endif
                                        </td>
                                        <td style="width: 55% !important; padding: 10px; color: #637381; font-weight: 500; border-bottom: 1px solid #E9ECEF;  vertical-align: middle; ">
                                            @foreach($variaciones as $v)
                                                @if($v->atributo_id == $a->id)
                                                    <b>{{ $v->nombre }} - </b>
                                                @endif
                                            @endforeach
                                        </td>
                                        <td style="width: 10% !important; padding: 10px; color: #637381; font-weight: 500; border-bottom: 1px solid #E9ECEF;  vertical-align: middle; ">
                                            @if(Auth::user()->sucursal != 1 && Auth::user()->profile != "Cajero")
                                                <a class="me-3" href="javascript:void(0)" wire:click="EditVariacion({{ $a->id }})">
                                                    <img src="{{ asset('assets/pos/img/icons/edit.svg') }}" alt="img">
                                                </a>
                                                <a href="javascript:void(0)" wire:click="AgregarVariacion({{ $a->id }})">
                                                    <img src="{{ asset('assets/pos/img/icons/plus.svg') }}" alt="img">
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

						</div>
					</div>
					
					

	@include('livewire.atributos.form')
	@include('livewire.atributos.form-variacion')
	@include('livewire.atributos.form-editar-variacion')
	
	
	</div>
					
	

<script>
	document.addEventListener('DOMContentLoaded', function() {

		window.livewire.on('show-modal', msg => {
			$('#theModal').modal('show')
		});

		window.livewire.on('show-modal-variacion', msg => {
			$('#theModalVariacion').modal('show')
		});

		window.livewire.on('show-modal-editar-variacion', msg => {
			$('#theModalEditarVariacion').modal('show')
		});

		window.livewire.on('variacion-editar-updated', msg => {
			$('#theModalEditarVariacion').modal('hide'),
			noty(msg);
		});
		
		window.livewire.on('variacion-editar-hide', msg => {
			$('#theModalEditarVariacion').modal('hide')
		});
		
		
		window.livewire.on('category-added', msg => {
			$('#theModal').modal('hide')
		});

		window.livewire.on('variacion-added', msg => {
			$('#theModalVariacion').modal('hide'),
			noty(msg);
		});
		
		window.livewire.on('msg', msg => {
			noty(msg);
		});

		window.livewire.on('variacion-updated', msg => {
			$('#theModalVariacion').modal('hide'),
			noty(msg);
		});

		window.livewire.on('category-updated', msg => {
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

	function ConfirmAtributo(id) {

		swal({
			title: 'CONFIRMAR',
			text: '¿CONFIRMAS ELIMINAR EL ATRIBUTO?',
			type: 'warning',
			showCancelButton: true,
			cancelButtonText: 'Cerrar',
			cancelButtonColor: '#fff',
			confirmButtonColor: '#3B3F5C',
			confirmButtonText: 'Aceptar'
		}).then(function(result) {
			if (result.value) {
				window.livewire.emit('deleteAtributo', id)
				swal.close()
			}

		})
	}
	
	
	function TieneProductos(id) {

		swal({
			title: 'IMPOSIBLE ELIMINAR',
			text: 'HAY PRODUCTOS QUE CONTIENEN LA VARIACION QUE DESEA ELIMINAR',
			type: 'warning',
			confirmButtonColor: '#3B3F5C',
			confirmButtonText: 'Aceptar'
		})

		
	}
	
	

	function ConfirmVariacion(id) {

		swal({
			title: 'CONFIRMAR',
			text: '¿CONFIRMAS ELIMINAR LA VARIACION?',
			type: 'warning',
			showCancelButton: true,
			cancelButtonText: 'Cerrar',
			cancelButtonColor: '#fff',
			confirmButtonColor: '#3B3F5C',
			confirmButtonText: 'Aceptar'
		}).then(function(result) {
			if (result.value) {
				window.livewire.emit('deleteVariacion', id)
				swal.close()
			}

		})
	}
</script>