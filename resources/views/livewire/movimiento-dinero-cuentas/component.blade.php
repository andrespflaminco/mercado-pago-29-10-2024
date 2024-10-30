<div >	                
	                @if($agregar == 0)
	                <div class="page-header">
					<div class="page-title">
							<h4>Movimientos de dinero entre cuentas</h4>
							<h6>Ver listado de movimientos de dinero</h6>
						</div>
						<div class="page-btn">               											    
                			
                			@if(Auth::user()->sucursal != 1)
                			@if(Auth::user()->profile != "Cajero" )
							<a href="javascript:void(0)" wire:click="Agregar()" class="btn btn-added"><img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar movimiento</a>
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
											<th>Cuenta Origen</th>
											<th>Cuenta destino</th>
											<th>Monto</th>
											<th>Acciones</th>
										</tr>
									</thead>
									<tbody>
									    @foreach($movimientos as $movimiento)
										<tr>
											<td>
											    @if(Auth::user()->sucursal != 1)
											    @if(Auth::user()->profile != "Cajero" )
												<label class="checkboxs">
												    <input type="checkbox" wire:model.defer="id_check" tu-attr-id="{{($movimiento->id)}}"  class="mis-checkboxes" value="{{$movimiento->id}}">
													<span class="checkmarks"></span>
												</label>
												@endif
												@endif
											</td>
											<td >
												<a href="javascript:void(0);" wire:click.prevent="Edit({{$movimiento->id}})">{{$movimiento->banco_origen_name}}</a>
											</td>
											<td >
												<a href="javascript:void(0);" wire:click.prevent="Edit({{$movimiento->id}})">{{$movimiento->banco_destino_name}}</a>
											</td>
											<td >
												<a href="javascript:void(0);" wire:click.prevent="Edit({{$movimiento->id}})">$ {{number_format($movimiento->monto,2,",",".")}}</a>
											</td>
								            
											<td>
											    @if(Auth::user()->sucursal != 1)
											    @if(Auth::user()->profile != "Cajero" )
											    @if($estado_filtro == 0 )
												<a class="me-3" href="javascript:void(0)" wire:click.prevent="Edit({{$movimiento->id}})" >
													<img src="{{ asset('assets/pos/img/icons/edit.svg') }}"  alt="img">
												</a>
												<a href="javascript:void(0)" onclick="Confirm('{{$movimiento->id}}')"  >
													<img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="img">
												</a>
												@else
            								    <a href="javascript:void(0)" onclick="Restaurar('{{$movimiento->id}}')" class="btn btn-light" title="Restaurar">
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
								{{$movimientos->links()}}
							</div>
						</div>
					</div>
					
					<!-- /product list -->
					@endif 
					
					@if($agregar == 1)
					@include('livewire.movimiento-dinero-cuentas.agregar-editar')
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

	function Restaurar(id) {

    swal({
      title: 'CONFIRMAR',
      text: 'QUIERE RESTAURAR EL MOVIMIENTO?',
      showCancelButton: true,
      cancelButtonText: 'Cancelar',
      cancelButtonColor: '#fff',
      confirmButtonColor: '#3B3F5C',
      confirmButtonText: 'Aceptar'
    }).then(function(result) {
      if (result.value) {
        window.livewire.emit('Restaurar', id)
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