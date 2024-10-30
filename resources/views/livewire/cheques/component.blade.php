<div class="row sales layout-top-spacing">

	<div class="col-sm-12">
		<div class="widget widget-chart-one">
			<div class="widget-heading">
				<h4 class="card-title">
					<b>Cheques | Listado</b>
				</h4>
				<ul class="tabs tab-pills">

					<li>
						<a href="javascript:void(0)" class="tabmenu bg-dark" data-toggle="modal" data-target="#theModal">Agregar</a>
					</li>

				</ul>
			</div>
			@include('common.searchbox')



			<div class="widget-content">

				<div class="row" style="margin:0px; margin-bottom:5px;">
					<div class="col-3 text-center"  style="border: solid 1px #eee; padding: 25px;">
						<h5>Vencido: $ {{$vencido->monto ?? 0}}</h5>
					</div>
					<div class="col-3 text-center" style="border: solid 1px #eee; padding: 25px;">
						<h5>A cobrar: $ {{$activo->monto ?? 0}}</h5>
					</div>
					<div class="col-3 text-center" style="border: solid 1px #eee; padding: 25px;">
						<h5>Cobrado: $ {{$cobrado->monto ?? 0}}</h5>
					</div>
					<div class="col-3 text-center" style="border: solid 1px #eee; padding: 25px;">
						<h5>Incobrable: $ {{$incobrable->monto ?? 0}}</h5>
					</div>
				</div>

				<div class="table-responsive">
					<table class="table table-bordered table striped mt-1">
						<thead class="text-white" style="background: #3B3F5C">
							<tr>
								<th class="table-th text-white">EMISOR</th>
								<th class="table-th text-white text-center">BANCO</th>
								<th class="table-th text-white text-center">CLIENTE</th>
								<th class="table-th text-white text-center">MONTO</th>
								<th class="table-th text-white text-center">FECHA DE EMISION</th>
								<th class="table-th text-white text-center">FECHA DE COBRO</th>
								<th class="table-th text-white text-center">ESTADO</th>
								<th class="table-th text-white text-center">ACCIONES</th>
							</tr>
						</thead>
						<tbody>
							@foreach($cheques as $cheque)
							<tr>
								<td class="text-center">
									<h6>{{$cheque->emisor}}</h6>
								</td>
								<td class="text-center">
									<h6>{{$cheque->banco}}</h6>
								</td>

								<td class="text-center">
									<h6>{{$cheque->cliente}}</h6>
								</td>

								<td class="text-center">
								<h6>$ {{$cheque->monto}}</h6>
								</td>

								<td class="text-center">
									<h6>
										{{\Carbon\Carbon::parse($cheque->fecha_emision)->format('d/m/Y')}}
									</h6>
								</td>
								<td class="text-center">
									<h6>
									{{\Carbon\Carbon::parse($cheque->fecha_cobro)->format('d/m/Y')}}
								</h6>
								</td>
								<td class="text-center">
									@if($cheque->status == 'Activo')
										<button onclick="cambiar()"  style="    min-width: 130px; margin-bottom: 0 !important;  margin-top: -2px !important;  margin-right: 15px;  padding: 3px !important;" wire:click.prevent="CambioEstado({{$cheque->id}})"
											class="btn btn-secondary mb-2">
											A cobrar
									</button>
									@endif


									@if($cheque->status == 'Vencido')
										<button onclick="cambiar()" style="   min-width: 130px; margin-bottom: 0 !important;  margin-top: -2px !important;  margin-right: 15px;  padding: 3px !important;" wire:click.prevent="CambioEstado({{$cheque->id}})"
											class="btn btn-warning mb-2">
											Vencido
									</button>
									@endif

									@if($cheque->status == 'Cobrado')
										<button onclick="cambiar()"  style="    min-width: 130px; margin-bottom: 0 !important;  margin-top: -2px !important;  margin-right: 15px;  padding: 3px !important;" wire:click.prevent="CambioEstado({{$cheque->id}})"
											class="btn btn-success mb-2">
											{{$cheque->status}}
									</button>
									@endif
									@if($cheque->status == 'Incobrable')
										<button onclick="cambiar()"  style="    min-width: 130px; margin-bottom: 0 !important;  margin-top: -2px !important;  margin-right: 15px;  padding: 3px !important;" wire:click.prevent="CambioEstado({{$cheque->id}})"
											class="btn btn-danger mb-2">
											{{$cheque->status}}
									</button>
									@endif

								</td>


								<td class="text-center">
									<a href="javascript:void(0)" wire:click="Edit({{$cheque->id}})" class="btn btn-dark mtmobile" title="Edit">
										<i class="fas fa-edit"></i>
									</a>


									<a href="javascript:void(0)" onclick="Confirm('{{$cheque->id}}')" class="btn btn-dark" title="Delete">
									<i class="fas fa-trash"></i>
									</a>

								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
					{{$cheques->links()}}
				</div>

			</div>


		</div>


	</div>

	@include('livewire.cheques.form')
	@include('livewire.cheques.estado')
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

		window.livewire.on('estado', msg => {
			$('#Estado').modal('show')
		});
		window.livewire.on('estado-hide', msg => {
			$('#Estado').modal('hide')
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
