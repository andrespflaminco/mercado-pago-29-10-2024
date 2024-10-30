<div class="row sales layout-top-spacing">

	<div class="col-sm-12">
		<div class="widget widget-chart-one">
			<div class="widget-heading">
				<h4 class="card-title">
					<b>{{$componentName}} | {{$pageTitle}}</b>
				</h4>
				<ul class="tabs tab-pills">

					<li>
						<a href="javascript:void(0)" class="tabmenu bg-dark" data-toggle="modal" data-target="#theModal">Agregar Atributo</a>
					</li>

				</ul>
			</div>
			@can('category_search')
			@include('common.searchbox')
			@endcan

			<div class="widget-content">


				<div class="table-responsive">
					<table class="table table-bordered table striped mt-1">
						<thead class="text-white" style="background: #3B3F5C">
							<tr>
								<th class="table-th text-white">ATRIBUTO</th>
								<th class="table-th text-white"></th>
								<th class="table-th text-white">NOMBRE</th>
									<th class="table-th text-white"></th>
							</tr>
						</thead>
						<tbody>
							@foreach($categories as $category)
							<tr>
								<td>
									<h6>{{$category->nombre}}</h6>
								</td>

								<td>
									<a href="javascript:void(0)" wire:click="Edit({{$category->id}})" class="btn btn-dark mtmobile" title="Edit">
										<i class="fas fa-edit"></i>
									</a>

								 <a href="javascript:void(0)" onclick="Confirm('{{$category->id}}')" class="btn btn-dark" title="Delete">
										<i class="fas fa-trash"></i>
										</a>



								</td>
								<td class="text-right">
									<div class="row">
								@foreach($variaciones as $v)

								@if($v->atributo_id == $category->id)

								<b> {{$v->nombre}}  -   </b>

								@endif
								@endforeach

							</div>


								</td>
								<td class="text-right">
								    
								    <a href="javascript:void(0)" wire:click="EditVariacion({{$category->id}})" class="btn btn-dark mtmobile" title="Editar Atributos">
									<i class="fas fa-edit"></i>
									</a>
									
									<a href="javascript:void(0)" wire:click="AgregarVariacion({{$category->id}})" class="btn btn-dark mtmobile" title="Agregar Atributos">
									+
									</a>
									
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
					{{$categories->links()}}
				</div>

			</div>


		</div>


	</div>

	@include('livewire.atributos.form')
	@include('livewire.atributos.form-variacion')
	@include('livewire.atributos.form-editar-variacion')

</div>


<script>
	document.addEventListener('DOMContentLoaded', function() {

		window.livewire.on('show-modal', msg => {
			$('#theModal').modal('show')
		});

		window.livewire.on('show-modal-variacion', msg => {
			$('#theModalVariacion').modal('show')
		});

		window.livewire.on('show-modal-editar-variacion', msg => {
			$('#theModalEditarVariacion').modal('show')
		});

		window.livewire.on('variacion-editar-updated', msg => {
			$('#theModalEditarVariacion').modal('hide'),
			noty(msg);
		});
		
		window.livewire.on('category-added', msg => {
			$('#theModal').modal('hide')
		});

		window.livewire.on('variacion-added', msg => {
			$('#theModalVariacion').modal('hide'),
			noty(msg);
		});

		window.livewire.on('variacion-updated', msg => {
			$('#theModalVariacion').modal('hide'),
			noty(msg);
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

	function ConfirmAtributo(id) {

		swal({
			title: 'CONFIRMAR',
			text: '¿CONFIRMAS ELIMINAR EL ATRIBUTO?',
			type: 'warning',
			showCancelButton: true,
			cancelButtonText: 'Cerrar',
			cancelButtonColor: '#fff',
			confirmButtonColor: '#3B3F5C',
			confirmButtonText: 'Aceptar'
		}).then(function(result) {
			if (result.value) {
				window.livewire.emit('deleteAtributo', id)
				swal.close()
			}

		})
	}

	function ConfirmVariacion(id) {

		swal({
			title: 'CONFIRMAR',
			text: '¿CONFIRMAS ELIMINAR LA VARIACION?',
			type: 'warning',
			showCancelButton: true,
			cancelButtonText: 'Cerrar',
			cancelButtonColor: '#fff',
			confirmButtonColor: '#3B3F5C',
			confirmButtonText: 'Aceptar'
		}).then(function(result) {
			if (result.value) {
				window.livewire.emit('deleteVariacion', id)
				swal.close()
			}

		})
	}
</script>
