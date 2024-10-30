
<div class="row sales layout-top-spacing">

	<div class="col-sm-12 ">

		<div class="widget widget-chart-one">
			<div class="widget-heading">
					<h4 class="card-title text-center"><b>Movimientos de cuenta corriente del proveedor</b></h4>


						@include('livewire.ctacte-proveedores-movimientos.agregar-pago')
	                  @include('livewire.ctacte-proveedores-movimientos.form')

<div style="float: right;">
		 <button type="button" style="margin-right:10px;" class="btn btn-dark" onClick="muestra_oculta()"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-filter"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>  FILTROS</button>

						<a  href="javascript:void(0)" class="btn btn-dark" wire:click="AgregarPagoModal({{$proveedor_id}})">
						$ Agregar pago </a>


</div>


					</div>





			<div id="filtros_div" style="display:none;" class="card component-card_1">
			<div class="card-body">
				<div class="row">

					<div class="col-sm-12 col-md-4">
					 <div class="form-group">
						<label>Proveedor</label>
						<select wire:model='proveedor_elegido' class="form-control">
							<option value="Elegir" disabled >Elegir</option>
							<option value="0" >Todos</option>
							@foreach($prov as $pr)
							<option value="{{$pr->id}}">{{$pr->nombre}}</option>
							@endforeach
						</select>
					</div>
					</div>

			 </div>

			 </div>

			 </div>

			 <div class="card component-card_1">
			 <div class="card-body">
				 <div class="row">


						 <div class="col-sm-12 col-md-3">
						 <div style="text-align:center !important; vertical-align: middle !important;" class="form-group">

						 <h5>Cant. de compras: {{$this->suma_proveedores}}</h5>


						 </div>
						 </div>

						 <div class="col-sm-12 col-md-3">

						<div style="text-align:center !important; vertical-align: middle !important;" class="form-group">

						<h5>Compras: $ {{number_format($this->suma_totales,2)}}</h5>


						</div>
						</div>

						<div class="col-sm-12 col-md-3">

					 <div style="text-align:center !important; vertical-align: middle !important;" class="form-group">

					 <h5>Pagos: $ {{number_format($this->suma_totales - $this->suma_deuda,2)}}</h5>


					 </div>
					 </div>



								<div class="col-sm-12 col-md-3">
								<div style="text-align:center !important; vertical-align: middle !important;" class="form-group">

									<h5>Deuda: $ {{number_format($this->suma_deuda,2)}}</h5>


								</div>
								</div>
	 </div>
		 </div>
		 </div>

			<div style="margin-top:2%;" class="widget-content">

									<div class="connect-sorting-content">
										<div class="table-responsive">
											<table  id="default-ordering" class="table table-hover">
												<thead class="text-white" style="background: #3B3F5C; vertical-align: middle !important;">
													<tr>
														<th style="background: #3B3F5C; vertical-align: middle !important;" class="table-th text-white text-center">TIPO DE MOVIMIENTO</th>
														<th style="background: #3B3F5C; vertical-align: middle !important;" class="table-th text-white text-center">FECHA</th>
														<th style="background: #3B3F5C; vertical-align: middle !important;" class="table-th text-white text-center">SALDO DEUDOR</th>
														<th style="background: #3B3F5C; vertical-align: middle !important;" class="table-th text-white text-center">SALDO ACREEDOR</th>
														<th style="width:20%; vertical-align: middle !important;" class="table-th text-white text-center">DETALLE</th>
														<th style="width:20%; vertical-align: middle !important;" class="table-th text-white text-center">ACCIONES</th>
													</tr>
												</thead>
												<tbody>
													@foreach($data as $compra)
													<tr>
														<td>
															<h6 class="text-center">
																@if($compra->monto_compra > 0)
																Compra # {{$compra->id_compra}}
																@endif


																@if($compra->monto_pago > 0)

																@if($compra->id_compra < 1)
																Pago a cuenta
																@else
																Pago factura
																@endif
																@endif

															</h6>
														</td>
														<td>
															<h6 class="text-center">{{\Carbon\Carbon::parse($compra->created_at)->format('d-m-Y')}}</h6>
														</td>
														<td>
															<h6 class="text-center"> $ {{$compra->monto_compra}}</h6>
														</td>
														<td>
															<h6 class="text-center"> $ {{$compra->monto_pago}}</h6>
														</td>
														<td>
															<h6 class="text-center">
																@if($compra->id_factura > 0)
																 Factura #{{$compra->id_factura}}
															 @endif</h6>
														</td>

														<td class="text-center">

																	<a href="{{ url('movimientos-ctacte/'.$compra->id) }}" class="btn btn-dark btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="fa fa-eye"></i> </button>




																	</div>
															</div>
														</td>
													</tr>
													@endforeach
												</tbody>
												<tfoot style="border-top: solid 1.5px #dcd9d9;">
													<td class="text-center">  <h6><b>Total</b></h6>  </td>
													<td class="text-center"></td>
													<td class="text-center">
													<h6><b>	$ {{number_format($this->suma_totales,2)}} </b></h6>

													</td>
													<td class="text-center">
													<h6><b> $ {{number_format($this->suma_totales - $this->suma_deuda,2)}} </b></h6>
													</td>
													<td class="text-center"></td>
													</h6></td>
													<td class="text-center"></td>
												</tfoot>
											</table>
										</div>


								</div>
						</div>
				</div>


				<script>
					document.addEventListener('DOMContentLoaded', function() {

						window.livewire.on('agregar-pago', msg => {
							$('#AgregarPago').modal('show')
						});
						window.livewire.on('agregar-pago-hide', msg => {
							$('#AgregarPago').modal('hide')
						});

						window.livewire.on('show-modal', msg => {
							$('#theModal').modal('show')
						});
						window.livewire.on('hide-modal', msg => {
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

				function muestra_oculta() {
				    var x = document.getElementById("filtros_div");
				    if (x.style.display === "none") {
				        x.style.display = "block";
				    } else {
				        x.style.display = "none";
				    }
				}

				</script>
