<div class="row sales layout-top-spacing">


	<div class="col-sm-12 ">
		<div class="widget ">
			<div class="widget-heading">
				<h4 class="card-title text-center"><b>{{$componentName}}</b></h4>
			</div>

			<div class="widget-content">

				<div class="row">
					<div class="col-sm-12 col-md-3">
						<div class="row">

							<div class="col-sm-12">
								<h6>Elige el sector del deposito</h6>
								<div class="form-group">
									<select wire:model="userId" class="form-control">
										<option value="0">Todos</option>
										@foreach($seccion_almacen as $seccion)
										<option value="{{$seccion->id}}">{{$seccion->nombre}}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="col-sm-12">
								<h6>Elige el tipo de reporte</h6>
								<div class="form-group">
									<select wire:model="reportType" class="form-control">
										<option value="0">Ventas del Día</option>
										<option value="1">Ventas por Fecha</option>
									</select>
								</div>

							</div>
							<div class="col-sm-12 mt-2">
								<h6>Fecha desde</h6>
								<div class="form-group">
									<input wire:model="dateFrom" class="form-control flatpickr " type="text" placeholder="Click para elegir ">
								</div>
							</div>
							<div class="col-sm-12">
								<h6>Fecha hasta</h6>
								<div class="form-group">
									<input wire:model="dateTo" id="dateTimeFlatpickr" class="form-control flatpickr" type="text" placeholder="Click para elegir ">
								</div>
							</div>
							<div class="col-sm-12">

								<button  wire:click="$refresh" class="btn btn-dark btn-block"> Consultar</button>

									@can('report_excel')

									<a href="{{ url('report-detalle/excel'. '/' . $userId . '/' . $reportType . '/' . $dateFrom . '/' . $dateTo ) }}"
									class="btn btn-dark btn-block {{count($data) <1 ? 'disabled' : ''}} mbmobile"
									target="_blank">Exportar a Excel</a>
										@endcan

									</div>

								</div>
							</div>
							<div class="col-sm-12 col-md-9">

								<div class="table-responsive">
									<table  class="table table-bordered table-striped  mt-1 ">
										<thead class="text-white" style="background: #3B3F5C">
											<tr>
												<th class="table-th text-center text-white">FOLIO</th>
												<th class="table-th text-center text-white">PRODUCTO</th>
												<th class="table-th text-center text-white">PRECIO</th>
												<th class="table-th text-center text-white">CANTIDAD</th>
												<th class="table-th text-center text-white">IMPORTE TOTAL</th>
												<th class="table-th text-center text-white">ALMACEN</th>
												<th class="table-th text-center text-white">FORMA DE PAGO</th>
												<th class="table-th text-center text-white">FECHA</th>

											</tr>
										</thead>
										<tbody>
											@if(count($data)<1)
											<tr><td class="text-center" colspan="8"><h5>Sin resultados</h5></td></tr>
											@endif
											@foreach($data as $d)
											<tr>
												<td class="text-center"><h6>{{$d->id}}</h6></td>
												<td class="text-center"><h6>{{$d->product}}</h6></td>
												<td class="text-center"><h6>${{number_format($d->price,2)}}</h6></td>
												<td class="text-center"><h6>{{number_format($d->quantity)}}</h6></td>
												<td class="text-center"><h6>${{number_format(($d->price*$d->quantity),2)}}</h6></td>
												<td class="text-center"><h6>{{$d->almacen}}</h6></td>
												<td class="text-center"><h6>{{$d->nombre_metodo_pago}}</h6></td>
												<td class="text-center"><h6>
													{{\Carbon\Carbon::parse($d->created_at)->format('d-m-Y')}}
												</h6></td>

										</tr>

										@endforeach
									</tbody>
								</table>




							</div>
						</div>
					</div>





				</div>
			</div>
		</div>
		@include('livewire.reports.sales-detail')
	</div>





	<script>
document.addEventListener('DOMContentLoaded', function () {
   	flatpickr(document.getElementsByClassName('flatpickr'), {
   		enableTime: false,
   		dateFormat: "Y-m-d",
   		locale: {
   			firstDayOfWeek: 1,
   			weekdays: {
   				shorthand: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
   				longhand: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
   			},
   			months: {
   				shorthand: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Оct', 'Nov', 'Dic'],
   				longhand: ['Enero', 'Febreo', 'Мarzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
   			},
   		},
   	});
})
   </script>


   <script>
   	document.addEventListener('DOMContentLoaded', function () {

		window.livewire.on('show-modal', Msg => {
			$('#modalDetails').modal('show')
		})

	})
   </script>
