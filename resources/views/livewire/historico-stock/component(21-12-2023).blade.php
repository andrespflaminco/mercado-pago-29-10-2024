<div >	                
	                <div class="page-header">
					<div class="page-title">
							<h4>Historico de movimientos de stock</h4>
							<h6>Muestra entradas y salidas de  <a style="color:black;" href="javascript:void(0)" wire:click="ConciliarStock()">unidades</a></h6>
						</div>
					
					</div>
					
                    
					<!-- /product list -->
					<div class="card">
					   <ul style="margin-left: 15px;" class="nav nav-tabs  mb-0">
                            <li style="background:white; border: solid 1px #eee;" class="nav-item">
                                <a style="{{ $sucursal_id == $comercio_id ? 'color: #e95f2b;' : '' }}" class="nav-link  {{ $sucursal_id == $comercio_id ? 'active' : '' }} " href="javascript:void(0)"  wire:click="ElegirSucursal({{$comercio_id}})"  > {{auth()->user()->name}} </a>
                            </li>
                            @foreach($sucursales as $item)
                            <li style="background:white; border: solid 1px #eee;"  class="nav-item">
                                <a style="{{ $sucursal_id == $item->sucursal_id ? 'color: #e95f2b;' : '' }}" class="nav-link {{ $sucursal_id == $item->sucursal_id ? 'active' : '' }}" href="javascript:void(0)"  wire:click="ElegirSucursal({{$item->sucursal_id}})"  >{{$item->name}}</a>
                            </li>
                            @endforeach
                          </ul>
					
						<div class="card-body">
							<div class="row mb-2">
                			<div class="col-lg-3 col-md-4 col-sm-12">
                								        
                 			    <label style="font-size: 11px !important;"><b>Buscar por nombre o codigo</b></label>
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
							<input style="height: 35px;" type="text" id="date-range-picker" name="date_range" />
							
							</div>
							<div class="col-lg-6 col-md-4 col-sm-12">
							</div>
						    
						    @if($cod_producto != null)
							<div class="col-lg-12 col-md-12 col-sm-12 mt-3 mb-0">
							<div class="card mb-0">
							    <div class="card-body">
							     <h6>Codigo: {{$cod_producto}}</h6>
							     <h6>Producto: {{$nombre_producto}} </h6>
							    </div>
							</div>
							
							</div>
							@endif
							
							<div class="table-responsive mt-3">
								<table class="table">
									<thead style=" background: white !important;  border: 2.2px solid #eee !important;">
										<tr>
											<th style=" background: white !important;  border: 2.2px solid #eee !important;">FECHA</th>
											<th style=" background: white !important;  border: 2.2px solid #eee !important;">DESCRIPCION</th>
											<th style="text-align:center !important; background: white !important;  border: 2.2px solid #eee !important;" class="text-right">CANTIDAD</th>
										
											
										</tr>
									</thead>
									<tbody style="border: solid 2px #eee!important;">
								        
								        @if(isset($stock_inicial))
									    <tr>
            						    
            						    <td style="background:#fafbfe; font-weight:bold;">
                                        {{ \Carbon\Carbon::parse($stock_inicial->created_at)->format('d/m/Y') }}
                                        </td>
                                        
                        			    <td style="background:#fafbfe; font-weight:bold;">
                                        Stock al [{{ \Carbon\Carbon::parse($stock_inicial->created_at)->format('d/m/Y') }}]
                                        </td>
                                        
                        			    <td style="text-align:center !important; padding-right:50px !important; background:#fafbfe; font-weight:bold;'">
                                        {{$stock_inicial->stock - $stock_inicial->cantidad_movimiento}}
                                        </td>
										
										</tr>
										@endif
										
									    @foreach($movimientos as $historico)
										
										<tr>
            						    <td style="{{ $historico->referencia != 'movimientos' ? 'background:#fafbfe; font-weight:bold;': '' }}" class="@if($historico->referencia == 'movimientos' && $historico->cantidad_movimiento > 0) text-success @elseif($historico->referencia == 'movimientos' && $historico->cantidad_movimiento < 0) text-danger @else negro @endif">
                                        
                                            @if($historico->referencia == "movimientos")
                                                {{ \Carbon\Carbon::parse($historico->created_at)->format('d/m/Y') }}
                                            @else
                                                {{ \Carbon\Carbon::parse($historico->created_at)->format('d/m/Y') }}
                                            @endif
                                        </td>
                                        
                        			    <td style="{{ $historico->referencia != 'movimientos' ? 'background:#fafbfe; font-weight:bold;': '' }}" class="@if($historico->referencia == 'movimientos' && $historico->cantidad_movimiento > 0) text-success @elseif($historico->referencia == 'movimientos' && $historico->cantidad_movimiento < 0) text-danger @else negro @endif">
                                            @if($historico->referencia == "movimientos")
                                                {{ $historico->tipo_movimiento }}
                                            @else
                                                {{ $historico->referencia }} al [{{ \Carbon\Carbon::parse($historico->created_at)->format('d/m/Y') }}]
                                            @endif
                                        </td>
                                        
                        			    <td style="text-align:center !important; padding-right:50px !important; {{ $historico->referencia != 'movimientos' ? 'background:#fafbfe; font-weight:bold;': '' }}"  class="text-right @if($historico->referencia == 'movimientos' && $historico->cantidad_movimiento > 0) text-success @elseif($historico->referencia == 'movimientos' && $historico->cantidad_movimiento < 0) text-danger @else negro @endif">
                                            @if($historico->referencia == "movimientos")
                                                {{ $historico->cantidad_movimiento }}
                                            @else
                                                {{ $historico->stock }}
                                            @endif
                                        </td>
										</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						
						</div>
					</div>
				
					<!-- /product list -->
			
			       @if($arraySinDuplicados != null)
			      <table>
                    <thead>
                        <tr>
                            <th>Producto ID</th>
                            <th>Stock</th>
                            <th>Stock Real</th>
                            <th>Diferencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($arraySinDuplicados as $diferencia)
                            <tr>
                                <td>{{ $diferencia['producto_id'] }}</td>
                                <td>{{ $diferencia['stock'] }}</td>
                                <td>{{ $diferencia['stock_real'] }}</td>
                                <td>{{ $diferencia['diferencia'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

			       @endif
					</div>
			