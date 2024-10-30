<div >	                
	                <div class="page-header">
					<div class="page-title">
							<h4>Historico de movimientos de stock</h4>
							<h6>Muestra entradas y salidas de unidades</h6>
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
							<div class="table-top">
								<div class="search-set">
									<div class="search-path">
										<a class="btn btn-filter" id="filter_search">
											<img src="{{ asset('assets/pos/img/icons/filter.svg') }}"  alt="img">
											<span><img src="{{ asset('assets/pos/img/icons/closes.svg') }}" alt="img"></span>
										</a>
									</div>
									<input type="text" autocomplete="off" wire:model="search" placeholder="Buscar.." class="form-control"	>
									<div hidden class="search-input">
										<a class="btn btn-searchset"><img src="{{ asset('assets/pos/img/icons/search-white.svg') }}" alt="img"></a>
									</div>
									
								</div>
								<div class="search-set">
								    <select wire:model.defer='id_almacen' class="form-control">
									<option value="Elegir" disabled >Elegir</option>
                                	<option value="0" >Todos</option>
                                	@foreach($tipo_movimiento as $t)
                                	<option value="{{$t->id}}">{{$t->nombre}}</option>
                                	@endforeach
								</select>
								</div>
								<div>
								    
								</div>
							
														
								<div class="wordset">
									<ul>
										<li>
											<a data-bs-toggle="tooltip" data-bs-placement="top" title="pdf"><img  src="{{ asset('assets/pos/img/icons/pdf.svg') }}"  alt="img"></a>
										</li>
										<li>
											<a data-bs-toggle="tooltip" data-bs-placement="top" title="excel"><img  src="{{ asset('assets/pos/img/icons/excel.svg') }}" alt="img"></a>
										</li>
										<li>
											<a data-bs-toggle="tooltip" data-bs-placement="top" title="print"><img  src="{{ asset('assets/pos/img/icons/printer.svg') }}" alt="img"></a>
										</li>
									</ul>
								</div>
							</div>
							
							
							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
										
											<th>Nombre del producto</th>
											<th>SKU</th>
											<th>Tipo de movimiento </th>
											<th>Cantidad movida</th>
											<th>Stock nuevo</th>
											<th>Fecha y hora</th>
										</tr>
									</thead>
									<tbody>
									    	@foreach($data as $historico)
										<tr>
											<td>
											{{$historico->name}}
        									@foreach($productos_variaciones as $pv)
        								
        									@if($historico->referencia_variacion == $pv->referencia_variacion)
        									
        									@foreach($variaciones as $v)
        									
        									@if($v->id == $pv->variacion_id)
        									{{$historico->referencia_variacion}}
        									@endif
        									
        									@endforeach
        								
        									@endif
        								
        									@endforeach
        									
											</td>

											<td>{{$historico->barcode}}</td>
											<td>
											@if($historico->sale_id != null)
            								<a class="btn" wire:click.prevent="getDetails({{$historico->sale_id}})">{{$historico->tipo_movimiento}} Nro {{$historico->sale_id}}</a>
            								@else
            								{{$historico->tipo_movimiento}}
            								@endif
											</td>
											<td>{{$historico->cantidad_movimiento}}</td>
											<td>{{$historico->stock}}</td>
											<td>
											    {{\Carbon\Carbon::parse($historico->created_at)->format('d-m-Y H:i')}}
											</td>
										</tr>
										@endforeach
									</tbody>
								</table>
								{{$data->links()}}
							</div>
						</div>
					</div>
				
					<!-- /product list -->
			
					</div>
			