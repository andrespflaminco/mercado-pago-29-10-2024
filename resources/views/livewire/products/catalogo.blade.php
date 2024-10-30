 
	                <div class="page-header">
					<div class="page-title">
							<h4>@if($es_insumo_elegido_url == 1) Insumos @else Productos @endif @if(Auth::user()->id == 506) <button wire:click="exportProductsTest">.</button>  @endif</h4>
							<h6>Catalogo</h6>
						</div>
						<div class="page-btn">
						    @if(Auth::user()->sucursal != 1 )
						    @if(Auth::user()->profile != "Cajero" )
						    
						    @can('agregar producto')
						    <!----- Boton que muestra cuando pasas por arriba un mensaje ----->
						    <div hidden class="popover-list">
						    <button class="example-popover btn btn-primary" type="button" data-container="body" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Popover title">Hover Me</button>
						    </div>
						
						    <!----- / Boton que muestra cuando pasas por arriba un mensaje ----->
						
							<a href="javascript:void(0)" wire:click="Agregar()" class="btn btn-added"><img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar @if($es_insumo_elegido_url == 1) Insumo @else Producto @endif</a>
						    
						    @endcan 
						    
						    @endif
						    @endif
						</div>
					</div>
					
                    
					<!-- /product list -->
					<div class="card">
					    <ul class="nav nav-tabs  mb-3">
            				<li class="nav-item">
            						<a class="nav-link active" @if($es_insumo_elegido_url == 1) href="https://testing.flamincoapp.com.ar/products?tipo=insumo" @else href="{{url('products')}}" @endif  > CATALOGO  </a>
            				</li>
            				<li class="nav-item">
            						<a class="nav-link " @if($es_insumo_elegido_url == 1) href="https://testing.flamincoapp.com.ar/products-precios?tipo=insumo" @else href="{{url('products-precios')}}" @endif  > PRECIOS </a>
            				</li>
            				<li class="nav-item">
            						<a class="nav-link" @if($es_insumo_elegido_url == 1) href="https://testing.flamincoapp.com.ar/products-stock?tipo=insumo" @else href="{{url('products-stock')}}" @endif  > STOCK </a>
            				</li>
            			</ul>
					
						<div class="card-body">
							<div class="table-top">
								<div class="search-set">
									<div class="search-path">
										@include('common.boton-filtros')
									</div>
									<input type="text" autocomplete="off" wire:model="search" placeholder="Buscar.." class="form-control"	>
									<div hidden class="search-input">
										<a class="btn btn-searchset"><img src="{{ asset('assets/pos/img/icons/search-white.svg') }}" alt="img"></a>
									</div>
								</div>
								<div class="wordset">
								    @if(Auth::user()->profile != "Cajero" )
								    
								    
									<ul>

										
									@can('exportar catalogo')     
										<li>
									
											<a style="font-size:12px !important; padding:5px !important; background: #198754 !important;" class="btn btn-cancel" wire:click="ExportarCatalogo()"  data-bs-placement="top" title="exportar excel"> 
											<svg style="margin-right: 5px;"  xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
											Exportar </a>
										</li>
									
									@endcan 
									
									
									@if(Auth::user()->sucursal != 1 )
									
									@can('importar catalogo')    
									<li>
											<a style="font-size:12px !important; padding:5px !important;" class="btn btn-cancel" href="{{ url('import') }}" data-bs-toggle="tooltip" data-bs-placement="top" title="importar"> 
									<svg  style="margin-right: 5px;" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-upload"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>		
											Importar</a>
									</li>
									@endif
									
									@endcan
									
									<li>
									<a style="font-size:12px !important; padding:5px !important; background: #F8F9FA !important; color:#212B36 !important; border:solid 1px #212B36 !important; " class="btn btn-cancel" href="javascript:void(0)" wire:click="AbrirModalConfiguracion()">
									 <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
									    Configuracion</a>
									</li>	
									
									</ul>
									
									
									
									@endif
								</div>
							</div>
							
							<!-- /Filter -->
							@include('common.filtros-productos') 
							<!-- /Filter -->
							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
											<th>
											    
											    @if(Auth::user()->profile != "Cajero" )
												
												@can('accion en lote productos')    
												<label class="checkboxs">
											    <input name="Todos" type="checkbox" value="1" onclick="CheckTodosLote()" class="check_todos"/>    
                            					<span class="checkmarks"></span>
											    </label>
												@endif
												@endcan
												
											</th>
											<th>Nombre del producto</th>
											<th>SKU</th>
											<th>Categoria </th>
											<th>Almacen</th>
											@if(Auth::user()->profile != "Cajero" )
											@can('ver proveedores en catalogo')   
											<th>Proveedor</th>
											@endcan
											@endif
											<th>Acciones</th>
										</tr>
									</thead>
									<tbody>
									    @foreach($data as $product)
										<tr>
											<td>
											    @can('accion en lote productos')  
												<label class="checkboxs">
												    <input type="checkbox" wire:model.defer="id_check" tu-attr-id="{{($product->id)}}"  class="mis-checkboxes" value="{{$product->id}}">
													<span class="checkmarks"></span>
												</label>
												@endcan
											</td>
											<td class="productimgname">
												<a href="javascript:void(0);" class="product-img">
												    @if($product->image != null)
            										<img src="{{ asset('storage/products/' . $product->image ) }}" alt="{{$product->name}}" height="70" width="80" class="rounded">
            										@else
            										
            										@if($product->wc_image_url)
            										<img src="{{ $product->wc_image_url }}" alt="{{$product->name}}" height="70" width="80" class="rounded">
            										@else
            										<img src="{{ asset('storage/products/noimg.png') }}" alt="{{$product->name}}" height="70" width="80" class="rounded">
            										@endif
            										
            										@endif
												
												</a>
												<a href="javascript:void(0);" wire:click.prevent="Ver({{$product->id}})">{{$product->name}}</a>
											</td>
											<td>{{$product->barcode}}</td>
											<td>{{$product->category}}</td>
											<td>{{$product->almacen}}</td>
											@if(Auth::user()->profile != "Cajero" )
										    @can('ver proveedores en catalogo')  
											<td>{{$product->nombre_proveedor}}</td>
											@endcan
											@endif
											<td>
												<a wire:click.prevent="Ver({{$product->id}})" class="me-3" href="javascript:void(0)">
													<img src="{{ asset('assets/pos/img/icons/eye.svg') }}" alt="img">
												</a>
											
												@if(Auth::user()->profile != "Cajero" )
												
												@can('editar productos')  
												<a class="me-3" href="javascript:void(0)" wire:click.prevent="Edit({{$product->id}})" >
													<img src="{{ asset('assets/pos/img/icons/edit.svg') }}"  alt="img">
												</a>
												@endcan
												
												@can('eliminar productos')  
												<a href="javascript:void(0)" onclick="ConfirmEliminarProducto('{{$product->id}}')"  >
													<img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="img">
												</a>
												@endcan
												
												@endif
												
											</td>
										</tr>
										@endforeach
									</tbody>
								</table>
							
							</div>
							<br>
							{{$data->links()}}
						</div>
					</div>