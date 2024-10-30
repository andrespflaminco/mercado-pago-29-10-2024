<div >	                
	                @if($agregar == 0)
	                <div class="page-header">
					<div class="page-title">
							<h4>Proveedores</h4>
							<h6>Ver listado de proveedores</h6>
						</div>
						<div class="page-btn">
							<button onclick="BorrarMsg"  wire:click="Agregar()" class="btn btn-added"><img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar proveedor</button>
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

								<div class="wordset">
									<ul>
										<li>
											<a hidden data-bs-toggle="tooltip" data-bs-placement="top" title="pdf"><img  src="{{ asset('assets/pos/img/icons/pdf.svg') }}"  alt="img"></a>
										</li>
										<li>
											<a  href="{{ url('report/excel-proveedores' . '/'. uniqid() ) }}" target="_blank"  title="excel"><img  src="{{ asset('assets/pos/img/icons/excel.svg') }}" alt="img"></a>
										</li>
										<li>
											<a style="width: 30px; margin-left: -3px;" href="{{ url('import-proveedores') }}" data-bs-toggle="tooltip" data-bs-placement="top" title="importar"><img  src="{{ asset('assets/pos/img/icons/upload.svg') }}"  alt="img"></a>
										</li>
									</ul>
								</div>
							</div>
							
							 @include('common.accion-lote')
							 
							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
											<th>
												<label class="checkboxs">
												    <input name="Todos" type="checkbox" value="1" onclick="CheckTodosLote()" class="check_todos"/>    
                            						<span class="checkmarks"></span>
												</label>
											</th>
											<th>Cod proveedor</th>
											<th>Nombre del proveedor</th>
											<th>Acciones</th>
										</tr>
									</thead>
									<tbody>
									    @foreach($data as $proveedor)
										<tr>
											<td>
												<label class="checkboxs">
												    <input type="checkbox" wire:model.defer="id_check" tu-attr-id="{{($proveedor->id)}}"  class="mis-checkboxes" value="{{$proveedor->id}}">
												    <span class="checkmarks"></span>
												</label>
											</td>
											<td>{{$proveedor->id_proveedor}}</td>
											<td>{{$proveedor->nombre}}</td>
								
											<td>
											    @if(auth()->user()->sucursal != 1)
											    
											    @if($estado_filtro == 0 )
												<a class="me-3" href="javascript:void(0)" wire:click.prevent="Edit({{$proveedor->id}})" >
													<img src="{{ asset('assets/pos/img/icons/edit.svg') }}"  alt="img">
												</a>
												<a href="javascript:void(0)" onclick="Confirm('{{$proveedor->id}}')"  >
													<img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="img">
												</a>
												@else
            								       <a href="javascript:void(0)" onclick="RestaurarProveedor('{{$proveedor->id}}')" class="btn btn-light" title="Delete">
                										RESTAURAR
                									</a>
								    
            								    
            								    @endif
            								    
            								    @else 
            								    
            								    @if($proveedor->creador_id == auth()->user()->id)
            								     @if($estado_filtro == 0 )
												<a class="me-3" href="javascript:void(0)" wire:click.prevent="Edit({{$proveedor->id}})" >
													<img src="{{ asset('assets/pos/img/icons/edit.svg') }}"  alt="img">
												</a>
												<a href="javascript:void(0)" onclick="Confirm('{{$proveedor->id}}')"  >
													<img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="img">
												</a>
												@else
            								       <a href="javascript:void(0)" onclick="RestaurarProveedor('{{$proveedor->id}}')" class="btn btn-light" title="Delete">
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
								{{$data->links()}}
							</div>
						</div>
					</div>
					
					<!-- /product list -->
					@endif 
					
					@if($agregar == 1)
					@include('livewire.proveedores.agregar-editar-proveedor')
					@endif 
					
					
					</div>

<script>
	document.addEventListener('DOMContentLoaded', function() {

		window.livewire.on('msg', msg => {
			noty(msg)
		});

		

	});
</script>

<script>
    
    function BorrarMsg() {
         alert("text-danger er");
    }

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
	
function RestaurarProveedor(id) {

    swal({
      title: 'CONFIRMAR',
      text: 'QUIERE RESTAURAR EL PROVEEDOR?',
      showCancelButton: true,
      cancelButtonText: 'Cancelar',
      cancelButtonColor: '#fff',
      confirmButtonColor: '#3B3F5C',
      confirmButtonText: 'Aceptar'
    }).then(function(result) {
      if (result.value) {
        window.livewire.emit('RestaurarProveedor', id)
        swal.close()
      } 

    })
  }
	</script>
					