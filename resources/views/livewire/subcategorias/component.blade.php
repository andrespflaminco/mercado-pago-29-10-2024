<div >	                
	                @if($agregar == 0)
	                <div class="page-header">
					<div class="page-title">
							<h4>Subcategorias</h4>
							<h6>Ver listado de subcategorias</h6>
						</div>
						<div class="page-btn">               											    
                			
                			@if(Auth::user()->sucursal != 1)
                			@if(Auth::user()->profile != "Cajero" )
							<a href="javascript:void(0)" wire:click="Agregar()" class="btn btn-added"><img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar subcategoria</a>
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
							                											    
                		     @if(Auth::user()->sucursal != 1)				   
                			 @if(Auth::user()->profile != "Cajero" )
							 @include('common.accion-lote')
							 @endif
							 @endif
							 
							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
											<th>
											    
											    @if(Auth::user()->sucursal != 1)
											    @if(Auth::user()->profile != "Cajero" )
												<label class="checkboxs">
												    <input name="Todos" type="checkbox" value="1" onclick="CheckTodosLote()" class="check_todos"/>    
                            						<span class="checkmarks"></span>
												</label>
												@endif
												@endif
												
												</th>
											<th>SUBCATEGORIA</th>
											<th>Categoria a la que pertenece</th>
											<th>Acciones</th>
										</tr>
									</thead>
									<tbody>
									    @foreach($subcategorias as $subcategoria)
										<tr>
											<td>
											    @if(Auth::user()->sucursal != 1)
											    @if(Auth::user()->profile != "Cajero" )
												<label class="checkboxs">
												    <input type="checkbox" wire:model.defer="id_check" tu-attr-id="{{($subcategoria->id)}}"  class="mis-checkboxes" value="{{$subcategoria->id}}">
													<span class="checkmarks"></span>
												</label>
												@endif
												@endif
											</td>
											<td >
												<a href="javascript:void(0);" wire:click.prevent="Edit({{$subcategoria->id}})">{{$subcategoria->nombre}}</a>
											</td>
											<td >
												{{$subcategoria->categoria}}
											</td>
											<td>
											    @if(Auth::user()->sucursal != 1)
											    @if(Auth::user()->profile != "Cajero" )
											    @if($estado_filtro == 0 )
												<a class="me-3" href="javascript:void(0)" wire:click.prevent="Edit({{$subcategoria->id}})" >
													<img src="{{ asset('assets/pos/img/icons/edit.svg') }}"  alt="img">
												</a>
												<a href="javascript:void(0)" onclick="Confirm('{{$subcategoria->id}}')"  >
													<img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="img">
												</a>
												@else
            								    <a href="javascript:void(0)" onclick="RestaurarCategoria('{{$subcategoria->id}}')" class="btn btn-light" title="Restaurar">
            										RESTAURAR
            									</a>
            								    
            								    @endif
            								    @endif
            								    @endif
            								    
											</td>
										</tr>
										@endforeach
									</tbody>
								</table>
								{{$subcategorias->links()}}
							</div>
						</div>
					</div>
					
					<!-- /product list -->
					@endif 
					
					@if($agregar == 1)
					@include('livewire.subcategorias.agregar-editar-categoria')
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

<script type="text/javascript">

	function RestaurarCategoria(id) {

    swal({
      title: 'CONFIRMAR',
      text: 'QUIERE RESTAURAR LA SUBCATEGORIA?',
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