<div >	                
	                @if($agregar == 0)
	                <div class="page-header">
					<div class="page-title">
							<h4>Comisiones por vendedor</h4>
							<h6>Ver listado de ventas por vendedor</h6>
						</div>
						<div class="page-btn">               											    
                			<a class="btn btn-added" href=" {{ url('comisiones') }}">Asignar comisiones</a>
            
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
									<input type="text" autocomplete="off" wire:model="search" placeholder="Buscar.." class="form-control" style="width: auto !important;"	>
									
									<input type="text" id="date-range-picker" name="date_range" style="display: block;
									    margin-left: 15px;
                                        width: 100%;
                                        padding: .375rem .75rem;
                                        font-size: 1rem;
                                        font-weight: 400;
                                        line-height: 1.5;
                                        color: #212529;
                                        background-color: #fff;
                                        background-clip: padding-box;
                                        border: 1px solid #ced4da;
                                        -webkit-appearance: none;
                                        -moz-appearance: none;
                                        appearance: none;
                                        border-radius: .25rem;
                                        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;"/>
									
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
											<th>Vendedor</th>
                                            <th>Total ventas</th>
                                        	<th>Comision</th>
                                        	<th>Acciones</th>
										</tr>
									</thead>
									<tbody>
									    @foreach( $comisiones as $cv)
												<tr>
													<td>{{$cv->name}}</td>
													<td>$ {{ number_format($cv->total_ventas,2)}}</td>
													<td>$ {{ number_format($cv->comision,2)}}</td>
													<td>
        												<a wire:click.prevent="Ver({{$cv->user_id}})" class="me-3" href="javascript:void(0)">
        													<img src="{{ asset('assets/pos/img/icons/eye.svg') }}" alt="img">
        												</a>
													</td>
												</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
					</div>
					
					<!-- /product list -->
					@endif 
					
					@if($agregar == 1)
					@include('livewire.comisiones-resumen.ver-ventas')
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