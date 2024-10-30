<div class="page-header">
						<div class="page-title">
							<h4>Venta #{{$nro_venta_ver_render}} </h4>
							<h6></h6>
						</div>
					</div>
					<div class="card">
						<div class="card-body">
							<div class="row">
								<div class="col-lg-4 col-sm-6 col-12">
									<div class="form-group">
										<label>Cliente</label>
										<div class="row">
											<div class="col-lg-10 col-sm-10 col-10">
											<select disabled wire:model="cliente_id" id="c" wire:change="selectCliente( $('#c').val() )" class="form-control">
											<option value="1">Consumidor final</option>
											@foreach($clientes as $c)
											<option value="{{$c->id}}">{{$c->nombre}}</option>
											@endforeach
											</select>
											</div>
										</div>
									</div>
								</div>
								<div class="col-lg-1 col-sm-6 col-12">
                                
								</div>
								<div class="col-lg-3 col-sm-6 col-12">

								</div>
																    
								<div class="col-lg-4 col-sm-6 col-12 text-right">
								    
								<div style="padding-top:18px;" class="form-group">
								<label > </label>

                                @if($ultima_factura == null)
					           	
					           	@foreach($detalle_cliente as $m)    
                                <a hidden class="btn" style="box-shadow: none; border: solid 1px #515365; background:transparent; margin-left: 10px !important;" href="javascript:void(0)" wire:click="MailModalVerVenta('venta',{{$ventaId}})" >
                                      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-mail"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                Enviar
                                </a>
                    
                                @endforeach    
                                
					            
                                <button style="color: #212529; box-shadow: none; border: solid 1px #515365; background:transparent; margin-left: 10px !important;" class="btn btn-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                 <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-printer action-print" data-toggle="tooltip" data-placement="top" data-original-title="Imprimir"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                                Imprimir 
                                </button>
                        
                                <div class="dropdown-menu">
                                      <a class="dropdown-item" href="javascript:void(0)" wire:click="AbrirImprimir({{$ventaId}})">IMPRIMIR A4</a>
                                      <a class="dropdown-item" target="_blank" href="{{ url('ticket' . '/' . $ventaId ) }}">IMPRIMIR TICKET</a>
                                </div>
                                
                                @else
                                
                                @if($ultima_factura->nota_credito == null)
                                <a class="btn" onclick="scrollToElement('miElemento')" style="box-shadow: none; border: solid 1px #515365; background:transparent; margin-left: 10px !important;" href="javascript:void(0)" >
                                Factura: {{$ultima_factura->nro_factura}}
                                </a>
                                @endif 
                                @endif  
								
								</div>
								</div>
								
								<div class="col-lg-12 col-sm-6 col-12">
									<div class="form-group">
										<label>Agregar producto</label>
										@if($status == "Cancelado")
										<div class="input-groupicon">
											<input readonly style="font-size:14px !important;" onclick="MsgError('La venta fue cancelada','Modifique el estado de la venta para editarla')" type="text" class="form-control" readonly type="text" placeholder="Scanear/Buscar producto...">
											<div class="addonset">
												<img src="{{ asset('assets/pos/img/icons/scanners.svg') }}" alt="img">
											</div>
										</div>										
										@else
										<div class="input-groupicon">
											<input readonly style="font-size:14px !important;" type="text" class="form-control" wire:model="query_product" wire:keydown.escape="resetProduct" wire:keydown.tab="resetProduct" type="text" placeholder="Scanear/Buscar producto...">
											<div class="addonset">
												<img src="{{ asset('assets/pos/img/icons/scanners.svg') }}" alt="img">
											</div>
										</div>
										@endif
										

                                        
                                        @if(!empty($query_product))
                                            <div class="fixed top-0 bottom-0 left-0 right-0" wire:click="reset"></div>
                            
                                            <div style="position:absolute;" class="absolute z-10 w-full bg-white rounded-t-none shadow-lg list-group">
                                                @if(!empty($products_s))
                                                    @foreach($products_s as $i => $product)
                                                    <a style="z-index: 9999;" href="javascript:void(0)"
                                                    wire:click="selectProduct({{$product['id']}})"
                                                    class="btn" title="Seleccionar">{{ $product['barcode'] }} - {{ $product['name'] }}
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
												<th>Observaciones</th>
												<th>Cantidad</th>
												@if($relacion_precio_iva == 2)
												<th>Precio + IVA</th>
												@else
												<th>Precio</th>
												@endif
												@if($relacion_precio_iva != 0)
												<th>IVA</th>
												@endif
												<!--------
												<th>Descuento x promociones</th>
												-------->
												@if($relacion_precio_iva == 2)
												<th> Total con IVA ($)	</th>
												@else
												<th> Total ($)	</th>
												@endif
												<th></th>
											</tr>
										</thead>
										<tbody>
										   <?php $i = 1; ?>
                                            @foreach($detalle_venta as $item)
                                            <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td>{{$item->product_barcode}}</td>
                                            <td>
                                               {{$item->product_name}}
                        
                                			   @if(0 < $item->cantidad_promo != null)
						
                        					   @if($relacion_precio_iva == 2)
                        						
                        					   <br><text style="color:red !important;">PROMO: {{$item->nombre_promo}}  ({{$item->cantidad_promo}} x -${{ number_format($item->descuento_promo * (1 + $item->iva),1,",",".")  }}) <a href="javascript:void(0)" onclick="QuitarPromo({{$item->id_promo}},{{$item->id}})" title="Quitar promo"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="red" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg></a> </text>
                                               @else
                                               	<br><text style="color:red !important;">PROMO: {{$item->nombre_promo}}  ({{$item->cantidad_promo}} x -${{$item->descuento_promo}}) <a href="javascript:void(0)" onclick="QuitarPromo({{$item->id_promo}},{{$item->id}})" title="Quitar promo"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="red" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg></a></text>
                                               @endif
                                               @endif
                        		
                                            </td>
                                            <td>
                                            @if($item->estado == 1)
                                            <p @if(!$columns['entrega_parcial']) style="display: none;" @endif>
                                            <text style="border: solid 1px #28a745; border-radius:4px; padding: 4px 15px; color: #28a745;">Entregado</text>    
                                            </p>
                                            @else
                                            <p @if(!$columns['entrega_parcial']) style="display: none;" @endif>
                                            <text style="border: solid 1px #ffc107; border-radius:4px; padding: 4px 15px; color: #ffc107;">No entregado</text>    
                                            </p>
                                            @endif
                                            </td>
                                            <td>
                                            @if($status == "Cancelado")
                                            <input readonly onclick="MsgError('La venta fue cancelada','Modifique el estado de la venta para editarla')" style="max-width: 70px;" type="text" readonly class="boton-editar"  value="{{number_format($item->quantity,0)}}"  >
                                             @else
                                            @if ($item->stock_descubierto === "si")
                                                <div class="input-group">

                                                    <input readonly type="number" class="boton-editar form-control" 
                                                           value="{{ number_format($item->quantity, $item->tipo_unidad_medida == 1 ? 3 : $configuracion_decimales_unidades) }}" 
                                                           id="qty{{$item->id}}" 
                                                           wire:keyup.enter="updateQtyPedido({{$item->id}}, $('#qty' + {{$item->id}}).val() )" 
                                                           wire:change="updateQtyPedido({{$item->id}}, $('#qty' + {{$item->id}}).val() )" 
                                                           min="1" 
                                                           onchange="Update({{$item->id}});" 
                                                           style="max-width: 90px;">
                                                    @if ($item->tipo_unidad_medida == 1)
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" style="border: none; background-color: white;">kg</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <p style="color:red;" id="stock_maximo{{$item->id}}" hidden>Stock maximo</p>
                                            @else
                                                <div class="input-group">

                                                    <input readonly type="number" class="boton-editar form-control" 
                                                           value="{{ number_format($item->quantity, $item->tipo_unidad_medida == 1 ? 3 : $configuracion_decimales_unidades) }}" 
                                                           id="qty{{$item->id}}" 
                                                           wire:keyup.enter="updateQtyPedido({{$item->id}}, $('#qty' + {{$item->id}}).val() )" 
                                                           wire:change="updateQtyPedido({{$item->id}}, $('#qty' + {{$item->id}}).val() )" 
                                                           min="1" 
                                                           onchange="Update({{$item->id}});" 
                                                           style="max-width: 90px;">
                                                           
                                                    @if ($item->tipo_unidad_medida == 1)
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" style="border: none; background-color: white;">kg</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif

                                            @endif
                                            
                                           <input hidden  id="stock_descubierto{{$item->id}}"	value="{{$item->stock_descubierto}}">
                                           <input hidden id="stock{{$item->id}}" value="{{$item->stock}}">
                                           <input hidden  id="stock_max{{$item->id}}" value="{{$item->stock+$item->quantity}}">
                                            
                                           
                                           </td>
                                           
                                           <td>
                                            @foreach($total_total as $f)

                                            <div class="input-group">
                                            $    
                                            
                                            @if($relacion_precio_iva == 2)
                                            
                                            @php
                                            if($item->price < $item->precio_original){
                                            $precio_item = number_format(($item->price * (1 + $item->iva)), 0, '.', '');
                                            } else {
                                            $precio_item = $item->precio_original;
                                            }
                                            @endphp
                                            
                                            @if(0 < $item->cantidad_promo != null)
                                            <input readonly style="max-width: 100px;" type="text" class="boton-editar"  value="{{$precio_item}}" onclick="MsgError('Atencion','No puede editar el precio de un producto con promocion')"  min="1" >
                                            
                                            @else
                                            <input readonly style="max-width: 100px;" type="text" class="boton-editar"  value="{{$precio_item}}" id="price{{$item->id}}"
                                            wire:keydown.enter="updatePricePedido({{$item->id}}, $('#price' + {{$item->id}}).val() )"
                                            wire:change="updatePricePedido({{$item->id}}, $('#price' + {{$item->id}}).val() )" min="1" >
                                            
                                            @endif
                                            @else
                                            @if($status == "Cancelado")
                                            <input readonly onclick="MsgError('La venta fue cancelada','Modifique el estado de la venta para editarla')" style="max-width: 70px;" type="text" readonly class="boton-editar"  value="{{number_format($item->price,2)}}"  >
                                            @else
                                            <input  readonly style="max-width: 100px;" type="text" class="boton-editar"  value="{{floatval($item->price)}}" id="price{{$item->id}}"
                                            wire:keydown.enter="updatePricePedido({{$item->id}}, $('#price' + {{$item->id}}).val() )"
                                            wire:change="updatePricePedido({{$item->id}}, $('#price' + {{$item->id}}).val() )" min="1" >
                                            
                                            @endif
                                            @endif

                                           </div>

                                            @endforeach
                                            </td>
                                            @if($relacion_precio_iva != 0)
                                            <td>
                                               <a style="color: #637381; font-weight: 500 !important;" href="javascript:void(0)" class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">{{$item->iva*100}} % </a>
                                             	<div class="dropdown-menu">
                                            		<button   id="iva{{$item->id}}" wire:click="UpdateIvaProducto('{{$item->id}}', $('#iva' + '{{$item->id}}').val() )"  value="0"   class="dropdown-item">Sin IVA</button>
                                            		<button  id="ivaprimero{{$item->id}}" wire:click="UpdateIvaProducto('{{$item->id}}', $('#ivaprimero' + '{{$item->id}}').val() )"  value="0.105" class="dropdown-item">10,5%</button>
                                            		<button  id="ivasegundo{{$item->id}}" wire:click="UpdateIvaProducto('{{$item->id}}', $('#ivasegundo' + '{{$item->id}}').val() )"  value="0.21"  class="dropdown-item">21%</button>
                                            		<button  id="ivatercero{{$item->id}}" wire:click="UpdateIvaProducto('{{$item->id}}', $('#ivatercero' + '{{$item->id}}').val() )"  value="0.27"  class="dropdown-item">27%</button>
                                            	</div>
                                            </td>  
                                            @endif
                                            <td>

                                            @foreach($total_total as $f)

                                            @if($relacion_precio_iva == 2)
                                            $ {{number_format( (($item->price*$item->quantity) *(1 + $item->iva)) ,0,",",".")}}
                                
                                			@if(0 < $item->cantidad_promo != null)
						                    <br><text style="color:red !important;">-${{number_format($item->cantidad_promo*$item->descuento_promo*(1 + $item->iva),0,",",".") }}</text>
                                            @endif
                        		
                        		
                                            @else
                                            
                                            $ {{number_format($item->price*$item->quantity ,0,",",".")}}
			                                
			                                @if(0 < $item->cantidad_promo != null)
			                                <br><text style="color:red !important;">-${{number_format($item->cantidad_promo*$item->descuento_promo,0,",",".") }}</text>
                                            @endif
                                            
                                            @endif

                                            @endforeach
    
                                            </td>
                                            <td>
                                            <div class="d-flex">
      
											@if($status != "Cancelado")
											<div hidden @if(!$columns['entrega_parcial']) style="display: none;" @endif class="btn-group">
											<button style="border:none !important; padding-top:0px; padding-bottom:0px; background:none; color: #637381 !important;" type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											<svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-truck"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>    
											</button>
											<div class="dropdown-menu">
											    <div style="color: #212B36; font-size: 13px; padding: 8px 15px; font-weight: 500;">Cambiar estado de entrega</div>
											    <div class="dropdown-divider"></div>
											    @if($status != "Entregado")
											    @if($item->estado == 0)
												<a class="dropdown-item" href="javascript:void(0);"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="green" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg> Pendiente</a>
												<a class="dropdown-item" href="javascript:void(0);" wire:click="EntregaParcial({{$item->id}},1)">Entregado</a> <!--- estado a elegir --->
												@endif
												@endif
												@if($item->estado == 1)
												<a class="dropdown-item" href="javascript:void(0);" wire:click="EntregaParcial({{$item->id}},2)">Pendiente</a> <!--- estado a elegir --->
												<a class="dropdown-item" href="javascript:void(0);"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="green" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg> Entregado</a>
												@endif
											</div>
										</div>
										
											@endif                                                
                                            </div>

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
									    
									    @foreach ($total_total as $t)
										
										
										@if($relacion_precio_iva != 1)
										<ul>
											<li>
												<h4>
												Subtotal 
												</h4>
												<h5>$ {{number_format($subtotal_con_iva,0)}} </h5>
											</li>
											<li>
												<h4>
												Descuento promociones 
												</h4>
												<h5>- $ {{number_format($descuento_promo_con_iva,0)}} </h5>
											</li>
											<li>
												<h4>
												Descuento general (
												@if(0 < $t->descuento)
												{{number_format(($t->subtotal-$t->descuento_promo) /$t->descuento,0)}} 
												@else
												0
												@endif
												%)
												</h4>
												<h5>- $ {{number_format($t->descuento * (1 +  $alicuota_iva),0)}} </h5>
											</li>
											<li>
												<h4>Recargo</h4>
												<h5>$ {{number_format($t->recargo * (1 + $alicuota_iva),0)}}</h5>
											</li>
											
											<li class="total">
												<h4>Total</h4>
												<h5>$ {{number_format($t->total,0)}}</h5>
											</li>
											
											<li>
												<h4>
												IVA (Incluido en el total)
												</h4>
												<h5>$ {{number_format($t->iva,0)}}</h5>
											</li>

											<li class="total">
												<h4>Deuda </h4>
												<h5>$ {{number_format(($tot) - ($suma_monto) - $rec - $sum_iva_pago - $sum_iva_recargo ,0)}}</h5>
											</li>
										</ul>
										@else
										<ul>
											<li>
												<h4>
												Subtotal 
												</h4>
												<h5>$ {{number_format($t->subtotal,2)}} </h5>
											</li>
											<li>
												<h4>
												Descuento promociones 
												</h4>
												<h5>$ {{number_format($t->descuento_promo,2)}} </h5>
											</li>
											<li>
												<h4>
												Descuento general 
												</h4>
												<h5>$ {{number_format($t->descuento,2)}} </h5>
											</li>
											<li>
												<h4>Recargo</h4>
												<h5>$ {{number_format($t->recargo,2)}}</h5>
											</li>
											<li>
												<h4>Base imponible IVA</h4>
												<h5>$ {{number_format($t->subtotal-$t->descuento+$t->recargo-$t->descuento_promo,2)}}</h5>
											</li>
											<li>
												<h4>
												IVA 
												@if($relacion_precio_iva == 2)
												(Incluido en el precio)
												@endif
												</h4>
												<h5>$ {{number_format($t->iva,2)}} ({{number_format($t->alicuota_iva,2)}}%)</h5>
											</li>

											<li class="total">
												<h4>Total</h4>
												<h5>$ {{number_format($t->total,2)}}</h5>
											</li>
											<li class="total">
												<h4>Deuda</h4>
												<h5>$ {{number_format(($tot) - ($suma_monto) - $rec - $sum_iva_pago - $sum_iva_recargo ,2)}}</h5>
											</li>
										</ul>
										@endif
										@endforeach
									</div>
								</div>
							</div>
							<div  class="row">
							    <div class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label>Tipo de factura </label>
											<select  disabled wire:model="tipo_factura" id="tipo_factura" wire:change="UpdateTipoComprobante( $('#tipo_factura').val() , {{$NroVenta}} , 1 )"  class="form-control">
											<option value="CF">CF</option>
											<option value="A">A</option>
											<option value="B">B</option>
											<option value="C">C</option>
											</select>
								</div>
								</div>

								
								<div class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label>Relacion PRECIO - IVA</label>
											<select disabled {!! $status == "Cancelado" ? 'onclick="MsgError(\'La venta está cancelada\',\'Modifique el estado para editarla\')"' : '' !!} {{$status == "Cancelado"? 'readonly' : '' }}   wire:model="relacion_precio_iva" id="relacion_precio_iva" wire:change="SwitchUpdateRelacionPrecioIva( $('#relacion_precio_iva').val() , {{$NroVenta}} , 1 )"  class="form-control">
											<option value="0">Sin IVA</option>
											<option value="1">Precio + IVA</option>
											<option value="2">IVA incluido en el precio</option>
											</select>
									</div>
								</div>
								
							    <div class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label>IVA</label>
									    
								        <a class="form-control" href="javascript:void(0)" class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"  {{$relacion_precio_iva == 0 ? 'readonly' : '' }}  {!! $relacion_precio_iva == 0 ? 'onclick="MsgError(\'Atencion\',\'Asigne primero la relacion precio - IVA\')"' : '' !!}   {!! $status == "Cancelado" ? 'onclick="MsgError(\'La venta está cancelada\',\'Modifique el estado para editarla\')"' : '' !!} {{$status == "Cancelado"? 'readonly' : '' }} >
								        
								            {{number_format($alicuota_iva*100,1)}} % 
								            
								        </a>
                                             	<div class="dropdown-menu">
                                            		<button  id="iva" wire:click="UpdateIvaGral( $('#iva1').val()  , {{$NroVenta}} , 1)"  value="0"   class="dropdown-item">Sin IVA</button>
                                            		<button  id="iva2" wire:click="UpdateIvaGral( $('#iva2').val()  , {{$NroVenta}} , 1 )"  value="0.105" class="dropdown-item">10,5%</button>
                                            		<button  id="iva3" wire:click="UpdateIvaGral( $('#iva3').val()  , {{$NroVenta}} , 1 )"  value="0.210"  class="dropdown-item">21%</button>
                                            		<button  id="iva4" wire:click="UpdateIvaGral( $('#iva4').val()  , {{$NroVenta}} , 1 )"  value="0.270"  class="dropdown-item">27%</button>
                                            	</div>
                                            	
                                            	
									</div>
								</div>

								<div hidden class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label>Descuento</label>
									    <div class="input-group mb-0">
                                        <input readonly type="number" 
                                        wire:change="UpdateDescuentoGral()" >
                                        <div class="input-group-append">
                                         <span class="input-group-text input-gp">
                                         %
                                         </span>
                                        </div>
                                        </div> 
									</div>
								</div>
								
								<div hidden class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label>Costo de envio</label>
										<input readonly type="number" class="form-control">
									</div>
								</div>
								
								
								<div class="col-lg-12">
									<div class="form-group">
										<label>Nota interna</label>
										<textarea disabled wire:model="nota_interna" class="form-control"></textarea>
									</div>
								</div>
								<div class="col-lg-12">
									<div class="form-group">
										<label>Observaciones</label>
										<textarea wire:model="observaciones" id="observaciones" wire:keydown.enter="UpdateObservaciones( $('#observaciones').val() )" wire:change="UpdateObservaciones( $('#observaciones').val() )"  class="form-control"></textarea>
									</div>
								</div>
								
									<div class="row">
								    
								        
                                        @if($ecommerce_envio_form != null)
                                        <h4> Datos de envio </h4>                                                
                                        <!---- Si es un cliente de con envio a domicilio ---->
                                        <br>
        							    <div class="col-lg-3 col-sm-6 col-12">
    									<div class="form-group">
    										<label>Tipo de entrega</label>
    											<select disabled wire:model="tipo_entrega" id="tipo_entrega" wire:change="UpdateTipoEntrega( $('#tipo_entrega').val() )"  class="form-control">
    											<option value="0">Retira por el local</option>
    											<option value="1">Envio al domicilio del cliente</option>
    											<option value="2">Envio a domicilio</option>
    											</select>
    									</div>
    								</div>
                                    @if($ecommerce_envio_form->metodo_entrega != 0)
                                    
                                    <div class="col-lg-3 col-sm-6 col-12">
    									<div class="form-group">
    										<label>Nombre del destinatario</label>
    									    <input  readonlytype="text" id="nombre_destinatario" wire:change="UpdateNombreDestinatario( $('#nombre_destinatario').val() )" wire:model="nombre_destinatario">
    									</div>
    								</div>
    								<div class="col-lg-3 col-sm-6 col-12">
    									<div class="form-group">
    										<label>Direccion</label>
    									    <input  readonlytype="text"  wire:model="direccion" id="direccion" wire:change="UpdateDireccion( $('#direccion').val() )">
    									</div>
    								</div>
    								<div class="col-lg-3 col-sm-6 col-12">
    									<div class="form-group">
    										<label>Ciudad</label>
    									    <input readonly type="text" wire:model="ciudad" id="ciudad" wire:change="UpdateCiudad( $('#ciudad').val() )">
    									</div>
    								</div>
    								<div class="col-lg-3 col-sm-6 col-12">
    									<div class="form-group">
    										<label>Provincia</label>
    									    <input readonly type="text" wire:model="nombre_provincia" id="provincia" wire:change="UpdateProvincia( $('#provincia').val() )">
    									</div>
    								</div>
							        @endif
								    <div class="col-lg-3 col-sm-6 col-12">
    									<div class="form-group">
    										<label>Telefono</label>
    									    <input readonly type="text" wire:model="telefono" id="telefono" wire:change="UpdateTelefono( $('#telefono').val() )">
    									</div>
    								</div>
								
                                        <div hidden class="col-sm-12 col-12">
                                             <p class=" inv-subtitle">Tipo de entrega : @if($ecommerce_envio_form->metodo_entrega == 1)  Retira por el local @else Entrega a Domicilio    @endif </p>
                                        </div>
                                                            
                                    <br><br><br><br>
                                    @endif
                                        <!------------------------------------------------>
                                                                     
                                                                     
								<div hidden class="row">
								    <div class="col-sm-12 col-md-9">
                                             <div class="col-sm-12 col-md-12">
                                             <h4> $ Pagos </h4>
                                             </div>

                                             <div class="col-sm-12 col-md-12">
                                               <div class="form-group">
                                                 <div style="margin-bottom: 0 !important;" class="table-responsive mb-4 mt-4">
                                                     <table class="multi-table table table-hover" style="width:100%">
                                                         <thead>
                                                             <tr>
                                                                 <th class="text-center">Caja</th>
                                                                 <th class="text-center">Fecha</th>
                                                                 <th class="text-center">Metodo de pago</th>
                                                                 <th class="text-center">Pago @if($relacion_precio_iva == 2) (IVA incluido) @endif </th>
                                                                 <th class="text-center">Recargo @if($relacion_precio_iva == 2) (IVA incluido) @endif </th>
                                                                 @if($relacion_precio_iva == 1) 
                                                                 <th class="text-center">IVA</th>
                                                                 @endif
                                                                 <th class="text-center">Total Pago</th>
                                                                 <th class="text-center"></th>
                                                                 <th class="text-center"></th>
                                                             </tr>
                                                         </thead>
                                                         <tbody>
                                                             @foreach($pagos2 as $p2)
                                                             @if ($p2->monto > 0)
                                                             <tr>
                                                               <td class="text-center">Caja # {{$p2->nro_caja}}</td>
                                                                 <td class="text-center">{{\Carbon\Carbon::parse( $p2->fecha_pago)->format('d-m-Y')}}</td>
                                                                 <td class="text-center">{{$p2->metodo_pago}}</td>
                                                                 <td class="text-center">$
                                                                 
                                                                 @if($relacion_precio_iva == 1)
                                                                    {{number_format($p2->monto,2) }} 
                                                                @else
                                                                    {{number_format($p2->monto + $p2->iva_pago,2) }} 
                                                                @endif    
                                                                </td>

                                                                <td class="text-center">$
                                                                  @if($relacion_precio_iva == 1)
                                                                  {{number_format($p2->recargo,2) }}
                                                                  @else
                                                                  {{number_format($p2->recargo + $p2->iva_recargo,2) }}
                                                                  @endif
                                                                </td>
                                                                    
                                                                 @if($relacion_precio_iva == 1)   
                                                                    <td class="text-center">$
                                                                    {{number_format($p2->iva_pago + $p2->iva_recargo,2) }} 
                                                                    </td>
                                                                  @endif
                                                                   <td class="text-center">$
                                                                        {{number_format($p2->monto + $p2->recargo + $p2->iva_pago + $p2->iva_recargo,2) }} </td>
                                                                   
                                                                   <td style="text-align: right; padding: 0; margin: 0;">
                													@if($p2->url_comprobante != null)
                													<a title="Ver comprobante" href="{{ asset('storage/comprobantes/' . $p2->url_comprobante) }}" target="_blank">
                						     					     <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text">
                                                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                                        <polyline points="14 2 14 8 20 8"></polyline>
                                                                        <line x1="16" y1="13" x2="8" y2="13"></line>
                                                                        <line x1="16" y1="17" x2="8" y2="17"></line>
                                                                        <polyline points="10 9 9 9 8 9"></polyline>
                                                                    </svg>
                													</a>
                													@endif    
                													</td>



                                                             </tr>

                                                               @endif
                                                               @endforeach





                                                         </tbody>
                                                         <tfoot>
                                                             <tr>
                                                                 <th style="font-weight:700 !important;" class="text-center">Total </th>
                                                                 <th style="font-weight:700 !important;" class="text-center"> </th>
                                                                 <th style="font-weight:700 !important;" class="text-center"> </th>
                                                                 <th style="font-weight:700 !important;" class="text-center">$ 
                                                                 @if($relacion_precio_iva == 1)
                                                                 {{number_format($suma_monto,2)}}
                                                                 @else
                                                                 {{number_format($suma_monto+$sum_iva_pago,2)}}
                                                                 @endif
                                                                 </th>
                                                                 
                                                                 <th style="font-weight:700 !important;" class="text-center">$ 
                                                                 @if($relacion_precio_iva == 1)
                                                                 {{number_format($rec,2)}}
                                                                 @else
                                                                 {{number_format($rec+$sum_iva_recargo,2)}}
                                                                 
                                                                 @endif
                                                                 </th>
                                                                 @if($relacion_precio_iva == 1)
                                                                 <th style="font-weight:700 !important;" class="text-center">$ {{number_format($sum_iva_pago+$sum_iva_recargo,2)}}</th>
                                                                 @endif
                                                                 <th style="font-weight:700 !important;" class="text-center">$ {{number_format($suma_monto+$rec+$sum_iva_pago+$sum_iva_recargo,2)}}</th>
                                                             </tr>
                                                         </tfoot>
                                                     </table>
                                                 </div>



                                              </div>

                                             </div>

                                             @if($relacion_precio_iva == 1)
                                             <strong>Deuda sin IVA: $ {{ number_format( (($tot - $iva_total) - ($suma_monto) - $rec  ),2) }}</strong><br>
                                             @endif
                                             <strong>Deuda  @if($relacion_precio_iva == 1 || $relacion_precio_iva == 2) con IVA @endif : $ {{ number_format( (($tot) - ($suma_monto) - $rec - $sum_iva_pago - $sum_iva_recargo),2) }}</strong>
                                
                                             <br><br>

                                              <div class="form-group">
                                              <a href="javascript:void(0);" wire:click.prevent="AgregarPago({{$ventaId}})">Agregar pago </a>
                                              </div>

                                           </div>
								</div>
								
								
									<div hidden id="miElemento" style class="row">
								    <div class="col-sm-12 col-md-9">
                                             <div class="col-sm-12 col-md-12">
                                             <h4> Facturas </h4>

                                             </div>
                                        
                                            @if(0 < $facturas->count())
                                            <div class="col-sm-12 col-md-12">
                                               <div class="form-group">
                                                 <div style="margin-bottom: 0 !important;" class="table-responsive mb-4 mt-4">
                                                     <table class="multi-table table table-hover" style="width:100%">
                                                         <thead>
                                                             <tr>
                                                                 <th class="text-center">Punto de venta</th>
                                                                 <th class="text-center">Factura</th>
                                                                 <th class="text-center">IVA</th>
                                                                 <th class="text-center">Monto</th>
                                                                 <th class="text-center">Nota credito</th>
                                                                 <th class="text-center">Acciones</th>
                                                             </tr>
                                                         </thead>
                                                         <tbody>
                                                             @foreach($facturas as $f)
                                                             <tr>
                                                               <td class="text-center">{{$f->razon_social}} (CUIT: {{$f->cuit_vendedor}})</td>
                                                               <td class="text-center">{{$f->nro_factura}}</td>
                                                               <td class="text-center">$ {{number_format($f->iva,2)}}</td>
                                                               <td class="text-center">$ {{number_format($f->total,2)}}</td>
                                                               <td class="text-center">
                                                               @if($f->nota_credito == null)
                                                              <a class="btn" style="box-shadow: none; border: solid 1px #515365; background:transparent; font-size:14px !important;" href="javascript:void(0)" onclick="ConfirmAnularFactura('{{$f->id}}')">
                                                              ANULAR FACTURA
                                                              </a>
                                                               @else
                                                                   {{$f->nota_credito}}
                                                               @endif
                                                               </td>
                                                               <td class="text-center">
                                                                @foreach($detalle_cliente as $m)    
                                                                    <a class="btn" style="box-shadow: none; border: solid 1px #515365; background:transparent; font-size:14px !important;" href="javascript:void(0)" wire:click="MailModalVerVenta('factura',{{$f->id}})" >
                                                                          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-mail"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                                                    Enviar
                                                                    </a>
                                                        
                                                                @endforeach    
                                            			            
                                                                    <button style="color: #212529; box-shadow: none; border: solid 1px #515365; background:transparent; font-size:14px !important;" class="btn btn-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                     <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-printer action-print" data-toggle="tooltip" data-placement="top" data-original-title="Imprimir"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                                                                    Imprimir 
                                                                    </button>
                                                            
                                                                    <div class="dropdown-menu">
                                                                          <a class="dropdown-item" target="_blank"  href="{{ url('imprimir-factura/pdf' . '/' . $f->id ) }}">IMPRIMIR A4.</a>
                                                                          <a class="dropdown-item" target="_blank" href="{{ url('ticket-factura' . '/' . $f->id  ) }}">IMPRIMIR TICKET</a>
                                                                    </div>
                                                               </td>
                                                           </tr>
                                                           @endforeach
                                                         </tbody>
                                                     </table>
                                                 </div>



                                              </div>

                                             </div>
                                            @endif    
                                            <br>
                                            <div class="col-sm-12 col-md-12">
                                                                                        
                                              @if($ultima_factura == null)

                                              <a wire:click="ElegirCUITFacturar('{{$NroVenta}}')"  class="btn" style="box-shadow: none; border: solid 1px #515365; background:transparent;" href="javascript:void(0)" >
                                              Crear nueva factura
                                              </a>
                                              @else
                                          
                                              @if($ultima_factura->nota_credito != null)                  								    
                						     <a class="btn" style="box-shadow: none; border: solid 1px #515365; background:transparent;" href="javascript:void(0)" wire:click="ElegirCUITFacturar('{{$NroVenta}}')" >
                                              Crear nueva factura 
                                              </a>
                                              @endif
                                              
                                              @endif
                                              
                                             </div>    
                                            <br>
                                           </div>
								</div>
								
							
								<div class="col-lg-12">
									<a href="javascript:void(0)" wire:click="CerrarModal" class="btn btn-cancel">Volver</a>
								</div>
							
						</div>
					</div>
				</div>
			</div>
        </div>