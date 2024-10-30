<div class="row sales layout-top-spacing">

	<div class="col-sm-12">
		<div class="widget widget-chart-one">
			<div class="widget-heading">
				<h4 class="card-title">
					<b>{{$componentName}} | {{$pageTitle}}</b>
				</h4>
				<ul class="tabs tab-pills">

					<li>
						<a href="{{ url('report-asistente/excel' . '/' . ($id_proveedor == '' ? '0' : $id_proveedor) . '/' . $tipo  . '/' . ($search == '' ? '0' : $search) ) }}" target="_blank" class="tabmenu bg-dark mr-3">Exportar</a>
					</li>

				</ul>
			</div>
			@can('product_search')

			<div class="row justify-content-between">

					<div class="col-lg-4 col-md-4 col-sm-4">

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

				<div class="col-lg-4 col-md-4 col-sm-4">

					<div class="input-group mb-4">
						<div class="input-group-prepend">
							<span class="input-group-text input-gp">
								Tipo:
							</span>
						</div>
						<select wire:model='tipo' class="form-control">
							<option value="Elegir" disabled >Elegir</option>
							<option value="1" >A comprar</option>
							<option value="2" >Todos</option>

						</select>

					</div>

				</div>
				<div class="col-lg-4 col-md-4 col-sm-4">

					<div class="input-group mb-4">
						<div class="input-group-prepend">
							<span class="input-group-text input-gp">
								<i class="fas fa-truck"></i>
							</span>
						</div>
						<select wire:model='id_proveedor' class="form-control">
							<option value="Elegir" disabled >Elegir</option>
							<option value="0" >Todos</option>
							@foreach ($prov as $pr)
							<option value="{{$pr->id}}" >{{$pr->nombre}}</option>

							@endforeach
						</select>

					</div>

				</div>
			</div>

			@endcan

			<div class="widget-content">

				<div class="table-responsive">
					<table class="table table-bordered table striped mt-1">
						<thead class="text-white" style="background: #3B3F5C;">
							<tr>
								<th class="table-th text-white">COD. PROVEEDOR</th>
								<th class="table-th text-white">DESCRIPCIÓN</th>
								<th class="table-th text-white text-center">CATEGORÍA</th>
								<th class="table-th text-white text-center">PROVEEDOR</th>
								<th class="table-th text-white text-center">STOCK</th>
								<th class="table-th text-white text-center">INV.IDEAL</th>
								<th class="table-th text-white">IMAGEN</th>
								<th class="table-th text-white text-center"> A COMPRAR </th>
							</tr>
						</thead>
						<tbody>
							@foreach($data as $product)

							@if($this->tipo == 1)

							@if(($product->inv_ideal - $product->stock) > 0)
							<tr>

								<td>
									<h6 class="text-left">{{$product->cod_proveedor}}</h6>
								</td>
								<td>
									<h6 class="text-left">{{$product->name}}</h6>
								</td>
								<td>
									<h6 class="text-center">{{$product->category}}</h6>
								</td>
								<td>
									<h6 class="text-center">{{$product->nombre_proveedor}}</h6>
								</td>
								<td>
									<h6 class="text-center {{$product->stock <= $product->alerts ? 'text-danger' : '' }} ">
										{{$product->stock}}
									</h6>
								</td>
								<td>
									<h6 class="text-center">{{$product->inv_ideal}}</h6>
								</td>
								<td class="text-center">
									<span>
										<img src="{{ asset('storage/products/' . $product->imagen ) }}" alt="{{$product->name}}" height="70" width="80" class="rounded">

									</span>
								</td>
									<td class="text-center">
											<span class="badge {{$product->stock <= $product->alerts ? 'badge-danger' : 'badge-secondary' }}  text-uppercase">{{$product->comprar}}</span>
									</td>

							</tr>

							@else

							@endif

							@else
							<!----------------------------------------------------------------->

							<tr>


								<td>
									<h6 class="text-left">{{$product->cod_proveedor}}</h6>
								</td>
								<td>
									<h6 class="text-left">{{$product->name}}</h6>
								</td>
								<td>
									<h6 class="text-center">{{$product->category}}</h6>
								</td>
								<td>
									<h6 class="text-center">{{$product->nombre_proveedor}}</h6>
								</td>
								<td>
									<h6 class="text-center {{$product->stock <= $product->alerts ? 'text-danger' : '' }} ">
										{{$product->stock}}
									</h6>
								</td>
								<td>
									<h6 class="text-center">{{$product->inv_ideal}}</h6>
								</td>
								<td class="text-center">
									<span>
										<img src="{{ asset('storage/products/' . $product->imagen ) }}" alt="{{$product->name}}" height="70" width="80" class="rounded">

									</span>
								</td>
									<td class="text-center">
										@if(($product->inv_ideal-$product->stock) < 1)

									@else


											<span class="badge {{$product->stock <= $product->alerts ? 'badge-danger' : 'badge-secondary' }}  text-uppercase">{{$product->inv_ideal-$product->stock}}</span>


								@endif
								</td>


							</tr>
							@endif
							@endforeach
						</tbody>
					</table>
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
		window.livewire.on('modal-show', msg => {
			$('#theModal').modal('show')
		});
		window.livewire.on('modal-hide', msg => {
			$('#theModal').modal('hide')
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
</script>
