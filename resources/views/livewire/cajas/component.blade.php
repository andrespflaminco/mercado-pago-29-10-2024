<div>	  

@if($configuracion_ver == 0)
	                <div class="page-header">
					<div class="page-title">
							<h4>Caja</h4> 
							<h6>Ver listado de cajas diarias</h6>
						</div>
						@include('livewire.cajas.botones-abrir-cerrar-caja')
					</div>
					
                    
					<!-- /product list -->
					<div class="card">
					    @can('ver caja sucursales')
					    
					    @if(0 < $sucursales->count() )
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
                    	@endif
				        
				        @endcan
				        
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
                                        
                                        @if(auth()->user()->sucursal != 1)
            						    @can('configurar cajas')
            						    <li>
										<a style="font-size:12px !important; padding:5px !important; background: #F8F9FA !important; color:#212B36 !important; border:solid 1px #212B36 !important; " class="btn btn-cancel" href="javascript:void(0)" wire:click="AbrirModalConfiguracion()">
            							    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
            							Configuracion
            							</a>
										</li>
										@endcan
										@endif
										
										<li>
											<a hidden title="excel"><img  src="{{ asset('assets/pos/img/icons/excel.svg') }}" alt="img"></a>
										</li>
										<li>
											<a hidden data-bs-toggle="tooltip" data-bs-placement="top" title="print"><img  src="{{ asset('assets/pos/img/icons/printer.svg') }}" alt="img"></a>
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
        									<th>CAJA NRO</th>
            								<th>FECHA APERTURA</th>
            								<th>FECHA CIERRE</th>
            								<th>HORA APERTURA</th>
            								<th>HORA CIERRE</th>
            								<th>USUARIO</th>
            								<th>ESTADO</th>
            								@can('ver resumen caja')
                                 			<th hidden>RETIRABLE EN EFECTIVO</th>
            					            @endcan
            								<th>ACCIONES</th>
										</tr>
									</thead>
									<tbody>
							@foreach($datos as $metodo)
							<tr>
								<td>
								{{$metodo->nro_caja}}
								</td>
								<td>
								{{\Carbon\Carbon::parse( $metodo->fecha_inicio)->format('d-m-Y')}}
								</td>
								<td>
								{{\Carbon\Carbon::parse( $metodo->fecha_cierre)->format('d-m-Y')}}
								</td>
								<td>
								{{\Carbon\Carbon::parse( $metodo->fecha_inicio)->format('H:i')}} hs
								</td>
								<td>
								{{\Carbon\Carbon::parse( $metodo->fecha_cierre)->format('H:i')}} hs
								</td>
								<td>
								{{$metodo->name}}
								</td>
								<td>
								
										@if($metodo->estado === 0)
										Activo
										@else
										Caja cerrada
										@endif

								</td>


                                @can('ver resumen caja')
                                <td hidden class="text-center">
                                @if($metodo->estado == 1)
                                 $   {{number_format($metodo->monto_final,2,",",".")}}
                                @endif
                                </td>
            					@endcan
            					

								<td>
								
								<div class="btn-group mb-1 mr-1" role="group">
						         <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg></button>
               
               
                                 <div class="dropdown-menu" aria-labelledby="btndefault">
                                @can('ver resumen caja')
                                  <a href="javascript:void(0);" wire:click.prevent="GetCaja({{$metodo->id}})"  class="dropdown-item"><i class="flaticon-dots mr-1"></i>  RESUMEN </a>
                                @endcan
                                
                                <!------ CAJAS POR SUCURSAL ----->
                                
                                @if($configuracion_caja == 0)
                                @if($metodo->estado == 0)

                                    @can('cerrar caja')
                                        <a href="javascript:void(0);" wire:click.prevent="CerrarModal({{$metodo->id}})" class="dropdown-item">
                                            <i class="flaticon-dots mr-1"></i> CERRAR CAJA
                                        </a>
                                    @endcan

                                @endif
                                @endif
                                
                                <!------ / CAJAS POR SUCURSAL ----->
                                
                                <!------ CAJAS POR USUARIO ----->
                                @if($configuracion_caja == 1)
                                @if($metodo->estado == 0)
                                    @can('cerrar caja otros usuarios')
                                        <a href="javascript:void(0);" wire:click.prevent="CerrarModal({{$metodo->id}})" class="dropdown-item">
                                            <i class="flaticon-dots mr-1"></i> CERRAR CAJA
                                        </a>
                                    @else
                                    
                                        @if($metodo->user_id == auth()->user()->id)
                                            @can('cerrar caja')
                                                <a href="javascript:void(0);" wire:click.prevent="CerrarModal({{$metodo->id}})" class="dropdown-item">
                                                    <i class="flaticon-dots mr-1"></i> CERRAR CAJA
                                                </a>
                                            @endcan
                                        @endif
                                    @endcan
                                @endif
                                @endif
                                
                                <!------ CAJAS POR USUARIO ----->
                                
                                									
                                
                                @can('ver detalle caja')
                                  <a href="{{ url('cajas-detalle' . '/' . $metodo->id) }}" target="_blank" class="dropdown-item"><i class="flaticon-dots mr-1"></i>  DETALLE </a>
                                @endcan
                                
                                @can('editar caja')
                                  <a href="javascript:void(0);" wire:click.prevent="EditCaja({{$metodo->id}})"  class="dropdown-item"><i class="flaticon-dots mr-1"></i>  EDITAR </a>
                                @endcan
                                
                                @can('ver ingresos y retiros')
                                  <a href="javascript:void(0);" wire:click.prevent="ModalResumenIngresoRetiro({{$metodo->id}})"  class="dropdown-item"><i class="flaticon-dots mr-1"></i>  INGRESOS Y RETIROS DE DINERO </a>
                                @endcan
                                
                                @can('eliminar caja')
                                  <a href="javascript:void(0);" onclick="ConfirmEliminar({{$metodo->id}})" class="dropdown-item"><i class="flaticon-dots mr-1"></i> <i class="fas fa-trash"></i> ELIMINAR </a>
                                @endcan
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
@endif

@if($configuracion_ver == 1)
@include('livewire.cajas.form-configuracion')
@endif



@include('livewire.cajas.form-cerrar')
@include('livewire.cajas.form-editar')
@include('livewire.cajas.form-abrir')
@include('livewire.cajas.form-caja-anterior')
@include('livewire.cajas.modal-ingreso-retiro')
@include('livewire.cajas.modal-resumen-ingreso-retiro')
@include('livewire.cajas.cajas')

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