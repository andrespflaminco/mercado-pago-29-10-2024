<div>	

@if($usuario->confirmed_at == null)

<div style="padding:30px !important;" class="card mt-3 mb-3">
<div class="row">
<div class="col-12 text-center">
    <h2>Este es un modulo pago, no disponible en la prueba gratuita. </h2>
    <br>
    <br>
    <br>
    <a class="btn btn-submit" href="https://www.flaminco.com.ar/planes/">SUSCRIBIRSE</a>
</div> 
</div> 
</div> 


@else
	                @if($agregar == 0)
	                <div class="page-header">
					<div class="page-title">
							<h4>Puntos de venta</h4>
							<h6>Ver listado de los puntos de venta</h6>
						</div>
						<div class="page-btn">               											    
                			@if(Auth::user()->profile != "Cajero" )
							<a href="javascript:void(0)" wire:click="Agregar()" class="btn btn-added"><img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar punto de venta</a>
						    @endif
						    
						    @if(Auth::user()->id == 1097)
							<a href="javascript:void(0)" wire:click="ObtenerTributos()" class="btn btn-added"><img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">ELIMINAR</a>
					        @endif
					        
						</div>
					</div>
					
                    
					<!-- /product list -->
					<div class="card">
			    	    <ul class="nav nav-tabs  mb-3">
                        <li style="background:white; border: solid 1px #eee;" class="nav-item">
                            <a style="{{ $sucursal_id == $comercio_id ? 'color: #e95f2b;' : '' }}" class="nav-link  {{ $sucursal_id == $comercio_id ? 'active' : '' }} " href="javascript:void(0)"  wire:click="ElegirSucursal({{$comercio_id}})"  > {{auth()->user()->name}} </a>
                        </li>
                        @foreach($sucursales as $item)
                        <li style="background:white; border: solid 1px #eee;"  class="nav-item">
                            <a style="{{ $sucursal_id == $item->sucursal_id ? 'color: #e95f2b;' : '' }}" class="nav-link {{ $sucursal_id == $item->sucursal_id ? 'active' : '' }}" href="javascript:void(0)"  wire:click="ElegirSucursal({{$item->sucursal_id}})"  >{{$item->name}}</a>
                        </li>
                        @endforeach
                    	</ul>
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
							                											    
                		   	 @if(Auth::user()->profile != "Cajero" )
							 @include('common.accion-lote')
							 @endif
							 
							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
											<th>
											    
											    @if(Auth::user()->profile != "Cajero" )
												<label class="checkboxs">
												    <input name="Todos" type="checkbox" value="1" onclick="CheckTodosLote()" class="check_todos"/>    
                            						<span class="checkmarks"></span>
												</label>
												@endif
												
												</th>
										
											<th>Razon social </th>
											<th>Punto de venta</th>
											<th>CUIT</th>
											<th>IIBB</th>
											<th>Condicion IVA</th>
											<th>Relacion precio - IVA</th>
											<th>IVA</th>
											<th>Prioridad</th>
											<th>Conectado en AFIP</th>
											<th>Acciones</th>
										</tr>
									</thead>
									<tbody>
									    @foreach($datos as $dato)
										<tr>
											<td>
											    @if(Auth::user()->profile != "Cajero" )
												<label class="checkboxs">
												    <input type="checkbox" wire:model.defer="id_check" tu-attr-id="{{($dato->id)}}"  class="mis-checkboxes" value="{{$dato->id}}">
													<span class="checkmarks"></span>
												</label>
												@endif
											</td>
										
											</td>
											
											<td >
												<a href="javascript:void(0);" wire:click.prevent="Edit({{$dato->id}})">{{$dato->razon_social}}</a>
											</td>
											<td> {{$dato->pto_venta}}</td>
								            <td> {{$dato->cuit}}</td>
								            <td> {{$dato->iibb}}</td>
								            <td> {{$dato->condicion_iva}}</td>
								            <td> 
								            @if($dato->relacion_precio_iva == 0)
								            Sin relacion
								            @endif
								            @if($dato->relacion_precio_iva == 1)
								            Precio + IVA
								            @endif
								            @if($dato->relacion_precio_iva == 2)
								            IVA incluido en el precio
								            @endif
								            </td>
								            <td> {{$dato->iva_defecto*100}} %</td>
											<td>
											@if($dato->predeterminado == 1)
											<div style="padding: 1px 8px; border-radius: 8px; border: 1px solid #E9ECEF;">
											Predeterminado    
											</div>
											@endif
											</td>
											<td class="text-center">
											@if($dato->habilitado_afip == 1)
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="green" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>
											@else
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="red" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
											@endif
											</td>

											<td>
											    @if(Auth::user()->profile != "Cajero" )
											    @if($estado_filtro == 0 )
										
										        <a id="dropdown-toggle" href="javascript:void(0)" class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"></a>
                                                 	<div class="dropdown-menu">
                                                	    <button wire:click.prevent="Edit({{$dato->id}})"class="dropdown-item">Editar</button>
														<button onclick="Confirm('{{$dato->id}}')"   class="dropdown-item">Eliminar</button>
														<div class="dropdown-divider"></div>
														<button wire:click="EstablecerPredeterminado({{$dato->id}})" class="dropdown-item">Establecer como predeterminado</button>
													</div>     
										        </a>
										  		@else
            								    <a href="javascript:void(0)" onclick="RestaurarCategoria('{{$dato->id}}')" class="btn btn-light" title="Restaurar">
            										RESTAURAR
            									</a>
            								    @endif
            								    @endif
            								    
											</td>
										</tr>
										@endforeach
									</tbody>
								</table>
								{{$datos->links()}}
							</div>
						</div>
					</div>
					
					<!-- /product list -->
					@endif 
					
					@if($agregar == 1)
					@include('livewire.puntos_venta.agregar-editar')
					@endif 
					
					
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