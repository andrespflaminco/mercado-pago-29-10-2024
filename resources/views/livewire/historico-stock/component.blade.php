<div>	                
	                <div class="page-header">
					<div class="page-title">
							<h4>Historico de movimientos de stock</h4>
							<h6>Muestra entradas y salidas de unidades</h6>
						</div>
					
					</div>
					
                    <div class="row">
						<div class="col-lg-3 col-sm-6 col-12">
							<div class="dash-widget">
								<div class="dash-widgetimg">
									<span style="background-color: #63738112 !important;">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#8ea0af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clipboard"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg></span>
								</div>
								<div class="dash-widgetcontent">
									<h5 ><span class="counters" data-count="{{ $stock_inicial ?: 0 }}">{{$stock_inicial ?: 0}}</span></h5>
									<h6>Stock Inicial</h6>
								</div>
							</div>
						</div>
						<div class="col-lg-3 col-sm-6 col-12">
							<div class="dash-widget dash2">
								<div class="dash-widgetimg">
									<span><img src="{{ asset('assets/pos/img/icons/dash3.svg') }}" alt="img"></span>
								</div>
								<div class="dash-widgetcontent">
									<h5 ><span class="counters" data-count="{{$ingresos ?: 0}}">{{$ingresos ?: 0}} </span></h5>
									<h6>Ingresos de stock</h6>
								</div>
							</div>
						</div>
						<div class="col-lg-3 col-sm-6 col-12">
							<div class="dash-widget dash3">
								<div class="dash-widgetimg">
									<span><img src="{{ asset('assets/pos/img/icons/dash4.svg') }}" alt="img"></span>
								</div>
								<div class="dash-widgetcontent">
									<h5 ><span class="counters" data-count="{{$egresos ?: 0}}">{{$egresos ?: 0}}</span></h5>
									<h6>Egresos de stock</h6>
								</div>
							</div>
						</div>
						<div class="col-lg-3 col-sm-6 col-12">
							<div class="dash-widget dash1">
								<div class="dash-widgetimg">
									<span>
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#00cd5c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layers"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>			</span>
								</div>
								<div class="dash-widgetcontent">
									<h5 ><span class="counters" data-count="{{$stock_final ?: 0}}">{{$stock_final ?: 0}}</span></h5>
									<h6>Stock Final</h6>
								</div>
							</div>
						</div>
					
					</div>
					
					<!-- /product list -->
					<div class="card">
					
						<div class="card-body">
							<div class="row mb-2">
                			<div class="col-lg-3 col-md-4 col-sm-12">
                								        
                 			    <label style="font-size: 11px !important;"><b>Buscar por nombre</b></label>
                				<div style="margin-bottom: 0 !important;" class="input-group mb-4">
                					<div class="input-group-prepend">
                						<span style="height: 100% !important;" class="input-group-text input-gp">
                							<i class="fas fa-clipboard-list"></i>
                						</span>
                					</div>
                					<input
                						style="font-size:14px !important;"
                						type="text"
                						class="form-control"
                						placeholder="Seleccione un producto"
                						wire:model="query_product"
                						wire:keydown.escape="resetProduct"
                						wire:keydown.tab="resetProduct"
                						wire:keydown.enter="selectProduct"
                					 />
                				</div>
                				@if(!empty($query_product))
                					<div class="fixed top-0 bottom-0 left-0 right-0" wire:click="reset"></div>
                						<div style="position:absolute; z-index: 999 !important; height: 250px; width: 300px; overflow: auto;"  class="absolute z-10 w-full bg-white rounded-t-none shadow-lg list-group">
                							@if(!empty($products_s))
                								@foreach($products_s as $i => $product)
                									<div class="btn-group" role="group" aria-label="Basic example">
                										<button value="{{$product['id']}}" 
                											id="code{{$product['id']}}"  
                											wire:click.prevent="$emit('buscar', $('#code{{$product['id']}}').val())" 
                											wire:click.lazy="selectProduct"
                											class="btn btn-light" 
                											title="Click en el producto">{{ $product['barcode'] }} - {{ $product['name'] }}
                										</button>
                									    <button hidden value="{{$product['barcode']}}" id="info{{$product['barcode']}}"  wire:click="$emit('info-producto', $('#info{{$product['barcode']}}').val())" style="max-width:50px;" type="button" class="btn btn-dark">
                											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                										</button>
                									</div>
                								@endforeach
                							@else
                								<div style="  padding: 10px;  text-align: center;"  class="absolute z-10 w-full bg-white rounded-t-none shadow-lg list-group">
                								No hay resultados
                								</div>
                							@endif
                
                							@can('product_create')
                								<a href="javascript:void(0)"  hidden style=" position: fixed;   width: 300px;  margin-top: 250px;" class="btn btn-dark" data-toggle="modal" data-target="#ModalProductos" >Agregar otro producto</a>
                							@endcan
                						</div>
                				@endif
                
                				
                				
                				@if(session('status'))
                                <br>
                                <strong style="padding: 5px 5px 5px 5px !important; border-radius: 3px; margin-right: 15px!important; color:#e2a03f !important" >{{ session('status') }}</strong>
                                @endif
                				
                				            </div>  
							
							<div class="col-lg-3 col-md-4 col-sm-12">
							<label style="font-size: 11px !important;"><b>Fecha</b></label><br>
							<input class="form-control" style="height: 35px; width:100%;" type="text" id="date-range-picker" name="date_range" />
							
							</div>
							
							<div class="col-lg-3 col-md-4 col-sm-12">
							<label style="font-size: 11px !important;"><b>Sucursal</b></label><br>
							<select wire:model="sucursal_id" class="form-control">
							<option value="{{$comercio_id}}">{{auth()->user()->name}}</option>
							
							@foreach($sucursales as $item)
                            <option value="{{$item->sucursal_id}}">{{$item->name}}</option>
                            @endforeach
                        	</select>
						
                            
                            </div>
                            
                            
							</div>
							
							
					
							<text>Detalle para  @if($cod_producto == null) todos los productos @else el producto Cod. {{$cod_producto}} - {{$nombre_producto}} @endif </b> desde {{$from_formateado}} hasta {{$to_formateado}} en <b> {{$nombre_sucursal_elegida}}  </b></text>
							
							@if(0 < strlen($search)) 
							<div class="table-responsive">
								<table class="table">
									<thead style=" background: white !important;  border: 2.2px solid #eee !important;">
										<tr>
											<th style=" background: white !important;  border: 2.2px solid #eee !important;">FECHA</th>
											<th style=" background: white !important;  border: 2.2px solid #eee !important;">DESCRIPCION</th>
											<th style="text-align:center !important; background: white !important;  border: 2.2px solid #eee !important;" class="text-right">CANTIDAD</th>
										
											
										</tr>
									</thead>
									<tbody style="border: solid 2px #eee!important;">

									    @foreach($movimientos as $historico)
										
										<tr>
                        			        
            						    <td class="@if($historico->cantidad_movimiento > 0) text-success @elseif($historico->referencia == 'movimientos' && $historico->cantidad_movimiento < 0) text-danger @else negro @endif">
                                                {{ \Carbon\Carbon::parse($historico->created_at)->format('d/m/Y') }}
                                        </td>
                                          <td class="@if($historico->cantidad_movimiento > 0) text-success @elseif($historico->referencia == 'movimientos' && $historico->cantidad_movimiento < 0) text-danger @else negro @endif">
                                                {{ $historico->tipo_movimiento }}
                                        </td>
                                        <td class="text-center @if($historico->cantidad_movimiento > 0) text-success @elseif($historico->referencia == 'movimientos' && $historico->cantidad_movimiento < 0) text-danger @else negro @endif">
                                                {{ $historico->cantidad_movimiento }}
                                           </td>
                               			</tr>
										@endforeach
									</tbody>
								</table>
							</div>
							@else
							
							
							
							<div class="table-responsive">
								<table class="table">
									<thead style=" background: white !important;  border: 2.2px solid #eee !important;">
										<tr>
											<th style=" background: white !important;  border: 2.2px solid #eee !important;">FECHA</th>
											<th style=" background: white !important;  border: 2.2px solid #eee !important;">PRODUCTO</th>
											<th style=" background: white !important;  border: 2.2px solid #eee !important;">DESCRIPCION</th>
											<th style="text-align:center !important; background: white !important;  border: 2.2px solid #eee !important;" class="text-right">CANTIDAD</th>
										
											
										</tr>
									</thead>
									<tbody style="border: solid 2px #eee!important;">

									    @foreach($movimientos as $historico)
										
										<tr>
                        			        
            						    <td class="@if($historico->cantidad_movimiento > 0) text-success @elseif($historico->cantidad_movimiento < 0) text-danger @else negro @endif">
                                                {{ \Carbon\Carbon::parse($historico->created_at)->format('d/m/Y') }}
                                        </td>
                                       <td class="@if($historico->cantidad_movimiento > 0) text-success @elseif($historico->cantidad_movimiento < 0) text-danger @else negro @endif">
                                                {{ $historico->barcode }} - {{ $historico->name }} 
                                        </td>
                                          <td class="@if($historico->cantidad_movimiento > 0) text-success @elseif($historico->cantidad_movimiento < 0) text-danger @else negro @endif">
                                                {{ $historico->tipo_movimiento }}
                                        </td>
                                        <td class="text-center @if($historico->cantidad_movimiento > 0) text-success @elseif($historico->cantidad_movimiento < 0) text-danger @else negro @endif">
                                                {{ $historico->cantidad_movimiento }}
                                           </td>
                                  
										</tr>
										@endforeach
									</tbody>
								</table>
							</div>
							@endif
						
						</div>
					</div>
				
					<!-- /product list -->
			        @include('livewire.historico-stock.variaciones')
					</div>

<script>
    document.addEventListener('DOMContentLoaded', function(){

		window.livewire.on('abrir-modal-variaciones', msg => {
			$('#Variaciones').modal('show')
		});
		
		window.livewire.on('cerrar-modal-variaciones', msg => {
			$('#Variaciones').modal('hide')
		});
		
    });
</script>
    