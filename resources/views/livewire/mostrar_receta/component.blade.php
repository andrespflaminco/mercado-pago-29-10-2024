
<div class="row sales layout-top-spacing">

	<div class="col-sm-12 ">

		<div class="widget widget-chart-one">
			<div class="widget-heading">
				<h4 class="card-title"> {{$nombre->name}} {{$nombre->variaciones}} </h4>

				<a  class="btn btn-dark" href="{{ url('produccion_recetas') }}">

						 Volver al resumen </a>

			</div>

			<div class="row">

				
				<div class="col-lg-3 col-md-4 col-sm-12">
                    
                    <h6>Total $ {{number_format($suma,2)}}</h6>
                    <h6>Rinde {{$rinde}} unidades</h6>
                    <h6>Costo por unidad: $ {{number_format($suma/$rinde,2)}}</h6>
				
						
				</div>




				</div>
			<div class="widget-content">

									<div class="connect-sorting-content">

											<div class="table-responsive tblscroll" style="max-height: 650px; overflow: hidden">
												<table class="table table-bordered table-striped mt-1">
													<thead class="text-white" style="background: #3B3F5C">
														<tr>
														  	<th class="table-th text-left text-white">DESCRIPCIÓN</th>
															<th class="table-th text-center text-white">COSTO</th>
															<th width="18%" class="table-th text-center text-white">CANT</th>
															<th width="18%" class="table-th text-center text-white">UNIDAD MEDIDA</th>
															
															<th class="table-th text-center text-white">IMPORTE</th>
														</tr>
													</thead>
													<tbody>
															@foreach ($data as $product)
														<tr>
                                                           <td><h6>{{ $product->name }}</h6></td>
															<td class="text-center"><h6>$ {{$product->cost*$product->relacion_medida }} </h6></td>
															
															<td class="text-center">
																<h6>{{number_format($product->cantidad,2)}}</h6>

															</td>
                                                            <td class="text-center">
																<h6>
																	{{ $product->nombre_unidad_medida }}
																</h6>
															</td>
															<td class="text-center">
																<h6>
																	$ {{ number_format($product->cost*$product->relacion_medida*$product->cantidad,2) }}
																</h6>
															</td>
															
														</tr>

														@endforeach
														<tfoot class="bg-dark" style="color:black;">
															<tr>

																<th class="table-th text-center text-white">TOTAL</th>
																<th class="table-th text-white"></th>
																<th class="table-th text-white"></th>
																<th class="table-th text-center text-white"></th>
																<th class="table-th text-center text-white "></th>
																<th class="table-th text-center ">{{$suma}}</th>

															</tr>
														</tfoot>
													</tbody>
												</table>
												<br><br>
												<h6>Total {{$suma}}</h6>
											</div>
										
									<!--
											<div wire:loading.inline wire:target="saveSale">
												<h4 class="text-danger text-center">Guardando Venta...</h4>
											</div>
										-->

									<br>

								</div>
						</div>
				</div>

				</a>
	
	</div>
</div>
