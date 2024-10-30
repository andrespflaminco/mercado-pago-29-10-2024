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
			@can('product_search')
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
						<thead class="text-white" style="background: #3B3F5C;">
							<tr>
							    <th>
                                <input name="Todos" type="checkbox" value="1" onclick="CheckTodosLote()" class="check_todos"/>    
                                </th>
								<th class="table-th text-white">NOMBRE DEL PROVEEDOR</th>
								<th style="width:20%;" class="table-th text-white text-center">ACCIONES</th>
							</tr>
						</thead>
						<tbody>
							@foreach($data as $proveedor)
							<tr>
							    <td class="text-left">
							        <input type="checkbox" wire:model.defer="id_check" tu-attr-id="{{($proveedor->id)}}"  class="mis-checkboxes" value="{{$proveedor->id}}">
							    </td>
								<td>
									<h6 class="text-left">{{$proveedor->nombre}}</h6>
								</td>
								<td class="text-center">
								    @if($estado_filtro == 0 )
									<a href="javascript:void(0)" wire:click.prevent="Edit({{$proveedor->id}})" class="btn btn-dark mtmobile" title="Edit">
										<i class="fas fa-edit"></i>
									</a>
									<a href="javascript:void(0)" onclick="Confirm('{{$proveedor->id}}')" class="btn btn-dark" title="Delete">
										<i class="fas fa-trash"></i>
									</a>
									 @else
								    <a href="javascript:void(0)" onclick="RestaurarProveedor('{{$proveedor->id}}')" class="btn btn-dark" title="Delete">
										RESTAURAR
									</a>
								    
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

	@include('livewire.proveedores.form')
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
	
		function RestaurarProveedor(id) {

    swal({
      title: 'CONFIRMAR',
      text: 'QUIERE RESTAURAR EL PROVEEDOR?',
      showCancelButton: true,
      cancelButtonText: 'Cancelar',
      cancelButtonColor: '#fff',
      confirmButtonColor: '#3B3F5C',
      confirmButtonText: 'Aceptar'
    }).then(function(result) {
      if (result.value) {
        window.livewire.emit('RestaurarProveedor', id)
        swal.close()
      } 

    })
  }
</script>
