<div >	                
	                @if($agregar == 0)
	                <div class="page-header">
					<div class="page-title">
							<h4>Comisiones</h4>
							<h6>Ver listado de comisiones por vendedor</h6>
						</div>
						<div class="page-btn">               											    
                			
                        <a class="btn btn-added" href=" {{ url('comisiones-resumen') }}">Ver Resumen de Comisiones</a>
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
											<th></th>
											<th>Nombre del vendedor</th>
											<th>Comision</th>
											<th>Acciones</th>
										</tr>
									</thead>
									<tbody>
									    @foreach($comisiones as $comision)
										<tr>
											<td>
											</td>
											<td>
												{{$comision->name}}
											</td>
											<td>
											    @if(0 < $comision->porcentaje_comision)
											    {{$comision->porcentaje_comision}} %
											    @else
											    -
											    @endif
											    </td>
								
											<td>
											    
											    @if(0 < $comision->porcentaje_comision)
											    
											    @if(Auth::user()->profile != "Cajero" )
											    @if($estado_filtro == 0 )
												<a class="me-3" href="javascript:void(0)" wire:click.prevent="Edit({{$comision->id}})" >
													<img src="{{ asset('assets/pos/img/icons/edit.svg') }}"  alt="img">
												</a>
												<a href="javascript:void(0)" onclick="Confirm('{{$comision->id}}')"  >
													<img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="img">
												</a>
												@else
            								    <a href="javascript:void(0)" onclick="Restaurar('{{$comision->id}}')" class="btn btn-light" title="Restaurar">
            										RESTAURAR
            									</a>
            								    
            								    @endif
            								    @endif
            								    
            								    @else
            								    <a class="me-3" href="javascript:void(0)" wire:click.prevent="Agregar({{$comision->user_id}})" >
													<img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img">
												</a>
												
            								    
            								    @endif
            								    
											</td>
										</tr>
										@endforeach
									</tbody>
								</table>
								{{$comisiones->links()}}
							</div>
						</div>
					</div>
					
					<!-- /product list -->
					@endif 
					
					@if($agregar == 1)
					@include('livewire.comisiones.agregar-editar')
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