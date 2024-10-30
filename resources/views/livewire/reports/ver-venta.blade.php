<div class="page-header">
						<div class="page-title">
							<h4>Venta #{{$Nro_Venta}}</h4>
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
											<select wire:model="cliente_id" id="c" wire:change="selectCliente( $('#c').val() )" class="form-control">
											<option value="1">Consumidor final</option>
											@foreach($clientes as $c)
											<option value="{{$c->id}}">{{$c->nombre}}</option>
											@endforeach
											</select>
											</div>
											<div class="col-lg-1"><button class="btn btn-dark" wire:click="ModalAgregarCliente">+</button></div>
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
                                <a class="btn" style="box-shadow: none; border: solid 1px #515365; background:transparent; margin-left: 10px !important;" href="javascript:void(0)" wire:click="MailModalVerVenta('venta',{{$ventaId}})" >
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
											<input style="font-size:14px !important;" onclick="MsgError('La venta fue cancelada','Modifique el estado de la venta para editarla')" type="text" class="form-control" readonly type="text" placeholder="Scanear/Buscar producto...">
											<div class="addonset">
												<img src="{{ asset('assets/pos/img/icons/scanners.svg') }}" alt="img">
											</div>
										</div>										
										@else
										<div class="input-groupicon">
											<input style="font-size:14px !important;" type="text" class="form-control" wire:model="query_product" wire:keydown.escape="resetProduct" wire:keydown.tab="resetProduct" wire:keydown.enter="selectProduct" type="text" placeholder="Scanear/Buscar producto...">
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
												<th>Observaciones</th>
												<th>Cantidad</th>
												@if($relacion_precio_iva == 2)
												<th>Precio + IVA</th>
												@else
												<th>Precio</th>
												@endif
												<th>Descuento</th>
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
                                            <td>{{$item->product_name}}</td>
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
                                            <input  onclick="MsgError('La venta fue cancelada','Modifique el estado de la venta para editarla')" style="max-width: 70px;" type="text" readonly class="boton-editar"  value="{{number_format($item->quantity,0)}}"  >
                                             @else
                                            @if ($item->stock_descubierto === "si")
                                            <input  style="max-width: 70px;"  type="number" class="boton-editar"  value="{{number_format($item->quantity,0)}}" id="qty{{$item->id}}"
                                            wire:change="updateQtyPedido({{$item->id}}, $('#qty' + {{$item->id}}  ).val() )" min="1" onchange="Update({{$item->id}});" >
                                            <p style="color:red;" id="stock_maximo{{$item->id}}" hidden >Stock maximo</p>
                                            @else
                                            <input  style="max-width: 70px;" type="number" class="boton-editar"  value="{{number_format($item->quantity,0)}}" id="qty{{$item->id}}"
                                            wire:change="updateQtyPedido({{$item->id}}, $('#qty' + {{$item->id}}).val() )" min="1" onchange="Update({{$item->id}});" >
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
                                            <input style="max-width: 100px;" type="text" class="boton-editar"  value="{{$precio_item}}" id="price{{$item->id}}"
                                            wire:keydown.enter="updatePricePedido({{$item->id}}, $('#price' + {{$item->id}}).val() )"
                                            wire:change="updatePricePedido({{$item->id}}, $('#price' + {{$item->id}}).val() )" min="1" >
                                            
                                            @else
                                            @if($status == "Cancelado")
                                            <input  onclick="MsgError('La venta fue cancelada','Modifique el estado de la venta para editarla')" style="max-width: 70px;" type="text" readonly class="boton-editar"  value="{{number_format($item->price,2)}}"  >
                                            @else
                                            <input style="max-width: 100px;" type="text" class="boton-editar"  value="{{floatval($item->price)}}" id="price{{$item->id}}"
                                            wire:keydown.enter="updatePricePedido({{$item->id}}, $('#price' + {{$item->id}}).val() )"
                                            wire:change="updatePricePedido({{$item->id}}, $('#price' + {{$item->id}}).val() )" min="1" >
                                            
                                            @endif
                                            @endif

                                           </div>

                                            @endforeach
                                            </td>
                                            
                                            
                                            <td class="text-center">
                                            
                                            @if($item->price != 0) 
                                            
                                            <div class="input-group mb-0">
                                            <input {{$status == "Cancelado"? 'readonly' : '' }} {!! $status == "Cancelado" ? 'onclick="MsgError(\'La venta está cancelada\',\'Modifique el estado para editarla\')"' : '' !!}  style="max-width: 80px;" type="text" class="boton-editar"  value="{{ number_format(($item->descuento)/$item->price*100/$item->quantity,2) }}" 
                                            id="d{{$item->id}}"
                                            wire:keydown.enter="UpdateDescuentoRecargo({{$item->id}}, $('#d' + {{$item->id}}).val() )"
                                            wire:change="UpdateDescuentoRecargo({{$item->id}}, $('#d' + {{$item->id}}).val() )"> 
                                            <div class="input-group-append">
                                             <span class="input-group-text input-gp">
                                             %
                                             </span>
                                            </div>
                                            </div>   
                                                                            
                                            @else 
                                            <div class="input-group mb-0">
                                                
                                                
                                            <input {{$status == "Cancelado"? 'readonly' : ''}} {!! $status == "Cancelado" ? 'onclick="MsgError(\'La venta está cancelada\',\'Modifique el estado para editarla\)"' : '' !!}  style="max-width: 80px;" type="text" class="boton-editar"  value="{{ number_format(0,2) }}"  id="d{{$item->id}}"
                                            wire:change="UpdateDescuentoRecargo({{$item->id}}, $('#d' + {{$item->id}}).val() )"
                                           <div class="input-group-append">
                                             <span class="input-group-text input-gp">
                                             %
                                             </span>
                                            </div> 
                                            </div> 
                                            @endif
                                            
                                            </td>
                                            <!------
                                            <td class="text-center">
                                            {{$item->iva*100}} % 
                                            </td>
                                            ----->
                                            
                                            <td>

                                            @foreach($total_total as $f)

                                            @if($relacion_precio_iva == 2)
                                            $ {{number_format( (( ($item->price*$item->quantity) - $item->descuento ) *(1 + $item->iva)) ,0)}}

                                            @else
                                            
                                            @if($f->tipo_comprobante == "A")
                                            $ {{number_format( (( ($item->price*$item->quantity) - $item->descuento ) *(1)) ,0)}}

                                            @else
                                            $ {{number_format( (( ($item->price*$item->quantity) - $item->descuento) *(1)) ,0)}}

                                            @endif
                                            
                                            @endif

                                            @endforeach
    
                                            </td>
                                            <td>
                                            <div class="d-flex">
                                            @if($detalle_venta->count() == 1)
                                            <a onclick="MsgError('La venta contiene un solo producto, no podes eliminarlo.','En caso de querer eliminar todo, tenes que eliminar la venta')" ><img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="svg"></a>
											@else
											<a onclick="ConfirmEliminarProductoPedido({{$item->id}})"><img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="svg"></a>
											@endif
											@if($status != "Cancelado")
											<div @if(!$columns['entrega_parcial']) style="display: none;" @endif class="btn-group">
											<button style="border:none !important; padding-top:0px; padding-bottom:0px; background:none; color: #637381 !important;" type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											<svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-truck"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>    
											</button>
											<div class="dropdown-menu">
											    <div style="color: #212B36; font-size: 13px; padding: 8px 15px; font-weight: 500;">Estado de entrega</div>
											    <div class="dropdown-divider"></div>
											    @if($status != "Entregado")
											    @if($item->estado == 0)
												<a class="dropdown-item" href="javascript:void(0);" wire:click="EntregaParcial({{$item->id}},1)">Entregado</a>
												@endif
												@endif
												@if($item->estado == 1)
												<a class="dropdown-item" href="javascript:void(0);" wire:click="EntregaParcial({{$item->id}},2)">Pendiente</a>
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
												<h5>$ {{number_format($t->subtotal-$t->descuento+$t->iva - ($t->recargo * $t->alicuota_iva) ,2)}} </h5>
											</li>
											<li>
												<h4>Recargo</h4>
												<h5>$ {{number_format($t->recargo + ($t->recargo * $t->alicuota_iva),2)}}</h5>
											</li>
											
											<li class="total">
												<h4>Total</h4>
												<h5>$ {{number_format($t->total,2)}}</h5>
											</li>
											
											<li>
												<h4>
												IVA (Incluido en el total)
												</h4>
												<h5>$ {{number_format($t->iva,2)}} ({{number_format($t->alicuota_iva,2)}}%)</h5>
											</li>

											<li class="total">
												<h4>Deuda </h4>
												<h5>$ {{number_format(($tot) - ($suma_monto) - $rec - $sum_iva_pago - $sum_iva_recargo ,2)}}</h5>
											</li>
										</ul>
										@else
										<ul>
											<li>
												<h4>
												Subtotal 
												</h4>
												<h5>$ {{number_format($t->subtotal-$t->descuento,2)}} </h5>
											</li>
											<li>
												<h4>Recargo</h4>
												<h5>$ {{number_format($t->recargo,2)}}</h5>
											</li>
											<li>
												<h4>Base imponible IVA</h4>
												<h5>$ {{number_format($t->subtotal-$t->descuento+$t->recargo,2)}}</h5>
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
											<select  wire:model="tipo_factura" id="tipo_factura" wire:change="UpdateTipoComprobante( $('#tipo_factura').val() , {{$NroVenta}} , 1 )"  class="form-control">
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
											<select {!! $status == "Cancelado" ? 'onclick="MsgError(\'La venta está cancelada\',\'Modifique el estado para editarla\')"' : '' !!} {{$status == "Cancelado"? 'readonly' : '' }}   wire:model="relacion_precio_iva" id="relacion_precio_iva" wire:change="SwitchUpdateRelacionPrecioIva( $('#relacion_precio_iva').val() , {{$NroVenta}} , 1 )"  class="form-control">
											<option value="0">Sin IVA</option>
											<option value="1">Precio + IVA</option>
											<option value="2">IVA incluido en el precio</option>
											</select>
									</div>
								</div>
								
							    <div class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label>IVA</label>
											<select  {{$relacion_precio_iva == 0 ? 'readonly' : '' }}  {!! $relacion_precio_iva == 0 ? 'onclick="MsgError(\'Atencion\',\'Asigne primero la relacion precio - IVA\')"' : '' !!}   {!! $status == "Cancelado" ? 'onclick="MsgError(\'La venta está cancelada\',\'Modifique el estado para editarla\')"' : '' !!} {{$status == "Cancelado"? 'readonly' : '' }}   wire:model="alicuota_iva" id="iva" wire:change="UpdateIvaGral( $('#iva').val()  , {{$NroVenta}} , 1)"  class="form-control">
											<option value="0">Sin IVA</option>
											<option value="0.105">10.5%</option>
											<option value="0.21">21%</option>
											<option value="0.27">27%</option>
											</select>
									</div>
								</div>
                                <div class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label>Estado</label>
										<select wire:model="status" id="status_ver" {{$status == 'Entrega Parcial' ? 'disabled' : '' }} wire:change="UpdateEstadoVerVenta( $('#status_ver').val() , 1 )" class="form-control">
											<option value="Pendiente">Pendiente</option>
											<option value="En proceso">En proceso</option>
											<option value="Entregado">Entregado</option>
											<option value="Cancelado">Cancelado</option>
											<option @if(!$columns['entrega_parcial']) style="display: none;" @endif value="Entrega Parcial">Entrega Parcial</option>
										</select>
									</div>
								</div>
								<div hidden class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label>Descuento</label>
									    <div class="input-group mb-0">
                                        <input  type="number" 
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
										<input type="number" class="form-control">
									</div>
								</div>
								
								
								<div class="col-lg-12">
									<div class="form-group">
										<label>Nota interna</label>
										<textarea wire:model="nota_interna" id="nota_interna" wire:keydown.enter="UpdateNotaInterna( $('#nota_interna').val() )" wire:change="UpdateNotaInterna( $('#nota_interna').val() )"  class="form-control"></textarea>
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
    											<select wire:model="tipo_entrega" id="tipo_entrega" wire:change="UpdateTipoEntrega( $('#tipo_entrega').val() )"  class="form-control">
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
    									    <input type="text" id="nombre_destinatario" wire:change="UpdateNombreDestinatario( $('#nombre_destinatario').val() )" wire:model="nombre_destinatario">
    									</div>
    								</div>
    								<div class="col-lg-3 col-sm-6 col-12">
    									<div class="form-group">
    										<label>Direccion</label>
    									    <input type="text"  wire:model="direccion" id="direccion" wire:change="UpdateDireccion( $('#direccion').val() )">
    									</div>
    								</div>
    								<div class="col-lg-3 col-sm-6 col-12">
    									<div class="form-group">
    										<label>Ciudad</label>
    									    <input type="text" wire:model="ciudad" id="ciudad" wire:change="UpdateCiudad( $('#ciudad').val() )">
    									</div>
    								</div>
    								<div class="col-lg-3 col-sm-6 col-12">
    									<div class="form-group">
    										<label>Provincia</label>
    									    <input type="text" wire:model="nombre_provincia" id="provincia" wire:change="UpdateProvincia( $('#provincia').val() )">
    									</div>
    								</div>
							        @endif
								    <div class="col-lg-3 col-sm-6 col-12">
    									<div class="form-group">
    										<label>Telefono</label>
    									    <input type="text" wire:model="telefono" id="telefono" wire:change="UpdateTelefono( $('#telefono').val() )">
    									</div>
    								</div>
								
                                        <div hidden class="col-sm-12 col-12">
                                             <p class=" inv-subtitle">Tipo de entrega : @if($ecommerce_envio_form->metodo_entrega == 1)  Retira por el local @else Entrega a Domicilio    @endif </p>
                                        </div>
                                                            
                                    <br><br><br><br>
                                    @endif
                                        <!------------------------------------------------>
                                                                     
                                                                     
								<div class="row">
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
                                                                     <td class="text-center">

                                                                   <a href="javascript:void(0)" wire:click="EditPago({{$p2->id}})" >
                                                                   <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                                   </a>


                                                                   <a href="javascript:void(0)" onclick="ConfirmPago('{{$p2->id}}')" >
                                                                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                                                                    </a>




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
								
								
								<div id="miElemento" style class="row">
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
                                              <a class="btn" style="box-shadow: none; border: solid 1px #515365; background:transparent;" href="javascript:void(0)" onclick="ConfirmFactura('{{$NroVenta}}')" >
                                              Crear nueva factura 
                                              </a>
                                              @else
                                          
                                              @if($ultima_factura->nota_credito != null)                  								    
                						     <a class="btn" style="box-shadow: none; border: solid 1px #515365; background:transparent;" href="javascript:void(0)" onclick="ConfirmFactura('{{$NroVenta}}')" >
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