<div class="row sales layout-top-spacing">
	<div class="col-sm-12">


		<div class="widget widget-chart-one">

			@include('livewire.hoja-ruta-pedido.sales-detail')
				@include('livewire.hoja-ruta-pedido.sales-detail2')

			<ul class="nav nav-tabs  mb-3">
    <li class="nav-item">
        <a class="nav-link" href="{{ url('hoja-ruta') }}" >Hojas de ruta</a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="{{ url('hoja-ruta-pedido') }}"> Pedidos</a>
    </li>
</ul>
<div class="widget-heading">
	<h4 class="card-title">
		<b> Hojas de ruta | Detalle</b>
	</h4>
	<ul class="tabs tab-pills">

		<li>

				<a class="tabmenu bg-dark" href="{{ url('report-hoja-ruta/excel' . '/' . ($id_hoja_ruta == '' ? '0' : $id_hoja_ruta).'/'.uniqid()) }}" target="_blank">Exportar</a>
		</li>

	</ul>
</div>

				<div class="col-lg-4 col-md-4 col-sm-4">

					<div class="input-group mb-4">
						<div class="input-group-prepend">
							<span class="input-group-text input-gp">
								<i class="fas fa-truck"></i>
							</span>
						</div>
						<select wire:model='id_hoja_ruta' class="form-control">
							<option value="Elegir" disabled >Elegir</option>
							<option value="0" >Todos</option>
							<option value="no" >Sin Hoja de ruta </option>
							@foreach ($hr as $lista_hr)
							<option value="{{$lista_hr->id}}" >{{\Carbon\Carbon::parse($lista_hr->fecha)->format('d-m-Y')}}

								 ({{$lista_hr->turno}})</option>

							@endforeach
						</select>

					</div>

				</div>
					<!--TABLAE-->
					<div class="table-responsive">
							<table class="table table-bordered table striped mt-1">
									<thead class="text-white" style="background: #3B3F5C">
											<tr>
													<th class="table-th text-white text-center">ID PEDIDO</th>
													<th class="table-th text-white text-center">FECHA PEDIDO</th>
													<th class="table-th text-white text-center">CLIENTE</th>
													<th class="table-th text-white text-center">DIRECCION</th>
													<th class="table-th text-white text-center">CANT. ITEMS</th>
													<th class="table-th text-white text-center">TOTAL</th>
													<th class="table-th text-white text-center">A COBRAR</th>
													<th style="width: 21%;" class="table-th text-white text-center">HOJA DE RUTA</th>
													<th class="table-th text-white text-center" >ACCIONES</th>
											</tr>
									</thead>
									<tbody>
											@if(count($pedidos) <1)
											<tr><td colspan="7"><h5>Sin Resultados</h5></td></tr>
											@endif
											@foreach($pedidos as $d)
											<?php $sum += $d->total; ?>
											<tr>
													<td class="text-center"><h6>{{$d->id}}</h6></td>
													<td class="text-center">
															<h6>
																	{{\Carbon\Carbon::parse($d->created_at)->format('d-m-Y')}}
															</h6>
													</td>
													<td class="text-center"><h6>{{$d->nombre_cliente}}</h6></td>
													<td class="text-center"><h6>{{$d->direccion}},{{$d->localidad}} - {{$d->provincia}}</h6></td>
													<td class="text-center"><h6>{{$d->items}}</h6></td>
													<td class="text-center"><h6>${{number_format($d->total+$d->recargo,2)}}</h6></td>
													<td class="text-center"><h6>
														@if(($d->total-$d->monto-$d->cash) > 0)
														${{number_format($d->total-$d->monto-$d->cash,2) }}</h6>
														@else $ 0
														@endif
													</td>
													<td class="text-center"	>
														@if($d->hoja_ruta != '')
														<button style="width:90%;" wire:click.prevent="getDetails2({{$d->id}})"	class="btn btn-dark  mb-2">
																{{\Carbon\Carbon::parse($d->fecha)->format('d-m-Y')}}
																@if($d->turno)

																 ({{$d->turno}})

																 @else

																 @endif

														</button>
													@else
													<button style="min-width:90%;" wire:click.prevent="getDetails2({{$d->id}})"	class="btn btn-light  mb-2">
															Asignar
													</button>
												@endif</td>

													<td style="width:20%;" class="text-center" >

														<a type="button" href="{{ url('report-factura/pdf' . '/' . $d->id) }}" target="_blank"
																class="btn btn-dark mb-2">
																<i class="fas fa-print"></i>
														</a>
															<button wire:click.prevent="getDetails({{$d->id}})"
																	class="btn btn-dark mb-2">
																	<i class="fas fa-list"></i>
															</button>



													</td>
											</tr>
											@endforeach
									</tbody>
							</table>


					</div>




		</div>


	</div>
	@include('livewire.hoja-ruta.form')
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

		//eventos
		window.livewire.on('show-modal', Msg =>{
				$('#modalDetails').modal('show')
		})

		//eventos
		window.livewire.on('show-modal2', Msg =>{
				$('#modalDetails2').modal('show')
		})

		//eventos
		window.livewire.on('hide-modal2', Msg =>{
				$('#modalDetails2').modal('hide')
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
