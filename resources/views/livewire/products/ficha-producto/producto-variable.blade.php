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
                                          <button type="button" style="float:right;"  onclick="ConfirmVariacion('{{$variaciones['id']}}')">
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
                                         <input maxlength="20" required {{ $es_sucursal == 1? 'readonly' : '' }} {{ $forma_edit == 1? 'readonly' : '' }} type="text" wire:model.lazy="cod_variacion.{{$variaciones['referencia_id']}}" class="form-control">
                                        <p style="color:#637381;">* Maximo 20 caracteres</p> 
                                        @error('cod_variacion.' . $variaciones['referencia_id'] ) <span class="text-danger er">{{ $message }}</span> @enderror 
									</div>
								</div>
								
								<div class="col-9"></div>
								<!--------- PRECIOS NUEVOS ------------>
								<div class="col-12">
								<table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Lista de precios</th>
                                            <th class="text-center">% margen sobre Costo</th>
                                            <th>Precio</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(Auth::user()->sucursal != 1 && Auth::user()->profile != "Cajero")
                                        <tr>
                                            <td>
                                                <label>Costo</label>
                                            </td>
                                            <td></td>
                                            <td>
                                                <!-- Reemplazado con el input del segundo bloque -->
                                                <input wire:keydown.enter="CambiarCostoReglaPrecio('{{$variaciones['referencia_id']}}')" wire:change="CambiarCostoReglaPrecio('{{$variaciones['referencia_id']}}')" wire:model.lazy="costos_variacion.{{$variaciones['referencia_id']}}" required {{ $es_sucursal == 1 ? 'readonly' : '' }} {{ $forma_edit == 1 ? 'readonly' : '' }} type="number" {{ $tipo_producto > 1 ? 'readonly' : '' }} class="form-control" placeholder="ej: 0.00">
                                                @error('costos_variacion')
                                                <span class="text-danger er">{{ $message }}</span>
                                                @enderror
                                            </td>
                                        </tr>
                                        @endif
                                
                                        @if(Auth::user()->profile != "Cajero" && 0 < $sucursales->count())
                                        <tr>
                                            <td>
                                                <label class="d-flex">
                                                    Precio de venta a sucursales
                                                    <div style="margin-left:3px; cursor:pointer;" class="example-popover" data-container="body" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Es el precio de venta a las sucursales">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle">
                                                            <circle cx="12" cy="12" r="10"></circle>
                                                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                                                        </svg>
                                                    </div>
                                                </label>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input {{ ($regla_precio_interno == 1 || $forma_edit) == 1 ? 'readonly' : '' }} type="number" wire:model="porcentaje_regla_precio_interno_variacion.{{$variaciones['referencia_id']}}" wire:change="CambiarPorcentajeReglaPrecio('{{$variaciones['referencia_id']}}',0,1)" wire:keydown.enter="CambiarPorcentajeReglaPrecio('{{$variaciones['referencia_id']}}',0,1)" class="form-control">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">% </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <!-- Reemplazado con el input del segundo bloque -->
                                                <input required {{ $configuracion_precio_interno == 1 ? 'readonly' : '' }} {{ $es_sucursal == 1 ? 'readonly' : '' }} {{ $forma_edit == 1 ? 'readonly' : '' }} type="number" wire:model.lazy="precios_internos_variacion.{{$variaciones['referencia_id']}}" wire:change="CambiarPorcentajePorCambioPrecio('{{$variaciones['referencia_id']}}',0,1)" wire:keydown.enter="CambiarPorcentajePorCambioPrecio('{{$variaciones['referencia_id']}}',0,1)" class="form-control" placeholder="ej: 0.00">
                                                @error('precios_internos_variacion')
                                                <span class="text-danger er">{{ $message }}</span>
                                                @enderror
                                                @if($configuracion_precio_interno == 1)
                                                <p style="color:green;">El precio interno es igual al costo</p>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                
                                        <tr>
                                            <td>
                                                <label class="d-flex">
                                                    Precio de venta
                                                </label>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input {{ $regla_precio[0] == 1 || $forma_edit == 1 ? 'readonly' : '' }} type="number" wire:model="porcentaje_regla_precio.{{$variaciones['referencia_id']}}|0" class="form-control" wire:change="CambiarPorcentajeReglaPrecio('{{$variaciones['referencia_id']}}',0,2)" wire:keydown.enter="CambiarPorcentajeReglaPrecio('{{$variaciones['referencia_id']}}',0,2)">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">% </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <!-- Reemplazado con el input del segundo bloque -->
                                                <input wire:change="CambiarPorcentajePorCambioPrecio('{{$variaciones['referencia_id']}}',0,2)" wire:keydown.enter="CambiarPorcentajePorCambioPrecio('{{$variaciones['referencia_id']}}',0,2)" required {{ $es_sucursal == 1 ? 'readonly' : '' }} {{ $forma_edit == 1 ? 'readonly' : '' }} type="number" wire:model.lazy="precio_lista.{{$variaciones['referencia_id']}}|0" class="form-control" placeholder="ej: 0.00">
                                                @error('precio_lista')
                                                <span class="text-danger er">{{ $message }}</span>
                                                @enderror
                                            </td>
                                        </tr>
                                
                                        @if($lista_precios != null)
                                        @foreach($lista_precios as $key => $lp)
                                        <tr>
                                            <td>
                                                <label>Precio {{$lp->nombre}}</label>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input {{ (isset($regla_precio[$lp->id]) && $regla_precio[$lp->id] == 1) ? 'readonly' : '' }} {{ $forma_edit == 1 ? 'readonly' : '' }} type="number" wire:model="porcentaje_regla_precio.{{$variaciones['referencia_id']}}|{{ $lp->id }}" wire:change="CambiarPorcentajeReglaPrecio('{{$variaciones['referencia_id']}}',{{$lp->id}},2)" wire:keydown.enter="CambiarPorcentajeReglaPrecio('{{$variaciones['referencia_id']}}',{{$lp->id}},2)" class="form-control">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">% </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <!-- Reemplazado con el input del segundo bloque -->
                                                <input wire:model="precio_lista.{{$variaciones['referencia_id']}}|{{ $lp->id }}" wire:change="CambiarPorcentajePorCambioPrecio('{{$variaciones['referencia_id']}}',{{ $lp->id }},2)" wire:keydown.enter="CambiarPorcentajePorCambioPrecio('{{$variaciones['referencia_id']}}',{{ $lp->id }},2)" required {{ $es_sucursal == 1 ? 'readonly' : '' }} {{ $forma_edit == 1 ? 'readonly' : '' }} type="number" class="form-control">
                                                @error('precio_lista')
                                                <span class="text-danger er">{{ $message }}</span>
                                                @enderror
                                            </td>
                                        </tr>
                                        @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                </div>
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
												<tr {{ ($muestra_stock_otras_sucursales == 0) && ($sucu->sucursal_id != $comercio_id) && (Auth::user()->casa_central_user_id != $comercio_id) ? 'hidden' : ''}}>
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