<div class="row sales layout-top-spacing">

	<div class="col-sm-12">
		<div class="widget widget-chart-one">
			<div class="widget-heading">
				<h4 class="card-title">
					<b>{{$componentName}} | {{$pageTitle}}</b>
				</h4>
				<ul class="tabs tab-pills">

					<li>
					   	@can('category_create')
						<a href="javascript:void(0)" class="tabmenu bg-dark" data-toggle="modal" data-target="#theModal">Agregar</a>
						@endcan
					</li>

				</ul>
			</div>
			@can('category_search')
			@include('common.searchbox')
			@endcan

			<div class="widget-content">
			    
			    <!---- FILTROS ESTADO ----->
                
                @include('common.filtro-estado')  
                    
                <!-------------------------->
			    
				<!---------- ACCIONES EN LOTE -------->
				
				@include('common.accion-lote') 
	
				<!---------------------------------->


				<div class="table-responsive">
					<table class="table table-bordered table striped mt-1">
						<thead class="text-white" style="background: #3B3F5C">
							<tr>
							    <th>
                                <input name="Todos" type="checkbox" value="1" onclick="CheckTodosLote()" class="check_todos"/>    
                                </th>
								<th class="table-th text-white">NOMBRE</th>
								<th class="table-th text-white text-center">IMAGEN</th>
								<th class="table-th text-white text-center">ACCIONES</th>
							</tr>
						</thead>
						<tbody>
							@foreach($categories as $category)
							<tr>
							     <td class="text-left">
							        <input type="checkbox" wire:model.defer="id_check" tu-attr-id="{{($category->id)}}"  class="mis-checkboxes" value="{{$category->id}}">
							    </td>
								<td>
									<h6>{{$category->name}}</h6>
									@if($wc != null)
									<small>ID wc: {{$category->wc_category_id}}</small>
									@endif
								</td>
								<td class="text-center">
									<span>
										<img src="{{ asset('storage/categories/' . $category->imagen) }}" alt="imagen de ejemplo" height="70" width="80" class="rounded">
									</span>
								</td>

								<td class="text-center">
								    
								    @if($estado_filtro == 0 )
									@can('category_update')
									<a href="javascript:void(0)" wire:click="Edit({{$category->id}})" class="btn btn-dark mtmobile" title="Edit">
										<i class="fas fa-edit"></i>
									</a>
									@endcan
                                    
                                    

									@can('category_destroy') 
									<a href="javascript:void(0)" onclick="Confirm('{{$category->id}}')" class="btn btn-dark" title="Delete">
										<i class="fas fa-trash"></i>
									</a>
									@endcan
								    
								    @if($wc != null)
									<a href="javascript:void(0)" wire:click="Sincronizar('{{$category->id}}')" class="btn btn-dark" title="Delete">
										<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-ccw"><polyline points="1 4 1 10 7 10"></polyline><polyline points="23 20 23 14 17 14"></polyline><path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"></path></svg>
								   	</a>
								   	@endif
									
									@else
								    <a href="javascript:void(0)" onclick="RestaurarCategoria('{{$category->id}}')" class="btn" title="Restaurar">
										RESTAURAR
									</a>
								    
								    @endif
								    
								    
										


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

	@include('livewire.category.form')
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