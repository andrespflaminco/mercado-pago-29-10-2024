

<div>
	                <div class="page-header">
					<div class="page-title">
							<h4>Detalle de la produccion del producto</h4>
							<h6></h6>
						</div>
						<div class="page-btn">               											    
                			
                			<a  class="btn btn-success" href="{{ url('receta-imprimir/pdf/'.$receta_id) }}"> Imprimir </a>    
                            <a  class="btn btn-dark" href="{{ url('produccion') }}"> Volver al resumen </a>
							
						</div>
					</div>
					
                    
					<!-- /product list -->
					<div class="card">
				
						<div class="card-body">
                    
 							 <div class="table-responsive">
 						     <p>Cantidad de producto final producida: {{$rinde}} unidades</p>
						     <!--TABLA-->
						     <table class="table">
													<thead>
														<tr>
														  	<th>INSUMO</th>
															<th>CANTIDAD TEORICA</th>
															<th hidden>CANTIDAD REAL</th>
															<th>COSTO CONSUMIDO</th>
														</tr>
													</thead>
													<tbody>
															@foreach ($data as $product)
														<tr>
														    <td>{{$product->insumo_codigo}} - {{$product->insumo_nombre}}</td>
															<td> {{number_format($product->cantidad_consumida,4)}} {{ $product->nombre_unidad_medida }}</td>
                                                            <td hidden></td>
															<td> $  {{ number_format($product->costo_total,2,",",".")  }} </td>
														</tr>

														@endforeach
													
													</tbody>
												</table>
 							 </div>
						</div>
					</div>
					


</div>