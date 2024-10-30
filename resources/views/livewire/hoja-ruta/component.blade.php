<div>
@if($ver == 0)
	                <div class="page-header">
					<div class="page-title">
							<h4>Hoja de ruta</h4>
							<h6>Ver listado de hojas de ruta</h6>
						</div>
						<div class="page-btn">               											    
                			
                			@if(Auth::user()->profile != "Cajero" )
							<a href="javascript:void(0)" wire:click="AbrirModal" class="btn btn-added"><img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar Hoja de ruta</a>
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
											    @if(Auth::user()->sucursal != 1)
											    @if(Auth::user()->profile != "Cajero" )
												<label class="checkboxs">
												    <input name="Todos" type="checkbox" value="1" onclick="CheckTodosLote()" class="check_todos"/>    
                            						<span class="checkmarks"></span>
												</label>
												@endif
												@endif
												
												</th>
											<th>Nro Hoja de ruta</th>
											<th>Transportista</th>
											<th>Fecha</th>
											<th>Turno</th>
											<th>Acciones</th>
										</tr>
									</thead>
									<tbody>

										
    						@foreach($data as $hr)
							<tr>
								<td>
								    @if(Auth::user()->sucursal != 1)
								    @if(Auth::user()->profile != "Cajero" )
										<label class="checkboxs">
										    <input type="checkbox" tu-attr-id="{{($hr->id)}}"  class="mis-checkboxes" value="{{$hr->id}}">
								    		<span class="checkmarks"></span>
										</label>
									@endif
									@endif
								</td>
								<td> Hoja nro {{$hr->nro_hoja}}	</td>
								<td>
								{{$hr->nombre}}
										@if ($hr->tipo != '')
										({{$hr->tipo}})
										@else

										@endif
								</td>
								<td>
									{{\Carbon\Carbon::parse($hr->fecha)->format('d-m-Y')}}
								</td>
								<td>
									{{$hr->turno}}
								</td>

								<td>
								<a style="color: black !important; background: #FAFBFE !important; padding: 1px 8px; border-radius: 8px; border: 1px solid #E9ECEF;" href="javascript:void(0)" class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"></a>
                                <div class="dropdown-menu"> 
			     		            <a href="{{ $app_url }}/hoja-ruta?hoja_id={{$hr->id}}" class="dropdown-item">Ver ventas asociadas</a>
                                	<div class="dropdown-divider"></div>
			     		            <button hidden wire:click.prevent="Ver({{$hr->id}})" class="dropdown-item">Ver ventas asociadas</button>
									<button wire:click.prevent="Edit({{$hr->id}})" class="dropdown-item">Editar</button>
									<button onclick="Confirm('{{$hr->id}}')"  class="dropdown-item">Eliminar</button>
			     		            <a hidden href="{{ url('report-hoja-ruta/excel' . '/' . ($hr->id == '' ? '0' : $hr->id).'/'.uniqid()) }}" target="_blank" class="dropdown-item">Exportar Excel Hoja Ruta</a>
			     		            <a hidden href="{{ url('report-hoja-ruta-consolidado/excel' . '/' . ($hr->id == '' ? '0' : $hr->id).'/'.uniqid()) }}" target="_blank" class="dropdown-item">Exportar Excel Consolidado</a>
			     		            <div class="dropdown-divider"></div>
			     		            <a href="{{ url('report-hoja-ruta/pdf' . '/' . ($hr->id == '' ? '0' : $hr->id).'/'.uniqid()) }}" target="_blank" class="dropdown-item">Exportar PDF Hoja de ruta</a>
			     		            <a href="{{ url('report-hoja-ruta-consolidado/pdf' . '/' . ($hr->id == '' ? '0' : $hr->id).'/'.uniqid()) }}" target="_blank" class="dropdown-item">Exportar PDF Consolidado</a>
	    						
			     		        </div>											
								</td>
							</tr>
							@endforeach
							</tbody>
							</table>
							{{$data->links()}}
							</div>
						</div>
					</div>
			
    
    @endif

	@if($ver == 1)
		@include('livewire.hoja-ruta.ver-ventas-asociadas')
	@endif
	
	@include('livewire.hoja-ruta.form')
	@include('livewire.hoja-ruta.form-agregar-venta')
</div>


<script>

    document.addEventListener('livewire:load', function () {
        @this.on('seleccionar-ventas-hoja-ruta', ids => {
            ids.forEach(id => {
                document.querySelector(`input[value="${id}"]`).checked = true;
            });
            
            $('#AgregarVenta').modal('show')
        });
    });
    
	document.addEventListener('DOMContentLoaded', function() {

		window.livewire.on('modal-show', msg => {
			$('#theModal').modal('show')
		});
		window.livewire.on('modal-hide', msg => {
			$('#theModal').modal('hide')
		});

		window.livewire.on('seleccionar-ventas-hoja-ruta-hide', msg => {
			$('#AgregarVenta').modal('hide')
		});
		
		window.livewire.on('noty', msg => {
			Noty(msg);
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
	
	    function Agregar() {
        
          const ids = [];
        
          $('.mis-checkboxes').each(function() {
            if($(this).hasClass('mis-checkboxes')) {
              
              if($(this).is(':checked')) {
              ids.push($(this).attr('tu-attr-id'));    
              }
               
            }
          });
          
          window.livewire.emit('agregar-lote', ids);
        
        document.querySelectorAll('.mis-check_todos').forEach(function(checkElement) {
        checkElement.checked = false;
        });
          
        }    
</script>
