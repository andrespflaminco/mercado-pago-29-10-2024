<div>	  

	                <div class="page-header">
					<div class="page-title">
							<h4>Ingresos y Retiros de Capital</h4> 
							<h6>Ver listado de ingresos y retiros realizados</h6>
						</div>
					    <a href="javascript:void(0)" class="btn btn-added" wire:click.prevent="ModalIngresoRetiro()" wire:loading.attr="disabled">+ AGREGAR INGRESO / RETIRO</a>
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
										</li>
									</ul>
								</div>
							</div>
							<!-- /Filter -->
							
								<div class="card mb-0" id="filter_inputs">
								<div class="card-body pb-0">
									<div class="row">
										<div class="col-lg-12 col-sm-12">
											<div class="row">
										
												<div class="col-lg col-sm-6 col-12">
												 <div class="form-group">
                                    				<label>Fecha desde</label>
                                    				<input type="date" wire:model.defer="dateFrom" class="form-control">
                                    
                                    			</div>
												</div>
												<div class="col-lg col-sm-6 col-12">
												 <div class="form-group">
                                    				<label>Fecha hasta</label>
                                    				<input type="date" wire:model.defer="dateTo" class="form-control">
                                    
                                    			</div>
												</div>
												
												<div class="col-lg-1 col-sm-6 col-12">
													<div class="form-group">
													    <label style="margin-top: 28px !important;"></label>
													    <button class="btn btn-filters ms-auto" wire:click="Filtrar()" >
													     <img src="{{ asset('assets/pos/img/icons/search-whites.svg') }}" alt="img">   
													    </button>
													</div>
												</div>
												<div class="col-lg col-sm-6 col-12 ">
													<div class="form-group">
													
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							
							<!-- /Filter -->
							<div class="table-responsive" style="min-height: 400px !important;">
								<table class="table">
									<thead>
										<tr>
        									<th>FECHA</th>
            								<th>TIPO</th>
            								<th>BANCO</th>
            								<th>MONTO</th>
            								<th>CAJA</th>
            								<th>DESCRIPCION</th>
            								<th>ACCIONES</th>
										</tr>
									</thead>
									<tbody>
							@foreach($datos as $metodo)
							<tr>
								<td>
								{{\Carbon\Carbon::parse( $metodo->created_at)->format('d-m-Y H:i')}}
								</td>
								<td> 
								@if($metodo->monto < 0) Retiro @endif
								@if(0 < $metodo->monto) Ingreso @endif
								</td>
								<td>{{ $metodo->metodo_pago}} </td>
								<td>
								$ {{ number_format( $metodo->monto , 2 , "," , "." ) }} </td>
								<td>
								@if($metodo->caja != null)    
								  @php
								    $caja = $metodo->caja;
        		                    $registro = \App\Models\cajas::find($caja);
                                  @endphp
							
								     Caja #{{ $registro->nro_caja }} 
							
								@else
								    Sin asignar
								@endif
								</td>
								<td>
								    {{$metodo->descripcion}}
								</td>
								<td>
								
								
								<div class="btn-group mb-1 mr-1" role="group">
						         <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg></button>
               
               
                                 <div class="dropdown-menu" aria-labelledby="btndefault">
                                  <a href="javascript:void(0);" wire:click.prevent="EditIngresoRetiro({{$metodo->id}})"  class="dropdown-item"><i class="flaticon-dots mr-1"></i>  EDITAR </a>
                                 </div>
                                  </div>
                                
								</td>
							</tr>
							@endforeach
        						
									</tbody>
								</table>
								{{$datos->links()}}
							</div>
						</div>
					</div>

@include('livewire.ingresos-retiros.modal-ingreso-retiro')

</div>
					
	

<script>
	document.addEventListener('DOMContentLoaded', function() {

		window.livewire.on('product-added', msg => {
			$('#theModal').modal('hide')
		});
		window.livewire.on('product-updated', msg => {
			$('#theModal').modal('hide')
		});
		
		
		window.livewire.on('actualizacion', msg => {
			noty(msg)
		});
		
		window.livewire.on('msg', msg => {
			noty(msg)
		});

		window.livewire.on('cierre', msg => {
			noty(msg)
		});
		window.livewire.on('modal-abrir-show', msg => {
			$('#theModal').modal('show')
		});
		window.livewire.on('modal-abrir-hide', msg => {
			$('#theModal').modal('hide')
		});
		window.livewire.on('modal-ingreso-retiro', msg => {
			$('#theModalIngresoRetiro').modal('show')
		});
		window.livewire.on('modal-ingreso-retiro-hide', msg => {
			$('#theModalIngresoRetiro').modal('hide')
		});
		
		window.livewire.on('modal-resumen-ingreso-retiro', msg => {
			$('#theModalResumenIngresoRetiro').modal('show')
		});
		window.livewire.on('modal-resumen-ingreso-retiro-hide', msg => {
			$('#theModalResumenIngresoRetiro').modal('hide')
		});
		
		window.livewire.on('modal-cerrar-show', msg => {
			$('#theModal2').modal('show')
		});
		window.livewire.on('modal-cerrar-hide', msg => {
			$('#theModal2').modal('hide')
		});
		window.livewire.on('modal-editar-show', msg => {
			$('#theModalEditar').modal('show')
		});
		window.livewire.on('modal-editar-hide', msg => {
			$('#theModalEditar').modal('hide')
		});
		
		window.livewire.on('modal-caja-show', msg => {
			$('#theModalCaja').modal('show')
		});
		window.livewire.on('modal-caja-hide', msg => {
			$('#theModalCaja').modal('hide')
			noty(msg)
		});
		window.livewire.on('tabs-show', msg => {
			$('#tabsModal').modal('show')
		});
		window.livewire.on('tabs-hide', msg => {
			$('#tabsModal').modal('hide')
		});
		window.livewire.on('hidden.bs.modal', msg => {
			$('.er').css('display', 'none')
		});
		$('#theModal').on('hidden.bs.modal', function(e) {
			$('.er').css('display', 'none')
		})
		$('#tabsModal').on('hidden.bs.modal', function(e) {
			$('.er').css('display', 'none')
		})
		$('#theModal').on('shown.bs.modal', function(e) {
			$('.product-name').focus()
		})



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
	
		function ConfirmIngresoRetiro(id) {

		swal({
			title: 'CONFIRMAR',
			text: 'CONFIRMAS ELIMINAR EL INGRESO/RETIRO?',
			type: 'warning',
			showCancelButton: true,
			cancelButtonText: 'Cerrar',
			cancelButtonColor: '#fff',
			confirmButtonColor: '#3B3F5C',
			confirmButtonText: 'Aceptar'
		}).then(function(result) {
			if (result.value) {
				window.livewire.emit('DeleteIngresoRetiro', id)
				swal.close()
			}

		})
	}
	
	
</script>
<script type="text/javascript">
function Confirm2(id_etiqueta) {

  swal({
    title: 'CONFIRMAR',
    text: '¿CONFIRMAS ELIMINAR LA ETIQUETA?',
    type: 'warning',
    showCancelButton: true,
    cancelButtonText: 'Cerrar',
    cancelButtonColor: '#fff',
    confirmButtonColor: '#3B3F5C',
    confirmButtonText: 'Aceptar'
  }).then(function(result) {
    if (result.value) {
      window.livewire.emit('deleteRow2', id_etiqueta)
      swal.close()
    }

  })
}

		function ConfirmEliminar(id) {

			swal({
				title: 'CONFIRMAR',
				text: 'CONFIRMAS ELIMINAR LA CAJA?',
				type: 'warning',
				showCancelButton: true,
				cancelButtonText: 'Cerrar',
				cancelButtonColor: '#fff',
				confirmButtonColor: '#3B3F5C',
				confirmButtonText: 'Aceptar'
			}).then(function(result) {
				if (result.value) {
					window.livewire.emit('deleteCaja', id)
					swal.close()
				}

			})
		}
</script>