<div class="row sales layout-top-spacing">

	<div class="col-sm-12">
		<div class="widget widget-chart-one">
			<div class="widget-heading">
				<h4 class="card-title">
					<b>{{$componentName}} | {{$pageTitle}}</b>
				</h4>
				<ul class="tabs tab-pills">

					<li>
						<button href="javascript:void(0)" class="btn btn-dark" data-toggle="modal" data-target="#theModal"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg> Agregar</button>
						<button type="button" class="btn btn-dark" wire:click.prevent="GetEtiqueta()"  >
							Etiquetas
						</button>


					</li>

				</ul>
			</div>
			<div class="card component-card_1">
			<div class="card-body">
		<div class="row">

				<div class="col-lg-3 col-md-4 col-sm-12">
	<label>Buscar</label>
					<div class="input-group mb-3">

						<div class="input-group-prepend">
							<span class="input-group-text input-gp">
								<i class="fas fa-search"></i>
							</span>
						</div>
						<input type="text" wire:model="search" placeholder="Buscar" class="form-control">
					</div>

				</div>
				<div class="col-sm-3 col-md-3">
				 <div class="form-group">
					<label>Categoria</label>
					<select wire:model='categoria_filtro' class="form-control">
						<option value="" >Todos</option>
						<option value="Alquileres" >Alquileres</option>
						<option value="Limpieza" >Limpieza</option>
						<option value="Impuestos" >Impuestos</option>
						<option value="Proveedores" >Proveedores</option>
						<option value="Otros" >Otros</option>
					</select>

				</div>
				</div>
			<div class="col-sm-3 col-md-3">
			 <div class="form-group">
				<label>Fecha desde</label>
				<input type="text" wire:model="dateFrom" class="form-control flatpickr" placeholder="Click para elegir">

			</div>
			</div>

			<div class="col-sm-3 col-md-3">
			 <div class="form-group">
				<label>Fecha hasta</label>
				<input type="text" wire:model="dateTo" class="form-control flatpickr" placeholder="Click para elegir">

			</div>
			</div>
			</div>
			</div>



			</div>

			<div class="widget-content">

				<div class="table-responsive">
					<table class="table table-bordered table striped mt-1">
						<thead class="text-white" style="background: #3B3F5C;">
							<tr>
								<th class="table-th text-white">FECHA</th>
								<th class="table-th text-white">NOMBRE</th>
								<th class="table-th text-white text-center">CATEGORIA</th>
								<th class="table-th text-white text-center">MONTO</th>
								<th style="width:20%;" class="table-th text-white text-center">ACCIONES</th>
							</tr>
						</thead>
						<tbody>
							@foreach($data as $metodo)
							<tr>
								<td>
									<h6 class="text-left">{{\Carbon\Carbon::parse( $metodo->created_at)->format('d-m-Y')}}</h6>
								</td>
								<td>
									<h6 class="text-left">{{$metodo->nombre}}</h6>
								</td>
								<td>
									<h6 class="text-center">{{$metodo->categoria}}</h6>
								</td>
								<td>
									<h6 class="text-center">$ {{number_format($metodo->monto,2)}} </h6>
								</td>

								<td class="text-center">
									<a href="javascript:void(0)" wire:click.prevent="Edit({{$metodo->id}})" class="btn btn-dark mtmobile" title="Edit">
										<i class="fas fa-edit"></i>
									</a>
									<a href="javascript:void(0)" onclick="Confirm('{{$metodo->id}}')" class="btn btn-dark" title="Delete">
										<i class="fas fa-trash"></i>
										</a>
								</td>
							</tr>
							@endforeach
						</tbody>
						<tfoot style="border-top: solid 1.5px #dcd9d9;">
							<td class="text-left">  <h6><b>Total</b></h6>  </td>
							<td class="text-center"></td>
							<td class="text-center"></td>
							<td class="text-center"> <h6> <b>$ {{number_format($gastos_total,2)}}</b>
							</h6></td>
							<td class="text-center"></td>
						</tfoot>
					</table>
					{{$data->links()}}
				</div>

			</div>


		</div>


	</div>

	@include('livewire.gastos.form')
	@include('livewire.gastos.etiquetas')
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
		window.livewire.on('modal-show2', msg => {
			$('#theModal2').modal('show')
		});
		window.livewire.on('modal-hide2', msg => {
			$('#theModal2').modal('hide')
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
<script>
    document.addEventListener('DOMContentLoaded', function(){


        flatpickr(document.getElementsByClassName('flatpickr'),{
            enableTime: false,
            dateFormat: 'Y-m-d',
            locale: {
                firstDayofWeek: 1,
                weekdays: {
                    shorthand: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
                    longhand: [
                    "Domingo",
                    "Lunes",
                    "Martes",
                    "Miércoles",
                    "Jueves",
                    "Viernes",
                    "Sábado",
                    ],
                },
                months: {
                    shorthand: [
                    "Ene",
                    "Feb",
                    "Mar",
                    "Abr",
                    "May",
                    "Jun",
                    "Jul",
                    "Ago",
                    "Sep",
                    "Oct",
                    "Nov",
                    "Dic",
                    ],
                    longhand: [
                    "Enero",
                    "Febrero",
                    "Marzo",
                    "Abril",
                    "Mayo",
                    "Junio",
                    "Julio",
                    "Agosto",
                    "Septiembre",
                    "Octubre",
                    "Noviembre",
                    "Diciembre",
                    ],
                },

            }

        })

			});

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
</script>
