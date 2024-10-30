<div class="row sales layout-top-spacing">

	<div class="col-sm-12">
		<div class="widget widget-chart-one">
			<div class="widget-heading">
				<h4 class="card-title">
					<b>{{$componentName}} | {{$pageTitle}}</b>
				</h4>
				<ul class="tabs tab-pills">

					<li>

						<a href="javascript:void(0)" class="tabmenu bg-dark" data-toggle="modal" data-target="#theModal">Agregar</a>
					</li>

				</ul>
			</div>

<div class="row">
	@can('product_search')
	<div class="col-lg-3 col-md-3 col-sm-3">
		<div class="form-group">
		 <label>Buscar</label>

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

</div>
	@endcan

	<div class="col-sm-12 col-md-4">
	 <div class="form-group">
		<label>Categoría</label>
			<select wire:model='categoria_search' class="form-control">
				<option value="" >Todos</option>
				<option value="1">Efectivo</option>
				<option value="2">Bancos</option>
				<option value="3">Plataformas de pago</option>
				<option value="4">A cobrar</option>
			</select>
			@error('categoria') <span class="text-danger err">{{ $message }}</span> @enderror
	</div>
	</div>
</div>
			<div class="widget-content">

				<div class="table-responsive">
					<table class="table table-bordered table striped mt-1">
						<thead class="text-white" style="background: #3B3F5C;">
							<tr>
								<th class="table-th text-white">METODO DE PAGO</th>
								<th class="table-th text-white text-center">TIPO DE CUENTA</th>
								<th class="table-th text-white text-center">  </th>
								<th class="table-th text-white text-center">RECARGO</th>
								<th style="width:20%;" class="table-th text-white text-center">ACCIONES</th>
							</tr>
						</thead>
						<tbody>
							@foreach($data as $metodo)
							<tr>
								<td>
									<h6 class="text-left">
										@if($metodo->nombre_banco != null)
										{{$metodo->nombre_banco}} -
										@endif
										{{$metodo->nombre}}

								</h6>
								</td>
								<td>
									<h6 class="text-center">
										@if($metodo->categoria == 0)
										Sin asignar
										@endif

										@if($metodo->categoria == 1)
										Efectivo
										@endif

										@if($metodo->categoria == 2)
										Bancos
										@endif

										@if($metodo->categoria == 3)
										Plataforma de pago
										@endif

										@if($metodo->categoria == 4)
										A cobrar
										@endif

										</h6>
								</td>
								<td>

									<h6 class="text-center">
									@if($metodo->muestra_sucursales != 0)
										Se muestra en sucursales
									@endif
									</h6>
								</td>

								<td>
									<h6 class="text-center">{{number_format($metodo->recargo,2)}} % </h6>
								</td>

								<td class="text-center">

								@if(auth()->user()->id == $metodo->comercio_id)

									<a href="javascript:void(0)" wire:click.prevent="Edit({{$metodo->id}})" class="btn btn-dark mtmobile" title="Edit">
										<i class="fas fa-edit"></i>
									</a>
									<a href="javascript:void(0)" onclick="Confirm('{{$metodo->id}}')" class="btn btn-dark" title="Delete">
										<i class="fas fa-trash"></i>
										</a>
									@else
									Heredado de casa central
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


	</div>

	@include('livewire.metodo-pago.form')
	@include('livewire.metodo-pago.form-bancos')
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
