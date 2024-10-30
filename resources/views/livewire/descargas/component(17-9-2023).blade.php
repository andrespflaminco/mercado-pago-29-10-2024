<div class="row sales layout-top-spacing">

	<div class="col-sm-12">
		<div class="widget widget-chart-one">
			<div class="widget-heading">
				<h4 class="card-title">
					<b>Descargas | {{$pageTitle}}</b>
				</h4>
			</div>
			@can('category_search')
			@include('common.searchbox')
			@endcan

			<div class="widget-content">


				<div class="table-responsive">
					<table class="table table-bordered table striped mt-1">
						<thead class="text-white" style="background: #3B3F5C">
							<tr>
								<th class="table-th text-white">Tipo de descarga</th>
								<th class="table-th text-white">Fecha de creacion</th>
								<th class="table-th text-white text-center">Descargar</th>
							</tr>
						</thead>
						<tbody>
							@foreach($reportes as $r)
							<tr>
								<td>
									<h6>
									    @if($r->tipo == "exportar_productos")
									    Excel de Catalogo
									    @endif
									    @if($r->tipo == "exportar_etiquetas")
									    PDF de etiquetas
									    @endif
									    @if($r->tipo == "exportar_etiquetas_excel")
									    Excel de etiquetas
									    @endif
									</h6>
								</td>
								
								<td>
									<h6>{{\Carbon\Carbon::parse($r->created_at)->format('d/m/Y H:i')}} hs.</h6>
								</td>
									<td class="text-center">
									    @if($r->estado == 0 )
											<span class="badge badge-warning text-uppercase">En preparacion</span>
								        @endif
								        @if($r->estado == 1 )
											<span class="badge badge-warning text-uppercase">En preparacion</span>
								        @endif
								        @if($r->estado == 2 )
								        
								        @if($r->tipo != "exportar_etiquetas_excel")
								        <a href="javascript:void(0)" wire:click="Descargar('{{$r->id}}')" class="btn btn-dark" title="Descargar">
										<i class="fas fa-download"></i>
										
									    </a>
									    @endif
									    
									     @if($r->tipo == "exportar_etiquetas_excel")
									    <a href="javascript:void(0)" wire:click="DescargarExcel('{{$r->id}}')" class="btn btn-dark" title="Descargar Excel">
										<i class="fas fa-file-excel"></i>
										</a>
								        @endif
									    
								        @endif
								        
								       
									</td>
								
							</tr>
							@endforeach
						</tbody>
					</table>
					{{$reportes->links()}}
				</div>

			</div>


		</div>


	</div>
</div>


<script>
	document.addEventListener('DOMContentLoaded', function() {

		window.livewire.on('show-modal', msg => {
			$('#theModal').modal('show')
		});
		window.livewire.on('category-added', msg => {
			$('#theModal').modal('hide')
		});
		window.livewire.on('category-updated', msg => {
			$('#theModal').modal('hide')
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
</script>
