
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
										<label class="d-flex">SKU (codigo interno) <div style="margin-left:3px; cursor:pointer;" class="example-popover" data-container="body" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Es el codigo del producto"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></div></label> 
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
										<label class="d-flex">Codigo proveedor  <div style="margin-left:3px; cursor:pointer;" class="example-popover" data-container="body" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Es el codigo del producto"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></div></label> 
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
										<label>Marca </label>
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
							            <input type="text" wire:model.defer='alerts' {{ $es_sucursal == 1? 'readonly' : '' }} {{ $forma_edit == 1? 'readonly' : '' }} class="form-control">
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
								    
								@include('livewire.products.iva')
								
								@include('livewire.products.unidades-medida')
							

                            
                                <div class="form-group">
                                    <label>Canal de ventas</label>
                                    
                                   <div style="display:flex; border: solid 1px #bfc9d4;  border: solid 1px #bfc9d4;  border-radius: 6px;   padding: 12px 3px 2px 14px;">
                                    
                                    <input {{ $es_sucursal == 1? 'disabled' : '' }}  {{ $forma_edit == 1? 'disabled' : '' }} style="margin-right: 10px; margin-bottom:10px;" type="checkbox" wire:model.defer="ecommerce_canal" checked> <label for="">Tienda online</label>

                                     @if($wc_yes != 0)
                                     <input {{ $es_sucursal == 1? 'disabled' : '' }} {{ $forma_edit == 1? 'disabled' : '' }} style="margin-right: 10px; margin-left: 10px; margin-bottom:10px;" type="checkbox" wire:model.defer="wc_canal" checked> <label for="">Wocommerce</label>
                                     @endif
                    
                                       <input {{ $es_sucursal == 1? 'disabled' : '' }} {{ $forma_edit == 1? 'disabled' : '' }} style="margin-right: 10px; margin-left: 10px; margin-bottom:10px;" type="checkbox" wire:model.defer="mostrador_canal" checked> <label for="">Mostrador</label>
                    
                    
                    
                                    </div>
                    
                    
                    
                               </div>
                               </div>								

								
								
								
								<!---- Si el producto es simple -----> 
								@if($producto_tipo == "s")
								
								<div class="row">
								 @if(Auth::user()->sucursal != 1 )
								 @if(Auth::user()->profile != "Cajero" )
								<div class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label>Costo</label>
                                        <input @if($configuracion_precio_interno == 1) wire:change="ModificarPrecioInterno(0)" wire:keydown.enter="ModificarPrecioInterno(0)" @endif {{ $es_sucursal == 1? 'readonly' : '' }} {{ $forma_edit == 1? 'readonly' : '' }} type="number" wire:model.lazy="cost" class="form-control" {{1 < $tipo_producto ? 'disabled' : '' }} >
									</div>
								</div>
								@endif
								@endif
							
							    @if(Auth::user()->profile != "Cajero" )
								@if(0 < $sucursales->count())
								<div   class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label class="d-flex">Precio interno <div style="margin-left:3px; cursor:pointer;" class="example-popover" data-container="body" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Es el precio de venta a las sucursales"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></div> </label>
									    <input {{ $es_sucursal == 1? 'readonly' : '' }} {{ $configuracion_precio_interno == 1? 'readonly' : '' }} {{ $forma_edit == 1? 'readonly' : '' }} type="number" wire:model.lazy="precio_interno" class="form-control" >
									@if($configuracion_precio_interno == 1) <p style="color:green;">El precio interno es igual al costo</p> @endif
									</div>
								</div>
								@endif
								@endif
								
								<div class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label class="d-flex">Precio de venta <div style="margin-left:3px; cursor:pointer;" class="example-popover" data-container="body" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Es el precio de venta al consumidor final (cliente final)"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></div> </label>
										<input {{ $es_sucursal == 1? 'readonly' : '' }} {{ $forma_edit == 1? 'readonly' : '' }} type="number" required wire:model.lazy="precio_lista.0|0|0|0" class="form-control" placeholder="ej: 0.00" >
                                        @error('precio_lista') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
								@if(Auth::user()->sucursal == 1 )
								<div class="col-lg-3 col-sm-6 col-12">
								</div>
								@endif
								@if($sucursales->count() == 0)
								<div class="col-lg-3 col-sm-6 col-12">
								</div>
								@endif
								@if(Auth::user()->profile == "Cajero" )
								<div class="col-lg-3 col-sm-6 col-12">
								</div>
								@endif
								
								
								<div class="col-lg-3 col-sm-6 col-12">
								</div>
								
								@if($lista_precios != null)
								
								@foreach ($lista_precios as $key => $lp)
								<div class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label>Precio {{$lp->nombre}}</label>
                                        <input {{ $es_sucursal == 1? 'readonly' : '' }} {{ $forma_edit == 1? 'readonly' : '' }} type="number" required class="form-control" wire:model="precio_lista.0|{{ $lp->id }}|0|0" />
                                        @error('precio_lista') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
								
								@endforeach
								@endif  
								<br>
								</div>
								
								<br>
								<br>
								<div class="row">
								<div class="col-lg-12 col-sm-12 col-12">
								<div class="form-group">
                                								
                                <div class="table-responsive">
                                    <label></label>
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Sucursal</th>
                                                <th>Almacen</th>
                                                <th>Stock real <div style="margin-left:3px; cursor:pointer;" class="example-popover" data-container="body" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Stock real a encontrar en un recuento fisico"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></div></th>
                                                <th>Stock disponible  <div style="margin-left:3px; cursor:pointer;" class="example-popover" data-container="body" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Cantidad de unidades disponibles para vender. Es el stock real, menos las unidades ya vendidas pero aun no entregadas"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></div> </th>
                                                <th>Stock comprometido <div style="margin-left:3px; cursor:pointer;" class="example-popover" data-container="body" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Es el stock aun en nuestro poder, pero que ya esta vendido, esperando por ser entregado."><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></div>  </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(auth()->user()->sucursal != 1) 
                                            <tr>
                                                <td>{{auth()->user()->name}}</td>
                                                <td>
                                                    <select wire:model.defer='almacen_id.0|0|0|0' {{ $forma_edit == 1? 'disabled' : '' }} class="form-control">
                                                        <option value="1" >Sin almacen</option>
                                                        @foreach($almacenes as $a)
                                                            <option value="{{$a->id}}">{{$a->nombre}}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="number" step="{{ $tipo_unidad_medida == 1 ? '0.001' : '1' }}" wire:model="real_stock_sucursal.0|0|0|0" {{ $forma_edit == 1 ? 'readonly' : '' }} required wire:change="CambiarStockDisponible('0|0|0|0')" class="form-control">
                                                        @if($tipo_unidad_medida == 1)
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Kg </span>
                                                            </div>
                                                        @endif
                                                        @if($tipo_unidad_medida == 9)
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">Unid</span>
                                                            </div>
                                                        @endif

                                                    </div>
                                                    @error('real_stock_sucursal') <span class="text-danger er">{{ $message }}</span> @enderror
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="number" step="{{ $tipo_unidad_medida == 1 ? '0.001' : '1' }}" readonly class="form-control" wire:model="stock_sucursal.0|0|0|0">
                                                        @if($tipo_unidad_medida == 1)
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Kg</span>
                                                            </div>
                                                        @endif
                                                        @if($tipo_unidad_medida == 9)
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">Unid</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="number" step="{{ $tipo_unidad_medida == 1 ? '0.001' : '1' }}" readonly class="form-control" wire:model="stock_sucursal_comprometido.0|0|0|0">
                                                        @if($tipo_unidad_medida == 9)
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">Unid</span>
                                                            </div>
                                                        @endif
                                                        @if($tipo_unidad_medida == 1)
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Kg</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @endif
                                            
                                            @foreach ($sucursales as $llave => $sucu)
                                            <tr>
                                                <td>{{$sucu->name}}</td>
                                                <td>
                                                    <select wire:model.defer='almacen_id.0|{{ $sucu->sucursal_id }}|0|0' {{ $forma_edit == 1? 'disabled' : '' }} class="form-control">
                                                        <option value="1" >Sin almacen</option>
                                                        @foreach($almacenes as $a)
                                                            <option value="{{$a->id}}">{{$a->nombre}}</option>
                                                        @endforeach
                                                    </select>    
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="number" step="{{ $tipo_unidad_medida == 1 ? '0.001' : '1' }}" {{ ($es_sucursal == 1) && (auth()->user()->id != $sucu->sucursal_id ) ? 'readonly' : '' }}  {{ $forma_edit == 1 ? 'readonly' : '' }} required class="form-control" wire:change="CambiarStockDisponible('0|{{ $sucu->sucursal_id }}|0|0')" wire:model="real_stock_sucursal.0|{{ $sucu->sucursal_id }}|0|0" />
                                                        @if($tipo_unidad_medida == 9)
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">Unid</span>
                                                            </div>
                                                        @endif
                                                        @if($tipo_unidad_medida == 1)
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Kg</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    @error('real_stock_sucursal') <span class="text-danger er">{{ $message }}</span> @enderror
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="number" step="{{ $tipo_unidad_medida == 1 ? '0.001' : '1' }}" readonly class="form-control" wire:model="stock_sucursal.0|{{ $sucu->sucursal_id }}|0|0">
                                                        @if($tipo_unidad_medida == 9)
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">Unid</span>
                                                            </div>
                                                        @endif
                                                        @if($tipo_unidad_medida == 1)
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Kg</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="number" step="{{ $tipo_unidad_medida == 1 ? '0.001' : '1' }}" readonly class="form-control" wire:model="stock_sucursal_comprometido.0|{{ $sucu->sucursal_id }}|0|0">
                                                        @if($tipo_unidad_medida == 9)
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">Unid</span>
                                                            </div>
                                                        @endif
                                                        @if($tipo_unidad_medida == 1)
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Kg</span>
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
                                </div>
                                </div>
                                
                                @endif
                                <!---- / Si el producto es simple -----> 
                                
                                
                                <!---- Si es un producto variable ----->
                                @if($producto_tipo == "v")
								
                            								
                               <div class="col-sm-12 col-md-12">
                                <div class="form-group">
                                  <label>Variaciones </label>
                            
                                   <div style="border: solid 1px #bfc9d4;  border: solid 1px #bfc9d4;  border-radius: 6px;   padding: 12px ;">
                            
                                     <div class="row">
                                        @foreach($atributos_var as $a)
                                       <div class="col-3">
                                         <select {{ $forma_edit == 1? 'disabled' : '' }} class="form-control" wire:model="variacion_atributo.{{ $a->id }}">
                                            <option value="c"> Cualquier {{$a->nombre}}</option>
                            
                                           @foreach($variaciones as $v)
                                            @if($a->id == $v->atributo_id)
                                                <option value="{{$v->id}}">{{$v->nombre}}</option>
                                            @endif
                                           @endforeach
                                         </select>
                                       </div>
                                       
                                       @endforeach
                                        @if ($mostrarErrorVariacion)
                                            <div style="color:red; font-weight:bold;">
                                                Debe agregar alguna variacion de alguno de los atributos.
                                            </div>
                                        @endif
                                       @if(count($variaciones) < 1)
                                       <p style="margin-left:10px;" class="text-danger">Debe agregar variaciones para asociarlas al producto</p>
                                       @else
                                        <div class="mt-2 col-12">
                                       <button type="button" class="btn btn-dark"  wire:click="GuardarVariacion">+ Agregar
                                       </button>     
                                        </div>
                                       
                                       @endif
                            
                                     </div>
                            
                                    </div>
                            
                            
                               </div>
                                @error('stock_sucursal') <span class="text-danger er">{{ $message }}</span> @enderror
                                @error('precio_lista') <span class="text-danger er">{{ $message }}</span> @enderror
                          
                                <?php //debug {{$testGuardarReferernciaID}} ?>
                            
                            
                               </div>
                               
                                @if ($cart->getContent()->count() > 0)
                                <?php $i = 1; ?>
                                <div class="col-12">
                                @foreach ($cart->getContent() as $key => $variaciones)
								
								
								<div class="form-group"   style="border: solid 1px #bfc9d4;  border: solid 1px #bfc9d4;  border-radius: 6px;">
                                
                                <!------ Titulo de la variacion ------->
                                
                                <div style="background: #ebedf2; color: #3b3f5c;  border: none; border-radius: 4px;">
                                        <div style="padding:12px;">
                                        <b>
                                          {{$variaciones['var_nombre']}}
                                         </b>
                                          <button type="button" style="float:right;"  onclick="ConfirmVariacion('{{$variaciones['referencia_id']}}')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                          </button>
                                         <button type="button" style="float:right; display:flex;" onclick="mostrarOcultarDiv({{$variaciones['referencia_id']}});">
                                           <svg  id="123-{{$variaciones['referencia_id']}}" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                        </button>
                                        </div>
                                </div>
                                
                                <!------/ Caja titulo de la variacion ------->
								<div class="col-12" id="{{$variaciones['referencia_id']}}">
								<div class="row" style="padding:12px;">
								
								<div class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label>SKU variacion</label>
                                         <input maxlength="11" required {{ $es_sucursal == 1? 'readonly' : '' }} {{ $forma_edit == 1? 'readonly' : '' }} type="text" wire:model.lazy="cod_variacion.{{$variaciones['referencia_id']}}" class="form-control">
                                        <p style="color:#637381;">* Maximo 11 caracteres</p> 
                                        @error('cod_variacion.' . $variaciones['referencia_id'] ) <span class="text-danger er">{{ $message }}</span> @enderror 
									</div>
								</div>
								@if($es_sucursal != 1)
								<div class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label>Costo</label>
                                        <input @if($configuracion_precio_interno == 1) wire:change="ModificarPrecioInterno('{{$variaciones['referencia_id']}}')"  wire:keydown.enter="ModificarPrecioInterno('{{$variaciones['referencia_id']}}')" @endif wire:model.lazy="costos_variacion.{{$variaciones['referencia_id']}}" required {{ $es_sucursal == 1? 'readonly' : '' }} {{ $forma_edit == 1? 'readonly' : '' }} type="number"  {{1 < $tipo_producto ? 'readonly' : '' }}  class="form-control" placeholder="ej: 0.00" >
                                        @error('costos_variacion') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
								@endif
								
								
								@if(Auth::user()->profile != "Cajero" )
								@if(0 < $sucursales->count())
								<div class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label>Precio interno</label>
									     <input required  {{ $configuracion_precio_interno == 1? 'readonly' : '' }} {{ $es_sucursal == 1? 'readonly' : '' }} {{ $forma_edit == 1? 'readonly' : '' }} type="number" wire:model.lazy="precios_internos_variacion.{{$variaciones['referencia_id']}}"  class="form-control" placeholder="ej: 0.00" >
                                         @error('precios_internos_variacion') <span class="text-danger er">{{ $message }}</span> @enderror
									
                                            @if($configuracion_precio_interno == 1) <p style="color:green;">El precio interno es igual al costo</p> @endif

									</div>
								</div>
								@endif
								@endif
								
								<div class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label>Precio base</label>
										<input required {{ $es_sucursal == 1? 'readonly' : '' }} {{ $forma_edit == 1? 'readonly' : '' }} type="number" wire:model.lazy="precio_lista.{{$variaciones['referencia_id']}}|0" class="form-control" placeholder="ej: 0.00" >
                                        @error('precio_lista') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
								@if($es_sucursal == 1)
								<div class="col-lg-3 col-sm-6 col-12">
								</div>
								@endif
								@if($sucursales->count() == 0)
								<div class="col-lg-3 col-sm-6 col-12">
								</div>
								@endif
								
								@if(Auth::user()->profile == "Cajero" )
								<div class="col-lg-3 col-sm-6 col-12">
								</div>
								@endif
							
								@foreach ($lista_precios as $key => $lp)
								<div class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label>Precio {{$lp->nombre}}</label>
                                        <input required {{ $es_sucursal == 1? 'readonly' : '' }} {{ $forma_edit == 1? 'readonly' : '' }} type="number" class="form-control" wire:model="precio_lista.{{$variaciones['referencia_id']}}|{{ $lp->id }}" />
									</div>
								</div>
								
								
								@endforeach
							
								<br>
								
								</div>
								<br>
								<br>
								<div class="row" style="padding:12px;">
								<div class="col-lg-12 col-sm-12 col-12">
								<div class="form-group">
								
								<div class="table-responsive">
								    <label></label>
										<table class="table table-hover mb-0">
											<thead>
												<tr>
													<th>Sucursal</th>
													<th>Almacen</th>
													<th>Stock real <div style="margin-left:3px; cursor:pointer;" class="example-popover" data-container="body" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Stock real a encontrar en un recuento fisico"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></div></th>
													<th>Stock disponible  <div style="margin-left:3px; cursor:pointer;" class="example-popover" data-container="body" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Cantidad de unidades disponibles para vender. Es el stock real, menos las unidades ya vendidas pero aun no entregadas"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></div> </th>
													<th>Stock comprometido <div style="margin-left:3px; cursor:pointer;" class="example-popover" data-container="body" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Es el stock aun en nuestro poder, pero que ya esta vendido, esperando por ser entregado."><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></div>  </th>
                                            	</tr>
											</thead>
											<tbody>
											    @if(auth()->user()->sucursal != 1) 
												<tr>
													<td>{{auth()->user()->name}}</td>
													<td>
													<select wire:model.lazy="almacen_id.{{$variaciones['referencia_id']}}|0|{{$variaciones['var_nombre'] ?? 0}}|{{$variaciones['var_id'] ?? 0}}"  {{ $forma_edit == 1? 'disabled' : '' }} class="form-control">
            								        <option value="1" >Sin almacen</option>
                                                    @foreach($almacenes as $a)
                                                    <option value="{{$a->id}}">{{$a->nombre}}</option>
                                                    @endforeach
    								            	</select>     
													</td>
													<td> 
													<input type="number" required {{ $forma_edit == 1? 'readonly' : '' }} wire:change="CambiarStockDisponible('{{$variaciones['referencia_id']}}|0|{{$variaciones['var_nombre'] ?? 0}}|{{$variaciones['var_id'] ?? 0}}')" wire:model.lazy="real_stock_sucursal.{{$variaciones['referencia_id']}}|0|{{$variaciones['var_nombre'] ?? 0}}|{{$variaciones['var_id'] ?? 0}}" class="form-control" placeholder="ej: 0.00" >
                                                    @error('stock_sucursal') <span class="text-danger er">{{ $message }}</span> @enderror
                                                    </td>
													<td>
													<input type="number" class="form-control" readonly wire:model.lazy="stock_sucursal.{{$variaciones['referencia_id']}}|0|{{$variaciones['var_nombre'] ?? 0}}|{{$variaciones['var_id'] ?? 0}}">    
													</td>
													<td>
													<input type="number" class="form-control" readonly wire:model.lazy="stock_sucursal_comprometido.{{$variaciones['referencia_id']}}|0|{{$variaciones['var_nombre'] ?? 0}}|{{$variaciones['var_id'] ?? 0}}">    
													</td>
												</tr>
												@endif
											
												@foreach ($sucursales as $llave => $sucu)
												<tr>
													<td>{{$sucu->name}}</td>
													<td>
													<select wire:model="almacen_id.{{$variaciones['referencia_id']}}|{{ $sucu->sucursal_id }}|{{$variaciones['var_nombre'] ?? 0}}|{{$variaciones['var_id'] ?? 0}}"  {{ $forma_edit == 1? 'disabled' : '' }} class="form-control">
            								        <option value="1" >Sin almacen</option>
                                                    @foreach($almacenes as $a)
                                                    <option value="{{$a->id}}">{{$a->nombre}}</option>
                                                    @endforeach
    								            	</select>       
													</td>
													<td>
						    					      <input type="number" required {{ ($es_sucursal == 1) && (auth()->user()->id != $sucu->sucursal_id )? 'readonly' : '' }} {{ $forma_edit == 1? 'readonly' : '' }} class="form-control" wire:change="CambiarStockDisponible('{{$variaciones['referencia_id']}}|{{ $sucu->sucursal_id }}|{{$variaciones['var_nombre'] ?? 0}}|{{$variaciones['var_id'] ?? 0}}')" wire:model="real_stock_sucursal.{{$variaciones['referencia_id']}}|{{ $sucu->sucursal_id }}|{{$variaciones['var_nombre'] ?? 0}}|{{$variaciones['var_id'] ?? 0}}" />
                            						</td>
													<td>
													<input type="number" class="form-control"  readonly wire:model="stock_sucursal.{{$variaciones['referencia_id']}}|{{ $sucu->sucursal_id }}|{{$variaciones['var_nombre'] ?? 0}}|{{$variaciones['var_id'] ?? 0}}">     
													</td>
													<td>
													<input type="number" class="form-control"  readonly wire:model="stock_sucursal_comprometido.{{$variaciones['referencia_id']}}|{{ $sucu->sucursal_id }}|{{$variaciones['var_nombre'] ?? 0}}|{{$variaciones['var_id'] ?? 0}}">     
													</td>
												</tr>
											    @endforeach
											
											</tbody>
										</table>
									</div>    
								</div>
                                </div>
                                </div>  
                                </div>
                                </div>
								

                                @endforeach
                                </div>
                                @endif
                                
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