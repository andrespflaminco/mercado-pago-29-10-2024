<div >	                
	                <div style=" {{ $agregar == 0 ? 'display:block;' : 'display:none;' }}">
	                    
	                
	                <div class="page-header">
					<div class="page-title">
							<h4>Promociones</h4>
							<h6>Ver listado de promociones</h6>
						</div>
						<div class="page-btn">               											    
                			
                			@if(Auth::user()->sucursal != 1)
                			@if(Auth::user()->profile != "Cajero" )
							<a href="javascript:void(0)" wire:click="Agregar()" class="btn btn-added"><img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar promocion</a>
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
											<th>
											    
											 
												
												</th>
											<th>Nombre</th>
											<th>Productos incluidos</th>
											<th>Acciones</th>
										</tr>
									</thead>
									<tbody>
									    @foreach($promos as $p)
										<tr>
											<td>
											   
											</td>
											<td >
												<a href="javascript:void(0);" wire:click.prevent="Edit({{$p->id}})">{{$p->nombre_promo}}</a>
											</td>
                                            <td>
                                                @php 
                                                $productosArray = explode(',', $p->productos);
                                                $cantidadProductos = count($productosArray);
                                                $maxMostrar = 3;
                                                @endphp
                                            
                                                @foreach ($productosArray as $key => $producto)
                                                    <span class="badge bg-dark">{{ $producto }}</span>
                                                    @if ($key === ($maxMostrar - 1))
                                                        @if ($cantidadProductos > $maxMostrar)
                                                            ...
                                                        @endif
                                                        @break
                                                    @endif
                                                @endforeach
                                            </td>

											<td class="d-flex">
											    								               
								             <div style="margin-right: 1rem !important;  margin-bottom: 5px;" class="status-toggle d-flex justify-content-between align-items-center">
                                                <input 
                                                    type="checkbox" 
                                                    id="check{{$p->id}}" 
                                                    class="check" 
                                                    {{ $p->activo == 1 ? 'checked' : '' }} 
                                                    wire:click="ToggleHabilitarPromo({{$p->id}})"
                                                >
                                                
                                                <label for="check{{$p->id}}" class="checktoggle">checkbox</label>
                                            </div>
                                            
												<a class="me-3" href="javascript:void(0)" wire:click.prevent="Edit({{$p->id}})" >
													<img src="{{ asset('assets/pos/img/icons/edit.svg') }}"  alt="img">
												</a>
												<a class="me-3" href="javascript:void(0)" onclick="Confirm({{$p->id}})" >
													<img src="{{ asset('assets/pos/img/icons/delete.svg') }}"  alt="img">
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
					<!-- /product list -->
					 
					<div style=" {{ $agregar == 1 ? 'display:block;' : 'display:none;' }}">
					@include('livewire.promos.agregar-editar-promo')    
					</div>
					

            		@include('livewire.promos.variaciones')					
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

		window.livewire.on('variaciones', msg => {
			$('#Variaciones').modal('show')
		});
		window.livewire.on('variaciones-hide', msg => {
			$('#Variaciones').modal('hide')
		});
		window.livewire.on('show-modal', msg => {
			$('#theModal').modal('show')
		});
		window.livewire.on('category-added', msg => {
			noty(msg)
		});
		window.livewire.on('category-updated', msg => {
			noty(msg)
		});
		
		
		
		


	});

</script>


<script>
    var seleccionados = [];

    function seleccionarTodos() {
        var checkboxes = document.querySelectorAll('input[type="checkbox"]');
        var selectAllCheckbox = document.getElementById('select-all-checkbox');

        if (selectAllCheckbox.checked) {
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = true;
                actualizarSeleccion(checkbox);
            });
        } else {
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = false;
                actualizarSeleccion(checkbox);
            });
        }
    }

    function actualizarSeleccion(checkbox) {
        if (checkbox.checked) {
            seleccionados.push(checkbox.value);
        } else {
            var index = seleccionados.indexOf(checkbox.value);
            if (index !== -1) {
                seleccionados.splice(index, 1);
            }
        }
    }

    function guardarSeleccion() {
        window.livewire.emit('GuardarVariaciones', seleccionados);
        console.log(seleccionados);
        seleccionados = [];
    }
</script>
