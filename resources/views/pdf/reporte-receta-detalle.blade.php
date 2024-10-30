
			<div class="row">

				
				<div class="col-lg-3 col-md-4 col-sm-12">
                
                
                @if($usuario->image != null)
                <img  width="100" class="rounded" src="{{ asset('storage/users/'.$usuario->image) }}" >
                @else
                <h5 class="inv-brand-name">{{$usuario->name}}</h5>
                @endif
                
                
                     <h6 style="font-size:18px;">{{$nombre->name}} {{$nombre->variaciones}} </h6>
                    <h6 style="font-size:18px;">Cantidad de unidades a producir:  {{ $produccion_detalles_insumos->cantidad  }} </h6>
                    
				</div>




				</div>
			<div class="widget-content">

									<div class="connect-sorting-content">

											<div class="table-responsive tblscroll">
												<table style="width:100%; border:solid 1px #333; border-collapse: collapse;" class="table table-bordered table-striped mt-1">
													<thead style="font-size:18px;">
														<tr>
														  	<th class="table-th text-left text-white">INSUMO</th>
															<th class="table-th text-center text-white">CANTIDAD</th>
														</tr>
													</thead>
													<tbody>
															@foreach ($data as $product)
														<tr style="font-size:16px; font-weight: 600;">
                                                           <td style="text-align:center; border:solid 1px #333; padding:10px; border-collapse: collapse;" class="text-center" >{{ $product->name }}</td>
															
															<td style="text-align:center; border:solid 1px #333; padding:10px; border-collapse: collapse;" class="text-center">
																{{number_format($product->cantidad/$rinde*$produccion_detalles_insumos->cantidad,4)}} {{ $product->nombre_unidad_medida }}

															</td>
                                                          	
														</tr>

														@endforeach
													
													</tbody>
												</table>
												<br><br>
												
											</div>
										
									<!--
											<div wire:loading.inline wire:target="saveSale">
												<h4 class="text-danger text-center">Guardando Venta...</h4>
											</div>
										-->

									<br>

								</div>
						</div>