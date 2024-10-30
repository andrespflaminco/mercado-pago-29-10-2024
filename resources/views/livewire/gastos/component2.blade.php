<style media="screen">
.table-hover:not(.table-dark) tbody tr:hover {
    background-color: transparent !important;
}
	.boton-etiqueta:hover {
		font-size: 1rem!important;
		width: auto;
		background-color:
		transparent;
		border-left: none;
		border-top: none;
		border-right: none;
		text-align: center;
		border-bottom: 1px solid #bfc9d4;
	}
	.boton-etiqueta:focus {
		font-size: 1rem!important;
		width: auto;
		background-color:
		transparent;
		border-left: none;
		border-top: none;
		border-right: none;
		text-align: center;
		border-bottom: 1px solid #bfc9d4;
	}
	.boton-etiqueta {
		font-size: 1rem!important;
		width: auto;
		background-color:
		transparent;
		border: none;
		text-align: center;
	}
</style>
<div class="row sales layout-top-spacing">

	<div class="col-sm-12">
		<div class="widget widget-chart-one">
			<div class="widget-heading">
				<h4 class="card-title">
					<b>{{$componentName}} | {{$pageTitle}}</b>

				</h4>


				<ul class="tabs tab-pills">

					<li>
						<button type="button" class="btn btn-dark" data-toggle="modal" data-target="#tabsModal">
							Configuración
						</button>
						<button href="javascript:void(0)" class="btn btn-dark" data-toggle="modal" data-target="#theModal"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg> Agregar</button>
						<button type="button" class="btn btn-dark" onclick="showHtmlDiv()"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-filter"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg> </button>

					</li>

				</ul>

			</div>
			@can('product_search')

			<div id="html-show" style="display:none;" class="card component-card_1">
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
						<option value="Elegir" disabled >Elegir</option>
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

			@endcan
			<!-- Modal -->
			<div class="modal fade" id="tabsModal" tabindex="-1" role="dialog" aria-labelledby="tabsModalLabel" aria-hidden="true">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="tabsModalLabel">Configuración</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
									<ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
											<li class="nav-item">
													<a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Etiquetas</a>
											</li>
											<li hidden class="nav-item">
													<a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Formas de pago</a>
											</li>
									</ul>
									<div class="tab-content" id="myTabContent">
										<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">


											<div style="margin-bottom: 0 !important;" class="table-responsive mb-4 mt-4">
													<table class="multi-table table table-hover" style="width:100%">
															<thead>
																	<tr>
																			<th class="text-left">Nombre</th>
																			<th class="text-left"></th>
																	</tr>
															</thead>
															<tbody>
																	@foreach($etiquetas as $e)
																	<tr>
																			<td class="text-left">
																				<input class="boton-etiqueta" type="text" id="p{{$e->id}}"
																				wire:change="updateEtiqueta({{$e->id}}, $('#p' + {{$e->id}}).val() )"
																				value="{{$e->nombre}}">

																				</td>
																			<td class="text-left">
																				<a href="javascript:void(0)" onclick="Confirm2('{{$e->id}}')" >
									                         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
									                       </a>
																			</td>

																	</tr>
																	@endforeach

															</tbody>

													</table>
											</div>



												</div>
										<div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
													<p class="modal-text">Formas de pago.</p></div>
									</div>
							</div>
						<div class="modal-footer">
							<button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> CERRAR</button>
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
</div>

<script>
function showHtmlDiv() {
  var htmlShow = document.getElementById("html-show");
  if (htmlShow.style.display === "none") {
    htmlShow.style.display = "block";
  } else {
    htmlShow.style.display = "none";
  }
}
</script>



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
		window.livewire.on('tabs-show', msg => {
			$('#tabsModal').modal('show')
		});
		window.livewire.on('tabs-hide', msg => {
			$('#tabsModal').modal('hide')
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


tabsModal
