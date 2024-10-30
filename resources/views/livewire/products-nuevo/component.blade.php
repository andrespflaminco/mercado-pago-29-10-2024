<div>	


@include('livewire.products-nuevo.form-proveedor')
@include('livewire.products-nuevo.form-imagen')	


@include('livewire.products-nuevo.form-categoria')
@include('livewire.products-nuevo.form-descuento-lote')
@include('livewire.products-nuevo.form-marcas')	


<div style="display:{{$ver_configuracion == 1? 'block' : 'none';}};">
@include('livewire.products-nuevo.configuracion')
</div>

<div style="display:{{ ($agregar == 0) && ($ver_configuracion == 0) ? 'block' : 'none';}};">
	                <div class="page-header">
					<div class="page-title">
							<h4>@if($es_insumo_elegido_url == 1) Insumos @else Productos @endif</h4>
							<h6>Precios</h6>
						</div>
						<div class="page-btn  d-lg-flex d-sm-block">
						    @if(Auth::user()->sucursal != 1 )
						    @if(Auth::user()->profile != "Cajero" )
						    
						    @if($product_section == "ProductsPrecio")
						    <a hidden href="javascript:void(0)" wire:click="ActualizacionModal()" class="btn btn-added" style="background: #212529 !important;"> Actualizacion </a>
						    <button href="javascript:void(0)"  id="openModalButton" class="btn btn-added" style="background: #212529 !important;"> Descuentos  </button>
						    <a hidden href="javascript:void(0)" wire:click="SeleccionEnLoteModal()" class="btn btn-added" style="background: #212529 !important;"> Seleccion en lote </a>
							@endif 
							
						    @can('agregar producto')
							<a href="javascript:void(0)" wire:click="Agregar()" class="btn btn-added"><img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar @if($es_insumo_elegido_url == 1) insumo @else producto @endif</a>
						    @endcan
						    
						    @endif
						    @endif
						</div>
					</div>
					
                    
					<!-- /product list -->
					<div class="card">
					    
					    	
                    <ul class="nav nav-tabs  mb-3">
                               <li class="nav-item">
                        <a class="nav-link @if($product_section == 'Products') active @endif" 
                           @if($es_insumo_elegido_url == 1) 
                               href="https://app.flamincoapp.com.ar/productos?view=Products&tipo=insumo" 
                           @else 
                               href="https://app.flamincoapp.com.ar/productos?view=Products" 
                           @endif>
                            CATALOGO
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link @if($product_section == 'ProductsPrecio') active @endif" 
                           @if($es_insumo_elegido_url == 1) 
                               href="https://app.flamincoapp.com.ar/productos?view=ProductsPrecio&tipo=insumo" 
                           @else 
                               href="https://app.flamincoapp.com.ar/productos?view=ProductsPrecio" 
                           @endif>
                            PRECIOS
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link @if($product_section == 'ProductsStock') active @endif" 
                           @if($es_insumo_elegido_url == 1) 
                               href="https://app.flamincoapp.com.ar/productos?view=ProductsStock&tipo=insumo" 
                           @else 
                               href="https://app.flamincoapp.com.ar/productos?view=ProductsStock" 
                           @endif>
                            STOCK
                        </a>
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
									@if($product_section == "ProductsPrecio")    
									<li>
                                        <a style="padding: 2px 4px; text-align: center; color: #6c757d !important; border: solid 1px #6c757d; border-radius: 4px;" href="javascript:void(0)" class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-columns">
                                                <path d="M12 3h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7m0-18H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h7m0-18v18"></path>
                                            </svg>
                                        </a>
                                        <div class="dropdown-menu">
                                            <div  style="color: #212B36; font-size: 13px; padding: 8px 15px; font-weight: 500;">
                                                <input type="checkbox" wire:model.defer="columns.precio_interno">
                                                <span style="margin-left:10px; margin-top:2px;">Precio de venta a sucursales</span>
                                            </div>
                                    
                                            <div style="color: #212B36; font-size: 13px; padding: 8px 15px; font-weight: 500;">
                                                <input type="checkbox" wire:model.defer="columns.precio_base">
                                                <span style="margin-left:10px; margin-top:2px;">Precio base</span>
                                            </div>
                                    
                                            @foreach($lista_precios as $list)
                                            <div  @if(isset($mapeoListaMuestra[$list->id] ) && $mapeoListaMuestra[$list->id] == 0) hidden  @elseif($list->id == $lista_costo_defecto && !auth()->user()->can('ver costo defecto'))    hidden @endif style="color: #212B36; font-size: 13px; padding: 8px 15px; font-weight: 500;">
                                                <input type="checkbox" wire:model.defer="columns.precio_{{$list->id}}">
                                                <span style="margin-left:10px; margin-top:2px;">{{$list->nombre}}</span>
                                            </div>
                                            @endforeach
                                    
                                            <!-- Botón para aplicar los cambios -->
                                            <div style="color: #212B36; font-size: 13px; padding: 8px 15px; font-weight: 500; float:right;">
                                                <button class="applyBtn btn btn-sm btn-primary"  wire:click="aplicarCambiosColumnas">
                                                    Aplicar
                                                </button>
                                            </div>
                                        </div>
                                    </li>
                                    @endif
                                
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
									
									@if($product_section == "Products")    
									@can('ver configuracion productos')
									<li>
									    <a style="font-size:12px !important; padding:5px !important; background: #F8F9FA !important; color:#212B36 !important; border:solid 1px #212B36 !important; " class="btn btn-cancel" href="javascript:void(0)" wire:click="AbrirModalConfiguracion()">
									 <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
									    Configuracion</a>
									</li>
									@endcan 
									@endif
									
									@if($product_section == "ProductsPrecio")   
									@if(Auth::user()->sucursal != 1 )
                                    
									<li>
									<a style="font-size:12px !important; padding:5px !important; background: #F8F9FA !important; color:#212B36 !important; border:solid 1px #212B36 !important; " class="btn btn-cancel" href="{{ url('historico-precios') }}">
									Historial de actualizaciones de precios
									</a>
									</li>
									
									@endif
									@endif
									
									</ul>


									
									
									
									@endif
						
								</div>
							</div>
							
							<!-- /Filter -->
							@include('common.filtros-productos') 
							<!-- /Filter -->
							
							<div class="table-responsive">
								<table class="table">

								    @if($product_section == "Products")
									@include('livewire.products-nuevo.tabla-headers.header-catalogo')
									@include('livewire.products-nuevo.tabla-body.tabla-catalogo')
									@endif

								    @if($product_section == "ProductsPrecio")
									@include('livewire.products-nuevo.tabla-headers.header-precios')
									@include('livewire.products-nuevo.tabla-body.tabla-precios')
									@endif
									
									@if($product_section == "ProductsStock")
									@include('livewire.products-nuevo.tabla-headers.header-stocks')
									@include('livewire.products-nuevo.tabla-body.tabla-stocks')
									@endif
									
									
								</table>
								<div class="mt-4 mb-4">
								{{$data->links()}}    
								</div>
								
							</div>
						
					
					
					<!-- /product list -->

					
	                @include('livewire.products.form-lista-precios')
	                
	                @include('livewire.products.exportar-stock')
                    @include('livewire.products.exportar-lista')
  
					</div>
					
					</div>    
</div>

<div style="display:{{ ($agregar == 1) && ($ver_configuracion == 0) ? 'block' : 'none';}};">
	@include('livewire.products-nuevo.ficha-producto.agregar-editar-producto')
</div>
	
@include('livewire.products-stock.form-stock')					
</div>

@include('common.script-productos-nuevo') 

@include('common.script-etiquetas') 

