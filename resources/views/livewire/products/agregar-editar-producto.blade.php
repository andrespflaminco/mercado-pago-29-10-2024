
@inject('cart', 'App\Services\CartVariaciones')
<div>
<div class="page-header">

						<div class="page-title"> 
						     
						    @if($forma_edit != 1)
							<h4>{{ $selected_id > 0 ? 'Editar Producto' : 'Agregar Producto' }} </h4>
							<h6>{{ $selected_id > 0 ? 'Modificar el producto' : 'Crear un nuevo producto' }} </h6>
							@else
							<h4>Detalle del producto. </h4>
							@endif
						</div>
					</div>
                    <!-- /add -->
                    <div class="card">
						<div class="card-body">
							<div class="row">
					       		
					       		<div class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label class="d-flex">SKU  <div style="margin-left:3px; cursor:pointer;" class="example-popover" data-container="body" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Es el codigo interno del producto. Usado para busquedas en ventas y compras"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></div></label> 
										<input class="form-control" maxlength="20" wire:model.defer="barcode" {{ $es_sucursal == 1? 'readonly' : '' }} {{ $forma_edit == 1? 'readonly' : '' }} type="text" >
										<p style="color:#637381;">* Maximo 20 caracteres</p> 
										@error('barcode') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
								
								<div class="col-lg-9 col-sm-6 col-12">
									<div class="form-group">
										<label>Nombre del producto</label>
										<input class="form-control" wire:model.defer="name" {{ $es_sucursal == 1? 'readonly' : '' }}  {{ $forma_edit == 1? 'readonly' : '' }} type="text" >
										@error('name') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
								
								<div class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label class="d-flex">Codigo del proveedor  <div style="margin-left:3px; cursor:pointer;" class="example-popover" data-container="body" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Es el codigo del proveedor, usado para busquedas en ventas y compras."><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></div></label> 
										<input class="form-control" maxlength="20" wire:model.defer="cod_proveedor" {{ $es_sucursal == 1? 'readonly' : '' }} {{ $forma_edit == 1? 'readonly' : '' }} type="text" >
										<p style="color:#637381;">* Maximo 20 caracteres</p> 
										@error('cod_proveedor') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
								
								@if(Auth::user()->profile != "Cajero" )
								<div class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label>Proveedor</label>
										<select wire:model.defer='proveedor' {{ $es_sucursal == 1? 'disabled' : '' }} {{ $forma_edit == 1? 'disabled' : '' }} {{1 < $tipo_producto? 'disabled' : '' }}  wire:change.defer='ModalProveedor($event.target.value)'  class="form-control">
    									    <option value="Elegir" disabled >Elegir</option>
                                            <option value="1" >Sin proveedor</option>
                                            @foreach($prov as $pr)
                                            <option value="{{$pr->id}}">{{$pr->nombre}}</option>
                                            @endforeach
                                        	<option value="AGREGAR" style="padding:20px !important;" class="btn btn-dark">+ NUEVO PROVEEDOR</option>
    									</select>
    								    @error('proveedor') <span class="text-danger err">{{ $message }}</span> @enderror
									</div>
								</div>
								@endif
								
								<div class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label>Categoria</label>
										<select wire:model.defer='categoryid' {{ $es_sucursal == 1? 'disabled' : '' }} {{ $forma_edit == 1? 'disabled' : '' }} wire:change.defer='ModalCategoria($event.target.value)' class="form-control">
									        <option value="Elegir" disabled >Elegir</option>
                                            <option value="1" selected >Sin categoria</option>
                                            @foreach($categories as $c)
                                            <option value="{{$c->id}}">{{$c->name}}</option>
                                            @endforeach
                                             <option value="AGREGAR" style="padding:20px !important; " class="btn btn-dark">+ NUEVA CATEGORIA</option>
                                           
										</select>
										@error('categoryid') <span class="text-danger err">{{ $message }}</span> @enderror
									</div>
								</div>
								<div class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label>Marcas</label>
										<select wire:model.defer='marca_id' {{ $es_sucursal == 1? 'disabled' : '' }} {{ $forma_edit == 1? 'disabled' : '' }} wire:change.defer='ModalMarca($event.target.value)' class="form-control">
									        <option value="1" selected >Sin asignar</option>
                                            @foreach($marcas as $m)
                                            <option value="{{$m->id}}">{{$m->name}}</option>
                                            @endforeach
                                             <option value="AGREGAR" style="padding:20px !important; " class="btn btn-dark">+ NUEVA MARCA</option>
                                           
										</select>
										@error('marca_id') <span class="text-danger err">{{ $message }}</span> @enderror
									</div>
								</div>

								
																								
									<div class="col-lg-3 col-sm-6 col-12">
									
									<div class="form-group">
									<label>Etiquetas</label>
									
									<div wire:ignore>
                                    <select id="select2-etiquetas" class="form-control tagging" multiple="multiple">
                                    </select>
                                </div>
                                
                          
								</div>
								</div>
								
								
								@if(Auth::user()->profile == "Cajero" )
								<div class="col-lg-3 col-sm-6 col-12">
								</div>
								@endif
						

								<div class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label class="d-flex">Maneja stock?  <div style="margin-left:3px; cursor:pointer;" class="example-popover" data-container="body" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Opcion que limita las ventas a la existencia de stock. Si la respuesta es Si, cuando el stock sea cero, no se podra vender mas. Si es No se podra vender aun cuando el stock sea negativo. "><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></div></label> 
							
									    <select wire:model.defer='stock_descubierto' {{ $es_sucursal == 1? 'disabled' : '' }} {{ $forma_edit == 1? 'disabled' : '' }} class="form-control">
                                          <option value="Elegir" disabled >Elegir</option>
                                          <option value="si" > Activo </option>
                                          <option value="no" > Inactivo </option>
                                    
                                        </select>
                                        @error('stock_descubierto') <span class="text-danger err">{{ $message }}</span> @enderror
    								</div>
								</div>
								<div class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label class="d-flex">Inventario minimo  <div style="margin-left:3px; cursor:pointer;" class="example-popover" data-container="body" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Nivel de stock al cual nos alerta el sistema que nos estamos quedando sin existencias"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></div></label> 
							            <input type="number" wire:model.defer='alerts' {{ $es_sucursal == 1? 'readonly' : '' }} {{ $forma_edit == 1? 'readonly' : '' }} class="form-control">
                                        @error('alerts') <span class="text-danger err">{{ $message }}</span> @enderror
									</div>
								</div>
								<div class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
									<label>Tipo de producto</label>
									<select class="form-control" wire:model="producto_tipo" {{$selected_id > 0 ? 'disabled' : '' }}  {{ $es_sucursal == 1? 'disabled' : '' }} {{ $forma_edit == 1? 'disabled' : '' }} wire:change="ProductoTipo()">
                                     <option value="Elegir">Elegir</option>
                                     <option value="s">Producto Simple</option>
                                     <option value="v">Producto Variable</option>
                                    </select>
                                    @error('producto_tipo') <span class="text-danger err">{{ $message }}</span> @enderror
                                    @if ($mostrarErrorTipoProducto)
                                    <span class="text-danger err">Debe elegir el tipo de producto</span>
                                    @endif
									</div>
								</div>
								<div class="col-sm-12 col-md-12">
								
								@include('livewire.products.unidades-medida')
								
								@include('livewire.products.iva')
								
                            
                                    <label>Canal de ventas</label>
                                    
                                   <div style="margin: 0; display:flex; border: solid 1px #bfc9d4;  border: solid 1px #bfc9d4;  border-radius: 6px;   padding: 12px 3px 2px 14px;">
                                    
                                    <input {{ $es_sucursal == 1? 'disabled' : '' }}  {{ $forma_edit == 1? 'disabled' : '' }} style="margin-right: 10px; margin-bottom:10px;" type="checkbox" wire:model.defer="ecommerce_canal" checked> <label for="">Tienda online</label>

                                     @if($wc_yes != 0)
                                     <input {{ $es_sucursal == 1? 'disabled' : '' }} {{ $forma_edit == 1? 'disabled' : '' }} style="margin-right: 10px; margin-left: 10px; margin-bottom:10px;" type="checkbox" wire:model.defer="wc_canal" checked> <label for="">Wocommerce</label>
                                     @endif
                    
                                       <input {{ $es_sucursal == 1? 'disabled' : '' }} {{ $forma_edit == 1? 'disabled' : '' }} style="margin-right: 10px; margin-left: 10px; margin-bottom:10px;" type="checkbox" wire:model.defer="mostrador_canal" checked> <label for="">Mostrador</label>
                    
                    
                    
                                    </div>
                                   <br>
                    
                    
                               </div>								

								
								
								
								<!---- Si el producto es simple -----> 
								@if($producto_tipo == "s")
                                
                                	@include('livewire.products.ficha-producto.producto-simple')
                                	
                                @endif
                                <!---- / Si el producto es simple -----> 
                                
                                
                                <!---- Si es un producto variable ----->
                                @if($producto_tipo == "v")
								
                            	@include('livewire.products.ficha-producto.producto-variable')
                            	
                                @endif
                                <!---- / Si es un producto variable ----->
		
		                        @include('livewire.products.input-imagenes')

                            
								<div class="col-lg-12">
								    
								    @if($forma_edit == 1)
									<a href="javascript:void(0);" wire:click.prevent="ResetAgregar()" class="btn btn-cancel">VOLVER</a>
								    @else
								    
								     @if($producto_tipo == "Elegir")
								      <button wire:click="ElegirTipoProducto()" class="btn btn-submit me-2">GUARDAR</button>
								      <a href="javascript:void(0);" wire:click.prevent="ResetAgregar()" class="btn btn-cancel">CANCELAR</a>
								     @endif
								    <!------- si es producto simple ---->
								    @if($producto_tipo == "s")
									<a href="javascript:void(0);" wire:click.prevent="ResetAgregar()" class="btn btn-cancel">CANCELAR</a>
								    
								    @if($selected_id < 1)
								     <a wire:click.prevent="Store()"  href="javascript:void(0);" class="btn btn-submit me-2">GUARDAR</a>
								     @else
								     <a wire:click.prevent="Update()"  href="javascript:void(0);" class="btn btn-submit me-2">ACTUALIZAR</a>
                                     @endif
									
									@endif
									<!------ si es producto variable ----->
									
									@if($producto_tipo == "v")
									
									
									@if (0 < $cart->getContent()->count())
									<a href="javascript:void(0);" wire:click.prevent="ResetAgregar()" class="btn btn-cancel">CANCELAR</a>
									@if($selected_id < 1)
								     <a wire:click.prevent="Store()"  href="javascript:void(0);" class="btn btn-submit me-2">GUARDAR</a>
								     @else
								     <a wire:click.prevent="Update()"  href="javascript:void(0);" class="btn btn-submit me-2">ACTUALIZAR</a>
                                     @endif
									@else
								
									<a href="javascript:void(0);" wire:click.prevent="ResetAgregar()" class="btn btn-cancel">CANCELAR</a>
									@if($selected_id < 1)
								     <button wire:click="FaltaVariacion()" class="btn btn-submit me-2">GUARDAR</button>
								     @else
								     <button wire:click="FaltaVariacion()"  class="btn btn-submit me-2">ACTUALIZAR</button>
                                     @endif
								
									@endif
									@endif
									@endif
								
								</div>
							</div>
								    
								</div>
							    
							   
						</div>
<!-- /add -->
</div>