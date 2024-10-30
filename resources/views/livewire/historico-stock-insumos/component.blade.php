<div class="row sales layout-top-spacing">

	<div class="col-sm-12">
		@include('livewire.historico-stock.sales-detail')
		<div class="widget widget-chart-one">
			<div class="widget-heading">

				<h4 class="card-title">
					<b>{{$componentName}} | {{$pageTitle}}</b>
				</h4>
				<ul class="tabs tab-pills">
                        @can('product_create')
					<li hidden>
						<a href="{{ url('report-producto/excel' ) }}" class="tabmenu bg-dark mr-3">Exportar</a>
					</li>
                        @endcan
				</ul>
			</div>



			<div class="row justify-content-between">

					<div class="col-lg-4 col-md-3 col-sm-3">

						<div class="input-group mb-4">
							<div class="input-group-prepend">
								<span class="input-group-text input-gp">
									<i class="fas fa-search"></i>
								</span>
							</div>
							<input type="text" wire:model="search" placeholder="Buscar" class="form-control"
							>
						</div>

					</div>

						<div class="col-lg-4 col-md-3 col-sm-3">

									<div class="input-group mb-4">
										<div class="input-group-prepend">
											<span class="input-group-text input-gp">
												<i class="fas fa-users"></i>
											</span>
										</div>
										<select wire:model='id_categoria' class="form-control">
											<option value="Elegir" disabled >Elegir</option>
											@foreach($usuario as $u)
											<option value="{{$u->id}}">{{$u->name}}</option>
											@endforeach


										</select>

									</div>

								</div>

				<div class="col-lg-4 col-md-3 col-sm-3">

					<div class="input-group mb-4">
						<div class="input-group-prepend">
							<span class="input-group-text input-gp">
								Movimiento
							</span>
						</div>
						<select wire:model='tipo_movimiento_id' class="form-control">
							<option value="Elegir" disabled >Elegir</option>
							<option value="0" >Todos</option>
							@foreach($tipo_movimiento as $t)
							<option value="{{$t->id}}">{{$t->nombre}}</option>
							@endforeach

						</select>

					</div>

				</div>


			</div>
			<div class="widget-content">

				<div class="table-responsive">
					<table  id="default-ordering" class="table table-hover">
						<thead class="text-white" style="background: #3B3F5C; vertical-align: middle !important;">
							<tr>
							    <th style="background: #3B3F5C; vertical-align: middle !important;" class="table-th text-white">ID PRODUCCION</th>
								<th style="background: #3B3F5C; vertical-align: middle !important;" class="table-th text-white">PRODUCTO</th>
								<th style="background: #3B3F5C; vertical-align: middle !important;" class="table-th text-white text-center">BARCODE</th>
								<th style="background: #3B3F5C; vertical-align: middle !important;" class="table-th text-white text-center">TIPO DE MOVIMIENTO</th>
								<th style="background: #3B3F5C; vertical-align: middle !important;" class="table-th text-white text-center">CANTIDAD MOVIMIENTO UNID.</th>
								<th style="background: #3B3F5C; vertical-align: middle !important;" class="table-th text-white text-center">UNIDADES</th>
								<th style="background: #3B3F5C; vertical-align: middle !important;" class="table-th text-white text-center">STOCK</th>
								<th style="background: #3B3F5C; vertical-align: middle !important;" class="table-th text-white text-center">FECHA</th>
							</tr>
						</thead>
						<tbody>
							@foreach($data as $historico)
							<tr>
							    <td>
									<h6 class="text-left">{{$historico->produccion_detalle_id}}</h6>
								</td>
								<td>
									<h6 class="text-left">{{$historico->name}}</h6>
								</td>
								<td>
									<h6 class="text-center">{{$historico->barcode}}</h6>
								</td>
								<td>
									<h6 class="text-center">
										@if($historico->sale_id != null)
										<a class="btn" wire:click.prevent="getDetails({{$historico->sale_id}})">{{$historico->tipo_movimiento}} Nro {{$historico->sale_id}}</a>
										@else
										{{$historico->tipo_movimiento}}
										@endif
									</h6>
								</td>
								<td>
									<h6 class="text-center">{{$historico->cantidad_movimiento * $historico->cantidad}} {{$historico->unidad_medida}}</h6>
								</td>
								<td>
									<h6 class="text-center">{{$historico->stock}}</h6>
								</td>
									<td>
									<h6 class="text-center">{{$historico->cantidad*$historico->stock}} {{$historico->unidad_medida}}</h6>
								</td>
								<td>
									<h6 class="text-center">  {{\Carbon\Carbon::parse($historico->created_at)->format('d-m-Y H:i')}}</h6>
								</td>

							</tr>
							@endforeach
						</tbody>
					</table>
					{{$data->links()}}
				</div>

			</div>


		</div>

	</div>

</div>



<script>
	document.addEventListener('DOMContentLoaded', function() {

		window.livewire.on('product-added', msg => {
			$('#theModal').modal('hide')
		});
		window.livewire.on('product-updated', msg => {
			$('#theModal').modal('hide')
		});
		window.livewire.on('product-deleted', msg => {
			// noty
		});
		window.livewire.on('show-modal', msg => {
			$('#modalDetails').modal('show')
		});
		window.livewire.on('modal-hide', msg => {
			$('#theModal1').modal('hide')
		});
		window.livewire.on('hidden.bs.modal', msg => {
			$('.er').css('display', 'none')
		});
		$('#theModal').on('hidden.bs.modal', function(e) {
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

	function ConfirmCheck(id) {

		swal({
			title: 'CONFIRMAR',
			text: '¿CONFIRMAS ELIMINAR LOS REGISTROS?',
			type: 'warning',
			showCancelButton: true,
			cancelButtonText: 'Cerrar',
			cancelButtonColor: '#fff',
			confirmButtonColor: '#3B3F5C',
			confirmButtonText: 'Aceptar'
		}).then(function(result) {
			if (result.value) {
				window.livewire.emit('ConfirmCheck', id)
				swal.close()
			}

		})
	}
</script>
<script>
		$('#default-ordering').DataTable( {
				"stripeClasses": [],
				drawCallback: function () { $('.dataTables_paginate > .pagination').addClass(' pagination-style-13 pagination-bordered mb-5'); }
	} );
</script>
