<div class="page-header">
						<div class="page-title">
							<h4>Movimiento # {{$nro_movimiento}} </h4>
							<h6></h6>
						</div>
					</div>
					<div class="card">
						<div class="card-body">
							<div class="row">
								<div class="col-lg-4 col-sm-6 col-12">
									<div class="form-group">
										<label>Sucursal origen: {{$sucursal_origen}}</label>
										<div class="row">
										</div>
									</div>
								</div>
								<div class="col-lg-4 col-sm-6 col-12">
									<div class="form-group">
										<label>Sucursal destino: {{$sucursal_destino}}</label>
										<div class="row">
										</div>
									</div>
								</div>
								<div class="col-lg-1 col-sm-6 col-12">
                                
								</div>
								<div class="col-lg-3 col-sm-6 col-12">

								</div>
								
								<div class="col-lg-12 col-sm-6 col-12">
									<div class="form-group">
										<label>Agregar producto</label>
										<div class="input-groupicon">
											<input disabled style="font-size:14px !important;" type="text" class="form-control" wire:model="query_product" wire:keydown.escape="resetProduct" wire:keydown.tab="resetProduct" wire:keydown.enter="selectProduct" type="text" placeholder="Scanear/Buscar producto...">
											<div class="addonset">
												<img src="{{ asset('assets/pos/img/icons/scanners.svg') }}" alt="img">
											</div>
										</div>
									
                                        
                                        @if(!empty($query_product))
                                            <div class="fixed top-0 bottom-0 left-0 right-0" wire:click="reset"></div>
                            
                                            <div style="position:absolute;" class="absolute z-10 w-full bg-white rounded-t-none shadow-lg list-group">
                                                @if(!empty($products_s))
                                                    @foreach($products_s as $i => $product)
                                                    <a style="z-index: 9999;" href="javascript:void(0)"
                                                    wire:click="selectProduct({{$product['id']}})"
                                                    class="btn" title="Edit">{{ $product['barcode'] }} - {{ $product['name'] }}
                                                    </a>
                            
                                                    @endforeach
                            
                                                @else
                            
                                                @endif
                                            </div>
                                        @endif

									</div>
								</div>
							</div>
							<div class="row">
								<div class="table-responsive">
									<table class="table">
										<thead>
											<tr>
												<th>Fila</th>
												<th>Codigo</th>
												<th>Producto</th>
												<th>Cantidad</th>
												<th>Precio Interno</th>
												<th class="text-end"> Total ($)	</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
										   <?php $i = 1; ?>
                                            @foreach($detalle_venta as $item)
                                            <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td>{{$item->product_barcode}}</td>
                                            <td>{{$item->product_name}}</td>
                                            <td class="text-center">
                                                <input  type="number" type="number" class="boton-editar"  value="{{number_format($item->cantidad,0)}}" id="qty{{$item->id}}"
                                                wire:change="updateQty('{{$item->id}}', $('#qty' + {{$item->id}}).val() )" wire:keyup.enter="updateQty('{{$item->id}}', $('#qty' + {{$item->id}}).val() )" min="1" onchange="Update({{$item->id}});" >
                                                
                                                </td>
                                            <td>$ {{$item->costo}}</td>
                                            <td class="text-end">$ {{$item->total}}</td>
                                            <td>
											<a onclick="ConfirmEliminarProductoPedido({{$item->id}})"><img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="svg"></a>
											</td>
                                           
                                            </tr>
                                          @endforeach
										</tbody>
									</table>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-12 float-md-right">
									<div class="total-order">
									  	<ul>
										    <li class="total">
												<h4>Total</h4>
												<h5>$ {{number_format($total_venta->total,2)}}</h5>
											</li>
											
										</ul>
									</div>
								</div>
							</div>
							<div  class="row">
							    <div class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
									
								</div>
								</div>
								
								
								<div class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
									
									</div>
								</div>
								
							    <div class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
									
									</div>
								</div>
                                <div class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label>Estado</label>
										<select wire:model="status" id="status" disabled wire:change="Update( $('#status').val() , 1 )" class="form-control">
											<option value="Entregado">Entregado</option>
											<option value="Pendiente">Pendiente</option>
											<option value="En proceso">En proceso</option>
											<option value="Cancelado">Cancelado</option>
										</select>
									</div>
								</div>
						
								<div class="col-lg-12">
									<div class="form-group">
										<label>Observaciones</label>
										<textarea disabled wire:model="observaciones" id="observaciones" wire:change="UpdateObservaciones( $('#observaciones').val() )"  class="form-control"></textarea>
									</div>
								</div>
								
									<div class="row">
					
							
								<div class="col-lg-12">
									<a href="javascript:void(0)" wire:click="CerrarModal" class="btn btn-cancel">Volver</a>
								</div>
							
						</div>
					</div>
				</div>
			</div>
        </div>