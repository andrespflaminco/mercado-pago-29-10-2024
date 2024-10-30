<div class="row sales layout-top-spacing">

	<div class="col-sm-12">
		<div class="widget widget-chart-one">
			<div class="widget-heading">
				<h4 class="card-title">
					<b>{{$componentName}} | {{$pageTitle}}</b>
				</h4>
				<ul class="tabs tab-pills">

					<li>

						@if($caja_activa != null)

						<button href="javascript:void(0)" class="btn btn-dark" wire:click.prevent="CerrarModal()"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg> CERRAR CAJA</button>

						@else


						<button href="javascript:void(0)" class="btn btn-dark" wire:click.prevent="AbrirModal()" wire:loading.attr="disabled">
							<span wire:loading.remove><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>ABRIR CAJA</span>
							<span class="custom-loader" wire:loading></span>
						</button>

						

						@endif

						<button href="javascript:void(0)" class="btn btn-dark" wire:click.prevent="AgregarCajaAnteriorModal()">AGREGAR CAJA ANTERIOR</button>
					</li>

				</ul>
				<style>
						.custom-loader {
							width:6px;
							height:6px;
							border-radius: 50%;
							clip-path: inset(-22.5px);
							color: #766DF4;
							box-shadow: -30px 7.5px,-60px 7.5px,-60px 7.5px;
							transform: translateY(-7.5px);
							animation: d9 1s infinite linear;
							}

							@keyframes d9{ 
							16.67% {box-shadow:-30px 7.5px,-30px 7.5px,9.5px 7.5px}
							33.33% {box-shadow:-30px 7.5px,  0px 7.5px,9.5px 7.5px}
							40%,60%{box-shadow:-9.5px 7.5px,  0px 7.5px,9.5px 7.5px}
							66.67% {box-shadow:-9.5px 7.5px,  0px 7.5px,30px 7.5px}
							83.33% {box-shadow:-9.5px 7.5px, 30px 7.5px,30px 7.5px}
							100%   {box-shadow: 30px 7.5px, 30px 7.5px,30px 7.5px}
						}
						.custom-loader::disabled {
							pointer-events: none;						
						}					


						</style>	
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

				<div style="min-height:600px !important;" class="table-responsive">
					<table class="table table-bordered table striped mt-1">
						<thead class="text-white" style="background: #3B3F5C;">
							<tr>
								<th class="table-th text-white">CAJA NRO</th>
								<th class="table-th text-white">FECHA APERTURA</th>
								<th class="table-th text-white">FECHA CIERRE</th>
								<th class="table-th text-white">HORA APERTURA</th>
								<th class="table-th text-white">HORA CIERRE</th>
								<th class="table-th text-white">USUARIO</th>
								<th hidden class="table-th text-white text-center">MONTO TOTAL</th>
								<th class="table-th text-white">ESTADO</th>
								<th style="width:28%;" class="table-th text-white text-center">ACCIONES</th>
							</tr>
						</thead>
						<tbody>
							@foreach($datos as $metodo)
							<tr>
								<td>
									<h6 class="text-left">{{$metodo->nro_caja}}</h6>
								</td>
								<td>
									<h6 class="text-left">{{\Carbon\Carbon::parse( $metodo->fecha_inicio)->format('d-m-Y')}}</h6>
								</td>
								<td>
									<h6 class="text-left">{{\Carbon\Carbon::parse( $metodo->fecha_cierre)->format('d-m-Y')}}</h6>
								</td>
								<td>
									<h6 class="text-left">{{\Carbon\Carbon::parse( $metodo->fecha_inicio)->format('H:i')}} hs</h6>
								</td>
								<td>
									<h6 class="text-left">{{\Carbon\Carbon::parse( $metodo->fecha_cierre)->format('H:i')}} hs</h6>
								</td>
								<td>
									<h6 class="text-left">{{$metodo->name}}</h6>
								</td>
								<td hidden>
									<h6 class="text-center">$ {{number_format($metodo->monto_inicial-$metodo->faltante_caja+$metodo->total+$metodo->recargo,2)}}</h6>
								</td>
								<td>
									<h6 class="text-left">
										@if($metodo->estado === 0)
										Activo
										@else
										Caja cerrada
										@endif

									</h6>
								</td>




								<td class="text-center">
								@can('caja_resumen')
								
								<div class="btn-group mb-1 mr-1" role="group">
                                 <button id="btndefault" type="button" class="btn btn-dark btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg></button>
                                 <div class="dropdown-menu" aria-labelledby="btndefault">
                                  <a href="javascript:void(0);" wire:click.prevent="GetCaja({{$metodo->id}})"  class="dropdown-item"><i class="flaticon-dots mr-1"></i>  RESUMEN </a>
                                  <a href="{{ url('cajas-detalle' . '/' . $metodo->id) }}" target="_blank" class="dropdown-item"><i class="flaticon-dots mr-1"></i>  DETALLE </a>
                                  <a href="javascript:void(0);" wire:click.prevent="EditCaja({{$metodo->id}})"  class="dropdown-item"><i class="flaticon-dots mr-1"></i>  EDITAR </a>
                                  <a href="javascript:void(0);" onclick="ConfirmEliminar({{$metodo->id}})" class="dropdown-item"><i class="flaticon-dots mr-1"></i> <i class="fas fa-trash"></i> ELIMINAR </a>
                               
                                 </div>
                                  </div>
                                              
                                              
									<a hidden href="javascript:void(0)" wire:click.prevent="GetCaja({{$metodo->id}})" class="btn btn-dark mtmobile" title="Resumen">
										RESUMEN
									</a>
									<a hidden href="{{ url('cajas-detalle' . '/' . $metodo->id) }}" target="_blank" class="btn btn-dark mtmobile" title="Detalle">
										DETALLE
									</a>
									
									@endcan
									<a hidden href="javascript:void(0)" wire:click.prevent="EditCaja({{$metodo->id}})" class="btn btn-dark mtmobile" title="Resumen">
										EDITAR
									</a>
									@if($metodo->count < 1)
                                    <a hidden href="javascript:void(0)" onclick="ConfirmEliminar({{$metodo->id}})" class="btn btn-dark mtmobile" title="">
									<i class="fas fa-trash"></i>
									</a>
									@endif



								</td>
							</tr>
							@endforeach
						</tbody>

					</table>
					{{$datos->links()}}
				</div>

			</div>


		</div>


	</div>



@include('livewire.cajas.form-cerrar')

@include('livewire.cajas.form-editar')

@include('livewire.cajas.form-abrir')


@include('livewire.cajas.form-caja-anterior')


@include('livewire.cajas.cajas')

</div>


<script>
	document.addEventListener('DOMContentLoaded', function() {

		window.livewire.on('product-added', msg => {
			$('#theModal').modal('hide')
		});
		window.livewire.on('product-updated', msg => {
			$('#theModal').modal('hide')
		});
		
		
		window.livewire.on('actualizacion', msg => {
			noty(msg)
		});

		window.livewire.on('cierre', msg => {
			noty(msg)
		});
		window.livewire.on('modal-abrir-show', msg => {
			$('#theModal').modal('show')
		});
		window.livewire.on('modal-abrir-hide', msg => {
			$('#theModal').modal('hide')
		});
		window.livewire.on('modal-cerrar-show', msg => {
			$('#theModal2').modal('show')
		});
		window.livewire.on('modal-cerrar-hide', msg => {
			$('#theModal2').modal('hide')
		});
		window.livewire.on('modal-editar-show', msg => {
			$('#theModalEditar').modal('show')
		});
		window.livewire.on('modal-editar-hide', msg => {
			$('#theModalEditar').modal('hide')
		});
		
		window.livewire.on('modal-caja-show', msg => {
			$('#theModalCaja').modal('show')
		});
		window.livewire.on('modal-caja-hide', msg => {
			$('#theModalCaja').modal('hide')
			noty(msg)
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
		$('#tabsModal').on('hidden.bs.modal', function(e) {
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

		function ConfirmEliminar(id) {

			swal({
				title: 'CONFIRMAR',
				text: 'CONFIRMAS ELIMINAR LA CAJA?',
				type: 'warning',
				showCancelButton: true,
				cancelButtonText: 'Cerrar',
				cancelButtonColor: '#fff',
				confirmButtonColor: '#3B3F5C',
				confirmButtonText: 'Aceptar'
			}).then(function(result) {
				if (result.value) {
					window.livewire.emit('deleteCaja', id)
					swal.close()
				}

			})
		}
</script>
